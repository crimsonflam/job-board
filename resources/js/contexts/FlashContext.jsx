import { createContext, useCallback, useContext, useState } from 'react';

/*
 * Replaces Laravel's session flash messages. `flash(type, text)` pushes a
 * banner (success | error | info) that auto-dismisses; the banners are rendered
 * by <Layout> at the top of the page, styled exactly like the old Blade flashes
 * (green / red / neutral-gray).
 */
const FlashContext = createContext(null);

let nextId = 1;

export function FlashProvider({ children }) {
    const [messages, setMessages] = useState([]);

    const dismiss = useCallback((id) => {
        setMessages((current) => current.filter((m) => m.id !== id));
    }, []);

    const flash = useCallback(
        (type, text) => {
            if (!text) return;
            const id = nextId++;
            setMessages((current) => [...current, { id, type, text }]);
            // Auto-dismiss after 5s, like a transient toast.
            setTimeout(() => dismiss(id), 5000);
        },
        [dismiss]
    );

    return (
        <FlashContext.Provider value={{ messages, flash, dismiss }}>
            {children}
        </FlashContext.Provider>
    );
}

export function useFlash() {
    const ctx = useContext(FlashContext);
    if (!ctx) throw new Error('useFlash must be used within a FlashProvider');
    return ctx;
}
