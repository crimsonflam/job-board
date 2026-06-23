
const meta = document.querySelector('meta[name="base-path"]');
export const BASE_PATH = (meta?.getAttribute('content') || '').replace(/\/$/, '');

/** Prefix an app-absolute path with the base path (e.g. for window.location / hrefs). */
export function withBase(path) {
    return `${BASE_PATH}${path}`;
}
