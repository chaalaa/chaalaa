<?php

namespace App\Support;

class Words
{
    public function pair(): string
    {
        return $this->adjective().'-'.$this->noun();
    }

    public function noun(): string
    {
        return $this->word('nouns', 2876);
    }

    public function adjective(): string
    {
        return $this->word('adjectives', 1010);
    }

    protected function word(string $type, int $count): string
    {
        $filename = resource_path('words/'.$type.'.txt');

        if (! file_exists($filename)) {
            throw new \RuntimeException('Resource for type `'.$type.'` does not exist');
        }

        $handle = fopen($filename, 'r');
        $random = mt_rand(1, $count);
        $i = 0;

        try {
            while ($line = fgets($handle)) {
                if (++$i === $random) {
                    return trim($line);
                }
            }

            return $this->word($type, $count);
        } finally {
            fclose($handle);
        }
    }
}
