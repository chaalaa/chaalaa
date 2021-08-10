<?php

namespace App\Actions\Git;

use App\Exceptions\GitHookException;
use App\Support\GitHook\Data;
use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class StartInstance
{
    public function __construct(
        protected Command $command,
    ) {}

    public function __invoke(Data $data, callable $next)
    {
        $files = array_reduce(
            $data->meta->dockerComposeFiles(),
            fn ($carry, $filename) => [...$carry, '--file', $filename],
            []
        );

        try {
            (new Process(['docker-compose', ...$files, 'up', '--detach', $data->service->name], $data->instance->directory))
                ->mustRun();

            $this->command->getOutput()->block([
                'Successfully started the instance',
                'Link: https://'.$data->instance->name.'.'.config('app.domain'),
            ], padding: true);
        } catch (ProcessFailedException) {
            throw new GitHookException('Unable to start instance');
        }

        return $next($data);
    }
}
