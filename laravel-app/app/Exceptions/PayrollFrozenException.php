<?php

namespace App\Exceptions;

class PayrollFrozenException extends \RuntimeException
{
    public function __construct(string $message = 'Data tidak dapat diubah karena periode payroll sedang diproses atau sudah dihitung.')
    {
        parent::__construct($message);
    }
}
