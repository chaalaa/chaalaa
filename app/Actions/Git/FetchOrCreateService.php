<?php

namespace App\Actions\Git;

use App\Exceptions\GitHookException;
use App\Models\Service;
use App\Support\GitHook\Data;
use Illuminate\Support\Str;

class FetchOrCreateService
{
    public function __invoke(Data $data, callable $next)
    {
        $name = Str::after($data->pushInfo->ref, 'refs/heads/');

        if (! ($meta = $data->meta->services()->get($name))) {
            throw new GitHookException('Invalid service name specified as ref.');
        }

        /** @var Service $service */
        $service = $data->project
            ->services()
            ->updateOrCreate([
                'name' => $meta->name,
            ], [
                'port' => $meta->port,
            ]);

        return $next(tap($data, fn (Data $data) => $data->service = $service));
    }
}
