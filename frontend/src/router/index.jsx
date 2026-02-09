import { createBrowserRouter } from "react-router-dom";
import AdminLayout from "../layouts/AdminLayout";

// Pages
import Dashboard from "../pages/dashboard/Dashboard";
import Welcome from "../pages/dashboard/Welcome";

export const router = createBrowserRouter([
    {
        path: '/admin',
        element: <AdminLayout />,
        children: [
            {index: true, element: <Welcome />},
            { path: 'dashboard', element: <Dashboard />}
        ]
    }
])