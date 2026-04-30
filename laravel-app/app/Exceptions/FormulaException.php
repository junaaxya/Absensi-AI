<?php

namespace App\Exceptions;

use RuntimeException;

class FormulaException extends RuntimeException
{
    public static function empty(): self
    {
        return new self('Rumus tidak boleh kosong.');
    }

    public static function tooLong(int $max): self
    {
        return new self("Rumus terlalu panjang (maksimal {$max} karakter).");
    }

    public static function forbidden(string $formula, string $pattern): self
    {
        return new self("Rumus mengandung pola terlarang: \"{$pattern}\".");
    }

    public static function forbiddenFunction(string $name): self
    {
        return new self("Fungsi '{$name}' tidak diizinkan.");
    }

    public static function invalidVariable(string $name): self
    {
        return new self("Nama variabel tidak valid: '{$name}'.");
    }

    public static function undefinedVariable(string $name, array $available): self
    {
        $suggestion = '';
        $minDistance = PHP_INT_MAX;
        $closest = '';

        foreach ($available as $var) {
            $distance = levenshtein($name, $var);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closest = $var;
            }
        }

        if ($minDistance <= 3 && $closest !== '') {
            $suggestion = " Mungkin maksud Anda: '{$closest}'?";
        }

        return new self("Variabel '{$name}' tidak terdefinisi.{$suggestion}");
    }

    public static function nonNumericVariable(string $name, mixed $value): self
    {
        $type = gettype($value);
        return new self("Variabel '{$name}' harus berupa angka, ditemukan: {$type}.");
    }

    public static function divisionByZero(): self
    {
        return new self('Pembagian dengan nol tidak diizinkan.');
    }

    public static function tooDeep(int $max): self
    {
        return new self("Rumus terlalu dalam (kedalaman AST maksimal: {$max}).");
    }

    public static function tooComplex(int $max): self
    {
        return new self("Rumus terlalu kompleks (node AST maksimal: {$max}).");
    }

    public static function invalidResult(string $formula, string $reason): self
    {
        return new self("Hasil evaluasi tidak valid: {$reason}.");
    }

    public static function invalidToken(string $token): self
    {
        return new self("Token tidak valid: '{$token}'.");
    }

    public static function unexpectedToken(string $token): self
    {
        return new self("Token tidak diharapkan: '{$token}'.");
    }

    public static function unexpectedEnd(): self
    {
        return new self('Rumus berakhir secara tidak terduga.');
    }

    public static function expectedToken(string $expected, string $got): self
    {
        return new self("Diharapkan '{$expected}', ditemukan '{$got}'.");
    }

    public static function wrongArgCount(string $func, int $min, int $max, int $got): self
    {
        if ($min === $max) {
            return new self("Fungsi '{$func}' membutuhkan tepat {$min} argumen, diberikan {$got}.");
        }
        return new self("Fungsi '{$func}' membutuhkan {$min}-{$max} argumen, diberikan {$got}.");
    }

    public static function unknownOperator(string $op): self
    {
        return new self("Operator tidak dikenal: '{$op}'.");
    }

    public static function unknownNodeType(string $type): self
    {
        return new self("Tipe node tidak dikenal: '{$type}'.");
    }
}
