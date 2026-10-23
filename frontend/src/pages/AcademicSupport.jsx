import React from 'react';
import { Link } from 'react-router-dom';
import { Users, Compass, CheckCircle2, MessageSquare, ArrowRight } from 'lucide-react';

export default function AcademicSupport() {
  return (
    <div style={{ backgroundColor: 'var(--bg-jet-black)', color: 'var(--text-secondary)', paddingTop: 'calc(var(--nav-height) + 40px)', paddingBottom: '100px' }}>
      <div className="container">
        {/* Header */}
        <div style={{ maxWidth: '820px', marginBottom: '50px' }}>
          <div className="eyebrow">ACADEMIC MENTORSHIP</div>
          <h1 className="h1-display" style={{ marginBottom: '20px' }}>
            Academic Support & Mentorship
          </h1>
          <p style={{ fontSize: '1.15rem', color: 'var(--text-secondary)', lineHeight: 1.7 }}>
            Connecting high school students with volunteer educators, college scholars, and guidance clinics to resolve academic hurdles in challenging STEM subjects.
          </p>
        </div>

        {/* Support Grid */}
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '28px', marginBottom: '70px' }}>
          <div className="card-base">
            <div style={{ color: 'var(--electric-cyan)', marginBottom: '16px' }}><MessageSquare size={28} /></div>
            <h3 style={{ fontSize: '1.25rem', color: '#FFF', marginBottom: '12px' }}>Doubt Resolution Clinics</h3>
            <p style={{ fontSize: '0.92rem', color: 'var(--text-muted)', lineHeight: 1.6 }}>
              Weekly virtual sessions where students can post specific textbook questions, board exam dilemmas, and conceptual bottlenecks to receive stepwise video or audio explanations.
            </p>
          </div>

          <div className="card-base">
            <div style={{ color: 'var(--electric-cyan)', marginBottom: '16px' }}><Compass size={28} /></div>
            <h3 style={{ fontSize: '1.25rem', color: '#FFF', marginBottom: '12px' }}>Higher Education Guidance</h3>
            <p style={{ fontSize: '0.92rem', color: 'var(--text-muted)', lineHeight: 1.6 }}>
              Mentoring for Grade 11 and 12 learners exploring entrance exams, STEM degrees, scholarship options, and admission strategies across Indian universities.
            </p>
          </div>

          <div className="card-base">
            <div style={{ color: 'var(--electric-cyan)', marginBottom: '16px' }}><Users size={28} /></div>
            <h3 style={{ fontSize: '1.25rem', color: '#FFF', marginBottom: '12px' }}>Peer Learning Batches</h3>
            <p style={{ fontSize: '0.92rem', color: 'var(--text-muted)', lineHeight: 1.6 }}>
              Moderated cohort study circles where motivated students collaborate on complex homework assignments under the guidance of certified academic leads.
            </p>
          </div>
        </div>

        {/* CTA */}
        <div className="card-surface-3" style={{ maxWidth: '800px', margin: '0 auto', textAlign: 'center', padding: '40px' }}>
          <h3 style={{ fontSize: '1.5rem', color: '#FFF', marginBottom: '14px' }}>
            Looking for Personal 1-on-1 Dedicated Tutoring?
          </h3>
          <p style={{ fontSize: '0.95rem', color: 'var(--text-muted)', marginBottom: '24px' }}>
            Our volunteer clinics run in groups. If your child requires individual custom pacing with private lesson scheduling, explore our verified tutoring directory.
          </p>
          <Link to="/tutoring" className="btn btn-cyan">
            Book 1-on-1 Demo Session <ArrowRight size={16} />
          </Link>
        </div>
      </div>
    </div>
  );
}
