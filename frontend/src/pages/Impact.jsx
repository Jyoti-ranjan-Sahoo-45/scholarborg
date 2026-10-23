import React from 'react';
import { Link } from 'react-router-dom';
import { CheckCircle2, ShieldCheck, FileText, ArrowRight } from 'lucide-react';

export default function Impact() {
  return (
    <div style={{ backgroundColor: 'var(--bg-jet-black)', color: 'var(--text-secondary)', paddingTop: 'calc(var(--nav-height) + 40px)', paddingBottom: '100px' }}>
      <div className="container">
        {/* Header */}
        <div style={{ maxWidth: '820px', marginBottom: '60px' }}>
          <div className="eyebrow">VERIFIED WORK & ACCOUNTABILITY</div>
          <h1 className="h1-display" style={{ marginBottom: '20px' }}>
            Measuring Real Educational Progress
          </h1>
          <p style={{ fontSize: '1.15rem', color: 'var(--text-secondary)', lineHeight: 1.7 }}>
            In strict adherence to our institutional governance standards, we present qualitative audits, verifiable curriculum deployments, and direct student feedback rather than fabricated marketing statistics.
          </p>
        </div>

        {/* Audited Outcomes Grid */}
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '30px', marginBottom: '80px' }}>
          <div className="card-base">
            <h3 style={{ fontSize: '1.3rem', color: '#FFF', marginBottom: '14px' }}>Digital Syllabus Accessibility</h3>
            <p style={{ fontSize: '0.92rem', color: 'var(--text-muted)', lineHeight: 1.6, marginBottom: '20px' }}>
              Full digital syllabus modules for core secondary mathematics, physics, and chemistry have been mapped and deployed, allowing open access without subscriptions or mobile app paywalls.
            </p>
            <div style={{ display: 'flex', alignItems: 'center', gap: '8px', color: 'var(--electric-cyan)', fontSize: '0.85rem' }}>
              <CheckCircle2 size={16} /> <span>Open Curriculum Standard Achieved</span>
            </div>
          </div>

          <div className="card-base">
            <h3 style={{ fontSize: '1.3rem', color: '#FFF', marginBottom: '14px' }}>Tutor Verification Standard</h3>
            <p style={{ fontSize: '0.92rem', color: 'var(--text-muted)', lineHeight: 1.6, marginBottom: '20px' }}>
              Every educator listed on our marketplace directory undergoes national identity checks, degree validation, and child-safe pedagogical evaluations prior to conducting live trial classes.
            </p>
            <div style={{ display: 'flex', alignItems: 'center', gap: '8px', color: 'var(--electric-cyan)', fontSize: '0.85rem' }}>
              <CheckCircle2 size={16} /> <span>100% Background Audit Protocol</span>
            </div>
          </div>

          <div className="card-base">
            <h3 style={{ fontSize: '1.3rem', color: '#FFF', marginBottom: '14px' }}>Parental Transparency Logs</h3>
            <p style={{ fontSize: '0.92rem', color: 'var(--text-muted)', lineHeight: 1.6, marginBottom: '20px' }}>
              Over 95% of live trial bookings result in detailed written educator feedback shared directly with parents, tracking attendance, student engagement, and topic bottlenecks.
            </p>
            <div style={{ display: 'flex', alignItems: 'center', gap: '8px', color: 'var(--electric-cyan)', fontSize: '0.85rem' }}>
              <CheckCircle2 size={16} /> <span>Verifiable Communication Audits</span>
            </div>
          </div>
        </div>

        {/* Reports & Governance Link */}
        <div className="card-surface-3" style={{ maxWidth: '800px', margin: '0 auto', textAlign: 'center', padding: '40px' }}>
          <h3 style={{ fontSize: '1.5rem', color: '#FFF', marginBottom: '12px' }}>
            Institutional Compliance & Filings
          </h3>
          <p style={{ fontSize: '0.95rem', color: 'var(--text-muted)', marginBottom: '24px' }}>
            Review our organizational objectives, non-profit charter disclosures, and public accounting filings.
          </p>
          <Link to="/governance" className="btn btn-outline">
            Review Governance Disclosures <ArrowRight size={16} />
          </Link>
        </div>
      </div>
    </div>
  );
}
