import React, { useState } from 'react';
import { ChevronDown } from 'lucide-react';

export default function FAQAccordion({ items = [] }) {
  const [openIndex, setOpenIndex] = useState(0);

  const toggleItem = (idx) => {
    setOpenIndex(openIndex === idx ? -1 : idx);
  };

  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: '14px', maxWidth: '800px', margin: '0 auto', width: '100%' }}>
      {items.map((item, index) => {
        const isOpen = openIndex === index;
        return (
          <div
            key={index}
            style={{
              backgroundColor: 'var(--surface-2)',
              border: isOpen ? '1px solid var(--border-cyan)' : '1px solid var(--border-subtle)',
              borderRadius: 'var(--radius-card)',
              overflow: 'hidden',
              transition: 'all 0.25s ease',
            }}
          >
            <button
              onClick={() => toggleItem(index)}
              style={{
                width: '100%',
                padding: '20px 24px',
                background: 'none',
                border: 'none',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
                color: '#FFFFFF',
                fontFamily: 'var(--font-heading)',
                fontSize: '1.05rem',
                fontWeight: 600,
                textAlign: 'left',
                cursor: 'pointer',
              }}
              aria-expanded={isOpen}
            >
              <span>{item.question}</span>
              <ChevronDown
                size={18}
                style={{
                  color: isOpen ? 'var(--electric-cyan)' : 'var(--text-muted)',
                  transform: isOpen ? 'rotate(180deg)' : 'none',
                  transition: 'transform 0.25s ease, color 0.25s ease',
                  flexShrink: 0,
                  marginLeft: '12px',
                }}
              />
            </button>

            {isOpen && (
              <div
                style={{
                  padding: '0 24px 22px 24px',
                  color: 'var(--text-secondary)',
                  fontSize: '0.94rem',
                  lineHeight: 1.65,
                }}
              >
                {item.answer}
              </div>
            )}
          </div>
        );
      })}
    </div>
  );
}
