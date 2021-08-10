<?php

namespace App\Support\GitHook;

use App\Models\Instance;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;

class Data
{
    public ?PushInfo $pushInfo;

    public ?User $user;

    public ?Project $project;

    public ?Service $service;

    public ?Instance $instance;

    public ?ProjectMeta $meta;
}
