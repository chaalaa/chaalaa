<?php

namespace App\Actions\Git;

use App\Exceptions\GitHookException;
use App\Support\GitHook\Data;
use App\Support\GitHook\ProjectMeta;
use Nette\Schema\ValidationException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Exception\ParseException;

class ReadMetaFromFile
{
    public function __invoke(Data $data, callable $next)
    {
        $meta = hexdec($data->pushInfo->newRev) !== 0
            ? $this->read($data->pushInfo->newRev)
            : $this->read($data->pushInfo->oldRev);

        return $next(tap($data, fn (Data $data) => $data->meta = $meta));
    }

    protected function read(string $rev): ProjectMeta
    {
        try {
            $contents = (new Process(['git', 'cat-file', '-p', $rev.':.chaalaarc']))
                ->enableOutput()
                ->mustRun()
                ->getOutput();

            return new ProjectMeta($contents);
        } catch (ProcessFailedException) {
            throw new GitHookException('No .chaalaarc file found.');
        } catch (ParseException|ValidationException) {
            throw new GitHookException('.chaalaarc file contains invalid data.');
        }
    }
}
