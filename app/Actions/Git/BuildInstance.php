<?php

namespace App\Actions\Git;

use App\Exceptions\GitHookException;
use App\Support\GitHook\Data;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BuildInstance
{
    public function __invoke(Data $data, callable $next)
    {
        try {
            (new Process(['docker', 'build', '--file', $data->meta->dockerFile(), '--tag', $data->instance->name, '.'], $data->instance->directory))
                ->mustRun();

            return $next($data);
        } catch (ProcessFailedException) {
            throw new GitHookException('Unable to build image.');
        }
    }
}
