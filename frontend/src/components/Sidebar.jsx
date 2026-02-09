import React from "react";
import { NavLink } from "react-router-dom";
import logo from "../assets/logo.png";

const Sidebar = ({ collapsed, setCollapsed }) => {
  const navItems = [
    { label: "Dashboard", icon: "📊", to: "/admin/dashboard" },
    { label: "Projects", icon: "📁", to: "/admin/projects" },
    { label: "Blog Posts", icon: "📝", to: "/admin/blog" },
    { label: "Media Library", icon: "🖼️", to: "/admin/media" },
    { label: "Comments", icon: "💬", to: "/admin/comments" },
    { label: "Categories & Tags", icon: "🏷️", to: "/admin/categories" },
    { label: "Users", icon: "👤", to: "/admin/users" },
    { label: "Settings", icon: "⚙️", to: "/admin/settings" }
  ];

  return (
    <div
      style={{
        width: collapsed ? "80px" : "300px",
        transition: "width 0.25s ease",
        backgroundColor: "#fff",
        borderRight: "1px solid #ddd",
        height: "100vh",
        position: "fixed",
        left: 0,
        top: 0,
        zIndex: 20
      }}
    >
      <aside className="menu p-4" style={{ height: "100%" }}>
        {!collapsed && (
          <div className="has-text-centered mb-5">
            <figure className="image is-96x96 is-inline-block">
              <img src={logo} alt="Measured & Made" style={{ borderRadius: "12px" }} />
            </figure>
            <p className="is-size-6 has-text-grey mt-2">Admin Panel</p>
          </div>
        )}

        <ul className="menu-list">
          {navItems.map((item) => (
            <li key={item.to}>
              <NavLink
                to={item.to}
                className={({ isActive }) =>
                  `is-flex is-align-items-center p-2 mb-2 ${
                    isActive ? "has-text-primary has-text-weight-semibold" : "has-text-grey-dark"
                  }`
                }
                style={{
                  borderRadius: "8px",
                  justifyContent: collapsed ? "center" : "flex-start"
                }}
              >
                <span className="mr-2">{item.icon}</span>
                {!collapsed && item.label}
              </NavLink>
            </li>
          ))}
        </ul>

        {/* Collapse button */}
        <button
          className="button is-white"
          onClick={() => setCollapsed(!collapsed)}
          style={{
            width: collapsed ? "40px" : "100%",
            marginTop: "1rem"
          }}
        >
          {collapsed ? "→" : "← Collapse"}
        </button>
      </aside>
    </div>
  );
};

export default Sidebar;
