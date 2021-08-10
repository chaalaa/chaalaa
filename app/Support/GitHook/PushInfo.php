<?php

namespace App\Support\GitHook;

class PushInfo
{
    public function __construct(
        public string $oldRev,
        public string $newRev,
        public string $ref,
    ) {}
}
