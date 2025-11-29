import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { simulationAPI } from '../../api/endpoints';

export function HistoryPage() {
    const navigate = useNavigate();
    const [simulations, setSimulations] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [selectedSimulation, setSelectedSimulation] = useState(null);
    const [deleteConfirm, setDeleteConfirm] = useState(null);
    const [page, setPage] = useState(1);

    useEffect(() => {
        loadSimulations();
    }, []);

    const loadSimulations = async () => {
        setLoading(true);
        setError('');
        try {
            const response = await simulationAPI.getAll(page, 15, 'created_at', 'desc');
            setSimulations(response.data.data || []);
        } catch (err) {
            if (err.response?.status === 401) {
                // Redirect to login
                navigate('/');
                return;
            }
            setError(err.response?.data?.error || 'Failed to load simulations');
            // Fallback to localStorage
            const stored = localStorage.getItem('simulatorHistory');
            if (stored) {
                const sims = JSON.parse(stored);
                setSimulations(sims.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp)));
            }
        }
        setLoading(false);
    };

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const handleLoadSimulation = (simulation) => {
        // Store the selected simulation in sessionStorage
        sessionStorage.setItem('loadSimulation', JSON.stringify(simulation.configuration));
        // Navigate back to home
        navigate('/');
    };

    const handleDeleteSimulation = async (id) => {
        try {
            await simulationAPI.delete(id);
            setSimulations(simulations.filter(sim => sim.id !== id));
            setDeleteConfirm(null);
        } catch (err) {
            setError(err.response?.data?.error || 'Failed to delete simulation');
        }
    };

    const handleClearAll = async () => {
        if (window.confirm('Are you sure you want to delete all simulation history? This action cannot be undone.')) {
            try {
                const ids = simulations.map(sim => sim.id);
                if (ids.length > 0) {
                    await simulationAPI.bulkDelete(ids);
                }
                setSimulations([]);
                setDeleteConfirm(null);
            } catch (err) {
                setError(err.response?.data?.error || 'Failed to clear history');
            }
        }
    };

    return (
        <div className="history-page glass-panel" style={{ marginTop: '20px' }}>
            <div className="history-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '24px', paddingBottom: '16px', borderBottom: '1px solid var(--glass-border)' }}>
                <div>
                    <h2 style={{ fontSize: 'var(--font-lg)', fontWeight: '600', marginBottom: '4px' }}>Simulation History</h2>
                    <p style={{ fontSize: 'var(--font-sm)', color: 'var(--text-secondary)' }}>Load and manage your previous simulations</p>
                </div>
                {simulations.length > 0 && (
                    <button
                        onClick={handleClearAll}
                        className="secondary-btn"
                        style={{ background: 'rgba(214, 48, 49, 0.1)', color: '#d63031', border: '1px solid rgba(214, 48, 49, 0.3)' }}
                    >
                        Clear All
                    </button>
                )}
            </div>

            {error && (
                <div style={{ background: 'rgba(214, 48, 49, 0.1)', border: '1px solid rgba(214, 48, 49, 0.3)', color: '#d63031', padding: '12px 16px', borderRadius: '8px', marginBottom: '16px', fontSize: 'var(--font-sm)' }}>
                    {error}
                </div>
            )}

            {loading ? (
                <div style={{ textAlign: 'center', padding: '40px' }}>
                    <p style={{ color: 'var(--text-secondary)' }}>Loading simulations...</p>
                </div>
            ) : simulations.length === 0 ? (
                <div style={{ textAlign: 'center', padding: '60px 40px', color: 'var(--text-secondary)' }}>
                    <p style={{ fontSize: 'var(--font-lg)', marginBottom: '8px' }}>📚 No simulation history yet</p>
                    <p style={{ fontSize: 'var(--font-sm)' }}>Run a simulation to start building your history</p>
                </div>
            ) : (
                <div className="simulations-grid" style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))', gap: '16px' }}>
                    {simulations.map((sim) => (
                        <div
                            key={sim.id}
                            className="simulation-card"
                            style={{
                                background: 'var(--card-bg)',
                                border: '1px solid var(--glass-border)',
                                borderRadius: '12px',
                                padding: '16px',
                                cursor: 'pointer',
                                transition: 'all 0.3s ease',
                                position: 'relative'
                            }}
                            onMouseEnter={(e) => e.currentTarget.style.transform = 'translateY(-4px)'}
                            onMouseLeave={(e) => e.currentTarget.style.transform = 'translateY(0)'}
                        >
                            <div style={{ marginBottom: '12px' }}>
                                <h3 style={{ fontSize: 'var(--font-base)', fontWeight: '600', marginBottom: '4px', color: 'var(--text-primary)' }}>
                                    {sim.name || 'Unnamed Simulation'}
                                </h3>
                                <p style={{ fontSize: 'var(--font-sm)', color: 'var(--text-secondary)' }}>
                                    {formatDate(sim.timestamp)}
                                </p>
                            </div>

                        <div style={{ background: 'rgba(0, 0, 0, 0.1)', borderRadius: '8px', padding: '12px', marginBottom: '12px', fontSize: 'var(--font-sm)', color: 'var(--text-secondary)' }}>
                            <div><strong>L1 Size:</strong> {sim.configuration?.l1Size || 'N/A'} KB</div>
                            <div><strong>L2 Size:</strong> {sim.configuration?.l2Size || 'N/A'} KB</div>
                            <div><strong>Block Size:</strong> {sim.configuration?.blockSize || 'N/A'} B</div>
                        </div>

                            <div style={{ display: 'flex', gap: '8px' }}>
                                <button
                                    onClick={() => handleLoadSimulation(sim)}
                                    className="primary-btn"
                                    style={{ flex: 1, fontSize: 'var(--font-sm)', padding: '8px 12px' }}
                                >
                                    Load
                                </button>
                                <button
                                    onClick={() => setDeleteConfirm(sim.id)}
                                    className="secondary-btn"
                                    style={{
                                        flex: 0.3,
                                        fontSize: 'var(--font-sm)',
                                        padding: '8px 12px',
                                        color: '#d63031',
                                        border: '1px solid rgba(214, 48, 49, 0.3)'
                                    }}
                                >
                                    🗑
                                </button>
                            </div>

                            {deleteConfirm === sim.id && (
                                <div style={{
                                    position: 'absolute',
                                    top: 0,
                                    left: 0,
                                    right: 0,
                                    bottom: 0,
                                    background: 'rgba(0, 0, 0, 0.8)',
                                    borderRadius: '12px',
                                    display: 'flex',
                                    flexDirection: 'column',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    gap: '12px',
                                    zIndex: 10
                                }}>
                                    <p style={{ color: 'white', fontSize: 'var(--font-sm)', fontWeight: '600', textAlign: 'center' }}>Delete this simulation?</p>
                                    <div style={{ display: 'flex', gap: '8px' }}>
                                        <button
                                            onClick={() => handleDeleteSimulation(sim.id)}
                                            style={{
                                                background: '#d63031',
                                                color: 'white',
                                                border: 'none',
                                                padding: '6px 12px',
                                                borderRadius: '6px',
                                                cursor: 'pointer',
                                                fontSize: 'var(--font-sm)',
                                                fontWeight: '600'
                                            }}
                                        >
                                            Delete
                                        </button>
                                        <button
                                            onClick={() => setDeleteConfirm(null)}
                                            style={{
                                                background: 'rgba(255, 255, 255, 0.2)',
                                                color: 'white',
                                                border: '1px solid rgba(255, 255, 255, 0.3)',
                                                padding: '6px 12px',
                                                borderRadius: '6px',
                                                cursor: 'pointer',
                                                fontSize: 'var(--font-sm)',
                                                fontWeight: '600'
                                            }}
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
