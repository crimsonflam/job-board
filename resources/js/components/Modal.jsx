import { useEffect } from 'react';

/*
 * A reusable centered modal with a dimmed backdrop. Closes on Escape and on
 * backdrop click — the React equivalent of the Alpine `x-show` + `@click.away`
 * + `@keydown.escape.window` pattern used across the Blade modals.
 */
export default function Modal({ open, onClose, children, maxWidth = 'max-w-lg' }) {
    useEffect(() => {
        if (!open) return undefined;
        const onKey = (e) => {
            if (e.key === 'Escape') onClose?.();
        };
        window.addEventListener('keydown', onKey);
        return () => window.removeEventListener('keydown', onKey);
    }, [open, onClose]);

    if (!open) return null;

    return (
        <div className="fixed inset-0 z-50 overflow-y-auto">
            <div className="flex min-h-screen items-center justify-center p-4">
                {/* Backdrop */}
                <div className="fixed inset-0 bg-gray-900/50 transition-opacity" onClick={onClose} />

                {/* Panel */}
                <div className={`relative bg-white rounded-2xl shadow-xl w-full ${maxWidth} p-6`}>
                    {children}
                </div>
            </div>
        </div>
    );
}
