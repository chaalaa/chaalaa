<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateAuthorizedKeysFile implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filename = env('HOME').'/.ssh/authorized_keys';
        $handle = fopen($filename, 'w');

        User::query()->cursor()->each(function (User $user) use ($handle) {
            fwrite($handle, 'environment="CHAALAA_USER='.$user->username.'",');
            fwrite($handle, 'no-port-forwarding,no-X11-forwarding,no-agent-forwarding,no-pty ');
            fwrite($handle, trim($user->public_key));
            fwrite($handle, "\n");
        });

        fclose($handle);
        chmod($filename, 0600);
    }
}
