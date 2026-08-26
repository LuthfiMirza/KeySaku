<?php

namespace App\Support;

class Money
{
    /**
     * Resolve currency code with fallback chain:
     * explicit arg → auth user preferred_currency → config default → 'IDR'.
     */
    public static function resolveCode(?string $currency = null): string
    {
        if ($currency !== null && $currency !== '') {
            $code = strtoupper($currency);
            if (is_array(config("currencies.currencies.$code"))) {
                return $code;
            }
        }

        try {
            $user = auth()->user();
        } catch (\Throwable) {
            $user = null;
        }

        if ($user && ! empty($user->preferred_currency)) {
            $code = strtoupper($user->preferred_currency);
            if (is_array(config("currencies.currencies.$code"))) {
                return $code;
            }
        }

        $default = strtoupper((string) config('currencies.default', 'IDR'));
        if (is_array(config("currencies.currencies.$default"))) {
            return $default;
        }

        return 'IDR';
    }

    /**
     * Get resolved config array for a currency.
     *
     * @return array{symbol:string,position:string,decimals:int,decimal_separator:string,thousands_separator:string,space:bool,code:string}
     */
    public static function config(?string $currency = null): array
    {
        $code = self::resolveCode($currency);
        $cfg = config("currencies.currencies.$code");

        return [
            'code' => $code,
            'symbol' => (string) ($cfg['symbol'] ?? 'Rp'),
            'position' => ($cfg['position'] ?? 'before') === 'after' ? 'after' : 'before',
            'decimals' => (int) ($cfg['decimals'] ?? 0),
            'decimal_separator' => (string) ($cfg['decimal_separator'] ?? ','),
            'thousands_separator' => (string) ($cfg['thousands_separator'] ?? '.'),
            'space' => (bool) ($cfg['space'] ?? true),
        ];
    }

    public static function symbol(?string $currency = null): string
    {
        return self::config($currency)['symbol'];
    }

    /**
     * Format an amount without the currency symbol, separators still applied.
     */
    public static function number(int|float|string|null $amount, ?string $currency = null): string
    {
        $value = 0.0;
        if ($amount !== null && $amount !== '') {
            $value = is_numeric($amount) ? (float) $amount : 0.0;
        }

        $cfg = self::config($currency);
        $isNegative = $value < 0;

        $number = number_format(
            $isNegative ? -$value : $value,
            $cfg['decimals'],
            $cfg['decimal_separator'],
            $cfg['thousands_separator']
        );

        return $isNegative ? '-'.$number : $number;
    }

    /**
     * Format an amount. Null becomes 0. Negatives get a leading minus BEFORE the symbol.
     */
    public static function format(int|float|string|null $amount, ?string $currency = null): string
    {
        $value = 0.0;
        if ($amount !== null && $amount !== '') {
            $value = is_numeric($amount) ? (float) $amount : 0.0;
        }

        $cfg = self::config($currency);
        $isNegative = $value < 0;
        $abs = $isNegative ? -$value : $value;

        $number = number_format(
            $abs,
            $cfg['decimals'],
            $cfg['decimal_separator'],
            $cfg['thousands_separator']
        );

        $gap = $cfg['space'] ? ' ' : '';
        $body = $cfg['position'] === 'after'
            ? $number.$gap.$cfg['symbol']
            : $cfg['symbol'].$gap.$number;

        return $isNegative ? '-'.$body : $body;
    }
}
