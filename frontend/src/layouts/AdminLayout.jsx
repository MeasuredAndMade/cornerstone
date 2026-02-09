import React, { useEffect, useState } from "react";
import { Outlet } from "react-router-dom";
import Sidebar from "../components/Sidebar";
import Topbar from "../components/Topbar";
import useBreakpoint from "../hooks/useBreakpoint";

const AdminLayout = () => {
  const { isMobile, isTablet } = useBreakpoint();

  const [collapsed, setCollapsed] = useState(false);

  // Auto-collapse on tablet & mobile
  useEffect(() => {
    if (isTablet || isMobile) {
      setCollapsed(true);
    } else {
      setCollapsed(false);
    }
  }, [isTablet, isMobile]);

  return (
    <div style={{ display: "flex", height: "100vh", overflow: "hidden" }} className="has-background-light">
      
      {/* SIDEBAR */}
      <Sidebar collapsed={collapsed} setCollapsed={setCollapsed} />

      {/* MAIN AREA */}
      <div
        style={{
          flex: 1,
          display: "flex",
          flexDirection: "column",
          marginLeft: collapsed ? "80px" : "300px",
          transition: "margin-left 0.25s ease"
        }}
      >
        <Topbar />

        <div className="has-background-light" style={{ flex: 1, overflowY: "auto", padding: "1.5rem" }}>
          <Outlet />
        </div>
      </div>
    </div>
  );
};

export default AdminLayout;
