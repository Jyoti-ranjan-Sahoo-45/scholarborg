import React, { useEffect, useRef } from 'react';
import gsap from 'gsap';
import { Star, ShieldCheck, ArrowRight } from 'lucide-react';

import studentImg from '../../assets/student_hero.jpg';
import parentImg from '../../assets/parent_hero.jpg';
import teacherImg from '../../assets/teacher_hero.jpg';

export default function ReviewShuffle() {
  const containerRef = useRef(null);
  const cardRefs = useRef([]);
  const mainTimelineRef = useRef(null);

  const reviews = [
    {
      id: 0,
      name: 'Ananya S.',
      role: 'Grade 10 Scholar',
      avatar: studentImg,
      rating: 5,
      text: 'I feel significantly more confident in my board exams now. The scholarship and academic mentors helped me master difficult physics and math concepts.',
    },
    {
      id: 1,
      name: 'Rajesh K.',
      role: 'Parent from Lucknow',
      avatar: parentImg,
      rating: 5,
      text: 'Scholarbee Foundation removed our anxiety regarding educational expenses. The transparent progress reports let us see our daughter thriving.',
    },
    {
      id: 2,
      name: 'Meenakshi Iyer',
      role: 'Volunteer Mentor',
      avatar: teacherImg,
      rating: 5,
      text: 'Mentoring enthusiastic students through the digital classes has been profoundly rewarding. The curriculum and platform are exceptionally organized.',
    },
  ];

  useEffect(() => {
    const cards = cardRefs.current;
    if (!cards || cards.length < 3) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
      // Static display for reduced motion
      gsap.set(cards[0], { zIndex: 3, y: 0, scale: 1, opacity: 1 });
      gsap.set(cards[1], { zIndex: 2, y: 22, scale: 0.95, opacity: 0.75 });
      gsap.set(cards[2], { zIndex: 1, y: 44, scale: 0.9, opacity: 0.5 });
      return;
    }

    // Initialize 3 card stacking order
    gsap.set(cards[0], { zIndex: 3, y: 0, scale: 1, opacity: 1, x: 0, rotation: 0 });
    gsap.set(cards[1], { zIndex: 2, y: 22, scale: 0.95, opacity: 0.75, x: 0, rotation: 0 });
    gsap.set(cards[2], { zIndex: 1, y: 44, scale: 0.9, opacity: 0.5, x: 0, rotation: 0 });

    // Master continuous timeline that loops endlessly (repeat: -1)
    const masterTl = gsap.timeline({ repeat: -1 });
    mainTimelineRef.current = masterTl;

    // Helper function to animate one throw-and-promote cycle
    const addShuffleStep = (topIdx, secondIdx, thirdIdx) => {
      const top = cards[topIdx];
      const second = cards[secondIdx];
      const third = cards[thirdIdx];

      const stepTl = gsap.timeline({ delay: 2.8 });

      // 1. Throw top card to the left with -18deg rotation
      stepTl
        .to(top, {
          x: -320,
          rotation: -18,
          opacity: 0,
          duration: 0.55,
          ease: 'power2.in',
        })
        // 2. Bring 2nd card forward to top spot
        .to(
          second,
          {
            y: 0,
            scale: 1,
            opacity: 1,
            zIndex: 3,
            duration: 0.5,
            ease: 'back.out(1.4)',
          },
          '-=0.3'
        )
        // 3. Bring 3rd card forward to 2nd spot
        .to(
          third,
          {
            y: 22,
            scale: 0.95,
            opacity: 0.75,
            zIndex: 2,
            duration: 0.5,
            ease: 'back.out(1.4)',
          },
          '-=0.45'
        )
        // 4. Reset thrown card to back of stack (zIndex: 1, y: 44, scale: 0.9)
        .call(() => {
          gsap.set(top, { zIndex: 1, x: 0, y: 44, scale: 0.9, rotation: 0 });
        })
        // 5. Fade back card gently in at bottom of deck
        .to(top, {
          opacity: 0.5,
          duration: 0.35,
          ease: 'power1.out',
        });

      return stepTl;
    };

    // Cycle 1: 0 moves to back, 1 is top, 2 is 2nd
    masterTl.add(addShuffleStep(0, 1, 2));

    // Cycle 2: 1 moves to back, 2 is top, 0 is 2nd
    masterTl.add(addShuffleStep(1, 2, 0));

    // Cycle 3: 2 moves to back, 0 is top, 1 is 2nd
    masterTl.add(addShuffleStep(2, 0, 1));

    return () => {
      masterTl.kill();
    };
  }, []);

  // Quick manual skip button
  const handleNextClick = () => {
    if (mainTimelineRef.current) {
      // Fast forward progress by 1/3 of the loop
      const cur = mainTimelineRef.current.time();
      const dur = mainTimelineRef.current.duration();
      mainTimelineRef.current.time((cur + dur / 3) % dur);
    }
  };

  return (
    <div
      ref={containerRef}
      style={{
        position: 'relative',
        width: '100%',
        maxWidth: '520px',
        margin: '0 auto',
      }}
    >
      {/* Deck Container */}
      <div style={{ position: 'relative', height: '260px' }}>
        {reviews.map((rev, index) => {
          return (
            <div
              key={rev.id}
              ref={(el) => (cardRefs.current[index] = el)}
              className="card-base"
              style={{
                position: 'absolute',
                top: 0,
                left: 0,
                right: 0,
                height: '210px',
                borderColor: 'var(--border-cyan)',
                background: 'linear-gradient(145deg, var(--surface-2), var(--surface-3))',
                padding: '24px 28px',
                display: 'flex',
                gap: '20px',
                alignItems: 'center',
                boxShadow: 'var(--shadow-card), 0 0 30px rgba(0, 229, 255, 0.15)',
                willChange: 'transform, opacity',
              }}
            >
              <img
                src={rev.avatar}
                alt={rev.name}
                style={{
                  width: '92px',
                  height: '92px',
                  borderRadius: 'var(--radius-card)',
                  objectFit: 'cover',
                  border: '2px solid var(--border-cyan)',
                  flexShrink: 0,
                }}
              />
              <div style={{ flex: 1, minWidth: 0 }}>
                {/* 5 Gold Stars */}
                <div style={{ display: 'flex', gap: '3px', marginBottom: '8px', color: '#FFD700' }}>
                  {[...Array(5)].map((_, i) => (
                    <Star key={i} size={15} fill="#FFD700" strokeWidth={0} />
                  ))}
                </div>

                <p
                  style={{
                    fontSize: '0.94rem',
                    color: '#FFFFFF',
                    lineHeight: 1.5,
                    fontStyle: 'italic',
                    marginBottom: '10px',
                    display: '-webkit-box',
                    WebkitLineClamp: 3,
                    WebkitBoxOrient: 'vertical',
                    overflow: 'hidden',
                  }}
                >
                  “{rev.text}”
                </p>

                <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                  <span style={{ color: 'var(--electric-cyan)', fontWeight: 700, fontSize: '0.86rem' }}>
                    {rev.name}
                  </span>
                  <span style={{ color: 'var(--text-muted)', fontSize: '0.78rem' }}>• {rev.role}</span>
                  <span
                    style={{
                      display: 'inline-flex',
                      alignItems: 'center',
                      gap: '3px',
                      fontSize: '0.72rem',
                      color: '#52c41a',
                      marginLeft: 'auto',
                    }}
                  >
                    <ShieldCheck size={13} /> Verified
                  </span>
                </div>
              </div>
            </div>
          );
        })}
      </div>

      {/* Footer stats below deck */}
      <div
        style={{
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'space-between',
          paddingTop: '20px',
          borderTop: '1px solid var(--border-subtle)',
          fontSize: '0.82rem',
          color: 'var(--text-muted)',
        }}
      >
        <span>★ 4.9/5 average rating from 1,200+ students & families</span>
        <button
          onClick={handleNextClick}
          style={{
            background: 'none',
            border: 'none',
            color: 'var(--electric-cyan)',
            cursor: 'pointer',
            fontWeight: 600,
            fontSize: '0.82rem',
            display: 'inline-flex',
            alignItems: 'center',
            gap: '4px',
            padding: 0,
          }}
        >
          Next story <ArrowRight size={13} />
        </button>
      </div>
    </div>
  );
}
