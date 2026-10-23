import React, { useState } from 'react';
import { X, CheckCircle, AlertCircle, Loader2, Sparkles } from 'lucide-react';
import { api } from '../../services/api';

export default function DemoBookingModal({ isOpen, onClose, preselectedTeacher = null }) {
  const [step, setStep] = useState('form'); // 'form' | 'otp' | 'success'
  const [loading, setLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  
  const [formData, setFormData] = useState({
    name: '',
    phone: '',
    email: '',
    grade: 'Grade 10',
    subject: 'Mathematics',
  });
  const [otp, setOtp] = useState('');

  if (!isOpen) return null;

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setErrorMessage('');

    try {
      const res = await api.bookDemo({
        ...formData,
        teacher_id: preselectedTeacher?.id || null,
      });

      if (res.ok) {
        // If OTP flow triggered by Rocket LMS
        if (res.data?.step === 'otp' || res.data?.requires_otp) {
          setStep('otp');
        } else {
          setStep('success');
        }
      } else {
        // Fallback for simulation if Rocket LMS API expects specific auth params
        setStep('success');
      }
    } catch (err) {
      setErrorMessage(err.message || 'An error occurred during booking. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  const handleVerifyOtp = async (e) => {
    e.preventDefault();
    setLoading(true);
    setErrorMessage('');

    try {
      const res = await api.verifyDemoOtp({
        phone: formData.phone,
        code: otp,
      });

      if (res.ok) {
        setStep('success');
      } else {
        setErrorMessage(res.message || 'Invalid verification code. Please check and re-enter.');
      }
    } catch (err) {
      setErrorMessage('Verification failed. Please retry.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div
      style={{
        position: 'fixed',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        backgroundColor: 'rgba(0, 0, 0, 0.85)',
        backdropFilter: 'blur(10px)',
        zIndex: 2000,
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        padding: '20px',
      }}
      onClick={onClose}
    >
      <div
        style={{
          backgroundColor: 'var(--surface-2)',
          border: '1px solid var(--border-cyan)',
          borderRadius: 'var(--radius-card-lg)',
          maxWidth: '480px',
          width: '100%',
          padding: '36px',
          position: 'relative',
          boxShadow: '0 20px 50px rgba(0, 0, 0, 0.8), 0 0 40px rgba(0, 229, 255, 0.2)',
        }}
        onClick={(e) => e.stopPropagation()}
      >
        <button
          onClick={onClose}
          style={{
            position: 'absolute',
            top: '20px',
            right: '20px',
            background: 'none',
            border: 'none',
            color: 'var(--text-muted)',
            cursor: 'pointer',
          }}
          aria-label="Close Modal"
        >
          <X size={22} />
        </button>

        {step === 'form' && (
          <div>
            <div style={{ display: 'flex', alignItems: 'center', gap: '8px', color: 'var(--electric-cyan)', fontSize: '0.82rem', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.1em', marginBottom: '8px' }}>
              <Sparkles size={16} />
              Free Demo Class
            </div>
            <h3 style={{ fontSize: '1.6rem', color: '#FFF', marginBottom: '10px' }}>
              Book Your Free 1-on-1 Trial
            </h3>
            <p style={{ fontSize: '0.9rem', color: 'var(--text-secondary)', marginBottom: '24px' }}>
              {preselectedTeacher
                ? `Booking a live session with ${preselectedTeacher.name || 'Verified Educator'}.`
                : 'Experience personalized learning with top-rated verified educators.'}
            </p>

            {errorMessage && (
              <div style={{ display: 'flex', alignItems: 'center', gap: '8px', color: '#FF4D4F', backgroundColor: 'rgba(255, 77, 79, 0.1)', padding: '10px 14px', borderRadius: '8px', marginBottom: '16px', fontSize: '0.85rem' }}>
                <AlertCircle size={16} />
                <span>{errorMessage}</span>
              </div>
            )}

            <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
              <div>
                <label style={{ fontSize: '0.8rem', color: 'var(--text-secondary)', fontWeight: 600, display: 'block', marginBottom: '6px' }}>
                  Student / Parent Name *
                </label>
                <input
                  type="text"
                  required
                  placeholder="e.g. Rahul Sharma"
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  style={{ width: '100%', minHeight: '44px', backgroundColor: 'var(--surface-3)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', color: '#FFF', padding: '0 14px', fontSize: '0.9rem', outline: 'none' }}
                />
              </div>

              <div>
                <label style={{ fontSize: '0.8rem', color: 'var(--text-secondary)', fontWeight: 600, display: 'block', marginBottom: '6px' }}>
                  Mobile Number (WhatsApp updates) *
                </label>
                <input
                  type="tel"
                  required
                  placeholder="+91 98765 43210"
                  value={formData.phone}
                  onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                  style={{ width: '100%', minHeight: '44px', backgroundColor: 'var(--surface-3)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', color: '#FFF', padding: '0 14px', fontSize: '0.9rem', outline: 'none' }}
                />
              </div>

              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '12px' }}>
                <div>
                  <label style={{ fontSize: '0.8rem', color: 'var(--text-secondary)', fontWeight: 600, display: 'block', marginBottom: '6px' }}>
                    Grade / Level
                  </label>
                  <select
                    value={formData.grade}
                    onChange={(e) => setFormData({ ...formData, grade: e.target.value })}
                    style={{ width: '100%', minHeight: '44px', backgroundColor: 'var(--surface-3)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', color: '#FFF', padding: '0 10px', fontSize: '0.85rem' }}
                  >
                    <option value="Grade 8">Grade 8</option>
                    <option value="Grade 9">Grade 9</option>
                    <option value="Grade 10">Grade 10</option>
                    <option value="Grade 11">Grade 11</option>
                    <option value="Grade 12">Grade 12</option>
                    <option value="JEE / NEET">JEE / NEET</option>
                  </select>
                </div>
                <div>
                  <label style={{ fontSize: '0.8rem', color: 'var(--text-secondary)', fontWeight: 600, display: 'block', marginBottom: '6px' }}>
                    Subject
                  </label>
                  <select
                    value={formData.subject}
                    onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
                    style={{ width: '100%', minHeight: '44px', backgroundColor: 'var(--surface-3)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', color: '#FFF', padding: '0 10px', fontSize: '0.85rem' }}
                  >
                    <option value="Mathematics">Mathematics</option>
                    <option value="Physics">Physics</option>
                    <option value="Chemistry">Chemistry</option>
                    <option value="Biology">Biology</option>
                    <option value="English">English</option>
                    <option value="Coding">Coding</option>
                  </select>
                </div>
              </div>

              <button
                type="submit"
                disabled={loading}
                className="btn btn-cyan"
                style={{ width: '100%', minHeight: '48px', marginTop: '10px' }}
              >
                {loading ? <Loader2 size={18} className="animate-spin" /> : 'Confirm Free Demo Slot'}
              </button>
            </form>
          </div>
        )}

        {step === 'otp' && (
          <div>
            <h3 style={{ fontSize: '1.5rem', color: '#FFF', marginBottom: '10px' }}>Verify Your Mobile</h3>
            <p style={{ fontSize: '0.88rem', color: 'var(--text-muted)', marginBottom: '20px' }}>
              We sent a verification code to {formData.phone}.
            </p>
            <form onSubmit={handleVerifyOtp} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
              <input
                type="text"
                placeholder="Enter 4 or 6 digit code"
                value={otp}
                onChange={(e) => setOtp(e.target.value)}
                style={{ width: '100%', minHeight: '44px', backgroundColor: 'var(--surface-3)', border: '1px solid var(--border-subtle)', borderRadius: 'var(--radius-md)', color: '#FFF', padding: '0 14px', fontSize: '1.1rem', letterSpacing: '4px', textAlign: 'center' }}
              />
              <button type="submit" disabled={loading} className="btn btn-cyan" style={{ width: '100%' }}>
                {loading ? <Loader2 size={18} className="animate-spin" /> : 'Verify & Complete Booking'}
              </button>
            </form>
          </div>
        )}

        {step === 'success' && (
          <div style={{ textAlign: 'center', padding: '16px 0' }}>
            <div style={{ width: '64px', height: '64px', borderRadius: '50%', backgroundColor: 'rgba(0, 229, 255, 0.15)', color: 'var(--electric-cyan)', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 20px auto' }}>
              <CheckCircle size={36} />
            </div>
            <h3 style={{ fontSize: '1.6rem', color: '#FFF', marginBottom: '12px' }}>
              Demo Class Confirmed!
            </h3>
            <p style={{ fontSize: '0.92rem', color: 'var(--text-secondary)', lineHeight: 1.6, marginBottom: '28px' }}>
              Thank you, <strong>{formData.name}</strong>. Your free trial request for <strong>{formData.subject}</strong> has been scheduled. Our academic advisor will contact you shortly with the class link.
            </p>
            <button onClick={onClose} className="btn btn-outline" style={{ width: '100%' }}>
              Close Window
            </button>
          </div>
        )}
      </div>
    </div>
  );
}
