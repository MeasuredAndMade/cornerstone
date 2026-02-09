import React from 'react';

const Dashboard = () => {
    return (
        <div>
            {/* PAGE TITLE */}
            <h1 className='title is-3 mb-5 has-text-grey-dark'>Dashboard Overview</h1>

            {/* STATS CARDS */}
            <div className='columns is-multiline'>
                <div className='column is-3'>
                    <div className='box has-text-centered has-background-grey'>
                        <p className='heading'>Projects</p>
                        <p className='title'>12</p>
                    </div>
                </div>
                <div className='column is-3'>
                    <div className='box has-text-centered has-background-grey'>
                        <p className='heading'>Blog Posts</p>
                        <p className='title'>34</p>
                    </div>
                </div>
                <div className='column is-3'>
                    <div className='box has-text-centered has-background-grey'>
                        <p className='heading'>Media Items</p>
                        <p className='title'>89</p>
                    </div>
                </div>
                <div className='column is-3'>
                    <div className='box has-text-centered has-background-grey'>
                        <p className='heading'>Pending Comments</p>
                        <p className='title'>5</p>
                    </div>
                </div>
            </div>
            {/* RECENT ACTIVITY */}
            <div className='box mt-5 has-background-grey'>
                <h2 className='title is-5 mb-4 has-text-grey-lighter'>Recent Activity</h2>
                <ul>
                    <li className="mb-2 has-text-grey-lighter">📝 New blog post published: <strong>“How to Style Your Home Office”</strong></li>
                    <li className="mb-2 has-text-grey-lighter">📁 New project added: <strong>“Modern Kitchen Remodel”</strong></li>
                    <li className="mb-2 has-text-grey-lighter">💬 New comment on: <strong>“Choosing the Right Paint Colors”</strong></li>
                </ul>
            </div>
            {/* QUICK ACTIONS */}
            <div className='box mt-5 has-background-grey'>
                <h2 className='title is-5 mb-4'>Quick Actions</h2>
                <div className='buttons'>
                    <button className='button is-primary'>Add New Project</button>
                    <button className='button is-link'>Write Blog Post</button>
                    <button className='button is-info'>Upload Media</button>
                </div>
            </div>
        </div>
    );
};

export default Dashboard;