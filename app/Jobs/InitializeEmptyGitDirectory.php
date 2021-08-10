<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

class InitializeEmptyGitDirectory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Project $project
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $directory = config('app.root').'/git/'.$this->project->user->username.'/'.$this->project->name;
        mkdir($directory, recursive: true);

        (new Process(['git', 'init', '--bare'], $directory))
            ->mustRun();

        file_put_contents($directory.'/hooks/pre-receive', "#!/bin/sh\nexec chaalaa git:pre-receive \"$@\" <&0");
        chmod($directory.'/hooks/pre-receive', 0755);
    }
}
