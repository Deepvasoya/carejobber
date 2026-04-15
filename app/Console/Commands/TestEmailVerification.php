<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestEmailVerification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-verification {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register a test user and send verification email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'test' . time() . '@example.com';
        
        $this->info('Creating test user...');
        
        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $this->warn('User already exists. Deleting old user...');
            $existingUser->delete();
        }
        
        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $email,
            'password' => Hash::make('password123'),
            'verification_token' => Str::random(40),
            'verified' => false,
            'is_active' => 0,
            'is_email_verified' => 0,
        ]);
        
        $this->info('User created: ' . $user->email);
        $this->info('User ID: ' . $user->id);
        
        // Send verification email
        $this->info('Sending verification email...');
        
        try {
            $user->sendEmailVerificationNotification();
            $this->info('✅ Verification email sent successfully!');
            $this->info('');
            $this->info('Check your mailhog at: http://localhost:8025');
            $this->info('Email sent to: ' . $user->email);
            $this->info('');
            $this->info('Verification link: ' . route('email-verification.check', $user->verification_token) . '?email=' . urlencode($user->email));
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
        
        return 0;
    }
}
