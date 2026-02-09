import React from "react";
import { Outlet } from "react-router-dom";
import Sidebar from "../components/Sidebar";
import Topbar from "../components/Topbar";

const AdminLayout = () => {
  return (
    <div
      style={{
        display: "flex",
        height: "100vh",
        overflow: "hidden",
        backgroundColor: "#f5f5f5" // LIGHT BACKGROUND FOR THE WHOLE ADMIN
      }}
    >
      {/* SIDEBAR */}
      <Sidebar />

      {/* MAIN AREA */}
      <div
        style={{
          display: "flex",
          flexDirection: "column",
          flex: 1,
          overflow: "hidden",
          backgroundColor: "#ffffff" // LIGHT BACKGROUND FOR CONTENT AREA
        }}
      >
        <Topbar />

        <div
          style={{
            flex: 1,
            overflowY: "auto",
            padding: "1.5rem",
            backgroundColor: "#ffffff" // ENSURES NO DARK BLEED
          }}
        >
          <Outlet />
        </div>
      </div>
    </div>
  );
};

export default AdminLayout;
