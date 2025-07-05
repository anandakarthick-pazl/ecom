<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ApiController extends Controller
{
    /**
     * API management dashboard
     */
    public function index()
    {
        $apiStats = $this->getApiStats();
        $recentRequests = $this->getRecentApiRequests();
        $topEndpoints = $this->getTopEndpoints();
        $apiHealth = $this->checkApiHealth();

        return view('super-admin.api.index', compact('apiStats', 'recentRequests', 'topEndpoints', 'apiHealth'));
    }

    /**
     * API keys management
     */
    public function keys(Request $request)
    {
        $query = DB::table('api_keys')
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            })
            ->when($request->status, function ($q) use ($request) {
                if ($request->status === 'active') {
                    $q->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $q->where('is_active', false);
                }
            });

        $apiKeys = $query->orderBy('created_at', 'desc')->paginate(20);

        $keyStats = [
            'total' => DB::table('api_keys')->count(),
            'active' => DB::table('api_keys')->where('is_active', true)->count(),
            'inactive' => DB::table('api_keys')->where('is_active', false)->count(),
            'requests_today' => $this->getApiRequestsCount(today()),
        ];

        return view('super-admin.api.keys', compact('apiKeys', 'keyStats'));
    }

    /**
     * Create API key
     */
    public function createKey(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array',
            'permissions.*' => 'string',
            'rate_limit' => 'nullable|integer|min:1|max:10000',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $apiKey = Str::random(64);
        $hashedKey = Hash::make($apiKey);

        DB::table('api_keys')->insert([
            'name' => $request->name,
            'description' => $request->description,
            'key_hash' => $hashedKey,
            'permissions' => json_encode($request->permissions),
            'rate_limit' => $request->rate_limit ?? 1000,
            'expires_at' => $request->expires_at,
            'is_active' => true,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Log the creation
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'api_key_name' => $request->name,
                'permissions' => $request->permissions,
            ])
            ->log('API key created');

        return back()->with('success', 'API key created successfully.')
                    ->with('api_key', $apiKey); // Show key only once
    }

    /**
     * Delete API key
     */
    public function deleteKey($keyId)
    {
        $apiKey = DB::table('api_keys')->where('id', $keyId)->first();

        if (!$apiKey) {
            return back()->with('error', 'API key not found.');
        }

        DB::table('api_keys')->where('id', $keyId)->delete();

        // Log the deletion
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'api_key_name' => $apiKey->name,
                'api_key_id' => $keyId,
            ])
            ->log('API key deleted');

        return back()->with('success', 'API key deleted successfully.');
    }

    /**
     * API documentation
     */
    public function documentation()
    {
        $endpoints = $this->getApiEndpoints();
        $examples = $this->getApiExamples();
        $authentication = $this->getAuthenticationDocs();

        return view('super-admin.api.documentation', compact('endpoints', 'examples', 'authentication'));
    }

    /**
     * Webhooks management
     */
    public function webhooks(Request $request)
    {
        $query = DB::table('webhooks')
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('url', 'like', '%' . $request->search . '%');
            })
            ->when($request->status, function ($q) use ($request) {
                if ($request->status === 'active') {
                    $q->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $q->where('is_active', false);
                }
            })
            ->when($request->event, function ($q) use ($request) {
                $q->where('events', 'like', '%' . $request->event . '%');
            });

        $webhooks = $query->orderBy('created_at', 'desc')->paginate(20);

        $webhookStats = [
            'total' => DB::table('webhooks')->count(),
            'active' => DB::table('webhooks')->where('is_active', true)->count(),
            'deliveries_today' => $this->getWebhookDeliveriesCount(today()),
            'failed_deliveries' => $this->getFailedDeliveriesCount(),
        ];

        $availableEvents = $this->getAvailableWebhookEvents();

        return view('super-admin.api.webhooks', compact('webhooks', 'webhookStats', 'availableEvents'));
    }

    /**
     * Create webhook
     */
    public function createWebhook(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'events' => 'required|array',
            'events.*' => 'string',
            'secret' => 'nullable|string|min:8|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $webhookId = DB::table('webhooks')->insertGetId([
            'name' => $request->name,
            'url' => $request->url,
            'events' => json_encode($request->events),
            'secret' => $request->secret ? Hash::make($request->secret) : null,
            'description' => $request->description,
            'is_active' => true,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Test the webhook
        $this->testWebhook($webhookId, 'webhook.created');

        // Log the creation
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'webhook_name' => $request->name,
                'webhook_url' => $request->url,
                'events' => $request->events,
            ])
            ->log('Webhook created');

        return back()->with('success', 'Webhook created successfully.');
    }

    /**
     * Delete webhook
     */
    public function deleteWebhook($webhookId)
    {
        $webhook = DB::table('webhooks')->where('id', $webhookId)->first();

        if (!$webhook) {
            return back()->with('error', 'Webhook not found.');
        }

        DB::table('webhooks')->where('id', $webhookId)->delete();

        // Clean up webhook deliveries
        DB::table('webhook_deliveries')->where('webhook_id', $webhookId)->delete();

        // Log the deletion
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'webhook_name' => $webhook->name,
                'webhook_url' => $webhook->url,
                'webhook_id' => $webhookId,
            ])
            ->log('Webhook deleted');

        return back()->with('success', 'Webhook deleted successfully.');
    }

    /**
     * Test webhook delivery
     */
    public function testWebhook($webhookId, $event = 'test')
    {
        $webhook = DB::table('webhooks')->where('id', $webhookId)->first();

        if (!$webhook) {
            return response()->json(['error' => 'Webhook not found'], 404);
        }

        $payload = [
            'event' => $event,
            'timestamp' => now()->toISOString(),
            'data' => [
                'test' => true,
                'message' => 'This is a test webhook delivery',
                'webhook_id' => $webhookId,
            ],
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'HerbalEcom-Webhook/1.0',
            'X-Webhook-Event' => $event,
            'X-Webhook-Delivery' => Str::uuid(),
        ];

        if ($webhook->secret) {
            $signature = hash_hmac('sha256', json_encode($payload), $webhook->secret);
            $headers['X-Webhook-Signature'] = 'sha256=' . $signature;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($webhook->url, $payload);

            $deliveryId = DB::table('webhook_deliveries')->insertGetId([
                'webhook_id' => $webhookId,
                'event' => $event,
                'payload' => json_encode($payload),
                'response_code' => $response->status(),
                'response_body' => $response->body(),
                'delivered_at' => now(),
                'created_at' => now(),
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Webhook delivered successfully',
                    'delivery_id' => $deliveryId,
                    'response_code' => $response->status(),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Webhook delivery failed',
                    'delivery_id' => $deliveryId,
                    'response_code' => $response->status(),
                    'response_body' => $response->body(),
                ], 400);
            }

        } catch (\Exception $e) {
            DB::table('webhook_deliveries')->insert([
                'webhook_id' => $webhookId,
                'event' => $event,
                'payload' => json_encode($payload),
                'response_code' => 0,
                'response_body' => $e->getMessage(),
                'delivered_at' => now(),
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Webhook delivery failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Private Helper Methods

    private function getApiStats()
    {
        return [
            'total_requests_today' => $this->getApiRequestsCount(today()),
            'total_requests_week' => $this->getApiRequestsCount(Carbon::now()->subDays(7)),
            'total_requests_month' => $this->getApiRequestsCount(Carbon::now()->subDays(30)),
            'active_api_keys' => DB::table('api_keys')->where('is_active', true)->count(),
            'average_response_time' => 145, // milliseconds
            'error_rate' => 2.5, // percentage
            'rate_limit_hits' => 25,
            'most_used_endpoint' => '/api/v1/products',
        ];
    }

    private function getRecentApiRequests()
    {
        // Mock implementation - you'd need to implement actual API request logging
        return [
            [
                'timestamp' => Carbon::now()->subMinutes(5),
                'method' => 'GET',
                'endpoint' => '/api/v1/products',
                'api_key' => 'Mobile App',
                'response_code' => 200,
                'response_time' => 120,
                'ip' => '192.168.1.100',
            ],
            [
                'timestamp' => Carbon::now()->subMinutes(8),
                'method' => 'POST',
                'endpoint' => '/api/v1/orders',
                'api_key' => 'E-commerce Integration',
                'response_code' => 201,
                'response_time' => 89,
                'ip' => '10.0.0.5',
            ],
            [
                'timestamp' => Carbon::now()->subMinutes(12),
                'method' => 'GET',
                'endpoint' => '/api/v1/users/profile',
                'api_key' => 'Mobile App',
                'response_code' => 401,
                'response_time' => 45,
                'ip' => '192.168.1.100',
            ],
        ];
    }

    private function getTopEndpoints()
    {
        // Mock implementation
        return [
            ['endpoint' => '/api/v1/products', 'requests' => 1250, 'avg_response_time' => 125],
            ['endpoint' => '/api/v1/orders', 'requests' => 890, 'avg_response_time' => 89],
            ['endpoint' => '/api/v1/users', 'requests' => 650, 'avg_response_time' => 156],
            ['endpoint' => '/api/v1/categories', 'requests' => 450, 'avg_response_time' => 78],
            ['endpoint' => '/api/v1/auth/login', 'requests' => 320, 'avg_response_time' => 234],
        ];
    }

    private function checkApiHealth()
    {
        $endpoints = [
            '/api/v1/health',
            '/api/v1/status',
            '/api/v1/version',
        ];

        $health = [];
        foreach ($endpoints as $endpoint) {
            try {
                $response = Http::timeout(5)->get(url($endpoint));
                $health[$endpoint] = [
                    'status' => $response->successful() ? 'healthy' : 'error',
                    'response_time' => $response->transferStats?->getTransferTime() * 1000 ?? 0,
                    'status_code' => $response->status(),
                ];
            } catch (\Exception $e) {
                $health[$endpoint] = [
                    'status' => 'error',
                    'response_time' => 0,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $health;
    }

    private function getApiRequestsCount($since)
    {
        // Mock implementation - implement actual API request counting
        return rand(100, 1000);
    }

    private function getApiEndpoints()
    {
        return [
            [
                'group' => 'Authentication',
                'endpoints' => [
                    [
                        'method' => 'POST',
                        'path' => '/api/v1/auth/login',
                        'description' => 'Authenticate user and get access token',
                        'parameters' => ['email', 'password'],
                        'response' => '{"token": "...", "user": {...}}',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/v1/auth/refresh',
                        'description' => 'Refresh access token',
                        'parameters' => ['refresh_token'],
                        'response' => '{"token": "...", "expires_at": "..."}',
                    ],
                ],
            ],
            [
                'group' => 'Products',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/products',
                        'description' => 'Get list of products',
                        'parameters' => ['page', 'limit', 'category', 'search'],
                        'response' => '{"data": [...], "meta": {...}}',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/products/{id}',
                        'description' => 'Get product details',
                        'parameters' => ['id'],
                        'response' => '{"id": 1, "name": "...", ...}',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/v1/products',
                        'description' => 'Create new product',
                        'parameters' => ['name', 'description', 'price', 'category_id'],
                        'response' => '{"id": 1, "name": "...", ...}',
                    ],
                ],
            ],
            [
                'group' => 'Orders',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/v1/orders',
                        'description' => 'Get list of orders',
                        'parameters' => ['page', 'limit', 'status', 'date_from', 'date_to'],
                        'response' => '{"data": [...], "meta": {...}}',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/api/v1/orders',
                        'description' => 'Create new order',
                        'parameters' => ['customer_id', 'items', 'shipping_address'],
                        'response' => '{"id": 1, "status": "pending", ...}',
                    ],
                ],
            ],
        ];
    }

    private function getApiExamples()
    {
        return [
            [
                'title' => 'Authentication',
                'description' => 'How to authenticate and use the API',
                'code' => 'curl -X POST \
  ' . url('/api/v1/auth/login') . ' \
  -H "Content-Type: application/json" \
  -d \'{"email": "user@example.com", "password": "password"}\'',
            ],
            [
                'title' => 'Get Products',
                'description' => 'Fetch a list of products with pagination',
                'code' => 'curl -X GET \
  "' . url('/api/v1/products?page=1&limit=10') . '" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json"',
            ],
            [
                'title' => 'Create Order',
                'description' => 'Create a new order',
                'code' => 'curl -X POST \
  ' . url('/api/v1/orders') . ' \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d \'{"customer_id": 1, "items": [{"product_id": 1, "quantity": 2}]}\'',
            ],
        ];
    }

    private function getAuthenticationDocs()
    {
        return [
            'type' => 'Bearer Token',
            'header' => 'Authorization: Bearer YOUR_API_KEY',
            'description' => 'Include your API key in the Authorization header for all requests.',
            'rate_limits' => [
                'default' => '1000 requests per hour',
                'authenticated' => '5000 requests per hour',
                'premium' => '10000 requests per hour',
            ],
            'error_codes' => [
                '401' => 'Unauthorized - Invalid or missing API key',
                '403' => 'Forbidden - API key lacks required permissions',
                '429' => 'Too Many Requests - Rate limit exceeded',
                '500' => 'Internal Server Error - Something went wrong on our end',
            ],
        ];
    }

    private function getWebhookDeliveriesCount($since)
    {
        // Mock implementation
        return rand(50, 200);
    }

    private function getFailedDeliveriesCount()
    {
        // Mock implementation
        return rand(5, 25);
    }

    private function getAvailableWebhookEvents()
    {
        return [
            'user.created' => 'User Created',
            'user.updated' => 'User Updated',
            'user.deleted' => 'User Deleted',
            'order.created' => 'Order Created',
            'order.updated' => 'Order Updated',
            'order.completed' => 'Order Completed',
            'order.cancelled' => 'Order Cancelled',
            'payment.completed' => 'Payment Completed',
            'payment.failed' => 'Payment Failed',
            'product.created' => 'Product Created',
            'product.updated' => 'Product Updated',
            'product.deleted' => 'Product Deleted',
            'subscription.created' => 'Subscription Created',
            'subscription.updated' => 'Subscription Updated',
            'subscription.cancelled' => 'Subscription Cancelled',
            'company.created' => 'Company Created',
            'company.updated' => 'Company Updated',
            'company.suspended' => 'Company Suspended',
        ];
    }

    /**
     * Update API key status
     */
    public function updateKeyStatus($keyId, Request $request)
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $apiKey = DB::table('api_keys')->where('id', $keyId)->first();

        if (!$apiKey) {
            return back()->with('error', 'API key not found.');
        }

        DB::table('api_keys')
            ->where('id', $keyId)
            ->update([
                'is_active' => $request->is_active,
                'updated_at' => now(),
            ]);

        $status = $request->is_active ? 'activated' : 'deactivated';

        // Log the action
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'api_key_name' => $apiKey->name,
                'api_key_id' => $keyId,
                'new_status' => $status,
            ])
            ->log("API key {$status}");

        return back()->with('success', "API key {$status} successfully.");
    }

    /**
     * Update webhook status
     */
    public function updateWebhookStatus($webhookId, Request $request)
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $webhook = DB::table('webhooks')->where('id', $webhookId)->first();

        if (!$webhook) {
            return back()->with('error', 'Webhook not found.');
        }

        DB::table('webhooks')
            ->where('id', $webhookId)
            ->update([
                'is_active' => $request->is_active,
                'updated_at' => now(),
            ]);

        $status = $request->is_active ? 'activated' : 'deactivated';

        // Log the action
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'webhook_name' => $webhook->name,
                'webhook_id' => $webhookId,
                'new_status' => $status,
            ])
            ->log("Webhook {$status}");

        return back()->with('success', "Webhook {$status} successfully.");
    }

    /**
     * Get webhook delivery history
     */
    public function getWebhookDeliveries($webhookId)
    {
        $deliveries = DB::table('webhook_deliveries')
            ->where('webhook_id', $webhookId)
            ->orderBy('delivered_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($deliveries);
    }

    /**
     * Retry failed webhook delivery
     */
    public function retryWebhookDelivery($deliveryId)
    {
        $delivery = DB::table('webhook_deliveries')->where('id', $deliveryId)->first();

        if (!$delivery) {
            return response()->json(['error' => 'Delivery not found'], 404);
        }

        $webhook = DB::table('webhooks')->where('id', $delivery->webhook_id)->first();

        if (!$webhook) {
            return response()->json(['error' => 'Webhook not found'], 404);
        }

        // Retry the delivery
        return $this->testWebhook($webhook->id, $delivery->event);
    }
}
