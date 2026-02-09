import React, { useState } from "react";
import { Link } from 'react-router-dom';

const Topbar = () => {
  const [menuOpen, setMenuOpen] = useState(false);

  return (
    <nav
      className="px-4"
      style={{
        height: "64px",
        borderBottom: "1px solid #ddd",
        backgroundColor: "#fff",
        display: "flex",
        alignItems: "center",
        justifyContent: "space-between",
        position: "sticky",
        top: 0,
        zIndex: 10
      }}
    >
      <h1 className="title is-4 m-0">
        <Link to='/admin' className="has-text-grey-dark" style={{ textDecoration: 'none' }}>Measured & Made Admin</Link>
      </h1>

      <button className="button is-white" onClick={() => setMenuOpen(!menuOpen)}>
        ⋮
      </button>

      {menuOpen && (
        <div
          style={{
            position: "absolute",
            top: "64px",
            right: "0",
            background: "#fff",
            border: "1px solid #ddd",
            borderRadius: "6px",
            padding: "0.5rem",
            display: "flex",
            flexDirection: "column",
            gap: "0.5rem",
            zIndex: 20
          }}
        >
          <button className="button is-white">🔔 Notifications</button>
          <button className="button is-white">👤 Profile</button>
        </div>
      )}
    </nav>
  );
};

export default Topbar;
