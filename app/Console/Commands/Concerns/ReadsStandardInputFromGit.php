<?php

namespace App\Console\Commands\Concerns;

use App\Exceptions\GitHookException;
use Illuminate\Support\Str;

trait ReadsStandardInputFromGit
{
    protected function readReferencesFromGit(): iterable
    {
        while ($line = fgets(STDIN)) {
            [$oldRev, $newRev, $ref] = explode(' ', trim($line));

            if (! Str::startsWith($ref, 'refs/heads/')) {
                throw new GitHookException('Pushing invalid reference.');
            }

            yield [$oldRev, $newRev, $ref];
        }
    }
}
