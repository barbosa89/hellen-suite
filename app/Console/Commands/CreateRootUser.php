<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateRootUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'root:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create root user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Important!' . PHP_EOL);
        $this->info('If the supplied email exists in the database, then your password will be updated.' . PHP_EOL);
        $this->info('Requirements:');
        $this->info('- Unique email');
        $this->info('- Unique user with root role');
        $this->info('- Maximum password length: 24 chars');
        $this->info('- Minimum password length: 8 chars' . PHP_EOL);

        // Query existing root user
        $user = User::whereHas('roles', function ($query) {
            $query->where('name', 'root');
        })->first();

        if (empty($user)) {
            $email = $this->ask('Type your Root email: ');

            if ($this->validEmail($email)) {
                $user = new User();
                $user->email = $email;
            } else {
                $this->info('You cannot register the email as root user');

                return;
            }
        } else {
            $this->info("Existing root user: ");
            $this->info("Name: {$user->name}");
            $this->info("Email: {$user->email}" . PHP_EOL);
        }

        $password = $this->ask('Type your Root password: ');

        if ($this->isInvalidPassword($password)) {
            $password = Str::random(16);
        }

        // Add Root user data
        $user->fill([
            'name' => 'Root',
            'email_verified_at' => now(),
            'password' => bcrypt($password)
        ]);

        // If Root user was stored
        $user->saveOrFail();
        // Empty all old roles
        $user->syncRoles([]);

        // Relate Root role only
        $user->assignRole('root');

        // Output
        $this->line('The Root user');
        $this->line('Email: ' . $user->email);
        $this->line('Password: ' . $password);
    }

    /**
     * Validate root user
     *
     * @param string $email
     * @return boolean
     */
    public function validEmail(string $email): bool
    {
        $validator = Validator::make(
            [
                'email' => $email
            ],
            [
                'email' => 'required|email:rfc,dns,spoof,filter|unique:users,email'
            ]
        );

        if ($validator->fails()) {
            return false;
        }

        return true;
    }

    /**
     * Validate root password
     *
     * @param string $password
     * @return boolean
     */
    public function isInvalidPassword(string $password): bool
    {
        $validator = Validator::make(
            [
                'password' => $password
            ],
            [
                'password' => 'required|string|min:8|max:24'
            ]
        );

        if ($validator->fails()) {
            return true;
        }

        return false;
    }
}
