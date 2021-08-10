<?php

namespace App\Actions\Git;

use App\Exceptions\GitHookException;
use App\Models\Instance;
use App\Support\GitHook\Data;
use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class StopStaleInstance
{
    public function __construct(
        protected Command $command,
    ) {}

    public function __invoke(Data $data, callable $next)
    {
        /** @var Instance $instance */
        $instance = $data->service->instance;

        if (is_null($instance) || $instance->state == 'stopped') {
            return $next($data);
        }

        $this->command->line('Stopping stale instance...');

        $files = array_reduce(
            $data->meta->dockerComposeFiles(),
            fn ($carry, $filename) => [...$carry, '--file', $filename],
            []
        );

        try {
            (new Process(['docker-compose', ...$files, 'down'], $instance->directory, timeout: null))
                ->mustRun();
        } catch (ProcessFailedException) {
            throw new GitHookException('Unable to stop stale instance.');
        }

        $instance->update(['state' => 'stopped']);

        return $next($data);
    }
}
