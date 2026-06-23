//checks if user logged in, if so updates useAuth() for all components using it, 
// so that we don't check everytime only once

import { createContext, useCallback, useContext, useEffect, useState } from 'react';
import { authApi } from '../api';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    const refresh = useCallback(async () => {
        try {
            const { data } = await authApi.me();
            setUser(data.data ?? null);
        } catch {
            setUser(null);
        }
    }, []);

    useEffect(() => {
        (async () => {
            await refresh();
            setLoading(false);
        })();
    }, [refresh]);

    const login = useCallback(async (payload) => {
        const { data } = await authApi.login(payload);
        setUser(data.data);
        return data.data;
    }, []);

    const register = useCallback(async (payload) => {
        const { data } = await authApi.register(payload);
        setUser(data.data);
        return data.data;
    }, []);

    const logout = useCallback(async () => {
        try {
            await authApi.logout();
        } finally {
            setUser(null);
        }
    }, []);

    return (
        <AuthContext.Provider
            value={{ user, setUser, loading, refresh, login, register, logout }}
        >
            {children}
        </AuthContext.Provider>
    );
}

export function useAuth() {
    const ctx = useContext(AuthContext);
    if (!ctx) throw new Error('useAuth must be used within an AuthProvider');
    return ctx;
}
