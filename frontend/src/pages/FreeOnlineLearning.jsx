import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { BookOpen, Video, FileText, CheckCircle2, Search, ArrowRight, Loader2 } from 'lucide-react';
import { api } from '../services/api';

export default function FreeOnlineLearning() {
  const [courses, setCourses] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function loadData() {
      setLoading(true);
      try {
        const res = await api.getCourses();
        if (res.ok && Array.isArray(res.data)) {
          setCourses(res.data);
        } else {
          setCourses([
            { id: 1, title: 'Foundation Mathematics (Grades 9 & 10)', category: 'Mathematics', lessons: '24 Modules', level: 'Secondary' },
            { id: 2, title: 'Introductory Mechanics & Optics', category: 'Physics', lessons: '18 Modules', level: 'Secondary' },
            { id: 3, title: 'Chemical Reactions & Periodic Trends', category: 'Chemistry', lessons: '16 Modules', level: 'Secondary' },
            { id: 4, title: 'Foundations of Computer Programming (Python)', category: 'Computer Science', lessons: '20 Modules', level: 'Open' },
          ]);
        }
      } catch (e) {
        console.error('Error fetching courses:', e);
      } finally {
        setLoading(false);
      }
    }
    loadData();
  }, []);

  return (
    <div style={{ backgroundColor: 'var(--bg-jet-black)', color: 'var(--text-secondary)', paddingTop: 'calc(var(--nav-height) + 40px)', paddingBottom: '100px' }}>
      <div className="container">
        {/* Header */}
        <div style={{ maxWidth: '820px', marginBottom: '50px' }}>
          <div className="eyebrow">CHARITABLE PROGRAMME</div>
          <h1 className="h1-display" style={{ marginBottom: '20px' }}>
            Free Open-Access Online Learning
          </h1>
          <p style={{ fontSize: '1.15rem', color: 'var(--text-secondary)', lineHeight: 1.7 }}>
            High-quality digital modules, downloadable revision guides, and practice tests accessible at zero financial cost to every student across India.
          </p>
        </div>

        {/* Feature Badges */}
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(240px, 1fr))', gap: '20px', marginBottom: '60px' }}>
          <div className="card-surface-3">
            <h4 style={{ fontSize: '1.05rem', color: 'var(--electric-cyan)', marginBottom: '6px' }}>100% Free Always</h4>
            <p style={{ fontSize: '0.85rem', color: 'var(--text-muted)' }}>No credit card, subscriptions, or paywalls required.</p>
          </div>
          <div className="card-surface-3">
            <h4 style={{ fontSize: '1.05rem', color: 'var(--electric-cyan)', marginBottom: '6px' }}>Board Aligned</h4>
            <p style={{ fontSize: '0.85rem', color: 'var(--text-muted)' }}>Syllabus-mapped for CBSE, ICSE, and major state boards.</p>
          </div>
          <div className="card-surface-3">
            <h4 style={{ fontSize: '1.05rem', color: 'var(--electric-cyan)', marginBottom: '6px' }}>Verified Quality</h4>
            <p style={{ fontSize: '0.85rem', color: 'var(--text-muted)' }}>Curated and reviewed by certified education specialists.</p>
          </div>
        </div>

        {/* Available Modules Section */}
        <h2 className="h2-section" style={{ marginBottom: '28px' }}>Available Learning Modules</h2>

        {loading ? (
          <div style={{ textAlign: 'center', padding: '60px 0' }}>
            <Loader2 size={32} className="animate-spin" color="var(--electric-cyan)" />
          </div>
        ) : (
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))', gap: '24px', marginBottom: '60px' }}>
            {courses.map((c) => (
              <div key={c.id} className="card-base" style={{ display: 'flex', flexDirection: 'column' }}>
                <span style={{ fontSize: '0.75rem', textTransform: 'uppercase', letterSpacing: '0.1em', color: 'var(--electric-cyan)', fontWeight: 700, marginBottom: '6px' }}>
                  {c.category || 'Core Subject'}
                </span>
                <h3 style={{ fontSize: '1.2rem', color: '#FFF', marginBottom: '12px' }}>{c.title}</h3>
                <div style={{ display: 'flex', alignItems: 'center', gap: '12px', fontSize: '0.85rem', color: 'var(--text-muted)', marginBottom: '24px', flex: 1 }}>
                  <span>{c.lessons || 'Full Curriculum'}</span>
                  <span>•</span>
                  <span>{c.level || 'Secondary'}</span>
                </div>
                <a
                  href={`https://scholarb.org/course/${c.id}`}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="btn btn-outline"
                  style={{ width: '100%', fontSize: '0.88rem' }}
                >
                  Start Learning Now <ArrowRight size={16} />
                </a>
              </div>
            ))}
          </div>
        )}

        <div style={{ textAlign: 'center' }}>
          <p style={{ fontSize: '0.95rem', color: 'var(--text-muted)', marginBottom: '18px' }}>
            Looking for personal live attention with a dedicated tutor?
          </p>
          <Link to="/tutoring" className="btn btn-cyan">
            Explore 1-on-1 Supplementary Tutoring
          </Link>
        </div>
      </div>
    </div>
  );
}
