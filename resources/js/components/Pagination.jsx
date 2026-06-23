/*
 * Renders pagination from a Laravel resource-collection `meta` object
 * ({ current_page, last_page }) and calls onPage(page) — the parent updates its
 * query state (which preserves the active filters, like withQueryString()).
 */
export default function Pagination({ meta, onPage }) {
    if (!meta || meta.last_page <= 1) return null;

    const { current_page: current, last_page: last } = meta;

    // Compact page window around the current page.
    const pages = [];
    const start = Math.max(1, current - 2);
    const end = Math.min(last, current + 2);
    for (let p = start; p <= end; p++) pages.push(p);

    const baseBtn =
        'relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition-colors';
    const inactive = 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50';
    const active = 'bg-primary-600 border-primary-600 text-white';
    const disabled = 'bg-white border-gray-200 text-gray-300 cursor-not-allowed';

    return (
        <nav className="mt-8 flex items-center justify-center gap-1.5">
            <button
                className={`${baseBtn} ${current <= 1 ? disabled : inactive}`}
                disabled={current <= 1}
                onClick={() => onPage(current - 1)}
            >
                Prev
            </button>

            {start > 1 && (
                <>
                    <button className={`${baseBtn} ${inactive}`} onClick={() => onPage(1)}>1</button>
                    {start > 2 && <span className="px-2 text-gray-400">…</span>}
                </>
            )}

            {pages.map((p) => (
                <button
                    key={p}
                    className={`${baseBtn} ${p === current ? active : inactive}`}
                    onClick={() => onPage(p)}
                >
                    {p}
                </button>
            ))}

            {end < last && (
                <>
                    {end < last - 1 && <span className="px-2 text-gray-400">…</span>}
                    <button className={`${baseBtn} ${inactive}`} onClick={() => onPage(last)}>{last}</button>
                </>
            )}

            <button
                className={`${baseBtn} ${current >= last ? disabled : inactive}`}
                disabled={current >= last}
                onClick={() => onPage(current + 1)}
            >
                Next
            </button>
        </nav>
    );
}
