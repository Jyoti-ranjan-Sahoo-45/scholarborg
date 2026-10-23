import React, { useState } from 'react';
import { Search, ChevronDown, Sparkles } from 'lucide-react';

export default function TeacherSearch({ onSearch, subjects = [] }) {
  const [selectedClass, setSelectedClass] = useState('Grade 10');
  const [selectedSubject, setSelectedSubject] = useState('Mathematics');
  const [selectedMode, setSelectedMode] = useState('Online 1-on-1');

  const classesList = [
    'Grade 6 - 8 (Middle)',
    'Grade 9 - 10 (Secondary)',
    'Grade 11 - 12 (Higher Secondary)',
    'JEE / NEET Competitive',
    'College / University',
    'Coding & Languages',
  ];

  const defaultSubjects = [
    'Mathematics',
    'Physics',
    'Chemistry',
    'Biology',
    'English & Literature',
    'Computer Science & Coding',
    'Economics & Commerce',
  ];

  const activeSubjects = subjects.length > 0 ? subjects : defaultSubjects;

  const handleSubmit = (e) => {
    e.preventDefault();
    if (onSearch) {
      onSearch({
        class: selectedClass,
        subject: selectedSubject,
        mode: selectedMode,
      });
    }
  };

  return (
    <form
      onSubmit={handleSubmit}
      style={{
        background: 'rgba(13, 13, 20, 0.95)',
        border: '1px solid var(--border-cyan)',
        borderRadius: 'var(--radius-card)',
        padding: '16px 20px',
        boxShadow: 'var(--shadow-card), 0 0 30px rgba(0, 229, 255, 0.15)',
        display: 'flex',
        flexWrap: 'wrap',
        alignItems: 'center',
        gap: '12px',
        width: '100%',
        maxWidth: '900px',
      }}
    >
      {/* Class Field */}
      <div style={{ flex: '1 1 200px', display: 'flex', flexDirection: 'column', gap: '4px' }}>
        <label style={{ fontSize: '0.72rem', textTransform: 'uppercase', letterSpacing: '0.08em', color: 'var(--text-muted)', fontWeight: 600 }}>
          Class / Level
        </label>
        <div style={{ position: 'relative' }}>
          <select
            value={selectedClass}
            onChange={(e) => setSelectedClass(e.target.value)}
            style={{
              width: '100%',
              minHeight: '48px',
              backgroundColor: 'var(--surface-3)',
              color: '#FFFFFF',
              border: '1px solid var(--border-subtle)',
              borderRadius: 'var(--radius-md)',
              padding: '0 36px 0 14px',
              fontSize: '0.9rem',
              fontWeight: 500,
              appearance: 'none',
              cursor: 'pointer',
              outline: 'none',
            }}
          >
            {classesList.map((c) => (
              <option key={c} value={c} style={{ backgroundColor: '#0D0D14' }}>
                {c}
              </option>
            ))}
          </select>
          <ChevronDown size={14} style={{ position: 'absolute', right: '12px', top: '17px', pointerEvents: 'none', color: 'var(--text-muted)' }} />
        </div>
      </div>

      {/* Subject Field */}
      <div style={{ flex: '1 1 200px', display: 'flex', flexDirection: 'column', gap: '4px' }}>
        <label style={{ fontSize: '0.72rem', textTransform: 'uppercase', letterSpacing: '0.08em', color: 'var(--text-muted)', fontWeight: 600 }}>
          Subject
        </label>
        <div style={{ position: 'relative' }}>
          <select
            value={selectedSubject}
            onChange={(e) => setSelectedSubject(e.target.value)}
            style={{
              width: '100%',
              minHeight: '48px',
              backgroundColor: 'var(--surface-3)',
              color: '#FFFFFF',
              border: '1px solid var(--border-subtle)',
              borderRadius: 'var(--radius-md)',
              padding: '0 36px 0 14px',
              fontSize: '0.9rem',
              fontWeight: 500,
              appearance: 'none',
              cursor: 'pointer',
              outline: 'none',
            }}
          >
            {activeSubjects.map((s) => {
              const name = typeof s === 'string' ? s : s.title || s.name;
              return (
                <option key={name} value={name} style={{ backgroundColor: '#0D0D14' }}>
                  {name}
                </option>
              );
            })}
          </select>
          <ChevronDown size={14} style={{ position: 'absolute', right: '12px', top: '17px', pointerEvents: 'none', color: 'var(--text-muted)' }} />
        </div>
      </div>

      {/* Mode Field */}
      <div style={{ flex: '1 1 180px', display: 'flex', flexDirection: 'column', gap: '4px' }}>
        <label style={{ fontSize: '0.72rem', textTransform: 'uppercase', letterSpacing: '0.08em', color: 'var(--text-muted)', fontWeight: 600 }}>
          Mode
        </label>
        <div style={{ position: 'relative' }}>
          <select
            value={selectedMode}
            onChange={(e) => setSelectedMode(e.target.value)}
            style={{
              width: '100%',
              minHeight: '48px',
              backgroundColor: 'var(--surface-3)',
              color: '#FFFFFF',
              border: '1px solid var(--border-subtle)',
              borderRadius: 'var(--radius-md)',
              padding: '0 36px 0 14px',
              fontSize: '0.9rem',
              fontWeight: 500,
              appearance: 'none',
              cursor: 'pointer',
              outline: 'none',
            }}
          >
            <option value="Online 1-on-1" style={{ backgroundColor: '#0D0D14' }}>Online 1-on-1 Live</option>
            <option value="Group Classes" style={{ backgroundColor: '#0D0D14' }}>Group Live Batches</option>
            <option value="Home Tuition" style={{ backgroundColor: '#0D0D14' }}>In-Person / Home</option>
          </select>
          <ChevronDown size={14} style={{ position: 'absolute', right: '12px', top: '17px', pointerEvents: 'none', color: 'var(--text-muted)' }} />
        </div>
      </div>

      {/* Submit Button */}
      <div style={{ flex: '1 1 180px', display: 'flex', alignItems: 'flex-end' }}>
        <button
          type="submit"
          className="btn btn-cyan"
          style={{ width: '100%', minHeight: '48px', fontSize: '0.95rem', fontWeight: 700 }}
        >
          <Search size={18} />
          Find Teachers
        </button>
      </div>
    </form>
  );
}
