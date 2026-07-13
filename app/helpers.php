<?php

if (! function_exists('format_fcfa')) {
    /**
     * Montant affiché en franc CFA (FCFA), sans décimales.
     */
    function format_fcfa(float|int|string|null $amount, bool $withSuffix = true): string
    {
        $formatted = number_format((float) ($amount ?? 0), 0, ',', ' ');

        return $withSuffix ? $formatted . ' FCFA' : $formatted;
    }
}
