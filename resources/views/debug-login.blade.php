<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Login - Herbal Ecom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">
                <i class="fas fa-bug text-red-600 mr-3"></i>
                Debug Login System
            </h1>

            <!-- Quick Actions -->
            <div class="grid md:grid-cols-3 gap-4 mb-8">
                <button onclick="createUsers()" class="bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Create Test Users
                </button>
                <a href="/login" class="bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 text-center block">
                    <i class="fas fa-sign-in-alt mr-2"></i>Normal Login
                </a>
                <a href="/test-auth" class="bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 text-center block">
                    <i class="fas fa-flask mr-2"></i>Auth Test Page
                </a>
            </div>

            <!-- Users in Database -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4">Users in Database ({{ count($users) }})</h3>
                @if(count($users) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left">Email</th>
                                    <th class="px-4 py-2 text-left">Name</th>
                                    <th class="px-4 py-2 text-left">Role</th>
                                    <th class="px-4 py-2 text-left">Super Admin</th>
                                    <th class="px-4 py-2 text-left">Company ID</th>
                                    <th class="px-4 py-2 text-left">Quick Test</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($users as $user)
                                <tr>
                                    <td class="px-4 py-2">{{ $user->email }}</td>
                                    <td class="px-4 py-2">{{ $user->name }}</td>
                                    <td class="px-4 py-2">{{ $user->role }}</td>
                                    <td class="px-4 py-2">
                                        @if($user->is_super_admin)
                                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">Yes</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">No</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">{{ $user->company_id ?: 'None' }}</td>
                                    <td class="px-4 py-2">
                                        @if($user->is_super_admin)
                                            <button onclick="fillLogin('{{ $user->email }}', 'super_admin')" 
                                                    class="bg-purple-600 text-white px-2 py-1 rounded text-xs hover:bg-purple-700">
                                                Test Super Admin
                                            </button>
                                        @else
                                            <button onclick="fillLogin('{{ $user->email }}', 'admin')" 
                                                    class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">
                                                Test Admin
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded">
                        <strong>No users found!</strong> Click "Create Test Users" to set up accounts.
                    </div>
                @endif
            </div>

            <!-- Companies Available -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4">Companies Available ({{ count($companies) }})</h3>
                @if(count($companies) > 0)
                    <div class="grid md:grid-cols-3 gap-4">
                        @foreach($companies as $company)
                        <div class="bg-gray-50 border rounded p-3">
                            <strong>{{ $company->name }}</strong><br>
                            <small class="text-gray-600">Slug: {{ $company->slug }}</small>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 p-4 rounded">
                        No companies found. Some will be created with test users.
                    </div>
                @endif
            </div>

            <!-- Debug Login Form -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4">
                    <i class="fas fa-microscope mr-2"></i>Debug Login Test
                </h3>
                
                <form onsubmit="debugLogin(event)" class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Email</label>
                            <input type="email" id="debugEmail" class="w-full px-3 py-2 border rounded" 
                                   placeholder="admin@admin.com" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Password</label>
                            <input type="password" id="debugPassword" class="w-full px-3 py-2 border rounded" 
                                   placeholder="password" value="password" required>
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Login Type</label>
                            <select id="debugLoginType" class="w-full px-3 py-2 border rounded" onchange="toggleCompanySelect()">
                                <option value="super_admin">Super Admin</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div id="companySelectDiv" class="hidden">
                            <label class="block text-sm font-medium mb-1">Company</label>
                            <select id="debugCompany" class="w-full px-3 py-2 border rounded">
                                <option value="">Select Company...</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->slug }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded hover:bg-red-700">
                        <i class="fas fa-bug mr-2"></i>Debug Login Process
                    </button>
                </form>
                
                <div id="debugResults" class="mt-6 hidden">
                    <h4 class="font-semibold mb-2">Debug Results:</h4>
                    <div id="debugLog" class="bg-black text-green-400 p-4 rounded font-mono text-sm max-h-64 overflow-y-auto">
                        <!-- Debug output will appear here -->
                    </div>
                </div>
            </div>

            <!-- Quick Test Buttons -->
            <div class="mt-8 grid md:grid-cols-2 gap-4">
                <div class="bg-purple-50 border border-purple-200 p-4 rounded">
                    <h4 class="font-semibold text-purple-800 mb-2">Test Super Admin</h4>
                    <button onclick="quickTest('admin@admin.com', 'password', 'super_admin')" 
                            class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 w-full">
                        Test: admin@admin.com / password
                    </button>
                </div>
                <div class="bg-blue-50 border border-blue-200 p-4 rounded">
                    <h4 class="font-semibold text-blue-800 mb-2">Test Demo Admin</h4>
                    <button onclick="quickTest('demo@demo.com', 'password', 'admin', 'demo')" 
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                        Test: demo@demo.com / password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function createUsers() {
            fetch('/quick-create-users')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Users created successfully!\n\nSuper Admin: admin@admin.com / password\nDemo Admin: demo@demo.com / password');
                        location.reload();
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => alert('Network error: ' + error.message));
        }

        function fillLogin(email, type) {
            document.getElementById('debugEmail').value = email;
            document.getElementById('debugLoginType').value = type;
            toggleCompanySelect();
            if (type === 'admin') {
                document.getElementById('debugCompany').value = 'demo';
            }
        }

        function toggleCompanySelect() {
            const type = document.getElementById('debugLoginType').value;
            const companyDiv = document.getElementById('companySelectDiv');
            if (type === 'admin') {
                companyDiv.classList.remove('hidden');
            } else {
                companyDiv.classList.add('hidden');
            }
        }

        function quickTest(email, password, type, company = '') {
            document.getElementById('debugEmail').value = email;
            document.getElementById('debugPassword').value = password;
            document.getElementById('debugLoginType').value = type;
            if (company) {
                document.getElementById('debugCompany').value = company;
            }
            toggleCompanySelect();
            debugLogin({preventDefault: () => {}});
        }

        function debugLogin(event) {
            event.preventDefault();
            
            const email = document.getElementById('debugEmail').value;
            const password = document.getElementById('debugPassword').value;
            const loginType = document.getElementById('debugLoginType').value;
            const companySlug = document.getElementById('debugCompany').value;
            
            const resultsDiv = document.getElementById('debugResults');
            const logDiv = document.getElementById('debugLog');
            
            resultsDiv.classList.remove('hidden');
            logDiv.innerHTML = 'Starting debug login process...\n';
            
            fetch('/debug-login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email: email,
                    password: password,
                    login_type: loginType,
                    company_slug: companySlug
                })
            })
            .then(response => response.json())
            .then(data => {
                logDiv.innerHTML = data.log.join('\n');
                if (data.success) {
                    logDiv.innerHTML += '\n\n🎉 LOGIN SHOULD WORK!';
                    logDiv.innerHTML += '\nRedirect URL: ' + data.redirect;
                    logDiv.innerHTML += '\n\nTry the real login now at: /login';
                } else {
                    logDiv.innerHTML += '\n\n❌ LOGIN WILL FAIL';
                    logDiv.innerHTML += '\nFix the issues above and try again.';
                }
            })
            .catch(error => {
                logDiv.innerHTML += '\nNetwork Error: ' + error.message;
            });
        }

        // Initialize
        toggleCompanySelect();
    </script>
</body>
</html>
