import React from 'react';
import { Link } from 'react-router-dom';
import { Briefcase, MapPin, ArrowRight, Mail } from 'lucide-react';

export default function Careers() {
  const openings = [
    { title: 'Curriculum Developer (Secondary STEM)', type: 'Full-Time', location: 'Remote (India)', dept: 'Education & Pedagogy' },
    { title: 'Student Community Coordinator', type: 'Full-Time', location: 'New Delhi / Remote', dept: 'Student Outreach' },
    { title: 'Academic Content Reviewer (Physics & Chemistry)', type: 'Part-Time', location: 'Remote', dept: 'Editorial & Quality' },
  ];

  return (
    <div style={{ backgroundColor: 'var(--bg-jet-black)', color: 'var(--text-secondary)', paddingTop: 'calc(var(--nav-height) + 40px)', paddingBottom: '100px' }}>
      <div className="container" style={{ maxWidth: '880px' }}>
        <div style={{ textAlign: 'center', marginBottom: '50px' }}>
          <div className="eyebrow" style={{ margin: '0 auto 16px auto' }}>CAREERS AT SCHOLARBEE</div>
          <h1 className="h1-display" style={{ marginBottom: '18px' }}>
            Work With Purpose
          </h1>
          <p style={{ fontSize: '1.15rem', color: 'var(--text-secondary)', lineHeight: 1.6, maxWidth: '640px', margin: '0 auto' }}>
            Join our mission to democratize academic excellence and build transparent educational tools across India.
          </p>
        </div>

        <div style={{ display: 'flex', flexDirection: 'column', gap: '20px', marginBottom: '60px' }}>
          {openings.map((job, idx) => (
            <div key={idx} className="card-base" style={{ display: 'flex', flexWrap: 'wrap', alignItems: 'center', justifyContent: 'space-between', gap: '16px' }}>
              <div>
                <span style={{ fontSize: '0.78rem', textTransform: 'uppercase', color: 'var(--electric-cyan)', fontWeight: 700, letterSpacing: '0.08em' }}>
                  {job.dept}
                </span>
                <h3 style={{ fontSize: '1.25rem', color: '#FFF', margin: '6px 0' }}>{job.title}</h3>
                <div style={{ display: 'flex', alignItems: 'center', gap: '12px', fontSize: '0.85rem', color: 'var(--text-muted)' }}>
                  <span>{job.type}</span>
                  <span>•</span>
                  <span>{job.location}</span>
                </div>
              </div>
              <a href={`mailto:careers@scholarb.org?subject=Application for ${job.title}`} className="btn btn-outline" style={{ fontSize: '0.88rem', padding: '10px 20px' }}>
                Apply via Email <ArrowRight size={14} />
              </a>
            </div>
          ))}
        </div>

        <div className="card-surface-3" style={{ textAlign: 'center', padding: '36px' }}>
          <h4 style={{ fontSize: '1.2rem', color: '#FFF', marginBottom: '8px' }}>Don't see your specific role?</h4>
          <p style={{ fontSize: '0.9rem', color: 'var(--text-muted)', marginBottom: '18px' }}>
            We always welcome talented software developers, curriculum authors, and pedagogical coordinators.
          </p>
          <a href="mailto:careers@scholarb.org" className="btn btn-cyan" style={{ fontSize: '0.9rem' }}>
            <Mail size={16} /> Send Spontaneous Resume
          </a>
        </div>
      </div>
    </div>
  );
}
