import React from 'react';
import { Link } from 'react-router-dom';
import { Award, ShieldAlert, CheckCircle2, Calendar, FileText, ArrowRight } from 'lucide-react';

export default function Scholarships() {
  return (
    <div style={{ backgroundColor: 'var(--bg-jet-black)', color: 'var(--text-secondary)', paddingTop: 'calc(var(--nav-height) + 40px)', paddingBottom: '100px' }}>
      <div className="container">
        {/* Header */}
        <div style={{ maxWidth: '820px', marginBottom: '50px' }}>
          <div className="eyebrow">MERIT & NEED AID</div>
          <h1 className="h1-display" style={{ marginBottom: '20px' }}>
            Scholarships & Educational Assistance
          </h1>
          <p style={{ fontSize: '1.15rem', color: 'var(--text-secondary)', lineHeight: 1.7 }}>
            Providing direct financial continuity grants to meritorious Indian students facing economic hardship, preventing dropouts before critical board exam milestones.
          </p>
        </div>

        {/* Fraud Prevention Alert */}
        <div
          style={{
            backgroundColor: 'rgba(255, 77, 79, 0.08)',
            border: '1px solid rgba(255, 77, 79, 0.3)',
            borderRadius: 'var(--radius-card)',
            padding: '20px 24px',
            display: 'flex',
            gap: '16px',
            alignItems: 'flex-start',
            marginBottom: '60px',
          }}
        >
          <ShieldAlert size={24} color="#FF4D4F" style={{ flexShrink: 0, marginTop: '2px' }} />
          <div>
            <h4 style={{ color: '#FFF', fontSize: '1rem', marginBottom: '4px' }}>
              Zero Application Fee Warning
            </h4>
            <p style={{ fontSize: '0.88rem', color: 'var(--text-secondary)', lineHeight: 1.6 }}>
              Scholarbee Foundation <strong>NEVER</strong> charges any processing fee, deposit, or registration charge for scholarship applications. Do not pay any individual or agent claiming to guarantee a Scholarbee scholarship.
            </p>
          </div>
        </div>

        {/* Criteria & Eligibility Grid */}
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '32px', marginBottom: '70px' }}>
          <div className="card-base">
            <h3 style={{ fontSize: '1.3rem', color: '#FFF', marginBottom: '16px' }}>Eligibility Criteria</h3>
            <div style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>
              {[
                'Enrolled in Grade 9, 10, 11, or 12 in a recognized Indian school board (CBSE, ICSE, or State Board).',
                'Demonstrated academic merit (minimum 70% aggregate in the previous annual examination).',
                'Annual household income below ₹3.5 Lakhs (verified via official income certificate / ration card).',
                'Indian resident citizen with an active bank account in the student or legal guardian’s name.',
              ].map((c, i) => (
                <div key={i} style={{ display: 'flex', gap: '10px', alignItems: 'flex-start', fontSize: '0.9rem' }}>
                  <CheckCircle2 size={16} color="var(--electric-cyan)" style={{ flexShrink: 0, marginTop: '3px' }} />
                  <span>{c}</span>
                </div>
              ))}
            </div>
          </div>

          <div className="card-base">
            <h3 style={{ fontSize: '1.3rem', color: '#FFF', marginBottom: '16px' }}>What the Grant Covers</h3>
            <div style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>
              {[
                'Direct school tuition fee remittance to registered academic institutions.',
                'Prescribed textbooks, reference guides, and scientific stationery allowances.',
                'Subsidized digital device assistance for verified online learning requirements.',
                'Direct mentoring access with senior university scholars.',
              ].map((c, i) => (
                <div key={i} style={{ display: 'flex', gap: '10px', alignItems: 'flex-start', fontSize: '0.9rem' }}>
                  <CheckCircle2 size={16} color="var(--electric-cyan)" style={{ flexShrink: 0, marginTop: '3px' }} />
                  <span>{c}</span>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* How to Apply Section */}
        <div className="card-surface-3" style={{ maxWidth: '880px', margin: '0 auto 60px auto', padding: '36px' }}>
          <h3 style={{ fontSize: '1.4rem', color: '#FFF', marginBottom: '20px', textAlign: 'center' }}>
            Application Cycles & Procedure
          </h3>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))', gap: '20px', textAlign: 'center' }}>
            <div>
              <div style={{ fontSize: '0.8rem', color: 'var(--electric-cyan)', fontWeight: 700, textTransform: 'uppercase', marginBottom: '4px' }}>Cycle 1</div>
              <strong style={{ color: '#FFF', fontSize: '1.1rem', display: 'block', marginBottom: '6px' }}>July - August</strong>
              <span style={{ fontSize: '0.85rem', color: 'var(--text-muted)' }}>Autumn academic term allocations</span>
            </div>
            <div>
              <div style={{ fontSize: '0.8rem', color: 'var(--electric-cyan)', fontWeight: 700, textTransform: 'uppercase', marginBottom: '4px' }}>Cycle 2</div>
              <strong style={{ color: '#FFF', fontSize: '1.1rem', display: 'block', marginBottom: '6px' }}>December - January</strong>
              <span style={{ fontSize: '0.85rem', color: 'var(--text-muted)' }}>Winter examination preparation grants</span>
            </div>
            <div>
              <div style={{ fontSize: '0.8rem', color: 'var(--electric-cyan)', fontWeight: 700, textTransform: 'uppercase', marginBottom: '4px' }}>Verification</div>
              <strong style={{ color: '#FFF', fontSize: '1.1rem', display: 'block', marginBottom: '6px' }}>Institutional Audit</strong>
              <span style={{ fontSize: '0.85rem', color: 'var(--text-muted)' }}>Direct confirmation with school principals</span>
            </div>
          </div>
        </div>

        <div style={{ textAlign: 'center' }}>
          <Link to="/contact" className="btn btn-cyan">
            Contact Secretariat for Application Queries <ArrowRight size={16} />
          </Link>
        </div>
      </div>
    </div>
  );
}
