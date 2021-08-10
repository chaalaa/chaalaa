<?php

namespace App\Actions\Git;

use App\Support\GitHook\Data;
use Symfony\Component\Yaml\Yaml;

class CreateDockerComposeFile
{
    public function __invoke(Data $data, callable $next)
    {
        $yaml = [
            'version' => '3.8',
            'services' => $data->meta->services()->reduce(function (array $carry, object $service) use ($data) {
                $carry[$service->name] = [
                    'image' => $data->instance->name,
                    'labels' => [
                        'traefik.enable=true',
                        'traefik.http.routers.'.$data->project->name.'-'.$data->service->name.'.entrypoints=chaalaa',
                        'traefik.http.routers.'.$data->project->name.'-'.$data->service->name.'.service='.$data->project->name.'-'.$data->service->name,
                        'traefik.http.routers.'.$data->project->name.'-'.$data->service->name.'.rule=Host(`'.$data->instance->name.'.'.config('app.domain').'`)',
                        'traefik.http.services.'.$data->project->name.'-'.$data->service->name.'.loadbalancer.server.port='.$data->service->port,
                    ],
                    'networks' => [
                        'traefik-chaalaa-network',
                    ],
                    'environment' => $data->meta->environmentVariables($data->service->name),
                ];

                return $carry;
            }, []),
        ];

        file_put_contents($data->instance->directory.'/docker-compose.override.yml', Yaml::dump($yaml));

        return $next($data);
    }
}
