import React from 'react';
import { Link } from 'react-router-dom';
import { Home, Compass } from 'lucide-react';

export default function NotFound() {
  return (
    <div
      style={{
        backgroundColor: 'var(--bg-jet-black)',
        color: 'var(--text-secondary)',
        minHeight: '75vh',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        textAlign: 'center',
        padding: '40px 20px',
      }}
    >
      <div style={{ maxWidth: '520px' }}>
        <div style={{ fontSize: '5rem', fontWeight: 800, color: 'var(--electric-cyan)', fontFamily: 'var(--font-heading)', lineHeight: 1, marginBottom: '16px' }}>
          404
        </div>
        <h1 style={{ fontSize: '1.8rem', color: '#FFF', marginBottom: '12px' }}>Page Not Found</h1>
        <p style={{ fontSize: '1rem', color: 'var(--text-muted)', lineHeight: 1.6, marginBottom: '32px' }}>
          The page or curriculum section you are looking for has moved or does not exist.
        </p>
        <div style={{ display: 'flex', justifyContent: 'center', gap: '14px', flexWrap: 'wrap' }}>
          <Link to="/" className="btn btn-cyan">
            <Home size={16} /> Return to Homepage
          </Link>
          <Link to="/tutoring" className="btn btn-outline">
            <Compass size={16} /> Explore Tutors
          </Link>
        </div>
      </div>
    </div>
  );
}
