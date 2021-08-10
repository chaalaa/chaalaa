<?php

namespace App\Support\GitHook;

use Illuminate\Support\Collection;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\Schema;
use Symfony\Component\Yaml\Yaml;

class ProjectMeta
{
    protected object $data;

    public function __construct(string $contents)
    {
        $this->data = $this->validate(Yaml::parse($contents));
    }

    public function services(): Collection
    {
        return collect($this->data->services)
            ->mapWithKeys(function (object $service) {
                return [$service->name => $service];
            });
    }

    public function dockerFile(): string
    {
        return $this->data->docker->build_file;
    }

    public function dockerComposeFiles(): array
    {
        return [
            ...$this->data->docker->compose_files,
            'docker-compose.override.yml',
        ];
    }

    protected function validate(mixed $data): object
    {
        return (new Processor())->process($this->schema(), $data);
    }

    protected function schema(): Schema
    {
        return Expect::structure([
            'version' => Expect::string()->default('1.0')->castTo('float'),
            'services' => Expect::listOf(
                Expect::structure([
                    'name' => Expect::string()->required(),
                    'port' => Expect::int()->required(),
                ])
            )->required(),
            'docker' => Expect::structure([
                'build_file' => Expect::string()->default('Dockerfile'),
                'compose_files' => Expect::listOf('string')->default(['docker-compose.yml']),
            ]),
        ]);
    }
}
