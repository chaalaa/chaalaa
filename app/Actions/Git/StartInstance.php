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
            $this->command->line('Starting instance...');
            $this->command->newLine();

            (new Process(['docker-compose', ...$files, 'up', '--detach', $data->service->name], $data->instance->directory, timeout: null))
                ->mustRun();

            $this->command->line('Successfully started the instance');
            $this->command->line('Link: https://'.$data->instance->name.'.'.config('app.domain'));
            $this->command->newLine();
        } catch (ProcessFailedException $e) {
            throw $e;
            // throw new GitHookException('Unable to start instance');
        }

        return $next($data);
    }
}
