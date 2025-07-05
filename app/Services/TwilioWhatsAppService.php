<?php

namespace App\Services;

use App\Models\SuperAdmin\WhatsAppConfig;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class TwilioWhatsAppService
{
    protected $client;
    protected $config;

    public function __construct(WhatsAppConfig $config = null)
    {
        $this->config = $config;
        
        if ($config && $config->isConfigured()) {
            $this->client = new Client($config->twilio_account_sid, $config->twilio_auth_token);
        }
    }

    /**
     * Send WhatsApp message with PDF attachment
     */
    public function sendBillPDF(Order $order, $pdfPath, $customMessage = null)
    {
        try {
            if (!$this->config || !$this->config->isConfigured()) {
                throw new \Exception('WhatsApp is not configured for this company');
            }

            if (!$this->client) {
                throw new \Exception('Twilio client not initialized');
            }

            // Validate customer phone number
            $customerPhone = $this->formatPhoneNumber($order->customer_mobile);
            if (!$customerPhone) {
                throw new \Exception('Invalid customer phone number: ' . $order->customer_mobile);
            }

            // Prepare message
            $message = $customMessage ?: $this->prepareMessage($order);

            // Check if file exists and is within size limits
            if (!file_exists($pdfPath)) {
                throw new \Exception('PDF file not found: ' . $pdfPath);
            }

            $fileSize = filesize($pdfPath);
            $maxSize = ($this->config->max_file_size_mb ?? 5) * 1024 * 1024; // Convert to bytes

            if ($fileSize > $maxSize) {
                throw new \Exception('PDF file too large. Maximum size: ' . $this->config->max_file_size_mb . 'MB');
            }

            // Upload file to a publicly accessible URL
            $publicUrl = $this->uploadToPublicStorage($pdfPath, $order);

            // Send WhatsApp message
            $messageResult = $this->client->messages->create(
                $customerPhone, // To
                [
                    'from' => $this->config->getWhatsAppNumber(),
                    'body' => $message,
                    'mediaUrl' => [$publicUrl]
                ]
            );

            // Log successful sending
            Log::info('WhatsApp bill sent successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_phone' => $customerPhone,
                'message_sid' => $messageResult->sid,
                'company_id' => $order->company_id,
                'media_url' => $publicUrl
            ]);

            return [
                'success' => true,
                'message_sid' => $messageResult->sid,
                'message' => 'Bill sent successfully via WhatsApp',
                'sent_to' => $customerPhone,
                'sent_at' => now()
            ];

        } catch (TwilioException $e) {
            Log::error('Twilio WhatsApp error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            return [
                'success' => false,
                'error' => 'WhatsApp sending failed: ' . $e->getMessage(),
                'error_code' => $e->getCode()
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp service error', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send test WhatsApp message
     */
    public function sendTestMessage($phoneNumber, $message = null)
    {
        try {
            if (!$this->config || !$this->config->isConfigured()) {
                throw new \Exception('WhatsApp is not configured');
            }

            if (!$this->client) {
                throw new \Exception('Twilio client not initialized');
            }

            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            if (!$formattedPhone) {
                throw new \Exception('Invalid phone number format');
            }

            $testMessage = $message ?: 'This is a test message from your herbal e-commerce platform. WhatsApp integration is working correctly!';

            $messageResult = $this->client->messages->create(
                $formattedPhone,
                [
                    'from' => $this->config->getWhatsAppNumber(),
                    'body' => $testMessage
                ]
            );

            Log::info('WhatsApp test message sent', [
                'phone' => $formattedPhone,
                'message_sid' => $messageResult->sid
            ]);

            return [
                'success' => true,
                'message_sid' => $messageResult->sid,
                'message' => 'Test message sent successfully',
                'sent_to' => $formattedPhone
            ];

        } catch (TwilioException $e) {
            Log::error('Twilio test message error', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Test message failed: ' . $e->getMessage()
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp test message error', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number for WhatsApp
     */
    protected function formatPhoneNumber($phone)
    {
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Handle Indian numbers
        if (strlen($phone) === 10) {
            $phone = '91' . $phone; // Add India country code
        } elseif (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            $phone = '91' . substr($phone, 1); // Remove leading 0 and add India country code
        }
        
        // Add + sign and whatsapp: prefix
        if (strlen($phone) >= 10) {
            return 'whatsapp:+' . $phone;
        }
        
        return null;
    }

    /**
     * Prepare message text with order details
     */
    protected function prepareMessage(Order $order)
    {
        $template = $this->config->getDefaultMessageTemplate();
        
        $placeholders = [
            '{{customer_name}}' => $order->customer_name,
            '{{order_number}}' => $order->order_number,
            '{{total}}' => number_format($order->total, 2),
            '{{company_name}}' => $order->company->name ?? 'Our Store',
            '{{order_date}}' => $order->created_at->format('d M Y'),
            '{{status}}' => ucfirst($order->status),
            '{{payment_status}}' => ucfirst($order->payment_status ?? 'pending')
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $template);
    }

    /**
     * Upload PDF to public storage and return URL
     * FIXED VERSION - Multiple solutions for public accessibility
     */
    protected function uploadToPublicStorage($pdfPath, Order $order)
    {
        $filename = 'bill_' . $order->order_number . '_' . time() . '.pdf';
        
        // SOLUTION 1: Check if we're in production with a public domain
        if ($this->isProductionEnvironment()) {
            $publicPath = 'whatsapp-bills/' . $filename;
            Storage::disk('public')->put($publicPath, file_get_contents($pdfPath));
            return Storage::disk('public')->url($publicPath);
        }
        
        // SOLUTION 2: Use a temporary file hosting service for development
        return $this->uploadToTemporaryHosting($pdfPath, $filename);
    }

    /**
     * Check if we're in a production environment with public URL
     */
    protected function isProductionEnvironment()
    {
        $appUrl = config('app.url');
        
        // Check if URL is publicly accessible (not localhost, not 127.0.0.1, not local IPs)
        return !str_contains($appUrl, 'localhost') && 
               !str_contains($appUrl, '127.0.0.1') && 
               !str_contains($appUrl, '192.168.') && 
               !str_contains($appUrl, '10.0.') &&
               !str_contains($appUrl, '172.16.') &&
               config('app.env') !== 'local';
    }

    /**
     * Upload to temporary hosting service for development/testing
     * Using file.io as a free temporary file hosting service
     */
    protected function uploadToTemporaryHosting($pdfPath, $filename)
    {
        try {
            // Option 1: Use file.io (free temporary file hosting)
            $response = $this->uploadToFileIo($pdfPath, $filename);
            if ($response) {
                return $response;
            }

            // Option 2: Use transfer.sh as fallback
            $response = $this->uploadToTransferSh($pdfPath, $filename);
            if ($response) {
                return $response;
            }

            // Option 3: Use 0x0.st as another fallback
            $response = $this->uploadToNullPointer($pdfPath, $filename);
            if ($response) {
                return $response;
            }

            throw new \Exception('All temporary hosting services failed');

        } catch (\Exception $e) {
            Log::error('Temporary file hosting failed', [
                'error' => $e->getMessage(),
                'file' => $filename
            ]);
            
            // Fallback: Suggest using ngrok or similar tunneling service
            throw new \Exception('Unable to create publicly accessible URL for media. For development, please use ngrok or deploy to a public server. Error: ' . $e->getMessage());
        }
    }

    /**
     * Upload to file.io (24-hour temporary hosting)
     */
    protected function uploadToFileIo($pdfPath, $filename)
    {
        try {
            $ch = curl_init();
            
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://file.io/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => [
                    'file' => new \CURLFile($pdfPath, 'application/pdf', $filename)
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_USERAGENT => 'WhatsApp-Bill-Service/1.0'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                $data = json_decode($response, true);
                if ($data && $data['success'] && isset($data['link'])) {
                    Log::info('File uploaded to file.io', [
                        'url' => $data['link'],
                        'filename' => $filename
                    ]);
                    return $data['link'];
                }
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::warning('file.io upload failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Upload to transfer.sh (14-day hosting)
     */
    protected function uploadToTransferSh($pdfPath, $filename)
    {
        try {
            $ch = curl_init();
            
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://transfer.sh/' . $filename,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => file_get_contents($pdfPath),
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/pdf'
                ]
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && filter_var(trim($response), FILTER_VALIDATE_URL)) {
                Log::info('File uploaded to transfer.sh', [
                    'url' => trim($response),
                    'filename' => $filename
                ]);
                return trim($response);
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::warning('transfer.sh upload failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Upload to 0x0.st (365-day hosting)
     */
    protected function uploadToNullPointer($pdfPath, $filename)
    {
        try {
            $ch = curl_init();
            
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://0x0.st',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => [
                    'file' => new \CURLFile($pdfPath, 'application/pdf', $filename)
                ],
                CURLOPT_TIMEOUT => 30
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && filter_var(trim($response), FILTER_VALIDATE_URL)) {
                Log::info('File uploaded to 0x0.st', [
                    'url' => trim($response),
                    'filename' => $filename
                ]);
                return trim($response);
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::warning('0x0.st upload failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get account information
     */
    public function getAccountInfo()
    {
        try {
            if (!$this->client) {
                throw new \Exception('Twilio client not initialized');
            }

            $account = $this->client->api->accounts($this->config->twilio_account_sid)->fetch();

            return [
                'success' => true,
                'account_sid' => $account->sid,
                'friendly_name' => $account->friendlyName,
                'status' => $account->status,
                'type' => $account->type,
                'date_created' => $account->dateCreated->format('Y-m-d H:i:s')
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate Twilio credentials
     */
    public function validateCredentials($accountSid, $authToken)
    {
        try {
            $testClient = new Client($accountSid, $authToken);
            $account = $testClient->api->accounts($accountSid)->fetch();

            return [
                'success' => true,
                'account_name' => $account->friendlyName,
                'account_status' => $account->status
            ];

        } catch (TwilioException $e) {
            return [
                'success' => false,
                'error' => 'Invalid credentials: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get message delivery status
     */
    public function getMessageStatus($messageSid)
    {
        try {
            if (!$this->client) {
                throw new \Exception('Twilio client not initialized');
            }

            $message = $this->client->messages($messageSid)->fetch();

            return [
                'success' => true,
                'status' => $message->status,
                'error_code' => $message->errorCode,
                'error_message' => $message->errorMessage,
                'date_created' => $message->dateCreated->format('Y-m-d H:i:s'),
                'date_sent' => $message->dateSent ? $message->dateSent->format('Y-m-d H:i:s') : null,
                'date_updated' => $message->dateUpdated->format('Y-m-d H:i:s')
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get usage statistics
     */
    public function getUsageStats($startDate = null, $endDate = null)
    {
        try {
            if (!$this->client) {
                throw new \Exception('Twilio client not initialized');
            }

            $options = [];
            if ($startDate) {
                $options['startDate'] = $startDate;
            }
            if ($endDate) {
                $options['endDate'] = $endDate;
            }

            $usage = $this->client->usage->records->read($options);

            return [
                'success' => true,
                'usage' => collect($usage)->map(function ($record) {
                    return [
                        'category' => $record->category,
                        'description' => $record->description,
                        'usage' => $record->usage,
                        'usage_unit' => $record->usageUnit,
                        'price' => $record->price,
                        'price_unit' => $record->priceUnit,
                        'start_date' => $record->startDate,
                        'end_date' => $record->endDate
                    ];
                })->toArray()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
