<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Create all permissions ──────────────────────────────────
        $permissions = [
            // Dashboard
            'dashboard view',

            // Events
            'event list',
            'event create',
            'event edit',
            'event delete',
            'event trash',
            'event restore',
            'event force-delete',
            'event bulk-delete',
            'event trash-bulk-delete',

            // Tickets
            'ticket list',
            'ticket show',
            'ticket edit',
            'ticket update-status',
            'ticket delete',
            'ticket check-in',
            'ticket download-pdf',
            'ticket resend-email',
            'ticket export',

            // Coupons
            'coupon list',
            'coupon create',
            'coupon edit',
            'coupon delete',

            // Categories
            'category list',
            'category create',
            'category edit',
            'category delete',

            // Tax Rates
            'tax list',
            'tax create',
            'tax edit',
            'tax delete',

            // Users
            'user list',
            'user create',
            'user edit',
            'user delete',

            // Settings
            'setting update',

            // Ticket Scanner
            'ticket-scanner access',
            'ticket-scanner check-in',
            'ticket-scanner toggle-status',

            // Orders
            'order list',
            'order show',
            'order update',

            // Roles
            'role list',
            'role edit',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // ── 2. Create roles ────────────────────────────────────────────
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $moderator = Role::firstOrCreate(['name' => 'moderator']);
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $user = Role::firstOrCreate(['name' => 'user']);

        // ── 3. Assign permissions to roles ─────────────────────────────

        // admin — everything
        $admin->syncPermissions(Permission::all());

        // moderator — all except Users and Settings
        $moderator->syncPermissions([
            'dashboard view',
            'event list', 'event create', 'event edit', 'event delete',
            'event trash', 'event restore', 'event force-delete',
            'event bulk-delete', 'event trash-bulk-delete',
            'ticket list', 'ticket show', 'ticket edit', 'ticket update-status',
            'ticket delete', 'ticket check-in', 'ticket download-pdf',
            'ticket resend-email', 'ticket export',
            'coupon list', 'coupon create', 'coupon edit', 'coupon delete',
            'category list', 'category create', 'category edit', 'category delete',
            'tax list', 'tax create', 'tax edit', 'tax delete',
            'ticket-scanner access', 'ticket-scanner check-in',
            'ticket-scanner toggle-status',
            'order list', 'order show', 'order update',
        ]);

        // staff — view and basic operations only
        $staff->syncPermissions([
            'dashboard view',
            'event list',
            'ticket list', 'ticket show', 'ticket edit', 'ticket update-status',
            'ticket check-in', 'ticket download-pdf', 'ticket resend-email',
            'category list',
            'coupon list',
            'ticket-scanner access', 'ticket-scanner check-in',
            'ticket-scanner toggle-status',
        ]);

        // ── 4. Create admin user (only if no users exist) ──────────────
        if (User::count() === 0) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'phone' => '1234567890',
                'status' => 1,
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
            ]);

            $user->assignRole('admin');
        }
    }
}
