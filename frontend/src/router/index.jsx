import { createBrowserRouter } from "react-router-dom";
import AdminLayout from "../layouts/AdminLayout";

// Pages
import Dashboard from "../pages/dashboard/Dashboard";

export const router = createBrowserRouter([
    {
        path: '/admin',
        element: <AdminLayout />,
        children: [
            { path: 'dashboard', element: <Dashboard />}
        ]
    }
])