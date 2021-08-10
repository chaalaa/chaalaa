<?php

namespace App\Actions\Git;

use App\Exceptions\GitHookException;
use App\Models\User;
use App\Support\GitHook\Data;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthenticateUser
{
    public function __invoke(Data $data, callable $next)
    {
        try {
            /** @var User $user */
            $user = User::query()
                ->where('username', env('CHAALAA_USER'))
                ->firstOrFail();

            return $next(tap($data, fn (Data $data) => $data->user = $user));
        } catch (ModelNotFoundException) {
            throw new GitHookException('Unauthenticated.');
        }
    }
}
