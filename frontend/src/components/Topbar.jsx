import React, { useState } from "react";

const Topbar = ({ title }) => {
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
        position: "relative"
      }}
    >
      {/* LEFT: PAGE TITLE */}
      <h1 className="title is-4 m-0">{title}</h1>

      {/* RIGHT: ICONS (desktop only) */}
      <div className="is-hidden-touch" style={{ display: "flex", gap: "1rem" }}>
        <button className="button is-white">🔔</button>
        <button className="button is-white">👤</button>
      </div>

      {/* RIGHT: COLLAPSED MENU BUTTON (mobile only) */}
      <button
        className="button is-white is-hidden-desktop"
        onClick={() => setMenuOpen(!menuOpen)}
      >
        ☰
      </button>

      {/* MOBILE DROPDOWN */}
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
            zIndex: 10
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
