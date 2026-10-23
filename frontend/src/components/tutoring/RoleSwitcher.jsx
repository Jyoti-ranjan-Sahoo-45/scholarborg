import React from 'react';

export default function RoleSwitcher({ activeRole, onRoleChange }) {
  const roles = [
    { id: 'student', label: 'Student' },
    { id: 'parent', label: 'Parent' },
    { id: 'teacher', label: 'Teacher' },
  ];

  return (
    <div
      style={{
        display: 'inline-flex',
        alignItems: 'center',
        background: 'rgba(13, 13, 20, 0.85)',
        border: '1px solid rgba(255, 255, 255, 0.14)',
        borderRadius: 'var(--radius-pill)',
        padding: '5px',
        position: 'relative',
        boxShadow: '0 4px 20px rgba(0, 0, 0, 0.5)',
        maxWidth: '100%',
        margin: '20px 0 24px 0',
      }}
      role="tablist"
      aria-label="Platform Role Selection"
    >
      {roles.map((role) => {
        const isActive = activeRole === role.id;
        return (
          <button
            key={role.id}
            role="tab"
            aria-selected={isActive}
            onClick={() => onRoleChange(role.id)}
            style={{
              position: 'relative',
              zIndex: 2,
              background: isActive ? 'var(--electric-cyan)' : 'transparent',
              color: isActive ? '#050505' : '#FFFFFF',
              border: 'none',
              padding: '9px 26px',
              borderRadius: 'var(--radius-pill)',
              fontFamily: 'var(--font-heading)',
              fontSize: '0.92rem',
              fontWeight: 700,
              cursor: 'pointer',
              transition: 'all 0.28s cubic-bezier(0.16, 1, 0.3, 1)',
              boxShadow: isActive ? '0 2px 14px rgba(0, 229, 255, 0.45)' : 'none',
              whiteSpace: 'nowrap',
            }}
          >
            {role.label}
          </button>
        );
      })}
    </div>
  );
}
