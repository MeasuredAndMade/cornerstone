import React from 'react';
import { NavLink } from 'react-router-dom';
import logo from '../assets/logo.png'; // Company Logo

const Sidebar = () => {
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
        <aside className='menu p-4 has-background-light' style={{ width: '300px' }}>
            <div className='mb-5 has-text-centered'>
                <figure className='image is-96x96 is-inline-block'>
                    <img src={logo} alt='Measured & Made' style={{ borderRadius: '12px' }} />
                </figure>
                <p className='is-size-6 has-text-grey mt-2'>Admin Panel</p>
            </div>
            <ul className='menu-list'>
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
                                textDecoration: "none",
                                borderBottom: "2px solid transparent"
                            }}
                        >
                            <span className="mr-2">{item.icon}</span>
                            {item.label}
                        </NavLink>

                    </li>
                ))}
            </ul>
            <button className='button is-light is-fullwidth mt-6'>← Collapse</button>
        </aside>
    );
};

export default Sidebar;