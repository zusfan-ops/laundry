<?php

namespace App\Support;

class Money
{
    /** Format an integer rupiah amount as "Rp1.234.567". */
    public static function rupiah(int|float|null $amount): string
    {
        return 'Rp' . number_format((int) ($amount ?? 0), 0, ',', '.');
    }
}
