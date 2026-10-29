<?php

namespace App\Support;

class LineList
{
    /**
     * @return list<string>
     */
    public static function parse(?string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value) ?: [])
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
