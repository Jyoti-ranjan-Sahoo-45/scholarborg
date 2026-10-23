import React, { useEffect, useRef, useState } from 'react';
import gsap from 'gsap';
import { Users, BookOpen, GraduationCap, MapPin } from 'lucide-react';

export default function CountUpStats() {
  const containerRef = useRef(null);
  const [hasAnimated, setHasAnimated] = useState(false);

  const stats = [
    { id: 'stat-learners', icon: Users, target: 1200, suffix: '+', label: 'Learners Supported' },
    { id: 'stat-sessions', icon: BookOpen, target: 5000, suffix: '+', label: 'Learning Sessions' },
    { id: 'stat-scholarships', icon: GraduationCap, target: 250, suffix: '+', label: 'Scholarships Provided' },
    { id: 'stat-locations', icon: MapPin, target: 15, suffix: '+', label: 'Locations Reached' },
  ];

  const [values, setValues] = useState({
    'stat-learners': 0,
    'stat-sessions': 0,
    'stat-scholarships': 0,
    'stat-locations': 0,
  });

  useEffect(() => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
      setValues({
        'stat-learners': 1200,
        'stat-sessions': 5000,
        'stat-scholarships': 250,
        'stat-locations': 15,
      });
      return;
    }

    const observer = new IntersectionObserver(
      (entries) => {
        const [entry] = entries;
        if (entry.isIntersecting && !hasAnimated) {
          setHasAnimated(true);

          const animatedValues = {
            'stat-learners': 0,
            'stat-sessions': 0,
            'stat-scholarships': 0,
            'stat-locations': 0,
          };

          gsap.to(animatedValues, {
            'stat-learners': 1200,
            'stat-sessions': 5000,
            'stat-scholarships': 250,
            'stat-locations': 15,
            duration: 2.0,
            ease: 'power3.out',
            onUpdate: () => {
              setValues({
                'stat-learners': Math.round(animatedValues['stat-learners']),
                'stat-sessions': Math.round(animatedValues['stat-sessions']),
                'stat-scholarships': Math.round(animatedValues['stat-scholarships']),
                'stat-locations': Math.round(animatedValues['stat-locations']),
              });
            },
          });
        }
      },
      { threshold: 0.3 }
    );

    if (containerRef.current) {
      observer.observe(containerRef.current);
    }

    return () => observer.disconnect();
  }, [hasAnimated]);

  return (
    <div ref={containerRef} style={{ width: '100%' }}>
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(2, 1fr)', gap: '16px', marginBottom: '16px' }}>
        {stats.map((s) => {
          const Icon = s.icon;
          return (
            <div
              key={s.id}
              className="card-surface-3"
              style={{
                textAlign: 'center',
                padding: '28px 16px',
                borderRadius: 'var(--radius-card)',
                border: '1px solid var(--border-subtle)',
                transition: 'border-color 0.25s, transform 0.25s',
              }}
              onMouseEnter={(e) => {
                e.currentTarget.style.borderColor = 'var(--border-cyan)';
                e.currentTarget.style.transform = 'translateY(-3px)';
              }}
              onMouseLeave={(e) => {
                e.currentTarget.style.borderColor = 'var(--border-subtle)';
                e.currentTarget.style.transform = 'translateY(0)';
              }}
            >
              <div style={{ color: 'var(--electric-cyan)', marginBottom: '8px' }}>
                <Icon size={24} style={{ margin: '0 auto' }} />
              </div>
              <div
                style={{
                  fontSize: '2.2rem',
                  fontWeight: 800,
                  color: '#FFF',
                  fontFamily: 'var(--font-heading)',
                  letterSpacing: '-0.02em',
                }}
              >
                {values[s.id].toLocaleString()}{s.suffix}
              </div>
              <span style={{ fontSize: '0.82rem', color: 'var(--text-muted)', display: 'block', marginTop: '4px' }}>
                {s.label}
              </span>
            </div>
          );
        })}
      </div>
      <span style={{ fontSize: '0.78rem', color: 'var(--text-muted)', display: 'block', textAlign: 'center' }}>
        * These figures reflect verified foundation data and are updated periodically.
      </span>
    </div>
  );
}
