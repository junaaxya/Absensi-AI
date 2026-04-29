<?php

namespace App\DTOs;

class AntiCheatResult
{
    public int $score;
    public array $flags;
    public bool $passed;
    public bool $warning;

    public function __construct(int $score, array $flags, bool $passed, bool $warning)
    {
        $this->score = $score;
        $this->flags = $flags;
        $this->passed = $passed;
        $this->warning = $warning;
    }
}
