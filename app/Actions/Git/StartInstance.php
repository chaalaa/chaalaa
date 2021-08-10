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
            $this->command->newLine();
            $this->command->line('Starting instance...');

            (new Process(['docker-compose', ...$files, 'up', '--detach', $data->service->name], $data->instance->directory, timeout: null))
                ->mustRun();

            $this->command->newLine();
            $this->command->line('Successfully started a new instance for "'.$data->service->name.'"');
            $this->command->line('Link: https://'.$data->instance->name.'.'.config('app.domain'));

            $data->instance->update(['state' => 'running']);
        } catch (ProcessFailedException) {
            throw new GitHookException('Unable to start instance');
        }

        return $next($data);
    }
}
