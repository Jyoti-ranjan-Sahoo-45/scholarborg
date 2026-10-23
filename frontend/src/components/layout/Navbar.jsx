import React, { useState, useEffect } from 'react';
import { Link, NavLink, useLocation } from 'react-router-dom';
import { Menu, X, ChevronDown, GraduationCap, ArrowRight, Sparkles } from 'lucide-react';

export default function Navbar() {
  const [isScrolled, setIsScrolled] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [programmesOpen, setProgrammesOpen] = useState(false);
  const location = useLocation();

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 15);
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  // Close menus on route change
  useEffect(() => {
    setMobileMenuOpen(false);
    setProgrammesOpen(false);
    window.scrollTo(0, 0);
  }, [location.pathname]);

  // Lock body scroll when mobile menu is open
  useEffect(() => {
    if (mobileMenuOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  }, [mobileMenuOpen]);

  const navLinkStyle = ({ isActive }) => ({
    textDecoration: 'none',
    fontSize: '0.88rem',
    fontWeight: 500,
    color: isActive ? 'var(--electric-cyan)' : 'rgba(255, 255, 255, 0.78)',
    borderBottom: isActive ? '2px solid var(--electric-cyan)' : '2px solid transparent',
    paddingBottom: '4px',
    whiteSpace: 'nowrap',
    transition: 'color 0.18s, border-color 0.18s',
  });

  return (
    <>
      <header
        style={{
          position: 'fixed',
          top: 0,
          left: 0,
          right: 0,
          zIndex: 1000,
          height: 'var(--nav-height)',
          display: 'flex',
          alignItems: 'center',
          transition: 'all 0.25s ease',
          backgroundColor: isScrolled ? 'rgba(5, 5, 5, 0.95)' : 'rgba(5, 5, 5, 0.75)',
          backdropFilter: 'blur(16px)',
          WebkitBackdropFilter: 'blur(16px)',
          borderBottom: isScrolled
            ? '1px solid rgba(0, 229, 255, 0.15)'
            : '1px solid rgba(255, 255, 255, 0.06)',
        }}
      >
        <div
          style={{
            width: '100%',
            maxWidth: '1440px',
            margin: '0 auto',
            padding: '0 clamp(18px, 3vw, 40px)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'space-between',
            gap: '20px',
          }}
        >
          {/* Logo */}
          <Link
            to="/"
            style={{
              display: 'flex',
              alignItems: 'center',
              gap: '12px',
              textDecoration: 'none',
              flexShrink: 0,
            }}
            aria-label="Scholarbee Foundation Home"
          >
            <div
              style={{
                width: '36px',
                height: '36px',
                borderRadius: '9px',
                background: 'linear-gradient(135deg, #00E5FF, #3B0A6B)',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                color: '#050505',
                boxShadow: '0 0 16px rgba(0, 229, 255, 0.35)',
                flexShrink: 0,
              }}
            >
              <GraduationCap size={20} strokeWidth={2.4} />
            </div>
            <div style={{ display: 'flex', flexDirection: 'column', lineHeight: 1.15 }}>
              <span
                style={{
                  fontFamily: 'var(--font-heading)',
                  fontWeight: 700,
                  fontSize: '1.15rem',
                  letterSpacing: '-0.01em',
                  color: '#FFFFFF',
                }}
              >
                Scholarbee
              </span>
              <span
                style={{
                  fontSize: '0.72rem',
                  fontWeight: 600,
                  color: 'var(--electric-cyan)',
                  letterSpacing: '0.04em',
                }}
              >
                Foundation
              </span>
            </div>
          </Link>

          {/* Desktop Navigation Links */}
          <nav
            style={{
              display: 'none',
              alignItems: 'center',
              gap: 'clamp(14px, 1.8vw, 24px)',
              flexShrink: 0,
            }}
            className="desktop-nav"
          >
            <NavLink to="/" style={navLinkStyle}>
              Home
            </NavLink>

            <NavLink to="/education-challenge" style={navLinkStyle}>
              The Education Challenge
            </NavLink>

            <NavLink to="/about" style={navLinkStyle}>
              Who We Are
            </NavLink>

            {/* Programmes Dropdown */}
            <div
              style={{ position: 'relative' }}
              onMouseEnter={() => setProgrammesOpen(true)}
              onMouseLeave={() => setProgrammesOpen(false)}
            >
              <button
                style={{
                  background: 'none',
                  border: 'none',
                  color: location.pathname.startsWith('/programmes')
                    ? 'var(--electric-cyan)'
                    : 'rgba(255, 255, 255, 0.78)',
                  fontSize: '0.88rem',
                  fontWeight: 500,
                  cursor: 'pointer',
                  display: 'flex',
                  alignItems: 'center',
                  gap: '4px',
                  padding: '6px 0',
                  borderBottom: location.pathname.startsWith('/programmes')
                    ? '2px solid var(--electric-cyan)'
                    : '2px solid transparent',
                  whiteSpace: 'nowrap',
                  transition: 'color 0.18s, border-color 0.18s',
                }}
                aria-expanded={programmesOpen}
              >
                Our Programmes
                <ChevronDown
                  size={14}
                  style={{
                    transform: programmesOpen ? 'rotate(180deg)' : 'none',
                    transition: 'transform 0.2s',
                  }}
                />
              </button>

              {/* Dropdown Menu */}
              {programmesOpen && (
                <div
                  style={{
                    position: 'absolute',
                    top: '100%',
                    left: 0,
                    width: '270px',
                    backgroundColor: 'rgba(13, 13, 20, 0.98)',
                    backdropFilter: 'blur(20px)',
                    WebkitBackdropFilter: 'blur(20px)',
                    border: '1px solid rgba(0, 229, 255, 0.25)',
                    borderRadius: '12px',
                    padding: '8px',
                    boxShadow: '0 12px 36px rgba(0,0,0,0.7), 0 0 25px rgba(0, 229, 255, 0.12)',
                    display: 'flex',
                    flexDirection: 'column',
                    gap: '4px',
                    zIndex: 100,
                  }}
                >
                  <Link
                    to="/programmes"
                    style={{
                      padding: '9px 12px',
                      borderRadius: '8px',
                      textDecoration: 'none',
                      color: '#FFF',
                      fontSize: '0.86rem',
                      fontWeight: 600,
                      transition: 'all 0.15s',
                    }}
                    onMouseEnter={(e) => {
                      e.currentTarget.style.backgroundColor = 'rgba(0, 229, 255, 0.12)';
                      e.currentTarget.style.color = 'var(--electric-cyan)';
                    }}
                    onMouseLeave={(e) => {
                      e.currentTarget.style.backgroundColor = 'transparent';
                      e.currentTarget.style.color = '#FFF';
                    }}
                  >
                    All Programmes Overview
                  </Link>

                  <div style={{ height: '1px', backgroundColor: 'var(--border-subtle)', margin: '2px 0' }} />

                  <Link
                    to="/programmes/free-online-learning"
                    style={{
                      padding: '8px 12px',
                      borderRadius: '8px',
                      textDecoration: 'none',
                      color: 'var(--text-secondary)',
                      fontSize: '0.84rem',
                      transition: 'all 0.15s',
                    }}
                    onMouseEnter={(e) => {
                      e.currentTarget.style.backgroundColor = 'rgba(0, 229, 255, 0.1)';
                      e.currentTarget.style.color = '#FFF';
                    }}
                    onMouseLeave={(e) => {
                      e.currentTarget.style.backgroundColor = 'transparent';
                      e.currentTarget.style.color = 'var(--text-secondary)';
                    }}
                  >
                    Free Online Learning
                  </Link>

                  <Link
                    to="/programmes/scholarships"
                    style={{
                      padding: '8px 12px',
                      borderRadius: '8px',
                      textDecoration: 'none',
                      color: 'var(--text-secondary)',
                      fontSize: '0.84rem',
                      transition: 'all 0.15s',
                    }}
                    onMouseEnter={(e) => {
                      e.currentTarget.style.backgroundColor = 'rgba(0, 229, 255, 0.1)';
                      e.currentTarget.style.color = '#FFF';
                    }}
                    onMouseLeave={(e) => {
                      e.currentTarget.style.backgroundColor = 'transparent';
                      e.currentTarget.style.color = 'var(--text-secondary)';
                    }}
                  >
                    Scholarships & Assistance
                  </Link>

                  <Link
                    to="/programmes/academic-support"
                    style={{
                      padding: '8px 12px',
                      borderRadius: '8px',
                      textDecoration: 'none',
                      color: 'var(--text-secondary)',
                      fontSize: '0.84rem',
                      transition: 'all 0.15s',
                    }}
                    onMouseEnter={(e) => {
                      e.currentTarget.style.backgroundColor = 'rgba(0, 229, 255, 0.1)';
                      e.currentTarget.style.color = '#FFF';
                    }}
                    onMouseLeave={(e) => {
                      e.currentTarget.style.backgroundColor = 'transparent';
                      e.currentTarget.style.color = 'var(--text-secondary)';
                    }}
                  >
                    Academic Support & Mentoring
                  </Link>

                  <div style={{ height: '1px', backgroundColor: 'var(--border-subtle)', margin: '2px 0' }} />

                  <Link
                    to="/tutoring"
                    style={{
                      padding: '8px 12px',
                      borderRadius: '8px',
                      textDecoration: 'none',
                      color: 'var(--electric-cyan)',
                      fontSize: '0.84rem',
                      fontWeight: 600,
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'space-between',
                    }}
                    onMouseEnter={(e) => {
                      e.currentTarget.style.backgroundColor = 'rgba(0, 229, 255, 0.15)';
                    }}
                    onMouseLeave={(e) => {
                      e.currentTarget.style.backgroundColor = 'transparent';
                    }}
                  >
                    Supplementary Tutoring
                    <Sparkles size={13} />
                  </Link>
                </div>
              )}
            </div>

            <NavLink to="/impact" style={navLinkStyle}>
              Our Impact
            </NavLink>

            <NavLink to="/governance" style={navLinkStyle}>
              Governance
            </NavLink>

            <NavLink to="/contact" style={navLinkStyle}>
              Contact
            </NavLink>
          </nav>

          {/* Action CTA */}
          <div
            style={{
              display: 'none',
              alignItems: 'center',
              gap: '12px',
              flexShrink: 0,
            }}
            className="desktop-actions"
          >
            <Link
              to="/donate"
              className="btn btn-cyan"
              style={{
                padding: '9px 22px',
                fontSize: '0.88rem',
                fontWeight: 700,
                borderRadius: 'var(--radius-pill)',
                display: 'inline-flex',
                alignItems: 'center',
                gap: '6px',
                whiteSpace: 'nowrap',
              }}
            >
              Support Education <ArrowRight size={15} />
            </Link>
          </div>

          {/* Mobile Hamburger Button */}
          <button
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            style={{
              background: 'none',
              border: 'none',
              color: 'var(--text-primary)',
              cursor: 'pointer',
              padding: '8px',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
            }}
            className="mobile-hamburger"
            aria-label="Toggle Navigation Menu"
          >
            {mobileMenuOpen ? <X size={26} color="var(--electric-cyan)" /> : <Menu size={26} />}
          </button>
        </div>
      </header>

      {/* Mobile Drawer (Rendered outside header to avoid backdrop-filter containing block constraint) */}
      {mobileMenuOpen && (
        <div
          style={{
            position: 'fixed',
            top: 'var(--nav-height)',
            left: 0,
            right: 0,
            bottom: 0,
            width: '100%',
            height: 'calc(100vh - var(--nav-height))',
            backgroundColor: '#050505',
            zIndex: 9999,
            padding: '28px 24px',
            display: 'flex',
            flexDirection: 'column',
            gap: '20px',
            overflowY: 'auto',
            WebkitOverflowScrolling: 'touch',
            borderTop: '1px solid rgba(0, 229, 255, 0.2)',
          }}
        >
          <Link
            to="/"
            onClick={() => setMobileMenuOpen(false)}
            style={{
              color: '#FFF',
              textDecoration: 'none',
              fontSize: '1.2rem',
              fontWeight: 600,
              padding: '8px 0',
              borderBottom: '1px solid var(--border-subtle)',
            }}
          >
            Home
          </Link>

          <Link
            to="/education-challenge"
            onClick={() => setMobileMenuOpen(false)}
            style={{
              color: '#FFF',
              textDecoration: 'none',
              fontSize: '1.2rem',
              fontWeight: 600,
              padding: '8px 0',
              borderBottom: '1px solid var(--border-subtle)',
            }}
          >
            The Education Challenge
          </Link>

          <Link
            to="/about"
            onClick={() => setMobileMenuOpen(false)}
            style={{
              color: '#FFF',
              textDecoration: 'none',
              fontSize: '1.2rem',
              fontWeight: 600,
              padding: '8px 0',
              borderBottom: '1px solid var(--border-subtle)',
            }}
          >
            Who We Are
          </Link>

          <div style={{ padding: '8px 0', borderBottom: '1px solid var(--border-subtle)' }}>
            <span
              style={{
                color: 'var(--electric-cyan)',
                fontSize: '0.8rem',
                textTransform: 'uppercase',
                letterSpacing: '0.12em',
                fontWeight: 700,
                display: 'block',
                marginBottom: '12px',
              }}
            >
              Our Programmes
            </span>
            <div style={{ display: 'flex', flexDirection: 'column', gap: '10px', paddingLeft: '8px' }}>
              <Link
                to="/programmes"
                onClick={() => setMobileMenuOpen(false)}
                style={{ color: '#FFF', textDecoration: 'none', fontSize: '1rem', fontWeight: 500 }}
              >
                All Programmes Overview
              </Link>
              <Link
                to="/programmes/free-online-learning"
                onClick={() => setMobileMenuOpen(false)}
                style={{ color: 'var(--text-secondary)', textDecoration: 'none', fontSize: '0.94rem' }}
              >
                Free Online Learning
              </Link>
              <Link
                to="/programmes/scholarships"
                onClick={() => setMobileMenuOpen(false)}
                style={{ color: 'var(--text-secondary)', textDecoration: 'none', fontSize: '0.94rem' }}
              >
                Scholarships & Assistance
              </Link>
              <Link
                to="/programmes/academic-support"
                onClick={() => setMobileMenuOpen(false)}
                style={{ color: 'var(--text-secondary)', textDecoration: 'none', fontSize: '0.94rem' }}
              >
                Academic Support & Mentoring
              </Link>
              <Link
                to="/tutoring"
                onClick={() => setMobileMenuOpen(false)}
                style={{
                  color: 'var(--electric-cyan)',
                  textDecoration: 'none',
                  fontSize: '0.96rem',
                  fontWeight: 600,
                  display: 'flex',
                  alignItems: 'center',
                  gap: '6px',
                  marginTop: '4px',
                }}
              >
                Supplementary Tutoring (Paid)
                <Sparkles size={14} />
              </Link>
            </div>
          </div>

          <Link
            to="/impact"
            onClick={() => setMobileMenuOpen(false)}
            style={{
              color: '#FFF',
              textDecoration: 'none',
              fontSize: '1.2rem',
              fontWeight: 600,
              padding: '8px 0',
              borderBottom: '1px solid var(--border-subtle)',
            }}
          >
            Our Impact
          </Link>

          <Link
            to="/governance"
            onClick={() => setMobileMenuOpen(false)}
            style={{
              color: '#FFF',
              textDecoration: 'none',
              fontSize: '1.2rem',
              fontWeight: 600,
              padding: '8px 0',
              borderBottom: '1px solid var(--border-subtle)',
            }}
          >
            Governance & Transparency
          </Link>

          <Link
            to="/contact"
            onClick={() => setMobileMenuOpen(false)}
            style={{
              color: '#FFF',
              textDecoration: 'none',
              fontSize: '1.2rem',
              fontWeight: 600,
              padding: '8px 0',
              borderBottom: '1px solid var(--border-subtle)',
            }}
          >
            Contact
          </Link>

          <div
            style={{
              marginTop: 'auto',
              display: 'flex',
              flexDirection: 'column',
              gap: '12px',
              paddingTop: '24px',
              paddingBottom: '20px',
            }}
          >
            <Link
              to="/donate"
              onClick={() => setMobileMenuOpen(false)}
              className="btn btn-cyan"
              style={{ width: '100%', justifyContent: 'center', padding: '12px', fontSize: '0.98rem' }}
            >
              Support Education <ArrowRight size={16} />
            </Link>
            <Link
              to="/tutoring"
              onClick={() => setMobileMenuOpen(false)}
              className="btn btn-outline"
              style={{ width: '100%', justifyContent: 'center', padding: '12px', fontSize: '0.98rem' }}
            >
              Find Tutors
            </Link>
          </div>
        </div>
      )}

      {/* Responsive media query helper */}
      <style>{`
        @media (min-width: 1040px) {
          .desktop-nav { display: flex !important; }
          .desktop-actions { display: flex !important; }
          .mobile-hamburger { display: none !important; }
        }
        @media (max-width: 1039px) {
          .desktop-nav { display: none !important; }
          .desktop-actions { display: none !important; }
          .mobile-hamburger { display: flex !important; }
        }
      `}</style>
    </>
  );
}
