<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Test - Herbal Ecom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">
                <i class="fas fa-shield-alt text-blue-600 mr-3"></i>
                Authentication System Test
            </h1>

            <!-- Database Status -->
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-green-800 mb-2">Database Status</h3>
                    <p class="text-green-700">✅ Connected successfully</p>
                    <p class="text-sm text-green-600 mt-1">Users: {{ $usersCount }} | Companies: {{ $companiesCount }}</p>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">Quick Actions</h3>
                    <a href="/create-test-users" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                        Create Test Users
                    </a>
                    <a href="/login" class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm ml-2">
                        Go to Login
                    </a>
                </div>
            </div>

            <!-- Users List -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-users text-gray-600 mr-2"></i>
                    Available Users
                </h3>
                
                @if($users->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Super Admin</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Company ID</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Test Login</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($users as $user)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $user->id }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $user->name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $user->email }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        @if($user->is_super_admin)
                                            <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">Yes</span>
                                        @else
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">No</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $user->company_id ?: 'None' }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <button onclick="testLogin('{{ $user->email }}')" 
                                                class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">
                                            Test Login
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-yellow-800">No users found in database. Click "Create Test Users" to set up test accounts.</p>
                    </div>
                @endif
            </div>

            <!-- Companies List -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-building text-gray-600 mr-2"></i>
                    Available Companies
                </h3>
                
                @if($companies->count() > 0)
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($companies as $company)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900">{{ $company->name }}</h4>
                            <p class="text-sm text-gray-600">Slug: {{ $company->slug }}</p>
                            <p class="text-xs text-gray-500">ID: {{ $company->id }}</p>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-yellow-800">No companies found in database.</p>
                    </div>
                @endif
            </div>

            <!-- Test Login Form -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-key text-gray-600 mr-2"></i>
                    Test Login Manually
                </h3>
                
                <form onsubmit="testLoginManual(event)" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="testEmail" class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                               placeholder="Enter email address" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="testPassword" class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                               placeholder="Enter password (default: password123)" value="password123" required>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        Test Authentication
                    </button>
                </form>
                
                <div id="testResult" class="mt-4 hidden">
                    <!-- Test results will appear here -->
                </div>
            </div>

            <!-- Default Login Credentials -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-4">Default Test Credentials</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded border">
                        <h4 class="font-semibold text-purple-800">Super Admin</h4>
                        <p class="text-sm text-gray-600">Email: superadmin@herbalecom.com</p>
                        <p class="text-sm text-gray-600">Password: password123</p>
                        <p class="text-xs text-gray-500 mt-1">Access: Platform Management</p>
                    </div>
                    <div class="bg-white p-4 rounded border">
                        <h4 class="font-semibold text-blue-800">Demo Admin</h4>
                        <p class="text-sm text-gray-600">Email: admin@demo.com</p>
                        <p class="text-sm text-gray-600">Password: password123</p>
                        <p class="text-xs text-gray-500 mt-1">Access: Demo Company Admin</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testLogin(email) {
            document.getElementById('testEmail').value = email;
            testLoginManual({preventDefault: () => {}});
        }

        function testLoginManual(event) {
            event.preventDefault();
            
            const email = document.getElementById('testEmail').value;
            const password = document.getElementById('testPassword').value;
            const resultDiv = document.getElementById('testResult');
            
            resultDiv.innerHTML = '<div class="text-blue-600">Testing authentication...</div>';
            resultDiv.classList.remove('hidden');
            
            fetch('/test-login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email, password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    resultDiv.innerHTML = `<div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded">
                        <strong>Error:</strong> ${data.error}
                    </div>`;
                } else {
                    const status = data.password_correct ? 'success' : 'error';
                    const bgColor = status === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
                    
                    resultDiv.innerHTML = `<div class="${bgColor} border p-3 rounded">
                        <strong>Authentication Test Results:</strong><br>
                        User Found: ${data.user_found ? '✅ Yes' : '❌ No'}<br>
                        Password Correct: ${data.password_correct ? '✅ Yes' : '❌ No'}<br>
                        User Role: ${data.user_data.role}<br>
                        Is Super Admin: ${data.user_data.is_super_admin ? 'Yes' : 'No'}<br>
                        Company ID: ${data.user_data.company_id || 'None'}<br>
                        Status: ${data.user_data.status}
                    </div>`;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `<div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded">
                    <strong>Network Error:</strong> ${error.message}
                </div>`;
            });
        }
    </script>
</body>
</html>
