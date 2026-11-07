<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\HrEmployee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    public static array $modules = [
        'products' => [
            'label' => 'Products (পণ্য তালিকা)',
            'permissions' => ['view_products', 'create_products', 'edit_products', 'delete_products']
        ],
        'units' => [
            'label' => 'Units (পরিমাপ একক)',
            'permissions' => ['view_units', 'create_units', 'edit_units', 'delete_units']
        ],
        'customers' => [
            'label' => 'Customers (গ্রাহক ও ক্লায়েন্ট)',
            'permissions' => ['view_customers', 'create_customers', 'edit_customers', 'delete_customers']
        ],
        'due_payments' => [
            'label' => 'Due Payments (বকেয়া আদায়)',
            'permissions' => ['view_due_payments', 'create_due_payments']
        ],
        'suppliers' => [
            'label' => 'Suppliers (সরবরাহকারী)',
            'permissions' => ['view_suppliers', 'create_suppliers', 'edit_suppliers', 'delete_suppliers']
        ],
        'orders' => [
            'label' => 'Orders & Invoices (অর্ডার ও ইনভয়েস)',
            'permissions' => ['view_orders', 'create_orders', 'edit_orders', 'delete_orders']
        ],
        'productions' => [
            'label' => 'Production Tracking (উৎপাদন ট্র্যাকিং)',
            'permissions' => ['view_productions', 'create_productions', 'edit_productions', 'delete_productions']
        ],
        'deliveries' => [
            'label' => 'Deliveries / Challan (ডেলিভারি ও চালান)',
            'permissions' => ['view_deliveries', 'create_deliveries', 'delete_deliveries']
        ],
        'stocks' => [
            'label' => 'Stock & Inventory (স্টক ও গুদাম)',
            'permissions' => ['view_stocks', 'adjust_stocks']
        ],
        'hr_employees' => [
            'label' => 'HR Employees (কর্মচারী ব্যবস্থাপনা)',
            'permissions' => ['view_employees', 'create_employees', 'edit_employees', 'delete_employees']
        ],
        'hr_attendance' => [
            'label' => 'Attendance (হাজিরা ব্যবস্থাপনা)',
            'permissions' => ['view_attendance', 'mark_attendance']
        ],
        'hr_leaves' => [
            'label' => 'Leave Management (ছুটি ব্যবস্থাপনা)',
            'permissions' => ['view_leaves', 'manage_leaves']
        ],
        'hr_advances' => [
            'label' => 'Advance Salary & Loans (অগ্রিম বেতন ও ঋণ)',
            'permissions' => ['view_advances', 'manage_advances']
        ],
        'hr_payroll' => [
            'label' => 'Payroll & Payslips (মাসিক বেতন ও পে-স্লিপ)',
            'permissions' => ['view_payroll', 'generate_payroll', 'pay_salary']
        ],
        'hr_devices' => [
            'label' => 'Biometric Devices (বায়োমেট্রিক মেশিন)',
            'permissions' => ['view_devices', 'manage_devices']
        ],
        'accounts' => [
            'label' => 'Accounts & Finance (হিসাব ও অর্থায়ন)',
            'permissions' => ['view_accounts', 'manage_income', 'manage_expense', 'manage_transfers', 'view_reports']
        ],
        'settings' => [
            'label' => 'Company Settings (কোম্পানি সেটিংস)',
            'permissions' => ['manage_settings']
        ],
        'roles' => [
            'label' => 'Roles & Permissions (রোল ও পারমিশন)',
            'permissions' => ['view_roles', 'manage_roles']
        ],
    ];

    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all permissions
        foreach (self::$modules as $moduleKey => $moduleData) {
            foreach ($moduleData['permissions'] as $permName) {
                Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            }
        }

        // Create Roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $factoryManager = Role::firstOrCreate(['name' => 'Factory Manager', 'guard_name' => 'web']);
        $accountsManager = Role::firstOrCreate(['name' => 'Accounts Manager', 'guard_name' => 'web']);
        $hrManager = Role::firstOrCreate(['name' => 'HR Manager', 'guard_name' => 'web']);
        $employeeRole = Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);

        // Assign permissions to Super Admin & Admin
        $allPermissions = Permission::all();
        $superAdmin->syncPermissions($allPermissions);
        $admin->syncPermissions($allPermissions);

        // Factory Manager permissions
        $factoryPerms = Permission::whereIn('name', [
            'view_products', 'create_products', 'edit_products',
            'view_units',
            'view_customers', 'create_customers',
            'view_due_payments',
            'view_suppliers', 'create_suppliers',
            'view_orders', 'create_orders', 'edit_orders',
            'view_productions', 'create_productions', 'edit_productions',
            'view_deliveries', 'create_deliveries',
            'view_stocks', 'adjust_stocks',
        ])->get();
        $factoryManager->syncPermissions($factoryPerms);

        // Accounts Manager permissions
        $accountsPerms = Permission::whereIn('name', [
            'view_customers', 'view_due_payments', 'create_due_payments',
            'view_orders', 'view_deliveries',
            'view_accounts', 'manage_income', 'manage_expense', 'manage_transfers', 'view_reports',
            'view_payroll', 'pay_salary',
            'view_advances', 'manage_advances'
        ])->get();
        $accountsManager->syncPermissions($accountsPerms);

        // HR Manager permissions
        $hrPerms = Permission::whereIn('name', [
            'view_employees', 'create_employees', 'edit_employees',
            'view_attendance', 'mark_attendance',
            'view_leaves', 'manage_leaves',
            'view_advances', 'manage_advances',
            'view_payroll', 'generate_payroll', 'pay_salary',
            'view_devices', 'manage_devices'
        ])->get();
        $hrManager->syncPermissions($hrPerms);

        // 1. Create Default Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@turkeybangla.com'],
            [
                'name' => 'System Administrator',
                'role' => 'admin',
                'password' => Hash::make('admin123')
            ]
        );
        $adminUser->assignRole('Super Admin');

        // 2. Provision existing employees with user accounts
        $employees = HrEmployee::all();
        foreach ($employees as $emp) {
            if (!$emp->user_id) {
                $email = !empty($emp->email) && filter_var($emp->email, FILTER_VALIDATE_EMAIL)
                    ? strtolower(trim($emp->email))
                    : strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $emp->employee_code)) . '@turkeybangla.com';

                // Check if user already exists with this email
                $user = User::where('email', $email)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $emp->name,
                        'email' => $email,
                        'role' => 'employee',
                        'password' => Hash::make('password')
                    ]);
                }
                $user->assignRole('Employee');
                $emp->update(['user_id' => $user->id]);
            }
        }
    }
}

