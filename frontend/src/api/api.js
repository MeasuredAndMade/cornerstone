const API_URL = import.meta.env.VITE_API_URL;

export async function fetchData(endpoint) {
    const url = `${API_URL}${endpoint.startsWith('/') ? endpoint : `/${endpoint}`}`;

    const res = await fetch(url);

    if (!res.ok) {
        throw new Error(`Failed to fetch ${endpoint} (${res.status})`);
    }

    return res.json();
}
