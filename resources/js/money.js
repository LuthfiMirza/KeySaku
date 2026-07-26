const DEFAULT_CFG = {
    code: 'IDR',
    symbol: 'Rp',
    position: 'before',
    decimals: 0,
    decimal_separator: ',',
    thousands_separator: '.',
    space: true,
};

function resolveConfig() {
    const cfg = (typeof window !== 'undefined' && window.appCurrency) || null;
    if (!cfg || typeof cfg !== 'object') return DEFAULT_CFG;
    return {
        code: cfg.code || DEFAULT_CFG.code,
        symbol: cfg.symbol ?? DEFAULT_CFG.symbol,
        position: cfg.position === 'after' ? 'after' : 'before',
        decimals: Number.isFinite(cfg.decimals) ? cfg.decimals : DEFAULT_CFG.decimals,
        decimal_separator: cfg.decimal_separator ?? DEFAULT_CFG.decimal_separator,
        thousands_separator: cfg.thousands_separator ?? DEFAULT_CFG.thousands_separator,
        space: cfg.space !== false,
    };
}

/**
 * Format a number to match App\Support\Money::format() byte-for-byte.
 * opts.symbol === false → return number only.
 */
export function formatMoney(value, opts = {}) {
    const cfg = resolveConfig();
    let num = Number(value);
    if (!Number.isFinite(num)) num = 0;

    const isNegative = num < 0;
    const abs = isNegative ? -num : num;

    // Round to given decimals (match PHP number_format rounding: half away from zero).
    const factor = Math.pow(10, cfg.decimals);
    const rounded = Math.round(abs * factor) / factor;

    const fixed = rounded.toFixed(cfg.decimals);
    const [intPart, decPart] = fixed.split('.');
    const withThousands = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, cfg.thousands_separator);
    const number = decPart ? withThousands + cfg.decimal_separator + decPart : withThousands;

    if (opts.symbol === false) {
        return isNegative ? '-' + number : number;
    }

    const gap = cfg.space ? ' ' : '';
    const body = cfg.position === 'after'
        ? number + gap + cfg.symbol
        : cfg.symbol + gap + number;

    return isNegative ? '-' + body : body;
}

export default formatMoney;
