import { useEffect, useState } from 'react';
import { metaApi } from '../api';

/*
 * Loads the shared reference lists (categories, cities, job/experience/education
 * label maps) from /api/meta, once, and caches them module-wide so the filters
 * and the job create/edit forms don't refetch on every mount.
 */
let cache = null;
let inflight = null;

export function useMeta() {
    const [meta, setMeta] = useState(cache);

    useEffect(() => {
        if (cache) {
            setMeta(cache);
            return;
        }
        inflight = inflight || metaApi.get().then((res) => {
            cache = res.data;
            return cache;
        });
        let active = true;
        inflight.then((data) => {
            if (active) setMeta(data);
        });
        return () => {
            active = false;
        };
    }, []);

    return meta;
}
