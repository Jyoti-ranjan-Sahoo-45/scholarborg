import React, { useState } from 'react';
import { HeartHandshake, CheckCircle2, BookOpen, Users, ArrowRight } from 'lucide-react';

export default function Volunteer() {
  const [submitted, setSubmitted] = useState(false);
  const [form, setForm] = useState({ name: '', email: '', phone: '', expertise: 'Mathematics', hours: '4-6 hours/week' });

  const handleSubmit = (e) => {
    e.preventDefault();
    setSubmitted(true);
  };

  return (
    <div style={{ backgroundColor: 'var(--bg-jet-black)', color: 'var(--text-secondary)', paddingTop: 'calc(var(--nav-height) + 40px)', paddingBottom: '100px' }}>
      <div className="container" style={{ maxWidth: '880px' }}>
        <div style={{ textAlign: 'center', marginBottom: '50px' }}>
          <div className="eyebrow" style={{ margin: '0 auto 16px auto' }}>GET INVOLVED</div>
          <h1 className="h1-display" style={{ marginBottom: '18px' }}>
            Volunteer as an Educator or Mentor
          </h1>
          <p style={{ fontSize: '1.15rem', color: 'var(--text-secondary)', lineHeight: 1.6, maxWidth: '640px', margin: '0 auto' }}>
            Share your knowledge with high school students who need guidance in STEM and competitive subjects.
          </p>
        </div>

        {submitted ? (
          <div className="card-base" style={{ textAlign: 'center', padding: '60px 30px', borderColor: 'var(--border-cyan)' }}>
            <div style={{ width: '64px', height: '64px', borderRadius: '50%', backgroundColor: 'rgba(0, 229, 255, 0.15)', color: 'var(--electric-cyan)', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 20px auto' }}>
              <CheckCircle2 size={36} />
            </div>
            <h3 style={{ fontSize: '1.8rem', color: '#FFF', marginBottom: '12px' }}>Application Received</h3>
            <p style={{ fontSize: '1.05rem', color: 'var(--text-secondary)', lineHeight: 1.6, maxWidth: '540px', margin: '0 auto 28px auto' }}>
              Thank you, <strong>{form.name}</strong>. Our academic volunteer coordinator will reach out to schedule an orientation for the <strong>{form.expertise}</strong> mentorship track.
            </p>
            <button onClick={() => setSubmitted(false)} className="btn btn-outline">Submit Another Response</button>
          </div>
        ) : (
          <form onSubmit={handleSubmit} className="card-base" style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
            <h3 style={{ fontSize: '1.3rem', color: '#FFF' }}>Volunteer Registration Form</h3>
            
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(240px, 1fr))', gap: '16px' }}>
              <div>
                <label style={{ fontSize: '0.82rem', color: 'var(--text-muted)', fontWeight: 600, display: 'block', marginBottom: '6px' }}>Full Name *</label>
                <input type="text" required placeholder="e.g. Dr. Kavita Nair" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} style={{ width: '100%', minHeight: '44px', backgroundColor: 'var(--surface-3)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', color: '#FFF', padding: '0 14px', fontSize: '0.9rem', outline: 'none' }} />
              </div>
              <div>
                <label style={{ fontSize: '0.82rem', color: 'var(--text-muted)', fontWeight: 600, display: 'block', marginBottom: '6px' }}>Email Address *</label>
                <input type="email" required placeholder="kavita@example.com" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} style={{ width: '100%', minHeight: '44px', backgroundColor: 'var(--surface-3)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', color: '#FFF', padding: '0 14px', fontSize: '0.9rem', outline: 'none' }} />
              </div>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(240px, 1fr))', gap: '16px' }}>
              <div>
                <label style={{ fontSize: '0.82rem', color: 'var(--text-muted)', fontWeight: 600, display: 'block', marginBottom: '6px' }}>Primary Field of Expertise</label>
                <select value={form.expertise} onChange={(e) => setForm({ ...form, expertise: e.target.value })} style={{ width: '100%', minHeight: '44px', backgroundColor: 'var(--surface-3)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', color: '#FFF', padding: '0 12px', fontSize: '0.9rem' }}>
                  <option value="Mathematics">Mathematics</option>
                  <option value="Physics">Physics</option>
                  <option value="Chemistry">Chemistry</option>
                  <option value="Biology">Biology</option>
                  <option value="Computer Science / Coding">Computer Science / Coding</option>
                  <option value="Career & College Counseling">Career & College Counseling</option>
                </select>
              </div>
              <div>
                <label style={{ fontSize: '0.82rem', color: 'var(--text-muted)', fontWeight: 600, display: 'block', marginBottom: '6px' }}>Availability</label>
                <select value={form.hours} onChange={(e) => setForm({ ...form, hours: e.target.value })} style={{ width: '100%', minHeight: '44px', backgroundColor: 'var(--surface-3)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', color: '#FFF', padding: '0 12px', fontSize: '0.9rem' }}>
                  <option value="2-3 hours/week">2-3 hours / week</option>
                  <option value="4-6 hours/week">4-6 hours / week</option>
                  <option value="Weekends Only">Weekends Only</option>
                </select>
              </div>
            </div>

            <button type="submit" className="btn btn-cyan" style={{ width: '100%', minHeight: '48px', marginTop: '10px', fontSize: '1rem' }}>
              Submit Volunteer Application <ArrowRight size={18} />
            </button>
          </form>
        )}
      </div>
    </div>
  );
}
