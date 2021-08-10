<?php

namespace App\Actions\Git;

use App\Exceptions\GitHookException;
use App\Models\Project;
use App\Support\GitHook\Data;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FetchProject
{
    public function __invoke(Data $data, callable $next)
    {
        try {
            /** @var Project $project */
            $project = $data->user->projects()
                ->where('name', basename(getcwd()))
                ->firstOrFail();

            return $next(tap($data, fn (Data $data) => $data->project = $project));
        } catch (ModelNotFoundException) {
            throw new GitHookException('Project not found.');
        }
    }
}
