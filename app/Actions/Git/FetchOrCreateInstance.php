<?php

namespace App\Actions\Git;

use Alchemy\Zippy\Exception\RuntimeException;
use Alchemy\Zippy\Zippy;
use App\Exceptions\GitHookException;
use App\Models\Instance;
use App\Support\GitHook\Data;
use App\Support\Words;
use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FetchOrCreateInstance
{
    public function __construct(
        protected Words $words,
        protected Command $command,
    ) {}

    public function __invoke(Data $data, callable $next)
    {
        /** @var Instance $instance */
        $instance = $data->service->instance;

        if (is_null($instance) || $instance->state != 'recreating') {
            $instance = $data->service->instance()->create();
        }

        try {
            $tempfile = tempnam(sys_get_temp_dir(), 'chaalaa');
            mkdir($instance->directory);

            $this->command->newLine();
            $this->command->line('Creating archive...');

            (new Process(['git', 'archive', '--output', $tempfile, $data->pushInfo->newRev]))
                ->mustRun();

            Zippy::load()
                ->getAdapterFor('tar')
                ->open($tempfile)
                ->extract($instance->directory);

            return $next(tap($data, fn (Data $data) => $data->instance = $instance));
        } catch (ProcessFailedException) {
            throw new GitHookException('Unable to create archive for project.');
        } catch (RuntimeException) {
            throw new GitHookException('Unable to extract contents of the archive.');
        } finally {
            unlink($tempfile);
        }
    }
}
