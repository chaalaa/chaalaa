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
        $stopOnly = hexdec($data->pushInfo->newRev) === 0;

        /** @var Instance $instance */
        $instance = $data->service->instance;

        if (is_null($instance) || $instance->state == 'stopped') {
            if ($stopOnly) {
                $this->command->newLine();
                $this->command->line('Instance is already stopped.');

                return $data;
            }

            return $next($data);
        }

        $this->command->newLine();
        $this->command->line($stopOnly ? 'Stopping instance...' : 'Stopping stale instance...');

        $files = array_reduce(
            $data->meta->dockerComposeFiles(),
            fn ($carry, $filename) => [...$carry, '--file', $filename],
            []
        );

        try {
            (new Process(['docker-compose', ...$files, 'down'], $instance->directory, timeout: null))
                ->mustRun();

            (new Process(['rm', '-rf', $instance->directory]))
                ->mustRun();
        } catch (ProcessFailedException) {
            throw new GitHookException('Unable to stop stale instance.');
        }

        $instance->update(['state' => 'stopped']);

        return $stopOnly ? $data : $next($data);
    }
}
