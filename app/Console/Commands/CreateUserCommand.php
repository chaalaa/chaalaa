<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::create([
            'username' => $this->ask('Enter the username'),
            'email' => $this->ask('Enter the email address'),
            'public_key' => $this->ask('Enter the public key'),
        ]);

        return 0;
    }
}
