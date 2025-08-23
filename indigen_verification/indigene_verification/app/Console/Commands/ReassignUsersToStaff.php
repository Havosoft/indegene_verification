<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ReassignUsersToStaff extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:reassign-staff {--force : Force reassignment of all users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reassign users to staff members in their LGA for load balancing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting user reassignment to staff members...');

        // Get statistics before reassignment
        $totalUsers = User::where('role', 'user')->count();
        $usersWithoutStaff = User::where('role', 'user')->whereNull('assigned_staff_id')->count();
        $usersWithStaff = User::where('role', 'user')->whereNotNull('assigned_staff_id')->count();

        $this->info("Total users: {$totalUsers}");
        $this->info("Users without staff assignment: {$usersWithoutStaff}");
        $this->info("Users with staff assignment: {$usersWithStaff}");

        if ($this->option('force')) {
            $this->warn('Force option enabled - all users will be reassigned');
        }

        // Get staff statistics
        $totalStaff = User::where('role', 'staff')->count();
        $staffWithLga = User::where('role', 'staff')->whereNotNull('assigned_lga')->count();
        $staffWithoutLga = User::where('role', 'staff')->whereNull('assigned_lga')->count();

        $this->info("Total staff: {$totalStaff}");
        $this->info("Staff with LGA assignment: {$staffWithLga}");
        $this->info("Staff without LGA assignment: {$staffWithoutLga}");

        if ($staffWithLga === 0) {
            $this->error('No staff members have LGA assignments. Please assign LGAs to staff first.');
            return 1;
        }

        // Show LGA distribution
        $this->info("\nLGA Distribution:");
        $lgas = User::where('role', 'user')->distinct()->pluck('lga')->filter();
        foreach ($lgas as $lga) {
            $userCount = User::where('role', 'user')->where('lga', $lga)->count();
            $staffCount = User::where('role', 'staff')->where('assigned_lga', $lga)->count();
            $this->info("  {$lga}: {$userCount} users, {$staffCount} staff");
        }

        if (!$this->option('force') && $usersWithoutStaff === 0) {
            $this->info('All users are already assigned to staff members.');
            $this->info('Use --force option to reassign all users for load balancing.');
            return 0;
        }

        if ($this->confirm('Do you want to proceed with the reassignment?')) {
            $bar = $this->output->createProgressBar($totalUsers);
            $bar->start();

            // Reassign users
            User::reassignUsersToStaff();

            $bar->finish();
            $this->newLine(2);

            // Show results
            $this->info('Reassignment completed!');
            
            // Show new distribution
            $this->info("\nNew Staff Assignment Distribution:");
            $staffMembers = User::where('role', 'staff')->whereNotNull('assigned_lga')->get();
            foreach ($staffMembers as $staff) {
                $userCount = User::where('assigned_staff_id', $staff->id)->count();
                $this->info("  {$staff->name} ({$staff->assigned_lga}): {$userCount} users");
            }

            $this->info("\nReassignment completed successfully!");
        } else {
            $this->info('Reassignment cancelled.');
        }

        return 0;
    }
}
