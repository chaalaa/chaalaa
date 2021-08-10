<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\User;
use Illuminate\Console\Command;

class CreateProjectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '
        create:project
        { username : The user the project should belong to }
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a project for the user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /** @var User $user */
        $user = User::query()
            ->where('username', $this->argument('username'))
            ->firstOrFail();

        /** @var Project $project */
        $project = $user->projects()->create();

        $this->info('Successfully created a new project.');
        $this->info('Add this repository as staging remote: <comment>'.config('app.git.user').'@'.config('app.git.host').':'.$user->username.'/'.$project->name.'.git</comment>');

        return 0;
    }
}
