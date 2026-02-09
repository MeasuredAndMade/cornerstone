import React from 'react';
import { Link } from 'react-router-dom';

const Welcome = () => {
    return (
        <div className='welcome-page'>
            {/* HERO */}
            <section className='hero is-light mb-5' style={{ borderRadius: '10px' }}>
                <div className='hero-body'>
                    <h1 className='title is-3 has-text-grey-dark has-text-centered'>Welcome to the <br />Measured & Made Admin Panel</h1>
                    <p className='subtitle is-6 has-text-grey'></p>
                </div>
            </section>
            {/* QUICK ACTIONS */}
            <h2 className='title is-5 mb-4 has-text-grey-dark is-uppercase'>Quick Actions</h2>
            <div className='columns is-multiline'>
                <div className='column is-one-quarter is-half-tablet is-full-mobile'>
                    <Link to='/admin/projects' className='box has-text-centered quick-action has-background-grey-dark'>
                        <span style={{ fontSize: '2rem' }}>📁</span>
                        <p className='mt-2 has-text-weight-semibold'>View Projects</p>
                    </Link>
                </div>
                <div className='column is-one-quarter is-half-tablet is-full-mobile'>
                    <Link to='/admin/projects/new' className='box has-text-centered quick-action has-background-grey-dark'>
                        <span style={{ fontSize: '2rem' }}>➕</span>
                        <p className='mt-2 has-text-weight-semibold'>Add New Project</p>
                    </Link>
                </div>
                <div className='column is-one-quarter is-half-tablet is-full-mobile'>
                    <Link to='/admin/blog' className='box has-text-centered quick-action has-background-grey-dark'>
                        <span style={{ fontSize: '2rem' }}>📝</span>
                        <p className='mt-2 has-text-weight-semibold'>Add New Project</p>
                    </Link>
                </div>
                <div className='column is-one-quarter is-half-tablet is-full-mobile'>
                    <Link to='/admin/media' className='box has-text-centered quick-action has-background-grey-dark'>
                        <span style={{ fontSize: '2rem' }}>🖼️</span>
                        <p className='mt-2 has-text-weight-semibold'>Add New Project</p>
                    </Link>
                </div>
            </div>
        </div>
    );
};

export default Welcome;