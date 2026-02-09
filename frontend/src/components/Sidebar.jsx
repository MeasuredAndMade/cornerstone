import React from 'react';
import { NavLink } from 'react-router-dom';
import logo from '../assets/logo.png'; // Company Logo

const Sidebar = ({ collapsed, setCollapsed }) => {
    const navItems = [ 
        { label: "Dashboard", icon: "📊", to: "/dashboard" }, 
        { label: "Projects", icon: "📁", to: "/projects" }, 
        { label: "Blog Posts", icon: "📝", to: "/blog" }, 
        { label: "Media Library", icon: "🖼️", to: "/media" }, 
        { label: "Comments", icon: "💬", to: "/comments" }, 
        { label: "Categories & Tags", icon: "🏷️", to: "/categories" }, 
        { label: "Users", icon: "👤", to: "/users" },
        { label: "Settings", icon: "⚙️", to: "/settings" }, 
    ];
    return (
        <aside className="menu p-4 has-background-light" style={{ height: "100%" }}>
            <div className='mb-5 has-text-centered'>
                {!collapsed && (
                    <>
                        <figure className='image is-96x96 is-inline-block'>
                            <img src={logo} alt='Measured & Made' style={{ borderRadius: '12px' }} />
                        </figure>
                        <p className='is-size-6 has-text-grey mt-2'>Admin Panel</p>
                    </>
                )}
            </div>
            <ul className='menu-list'>
                {navItems.map((item) => (
                    <li key={item.to}>
                        <div className="sidebar-tooltip-wrapper">
                            <NavLink
                            to={item.to}
                            className={({ isActive }) =>
                                `is-flex is-align-items-center p-2 mb-2 ${
                                isActive
                                    ? "has-text-primary has-text-weight-semibold"
                                    : "has-text-grey-dark"
                                }`
                            }
                            style={{
                                borderRadius: "8px",
                                textDecoration: "none",
                                borderBottom: "2px solid transparent",
                                justifyContent: collapsed ? "center" : "flex-start",
                                position: "relative"
                            }}
                            >
                            <span className="mr-2">{item.icon}</span>
                            {!collapsed && item.label}
                            </NavLink>

                            {collapsed && (
                            <span className="sidebar-tooltip">{item.label}</span>
                            )}
                        </div>
                    </li>
                ))}
            </ul>
            <button className="button is-white" onClick={() => setCollapsed(!collapsed)} style={{ width: collapsed ? "40px" : "100%", padding: collapsed ? "0.25rem" : "0.5rem", display: "flex", justifyContent: "center", alignItems: "center", borderRadius: "6px", marginTop: "1rem", transition: "0.25s" }} > {collapsed ? "→" : "← Collapse"} </button>
        </aside>
    );
};

export default Sidebar;