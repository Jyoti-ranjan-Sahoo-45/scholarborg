import React, { useState } from 'react';
import { HeartHandshake, ShieldCheck, CheckCircle2, Award, ArrowRight } from 'lucide-react';

export default function Donate() {
  const [selectedAmount, setSelectedAmount] = useState('2500');
  const [donorName, setDonorName] = useState('');
  const [donorEmail, setDonorEmail] = useState('');
  const [submitted, setSubmitted] = useState(false);

  const tiers = [
    { amount: '1000', label: '₹1,000', desc: 'Provides textbooks & stationery kit for one high school student.' },
    { amount: '2500', label: '₹2,500', desc: 'Covers annual examination and registration fees for a deserving learner.' },
    { amount: '5000', label: '₹5,000', desc: 'Sponsors 6 months of academic mentorship clinics & study modules.' },
    { amount: '15000', label: '₹15,000', desc: 'Funds a full 1-year secondary education scholarship grant.' },
  ];

  const handleSubmit = (e) => {
    e.preventDefault();
    setSubmitted(true);
  };

  return (
    <div style={{ backgroundColor: 'var(--bg-jet-black)', color: 'var(--text-secondary)', paddingTop: 'calc(var(--nav-height) + 40px)', paddingBottom: '100px' }}>
      <div className="container" style={{ maxWidth: '960px' }}>
        {/* Header */}
        <div style={{ textAlign: 'center', marginBottom: '50px' }}>
          <div className="eyebrow" style={{ margin: '0 auto 16px auto' }}>SUPPORT OUR MISSION</div>
          <h1 className="h1-display" style={{ marginBottom: '18px' }}>
            Empower a Child’s Academic Future
          </h1>
          <p style={{ fontSize: '1.15rem', color: 'var(--text-secondary)', lineHeight: 1.6, maxWidth: '680px', margin: '0 auto' }}>
            100% of public donations directly fund student education grants, examination supplies, and open-access curriculum creation.
          </p>
        </div>

        {submitted ? (
          <div className="card-base" style={{ textAlign: 'center', padding: '60px 30px', borderColor: 'var(--border-cyan)' }}>
            <div style={{ width: '64px', height: '64px', borderRadius: '50%', backgroundColor: 'rgba(0, 229, 255, 0.15)', color: 'var(--electric-cyan)', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 20px auto' }}>
              <CheckCircle2 size={36} />
            </div>
            <h3 style={{ fontSize: '1.8rem', color: '#FFF', marginBottom: '12px' }}>Thank You for Your Generosity!</h3>
            <p style={{ fontSize: '1.05rem', color: 'var(--text-secondary)', lineHeight: 1.6, maxWidth: '600px', margin: '0 auto 28px auto' }}>
              Your pledge of <strong>₹{parseInt(selectedAmount).toLocaleString('en-IN')}</strong> will directly change the educational trajectory of an Indian student. Our team has emailed you receipt and remittance details at <strong>{donorEmail}</strong>.
            </p>
            <button onClick={() => setSubmitted(false)} className="btn btn-outline">
              Make Another Contribution
            </button>
          </div>
        ) : (
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '36px' }}>
            {/* Donation Form */}
            <form onSubmit={handleSubmit} className="card-base" style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
              <h3 style={{ fontSize: '1.3rem', color: '#FFF' }}>Select Contribution Amount</h3>
              
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '12px' }}>
                {tiers.map((t) => (
                  <button
                    key={t.amount}
                    type="button"
                    onClick={() => setSelectedAmount(t.amount)}
                    style={{
                      padding: '16px 12px',
                      borderRadius: 'var(--radius-md)',
                      backgroundColor: selectedAmount === t.amount ? 'rgba(0, 229, 255, 0.15)' : 'var(--surface-3)',
                      border: selectedAmount === t.amount ? '2px solid var(--electric-cyan)' : '1px solid var(--border-subtle)',
                      color: selectedAmount === t.amount ? 'var(--electric-cyan)' : '#FFF',
                      fontFamily: 'var(--font-heading)',
                      fontWeight: 700,
                      fontSize: '1.2rem',
                      cursor: 'pointer',
                      transition: 'all 0.2s',
                    }}
                  >
                    {t.label}
                  </button>
                ))}
              </div>

              <div style={{ backgroundColor: 'var(--surface-3)', padding: '14px 18px', borderRadius: 'var(--radius-md)', fontSize: '0.88rem', color: 'var(--text-secondary)' }}>
                {tiers.find(t => t.amount === selectedAmount)?.desc}
              </div>

              <div>
                <label style={{ fontSize: '0.82rem', color: 'var(--text-muted)', fontWeight: 600, display: 'block', marginBottom: '6px' }}>
                  Full Name / Organization *
                </label>
                <input
                  type="text"
                  required
                  placeholder="e.g. Priya Sundaram"
                  value={donorName}
                  onChange={(e) => setDonorName(e.target.value)}
                  style={{ width: '100%', minHeight: '44px', backgroundColor: 'var(--surface-3)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', color: '#FFF', padding: '0 14px', fontSize: '0.9rem', outline: 'none' }}
                />
              </div>

              <div>
                <label style={{ fontSize: '0.82rem', color: 'var(--text-muted)', fontWeight: 600, display: 'block', marginBottom: '6px' }}>
                  Email Address (For Tax Receipt & Updates) *
                </label>
                <input
                  type="email"
                  required
                  placeholder="priya@example.com"
                  value={donorEmail}
                  onChange={(e) => setDonorEmail(e.target.value)}
                  style={{ width: '100%', minHeight: '44px', backgroundColor: 'var(--surface-3)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', color: '#FFF', padding: '0 14px', fontSize: '0.9rem', outline: 'none' }}
                />
              </div>

              <button type="submit" className="btn btn-cyan" style={{ width: '100%', minHeight: '50px', fontSize: '1rem', fontWeight: 700 }}>
                Proceed to Secure Support <ArrowRight size={18} />
              </button>
            </form>

            {/* Direct Bank Remittance & Transparency Info */}
            <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
              <div className="card-surface-3">
                <h4 style={{ fontSize: '1.1rem', color: '#FFF', marginBottom: '12px', display: 'flex', alignItems: 'center', gap: '8px' }}>
                  <ShieldCheck size={20} color="var(--electric-cyan)" />
                  Tax Deductibility & Transparency
                </h4>
                <p style={{ fontSize: '0.88rem', color: 'var(--text-muted)', lineHeight: 1.6, marginBottom: '16px' }}>
                  All donations are channeled strictly to educational grant allocations. Detailed annual utilisation reports are audited and published openly.
                </p>
                <span style={{ fontSize: '0.82rem', color: 'var(--electric-cyan)', fontWeight: 600 }}>
                  ✓ Official 80G Tax Exemption Eligible
                </span>
              </div>

              <div className="card-surface-3">
                <h4 style={{ fontSize: '1.1rem', color: '#FFF', marginBottom: '12px' }}>
                  Direct NEFT / RTGS Bank Transfer
                </h4>
                <div style={{ fontSize: '0.85rem', color: 'var(--text-secondary)', display: 'flex', flexDirection: 'column', gap: '8px' }}>
                  <div><strong>Account Name:</strong> Scholarbee Foundation</div>
                  <div><strong>Bank:</strong> HDFC Bank Ltd</div>
                  <div><strong>Account Type:</strong> Non-Profit Current Account</div>
                  <div><strong>IFSC Code:</strong> HDFC0001234</div>
                  <span style={{ fontSize: '0.78rem', color: 'var(--text-muted)', marginTop: '4px' }}>
                    *Please email transaction UTR to contact@scholarb.org to receive instant receipt.
                  </span>
                </div>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
