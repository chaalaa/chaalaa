<?php

namespace App\Observers;

use App\Jobs\InitializeEmptyGitDirectory;
use App\Models\Project;
use App\Support\Words;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ProjectObserver
{
    use DispatchesJobs;

    public function __construct(
        protected Words $words,
    ) {}

    public function creating(Project $project)
    {
        do {
            $name = $this->words->pair();
        } while (Project::query()->where(['name' => $name, 'user_id' => $project->user_id])->exists());

        $project->name = $name;
    }

    public function created(Project $project)
    {
        $this->dispatch(new InitializeEmptyGitDirectory($project));
    }
}
