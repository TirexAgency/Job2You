<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyAllUsers extends Command
{
    protected $signature = 'users:verify-all';
    protected $description = 'Mark all users as email verified (for development only)';

    public function handle(): int
    {
        $count = User::whereNull('email_verified_at')->count();

        if ($count === 0) {
            $this->info('All users are already verified.');
            return self::SUCCESS;
        }

        User::whereNull('email_verified_at')->update(['email_verified_at' => now()]);

        $this->info("{$count} user(s) marked as verified.");

        return self::SUCCESS;
    }
}
