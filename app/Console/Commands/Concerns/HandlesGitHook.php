<?php

namespace App\Console\Commands\Concerns;

use App\Exceptions\GitHookException;
use App\Support\GitHook\Data;
use App\Support\GitHook\PushInfo;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;

trait HandlesGitHook
{
    protected function handleGitHook(ExceptionHandler $exceptionHandler, array $pipeline): int
    {
        app()->instance(Command::class, $this);

        try {
            foreach ($this->readReferencesFromGit() as [$oldRev, $newRev, $ref]) {
                $pushInfo = new PushInfo($oldRev, $newRev, $ref);

                DB::transaction(function () use ($pushInfo, $pipeline) {
                    (new Pipeline(app()))
                        ->send(tap(new Data(), fn (Data $data) => $data->pushInfo = $pushInfo))
                        ->through($pipeline)
                        ->then(function (Data $data) {
                            // Nothing to do here...
                        });
                });
            }

            return 0;
        } catch (GitHookException $e) {
            $this->newLine();
            $this->line($e->getMessage());
        } catch (Exception $e) {
            $exceptionHandler->renderForConsole($this->output, $e);
        }

        app()->forgetInstance(Command::class);

        return 1;
    }
}
