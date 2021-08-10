<?php

namespace App\Observers;

use App\Models\Instance;
use App\Support\Words;

class InstanceObserver
{
    public function __construct(
        protected Words $words,
    ) {}

    public function creating(Instance $instance)
    {
        do {
            $name = $this->words->pair();
        } while (Instance::query()->where(['name' => $name])->exists());

        $instance->name = $name;
    }
}
