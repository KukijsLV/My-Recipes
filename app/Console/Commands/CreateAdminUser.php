<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('app:create-admin-user {--email= : Email address for the new administrator} {--name= : Administrator name} {--password= : Administrator password}')]
#[Description('Create an administrator without promoting an existing user')]
class CreateAdminUser extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->option('email');
        $name = $this->option('name');
        $password = $this->option('password');

        if (! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('A valid email address is required.');

            return self::FAILURE;
        }

        if (! is_string($name) || trim($name) === '') {
            $this->error('A name is required.');

            return self::FAILURE;
        }

        if (! is_string($password) || strlen($password) < 12) {
            if (! is_string($password)) {
                $password = $this->askHidden('Password');
            }

            if (! is_string($password) || strlen($password) < 12) {
                $this->error('The password must be at least 12 characters long.');

                return self::FAILURE;
            }
        }

        $email = strtolower(trim($email));
        $existingUser = User::query()->where('email', $email)->first();

        if ($existingUser) {
            $this->error('A user with this email already exists; no existing account was changed.');

            return self::FAILURE;
        }

        $user = User::create([
            'name' => trim($name),
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
            'is_blocked' => false,
        ]);

        $this->info("Administrator {$user->email} created successfully.");

        return self::SUCCESS;
    }
}
