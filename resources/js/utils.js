/* Small shared helpers for the SPA. */

/** Flatten a Laravel 422 error bag (err.response.data.errors) to a string[]. */
export function allErrors(err) {
    const bag = err?.response?.data?.errors;
    if (bag) return Object.values(bag).flat();
    const message = err?.response?.data?.message;
    return message ? [message] : ['Something went wrong. Please try again.'];
}

/** The per-field error bag ({ field: [msg, ...] }) from a 422 response. */
export function fieldErrors(err) {
    return err?.response?.data?.errors ?? {};
}

const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

/** Format an ISO date as "Jun 23, 2026" (Blade's ->format('M d, Y')). */
export function formatDate(iso) {
    if (!iso) return '';
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) return '';
    const day = String(d.getDate()).padStart(2, '0');
    return `${MONTHS[d.getMonth()]} ${day}, ${d.getFullYear()}`;
}

/** Build a clean query object from filter state (drops empty values). */
export function cleanParams(obj) {
    const out = {};
    Object.entries(obj).forEach(([k, v]) => {
        if (Array.isArray(v)) {
            if (v.length) out[k] = v;
        } else if (v !== '' && v !== null && v !== undefined) {
            out[k] = v;
        }
    });
    return out;
}
