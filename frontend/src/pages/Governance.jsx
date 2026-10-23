import React from 'react';
import { Link } from 'react-router-dom';
import { ShieldCheck, FileCheck, CheckCircle2, Mail, ExternalLink } from 'lucide-react';

export default function Governance() {
  return (
    <div style={{ backgroundColor: 'var(--bg-jet-black)', color: 'var(--text-secondary)', paddingTop: 'calc(var(--nav-height) + 40px)', paddingBottom: '100px' }}>
      <div className="container">
        {/* Header */}
        <div style={{ maxWidth: '820px', marginBottom: '60px' }}>
          <div className="eyebrow">TRANSPARENCY & COMPLIANCE</div>
          <h1 className="h1-display" style={{ marginBottom: '20px' }}>
            Governance & Institutional Accountability
          </h1>
          <p style={{ fontSize: '1.15rem', color: 'var(--text-secondary)', lineHeight: 1.7 }}>
            Scholarbee Foundation operates under strict institutional guidelines prioritizing donor integrity, public benefit, non-profit stewardship, and complete student data protection.
          </p>
        </div>

        {/* Governance Disclosures Grid */}
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '28px', marginBottom: '80px' }}>
          <div className="card-base">
            <div style={{ color: 'var(--electric-cyan)', marginBottom: '16px' }}><ShieldCheck size={28} /></div>
            <h3 style={{ fontSize: '1.3rem', color: '#FFF', marginBottom: '12px' }}>Charter & Objectives</h3>
            <p style={{ fontSize: '0.92rem', color: 'var(--text-muted)', lineHeight: 1.6 }}>
              Established exclusively for charitable educational advancement, promoting open learning materials, and deploying scholarships without private pecuniary gain for founders or governing trustees.
            </p>
          </div>

          <div className="card-base">
            <div style={{ color: 'var(--electric-cyan)', marginBottom: '16px' }}><FileCheck size={28} /></div>
            <h3 style={{ fontSize: '1.3rem', color: '#FFF', marginBottom: '12px' }}>Independent Audits</h3>
            <p style={{ fontSize: '0.92rem', color: 'var(--text-muted)', lineHeight: 1.6 }}>
              All incoming donations, corporate CSR grants, and scholarship disbursements are audited annually by independent chartered accountants in compliance with statutory standards.
            </p>
          </div>

          <div className="card-base">
            <div style={{ color: 'var(--electric-cyan)', marginBottom: '16px' }}><CheckCircle2 size={28} /></div>
            <h3 style={{ fontSize: '1.3rem', color: '#FFF', marginBottom: '12px' }}>Child Protection & Safety</h3>
            <p style={{ fontSize: '0.92rem', color: 'var(--text-muted)', lineHeight: 1.6 }}>
              Rigorous identity validation, automated session monitoring, parent audit portals, and strict zero-tolerance policies regarding unsolicited or unmonitored educator contact.
            </p>
          </div>
        </div>

        {/* Legal Inquiries Contact */}
        <div className="card-surface-3" style={{ maxWidth: '800px', margin: '0 auto', textAlign: 'center', padding: '40px' }}>
          <h3 style={{ fontSize: '1.4rem', color: '#FFF', marginBottom: '12px' }}>
            Statutory & Legal Secretariat Inquiries
          </h3>
          <p style={{ fontSize: '0.92rem', color: 'var(--text-muted)', marginBottom: '24px' }}>
            For compliance filings, audited financial statements, or institutional queries, contact our legal desk directly.
          </p>
          <a href="mailto:compliance@scholarb.org" className="btn btn-cyan">
            <Mail size={16} /> compliance@scholarb.org
          </a>
        </div>
      </div>
    </div>
  );
}
