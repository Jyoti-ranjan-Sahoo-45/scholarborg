import React, { useState } from 'react';
import { Mail, Phone, MapPin, Send, CheckCircle2, MessageSquare, ShieldCheck } from 'lucide-react';
import { api } from '../services/api';

export default function Contact() {
  const [submitted, setSubmitted] = useState(false);
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState({ name: '', email: '', subject: 'General Query', message: '' });

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    await api.submitContact(form);
    setLoading(false);
    setSubmitted(true);
  };

  return (
    <div style={{ backgroundColor: 'var(--bg-jet-black)', color: 'var(--text-secondary)', paddingTop: 'calc(var(--nav-height) + 40px)', paddingBottom: '100px' }}>
      <div className="container" style={{ maxWidth: '980px' }}>
        <div style={{ textAlign: 'center', marginBottom: '50px' }}>
          <div className="eyebrow" style={{ margin: '0 auto 16px auto' }}>GET IN TOUCH</div>
          <h1 className="h1-display" style={{ marginBottom: '18px' }}>
            Contact Scholarbee Foundation
          </h1>
          <p style={{ fontSize: '1.15rem', color: 'var(--text-secondary)', lineHeight: 1.6, maxWidth: '640px', margin: '0 auto' }}>
            Reach out to our secretariat for scholarship questions, institutional partnerships, or educator support.
          </p>
        </div>

        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '36px' }}>
          {/* Contact Details */}
          <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
            <div className="card-base">
              <h3 style={{ fontSize: '1.25rem', color: '#FFF', marginBottom: '18px' }}>Official Secretariat</h3>
              <div style={{ display: 'flex', flexDirection: 'column', gap: '16px', fontSize: '0.92rem' }}>
                <div style={{ display: 'flex', gap: '12px', alignItems: 'center' }}>
                  <Mail size={18} color="var(--electric-cyan)" />
                  <div>
                    <span style={{ color: 'var(--text-muted)', display: 'block', fontSize: '0.8rem' }}>Email Inquiries</span>
                    <a href="mailto:contact@scholarb.org" style={{ color: '#FFF', textDecoration: 'none' }}>contact@scholarb.org</a>
                  </div>
                </div>

                <div style={{ display: 'flex', gap: '12px', alignItems: 'center' }}>
                  <ShieldCheck size={18} color="var(--electric-cyan)" />
                  <div>
                    <span style={{ color: 'var(--text-muted)', display: 'block', fontSize: '0.8rem' }}>Compliance & Legal</span>
                    <a href="mailto:compliance@scholarb.org" style={{ color: '#FFF', textDecoration: 'none' }}>compliance@scholarb.org</a>
                  </div>
                </div>

                <div style={{ display: 'flex', gap: '12px', alignItems: 'center' }}>
                  <MapPin size={18} color="var(--electric-cyan)" />
                  <div>
                    <span style={{ color: 'var(--text-muted)', display: 'block', fontSize: '0.8rem' }}>Jurisdiction</span>
                    <span style={{ color: '#FFF' }}>India (Registered Educational Non-Profit)</span>
                  </div>
                </div>
              </div>
            </div>

            <div className="card-surface-3">
              <h4 style={{ fontSize: '1.05rem', color: '#FFF', marginBottom: '8px' }}>Looking for Tutor Support?</h4>
              <p style={{ fontSize: '0.88rem', color: 'var(--text-muted)', lineHeight: 1.5 }}>
                For active booking modifications or trial session scheduling, please check our supplementary tutoring portal.
              </p>
            </div>
          </div>

          {/* Form */}
          {submitted ? (
            <div className="card-base" style={{ textAlign: 'center', padding: '50px 30px', borderColor: 'var(--border-cyan)' }}>
              <div style={{ width: '60px', height: '60px', borderRadius: '50%', backgroundColor: 'rgba(0, 229, 255, 0.15)', color: 'var(--electric-cyan)', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 16px auto' }}>
                <CheckCircle2 size={32} />
              </div>
              <h3 style={{ fontSize: '1.6rem', color: '#FFF', marginBottom: '10px' }}>Message Transmitted</h3>
              <p style={{ fontSize: '0.95rem', color: 'var(--text-secondary)', lineHeight: 1.6, marginBottom: '24px' }}>
                Thank you for contacting Scholarbee Foundation. Your inquiry has been routed to our secretariat.
              </p>
              <button onClick={() => setSubmitted(false)} className="btn btn-outline">Send Another Query</button>
            </div>
          ) : (
            <form onSubmit={handleSubmit} className="card-base" style={{ display: 'flex', flexDirection: 'column', gap: '18px' }}>
              <h3 style={{ fontSize: '1.25rem', color: '#FFF' }}>Send Official Inquiry</h3>
              
              <div>
                <label style={{ fontSize: '0.82rem', color: 'var(--text-muted)', fontWeight: 600, display: 'block', marginBottom: '6px' }}>Your Name *</label>
                <input type="text" required placeholder="e.g. Anand Sen" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} style={{ width: '100%', minHeight: '44px', backgroundColor: 'var(--surface-3)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', color: '#FFF', padding: '0 14px', fontSize: '0.9rem', outline: 'none' }} />
              </div>

              <div>
                <label style={{ fontSize: '0.82rem', color: 'var(--text-muted)', fontWeight: 600, display: 'block', marginBottom: '6px' }}>Email Address *</label>
                <input type="email" required placeholder="anand@example.com" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} style={{ width: '100%', minHeight: '44px', backgroundColor: 'var(--surface-3)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', color: '#FFF', padding: '0 14px', fontSize: '0.9rem', outline: 'none' }} />
              </div>

              <div>
                <label style={{ fontSize: '0.82rem', color: 'var(--text-muted)', fontWeight: 600, display: 'block', marginBottom: '6px' }}>Inquiry Topic</label>
                <select value={form.subject} onChange={(e) => setForm({ ...form, subject: e.target.value })} style={{ width: '100%', minHeight: '44px', backgroundColor: 'var(--surface-3)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', color: '#FFF', padding: '0 12px', fontSize: '0.88rem' }}>
                  <option value="General Query">General Foundation Inquiry</option>
                  <option value="Scholarship Support">Scholarship & Grants Application</option>
                  <option value="Curriculum Collaboration">Open Curriculum Contribution</option>
                  <option value="CSR Partnership">Corporate CSR Partnership</option>
                  <option value="Tutor Verification">Educator Onboarding & Verification</option>
                </select>
              </div>

              <div>
                <label style={{ fontSize: '0.82rem', color: 'var(--text-muted)', fontWeight: 600, display: 'block', marginBottom: '6px' }}>Message *</label>
                <textarea required rows={4} placeholder="Please provide details regarding your inquiry..." value={form.message} onChange={(e) => setForm({ ...form, message: e.target.value })} style={{ width: '100%', backgroundColor: 'var(--surface-3)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', color: '#FFF', padding: '12px 14px', fontSize: '0.9rem', outline: 'none', resize: 'vertical' }} />
              </div>

              <button type="submit" disabled={loading} className="btn btn-cyan" style={{ width: '100%', minHeight: '48px', marginTop: '6px' }}>
                <Send size={16} /> {loading ? 'Submitting...' : 'Transmit Inquiry'}
              </button>
            </form>
          )}
        </div>
      </div>
    </div>
  );
}
