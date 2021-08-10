<?php

namespace App\Observers;

use App\Jobs\CreateAuthorizedKeysFile;
use Illuminate\Foundation\Bus\DispatchesJobs;

class UserObserver
{
    use DispatchesJobs;

    public function created()
    {
        $this->dispatch(new CreateAuthorizedKeysFile());
    }

    public function updated()
    {
        $this->dispatch(new CreateAuthorizedKeysFile());
    }

    public function saved()
    {
        $this->dispatch(new CreateAuthorizedKeysFile());
    }

    public function deleted()
    {
        $this->dispatch(new CreateAuthorizedKeysFile());
    }
}
