<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminTestController extends Controller
{
    public function testAdmin()
    {
        echo "<h2>Admin Access Test</h2>";
        
        // Check if user is logged in
        if (Auth::check()) {
            echo "<p style='color: green;'>✓ User is logged in: " . Auth::user()->name . " (" . Auth::user()->email . ")</p>";
        } else {
            echo "<p style='color: red;'>✗ User is not logged in</p>";
        }
        
        // Check admin users
        $adminUsers = User::all();
        echo "<h3>Available Admin Users:</h3>";
        
        if ($adminUsers->count() > 0) {
            foreach ($adminUsers as $user) {
                echo "<div>";
                echo "Name: " . $user->name . "<br>";
                echo "Email: " . $user->email . "<br>";
                echo "Created: " . $user->created_at . "<br>";
                echo "</div><hr>";
            }
        } else {
            echo "<p style='color: red;'>No admin users found! Creating default admin...</p>";
            
            try {
                $admin = User::create([
                    'name' => 'Admin',
                    'email' => 'admin@herbalbliss.com',
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]);
                echo "<p style='color: green;'>✓ Default admin created: admin@herbalbliss.com / password123</p>";
            } catch (\Exception $e) {
                echo "<p style='color: red;'>Error creating admin: " . $e->getMessage() . "</p>";
            }
        }
        
        // Test route access
        echo "<h3>Route Test:</h3>";
        try {
            $adminDashboardUrl = route('admin.dashboard');
            echo "<p>Admin Dashboard URL: <a href='" . $adminDashboardUrl . "' target='_blank'>" . $adminDashboardUrl . "</a></p>";
        } catch (\Exception $e) {
            echo "<p style='color: red;'>Error generating admin dashboard route: " . $e->getMessage() . "</p>";
        }
        
        // Login form
        if (!Auth::check()) {
            echo "<h3>Quick Login:</h3>";
            echo '<form method="POST" action="' . route('admin.login.post') . '">';
            echo csrf_field();
            echo '<div style="margin: 10px 0;">';
            echo '<label>Email:</label><br>';
            echo '<input type="email" name="email" value="admin@herbalbliss.com" style="width: 300px; padding: 5px;">';
            echo '</div>';
            echo '<div style="margin: 10px 0;">';
            echo '<label>Password:</label><br>';
            echo '<input type="password" name="password" value="password123" style="width: 300px; padding: 5px;">';
            echo '</div>';
            echo '<div style="margin: 10px 0;">';
            echo '<input type="checkbox" name="remember" id="remember"> <label for="remember">Remember me</label>';
            echo '</div>';
            echo '<button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">Login</button>';
            echo '</form>';
        } else {
            echo "<p><a href='" . route('admin.dashboard') . "' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none;'>Go to Admin Dashboard</a></p>";
            echo '<form method="POST" action="' . route('admin.logout') . '" style="display: inline;">';
            echo csrf_field();
            echo '<button type="submit" style="padding: 10px 20px; background: #dc3545; color: white; border: none; cursor: pointer;">Logout</button>';
            echo '</form>';
        }
    }
}
