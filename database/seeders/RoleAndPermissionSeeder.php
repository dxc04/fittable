<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'analyze job posting',
            'assess resume',
            'view own job postings',
            'view own resumes',
            'view own assessments',
            'view all users',
            'manage users',
            'view analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Job Seeker role
        $jobSeekerRole = Role::firstOrCreate(['name' => 'job_seeker']);
        $jobSeekerRole->givePermissionTo([
            'analyze job posting',
            'assess resume',
            'view own job postings',
            'view own resumes',
            'view own assessments',
        ]);

        // Recruiter role
        $recruiterRole = Role::firstOrCreate(['name' => 'recruiter']);
        $recruiterRole->givePermissionTo([
            'analyze job posting',
            'assess resume',
            'view own job postings',
            'view own assessments',
        ]);

        // Admin role (has all permissions)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
    }
}
