<?php

namespace App\Console\Commands;

use App\Actions\Git\AuthenticateUser;
use App\Actions\Git\BuildInstance;
use App\Actions\Git\CreateDockerComposeFile;
use App\Actions\Git\CreateNewInstance;
use App\Actions\Git\FetchProject;
use App\Actions\Git\FetchOrCreateService;
use App\Actions\Git\ReadMetaFromFile;
use App\Actions\Git\StartInstance;
use App\Actions\Git\StopStaleInstance;
use App\Console\Commands\Concerns\HandlesGitHook;
use App\Console\Commands\Concerns\ReadsStandardInputFromGit;
use Illuminate\Console\Command;
use Illuminate\Contracts\Debug\ExceptionHandler;

class GitPreReceiveCommand extends Command
{
    use ReadsStandardInputFromGit, HandlesGitHook;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'git:pre-receive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ExceptionHandler $exceptionHandler)
    {
        return $this->handleGitHook($exceptionHandler, [
            AuthenticateUser::class,
            FetchProject::class,
            ReadMetaFromFile::class,
            FetchOrCreateService::class,
            StopStaleInstance::class,
            CreateNewInstance::class,
            BuildInstance::class,
            CreateDockerComposeFile::class,
            StartInstance::class,
        ]);
    }
}
