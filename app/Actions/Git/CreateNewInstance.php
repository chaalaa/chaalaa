<?php

namespace App\Actions\Git;

use Alchemy\Zippy\Exception\RuntimeException;
use Alchemy\Zippy\Zippy;
use App\Exceptions\GitHookException;
use App\Models\Instance;
use App\Support\GitHook\Data;
use App\Support\Words;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CreateNewInstance
{
    public function __construct(
        protected Words $words,
    ) {}

    public function __invoke(Data $data, callable $next)
    {
        try {
            /** @var Instance $instance */
            $instance = $data->service->instance()->create();

            $tempfile = tempnam(sys_get_temp_dir(), 'chaalaa');
            mkdir($instance->directory);

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
