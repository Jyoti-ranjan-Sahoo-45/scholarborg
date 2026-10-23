import React, { useEffect, useRef, useState } from 'react';
import gsap from 'gsap';

export default function ProcessDraw() {
  const containerRef = useRef(null);
  const pathRef = useRef(null);
  const [activeStep, setActiveStep] = useState(0);

  const steps = [
    { num: '01', title: 'Explore Our Programmes', desc: 'Understand available opportunities.' },
    { num: '02', title: 'Check Eligibility', desc: 'Review basic eligibility criteria.' },
    { num: '03', title: 'Submit Application', desc: 'Complete the enquiry form with accurate details.' },
    { num: '04', title: 'Foundation Review', desc: 'Our academic panel assesses the request.' },
    { num: '05', title: 'Receive Outcome', desc: 'Get onboarded and start learning.' },
  ];

  useEffect(() => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
      setActiveStep(5);
      return;
    }

    const observer = new IntersectionObserver(
      (entries) => {
        const [entry] = entries;
        if (entry.isIntersecting) {
          if (pathRef.current) {
            const length = pathRef.current.getTotalLength();
            gsap.set(pathRef.current, { strokeDasharray: length, strokeDashoffset: length });

            gsap.to(pathRef.current, {
              strokeDashoffset: 0,
              duration: 2.0,
              ease: 'power2.inOut',
              onUpdate: function () {
                const progress = this.progress();
                setActiveStep(Math.min(5, Math.floor(progress * 5) + 1));
              },
            });
          }
        }
      },
      { threshold: 0.35 }
    );

    if (containerRef.current) {
      observer.observe(containerRef.current);
    }

    return () => observer.disconnect();
  }, []);

  return (
    <div ref={containerRef} style={{ position: 'relative', width: '100%', marginTop: '20px' }}>
      {/* Background SVG connecting line (Desktop only) */}
      <div
        style={{
          position: 'absolute',
          top: '46px',
          left: '10%',
          right: '10%',
          height: '4px',
          zIndex: 0,
          display: 'none',
        }}
        className="process-line-desktop"
      >
        <svg width="100%" height="20" style={{ overflow: 'visible' }}>
          {/* Subtle track line */}
          <line x1="0" y1="10" x2="100%" y2="10" stroke="rgba(255,255,255,0.08)" strokeWidth="3" />
          {/* Animated drawing line */}
          <line
            ref={pathRef}
            x1="0"
            y1="10"
            x2="100%"
            y2="10"
            stroke="var(--electric-cyan)"
            strokeWidth="3"
            strokeLinecap="round"
          />
        </svg>
      </div>

      {/* Steps Grid */}
      <div
        style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))',
          gap: '16px',
          position: 'relative',
          zIndex: 1,
        }}
      >
        {steps.map((s, i) => {
          const isReached = i < activeStep;
          return (
            <div
              key={i}
              className="card-surface-3"
              style={{
                padding: '24px 18px',
                textAlign: 'center',
                position: 'relative',
                borderRadius: 'var(--radius-card)',
                border: isReached ? '1px solid var(--border-cyan)' : '1px solid var(--border-subtle)',
                transition: 'all 0.3s ease',
                background: isReached ? 'rgba(13, 13, 20, 0.95)' : 'var(--surface-3)',
                boxShadow: isReached ? '0 0 25px rgba(0, 229, 255, 0.08)' : 'none',
              }}
            >
              <div
                style={{
                  width: '46px',
                  height: '46px',
                  borderRadius: '50%',
                  backgroundColor: isReached ? 'var(--electric-cyan)' : 'var(--surface-2)',
                  color: isReached ? '#050505' : 'var(--text-muted)',
                  border: isReached ? 'none' : '1px solid var(--border-subtle)',
                  fontFamily: 'var(--font-heading)',
                  fontWeight: 800,
                  fontSize: '1.05rem',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  margin: '0 auto 16px auto',
                  transition: 'all 0.35s ease',
                  boxShadow: isReached ? '0 0 20px rgba(0, 229, 255, 0.5)' : 'none',
                  transform: isReached ? 'scale(1.06)' : 'scale(1)',
                }}
              >
                {s.num}
              </div>
              <h4 style={{ fontSize: '0.98rem', color: isReached ? '#FFF' : 'var(--text-secondary)', marginBottom: '8px', fontWeight: 700 }}>
                {s.title}
              </h4>
              <p style={{ fontSize: '0.82rem', color: 'var(--text-muted)', lineHeight: 1.5 }}>
                {s.desc}
              </p>
            </div>
          );
        })}
      </div>

      <style>{`
        @media (min-width: 900px) {
          .process-line-desktop { display: block !important; }
        }
      `}</style>
    </div>
  );
}
