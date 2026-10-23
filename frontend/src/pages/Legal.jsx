import React from 'react';
import { useLocation } from 'react-router-dom';
import { ShieldCheck } from 'lucide-react';

export default function Legal() {
  const location = useLocation();
  const path = location.pathname;

  let title = 'Privacy Policy';
  if (path === '/terms') title = 'Terms of Service';
  if (path === '/cookies') title = 'Cookie Policy';

  return (
    <div style={{ backgroundColor: 'var(--bg-jet-black)', color: 'var(--text-secondary)', paddingTop: 'calc(var(--nav-height) + 40px)', paddingBottom: '100px' }}>
      <div className="container" style={{ maxWidth: '840px' }}>
        <div style={{ marginBottom: '40px' }}>
          <div className="eyebrow">LEGAL DISCLOSURE</div>
          <h1 className="h1-display" style={{ marginBottom: '14px' }}>{title}</h1>
          <span style={{ fontSize: '0.85rem', color: 'var(--text-muted)' }}>
            Last revised: October 2026 • Scholarbee Foundation Governance Protocol
          </span>
        </div>

        <div className="card-base" style={{ display: 'flex', flexDirection: 'column', gap: '28px', lineHeight: 1.7, fontSize: '0.95rem' }}>
          <div>
            <h3 style={{ color: '#FFF', fontSize: '1.25rem', marginBottom: '8px' }}>1. Scope & Educational Charter</h3>
            <p style={{ color: 'var(--text-secondary)' }}>
              Scholarbee Foundation is an educational initiative dedicated to providing non-profit digital learning, merit scholarships, and independent marketplace facilitation for supplementary tutoring. This policy governs all interactions across our digital services.
            </p>
          </div>

          <div>
            <h3 style={{ color: '#FFF', fontSize: '1.25rem', marginBottom: '8px' }}>2. Data Protection & Child Safety</h3>
            <p style={{ color: 'var(--text-secondary)' }}>
              We adhere to strict data privacy principles under applicable Indian data protection frameworks. We do not sell, rent, or trade student educational records or personal identity data to third-party advertisers. All guardian and student records collected during demo booking or scholarship applications are stored on secure servers with encrypted access controls.
            </p>
          </div>

          <div>
            <h3 style={{ color: '#FFF', fontSize: '1.25rem', marginBottom: '8px' }}>3. Independent Tutor Marketplace Notice</h3>
            <p style={{ color: 'var(--text-secondary)' }}>
              Independent educators conducting supplementary tutoring classes are verified for identity and qualifications but deliver classes as independent professionals. Scholarbee Foundation enforces strict pedagogical standards and maintains automated session recording logs for student safety.
            </p>
          </div>

          <div>
            <h3 style={{ color: '#FFF', fontSize: '1.25rem', marginBottom: '8px' }}>4. Grievance Officer & Inquiries</h3>
            <p style={{ color: 'var(--text-secondary)' }}>
              For legal notifications, privacy concerns, or data deletion requests, contact our designated grievance desk at <a href="mailto:compliance@scholarb.org" style={{ color: 'var(--electric-cyan)' }}>compliance@scholarb.org</a>.
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}
