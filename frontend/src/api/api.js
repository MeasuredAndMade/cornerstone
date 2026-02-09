const API_URL = import.meta.env.VITE_API_URL;
export async function fetchData(endpoint) {
    const res = await fetch(`${API_URL}${endpoint}`);
    if(!res.ok) throw new Error(`Failed to fetch ${endpoint}`);
    return res.json();
}