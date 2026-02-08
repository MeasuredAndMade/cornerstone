import Sidebar from "../components/Sidebar";
import Topbar from "../components/Topbar";
import { Outlet, useLocation } from "react-router-dom";

const AdminLayout = () => {
    const location = useLocation();
    const title = location.pathname.split('/')[1] || "Dashboard";

    return (
        <div className="columns m-0" style={{ height: '100vh' }}>
            {/* Sidebar */}
            <div className="column is-narrow p-0">
                <Sidebar />
            </div>
            {/* Right Side - Topbar/Content */}
            <div className="column p-0 is-flex is-flex-direction-column">
                <Topbar title={title.charAt(0).toUpperCase() + title.slice(1)} />
                <main className="p-5 has-background-light is-flex-grow-1">
                    <Outlet />
                </main>
            </div>
        </div>
    );
};

export default AdminLayout;