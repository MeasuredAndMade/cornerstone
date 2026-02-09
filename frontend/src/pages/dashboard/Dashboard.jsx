import React, { useEffect, useState } from 'react';
import { fetchData } from '../../api/api.js';

const Dashboard = () => {
    const [projects, setProjects] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() =>{
        const loadProjects = async () => {
            try{
                const data = await fetchData('/projects');
                console.log('API URL: ', API_URL)
                setProjects(data);
            } catch (err) {
                console.error(err);
                setError('Failed to load dashboard data');
            } finally {
                setLoading(false);
            }
        };
        loadProjects();
    }, []);

    const totalProjects = projects.length;
    const completeProjects = projects.filter(p => p.status === 'completed').length;
    const inProgressProjects = projects.filter(p => p.status === 'in_progress').length;

    const recentProjects = [...projects]
        .sort((a, b) => new Date(b.created_at) - newDate(a.created_at))
        .slice(0, 3);

    if (loading) {
        return <p className='has-text-grey'>Loading Dashboard...</p>
    }

    if (error) {
        return <p className='has-text-danger'>{error}</p>
    }
    return (
        <div>
            {/* PAGE TITLE */}
            <h1 className="title is-3 mb-5 has-text-grey-dark">Dashboard Overview</h1>

            {/* STATS CARDS */}
            <div className="columns is-multiline">
                <div className="column is-3">
                    <div className="box has-text-centered has-background-grey">
                        <p className="heading">Total Projects</p>
                        <p className="title">{totalProjects}</p>
                    </div>
                </div>

                <div className="column is-3">
                    <div className="box has-text-centered has-background-grey">
                        <p className="heading">Completed</p>
                        <p className="title">{completedProjects}</p>
                    </div>
                </div>

                <div className="column is-3">
                    <div className="box has-text-centered has-background-grey">
                        <p className="heading">In Progress</p>
                        <p className="title">{inProgressProjects}</p>
                    </div>
                </div>

                <div className="column is-3">
                    <div className="box has-text-centered has-background-grey">
                        <p className="heading">Media Items</p>
                        <p className="title">0</p>
                    </div>
                </div>
            </div>

            {/* RECENT ACTIVITY */}
            <div className="box mt-5 has-background-grey">
                <h2 className="title is-5 mb-4 has-text-grey-lighter">Recent Projects</h2>

                <ul>
                    {recentProjects.length > 0 ? (
                        recentProjects.map(project => (
                            <li key={project.id} className="mb-2 has-text-grey-lighter">
                                📁 New project added: <strong>{project.title}</strong>
                            </li>
                        ))
                    ) : (
                        <li className="has-text-grey-lighter">No recent activity yet.</li>
                    )}
                </ul>
            </div>

            {/* QUICK ACTIONS */}
            <div className="box mt-5 has-background-grey">
                <h2 className="title is-5 mb-4">Quick Actions</h2>
                <div className="buttons">
                    <button className="button is-primary">Add New Project</button>
                    <button className="button is-link">Write Blog Post</button>
                    <button className="button is-info">Upload Media</button>
                </div>
            </div>
        </div>
    );
};

export default Dashboard;