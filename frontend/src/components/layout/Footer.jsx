import React from 'react';
import { Link } from 'react-router-dom';
import { GraduationCap, ShieldCheck, Mail, MapPin, ExternalLink, Heart } from 'lucide-react';

export default function Footer() {
  return (
    <footer
      style={{
        backgroundColor: '#050505',
        borderTop: '1px solid var(--border-card)',
        paddingTop: '80px',
        paddingBottom: '40px',
        marginTop: 'auto',
        position: 'relative',
        overflow: 'hidden',
      }}
    >
      {/* Ambient background glow */}
      <div
        style={{
          position: 'absolute',
          top: 0,
          left: '50%',
          transform: 'translateX(-50%)',
          width: '600px',
          height: '1px',
          background: 'linear-gradient(90deg, transparent, var(--electric-cyan), var(--royal-amethyst), transparent)',
          opacity: 0.6,
        }}
      />

      <div className="container">
        <div
          style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))',
            gap: '40px',
            marginBottom: '60px',
          }}
        >
          {/* Brand Info */}
          <div style={{ maxWidth: '320px' }}>
            <Link to="/" style={{ display: 'flex', alignItems: 'center', gap: '10px', textDecoration: 'none', marginBottom: '16px' }}>
              <div
                style={{
                  width: '36px',
                  height: '36px',
                  borderRadius: '10px',
                  background: 'linear-gradient(135deg, #00E5FF, #3B0A6B)',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  color: '#050505',
                }}
              >
                <GraduationCap size={20} strokeWidth={2.5} />
              </div>
              <span style={{ fontFamily: 'var(--font-heading)', fontWeight: 800, fontSize: '1.2rem', color: '#FFF' }}>
                SCHOLARBEE
              </span>
            </Link>
            <p style={{ fontSize: '0.9rem', color: 'var(--text-muted)', lineHeight: 1.6, marginBottom: '20px' }}>
              Dedicated to eliminating educational inequality across India through free online learning, academic scholarships, and transparent community support.
            </p>
            <div style={{ display: 'flex', alignItems: 'center', gap: '8px', fontSize: '0.82rem', color: 'var(--text-secondary)' }}>
              <ShieldCheck size={16} color="var(--electric-cyan)" />
              <span>Registered Nonprofit Educational Initiative</span>
            </div>
          </div>

          {/* Programmes */}
          <div>
            <h4 style={{ fontSize: '0.95rem', textTransform: 'uppercase', letterSpacing: '0.08em', color: '#FFF', marginBottom: '18px' }}>
              Our Programmes
            </h4>
            <ul style={{ listStyle: 'none', display: 'flex', flexDirection: 'column', gap: '12px' }}>
              <li>
                <Link to="/programmes/free-online-learning" style={{ textDecoration: 'none', color: 'var(--text-muted)', fontSize: '0.9rem', transition: 'color 0.2s' }}>
                  Free Online Learning
                </Link>
              </li>
              <li>
                <Link to="/programmes/scholarships" style={{ textDecoration: 'none', color: 'var(--text-muted)', fontSize: '0.9rem', transition: 'color 0.2s' }}>
                  Scholarships & Aid
                </Link>
              </li>
              <li>
                <Link to="/programmes/academic-support" style={{ textDecoration: 'none', color: 'var(--text-muted)', fontSize: '0.9rem', transition: 'color 0.2s' }}>
                  Academic Support & Mentoring
                </Link>
              </li>
              <li>
                <Link to="/tutoring" style={{ textDecoration: 'none', color: 'var(--electric-cyan)', fontSize: '0.9rem', fontWeight: 600 }}>
                  Supplementary Tutoring (Paid)
                </Link>
              </li>
            </ul>
          </div>

          {/* Organization & Governance */}
          <div>
            <h4 style={{ fontSize: '0.95rem', textTransform: 'uppercase', letterSpacing: '0.08em', color: '#FFF', marginBottom: '18px' }}>
              Organization
            </h4>
            <ul style={{ listStyle: 'none', display: 'flex', flexDirection: 'column', gap: '12px' }}>
              <li>
                <Link to="/about" style={{ textDecoration: 'none', color: 'var(--text-muted)', fontSize: '0.9rem' }}>Who We Are</Link>
              </li>
              <li>
                <Link to="/education-challenge" style={{ textDecoration: 'none', color: 'var(--text-muted)', fontSize: '0.9rem' }}>The Education Challenge</Link>
              </li>
              <li>
                <Link to="/impact" style={{ textDecoration: 'none', color: 'var(--text-muted)', fontSize: '0.9rem' }}>Verified Impact</Link>
              </li>
              <li>
                <Link to="/governance" style={{ textDecoration: 'none', color: 'var(--text-muted)', fontSize: '0.9rem' }}>Governance & Compliance</Link>
              </li>
              <li>
                <Link to="/careers" style={{ textDecoration: 'none', color: 'var(--text-muted)', fontSize: '0.9rem' }}>Careers & Openings</Link>
              </li>
            </ul>
          </div>

          {/* Get Involved & Contact */}
          <div>
            <h4 style={{ fontSize: '0.95rem', textTransform: 'uppercase', letterSpacing: '0.08em', color: '#FFF', marginBottom: '18px' }}>
              Get Involved
            </h4>
            <ul style={{ listStyle: 'none', display: 'flex', flexDirection: 'column', gap: '12px', marginBottom: '20px' }}>
              <li>
                <Link to="/donate" style={{ textDecoration: 'none', color: 'var(--text-muted)', fontSize: '0.9rem' }}>Make a Contribution</Link>
              </li>
              <li>
                <Link to="/volunteer" style={{ textDecoration: 'none', color: 'var(--text-muted)', fontSize: '0.9rem' }}>Volunteer as an Educator</Link>
              </li>
              <li>
                <Link to="/contact" style={{ textDecoration: 'none', color: 'var(--text-muted)', fontSize: '0.9rem' }}>Contact Official Secretariat</Link>
              </li>
            </ul>
            <div style={{ display: 'flex', alignItems: 'center', gap: '6px', fontSize: '0.85rem', color: 'var(--text-muted)' }}>
              <Mail size={14} color="var(--electric-cyan)" />
              <span>contact@scholarb.org</span>
            </div>
          </div>
        </div>

        {/* Legal Transparency & Separation Notice */}
        <div
          style={{
            backgroundColor: 'var(--surface-1)',
            border: '1px solid var(--border-subtle)',
            borderRadius: 'var(--radius-md)',
            padding: '16px 20px',
            marginBottom: '32px',
            fontSize: '0.8rem',
            color: 'var(--text-muted)',
            lineHeight: 1.6,
          }}
        >
          <strong style={{ color: 'var(--text-secondary)' }}>Transparency Notice:</strong> Scholarbee Foundation is dedicated to nonprofit educational initiatives, free resources, and merit scholarships. Supplementary 1-on-1 and group private tutoring are marketplace services facilitated by independent verified educators and are clearly distinguished from charitable programmes.
        </div>

        {/* Bottom Bar */}
        <div
          style={{
            display: 'flex',
            flexWrap: 'wrap',
            alignItems: 'center',
            justifyContent: 'space-between',
            gap: '16px',
            paddingTop: '24px',
            borderTop: '1px solid var(--border-subtle)',
            fontSize: '0.82rem',
            color: 'var(--text-muted)',
          }}
        >
          <div>
            © {new Date().getFullYear()} Scholarbee Foundation. All rights reserved.
          </div>
          <div style={{ display: 'flex', gap: '20px' }}>
            <Link to="/privacy" style={{ color: 'var(--text-muted)', textDecoration: 'none' }}>Privacy Policy</Link>
            <Link to="/terms" style={{ color: 'var(--text-muted)', textDecoration: 'none' }}>Terms of Service</Link>
            <Link to="/cookies" style={{ color: 'var(--text-muted)', textDecoration: 'none' }}>Cookie Preferences</Link>
          </div>
        </div>
      </div>
    </footer>
  );
}
