import React, { useEffect, useRef } from 'react';
import { Link } from 'react-router-dom';
import gsap from 'gsap';
import { 
  ArrowRight, BookOpen, Users, IndianRupee, 
  TrendingUp, Wifi, GraduationCap 
} from 'lucide-react';

// High-resolution generated images
import heroGirl from '../assets/edu_hero_girl.png';
import cardAccess from '../assets/edu_challenge_access.png';
import cardAffordability from '../assets/edu_challenge_affordability.png';
import cardSupport from '../assets/edu_challenge_support.png';
import cardDigital from '../assets/edu_challenge_digital.png';
import cardFinancial from '../assets/edu_challenge_financial.png';
import responseStudents from '../assets/edu_response_students.png';

export default function EducationChallenge() {
  const heroWordsRef = useRef([]);
  const heroSubRef = useRef(null);
  const heroBtnRef = useRef(null);
  const heroImgRef = useRef(null);

  useEffect(() => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    const words = heroWordsRef.current.filter(Boolean);
    if (words.length === 0) return;

    const tl = gsap.timeline();
    tl.fromTo(
      words,
      { opacity: 0, y: 35, filter: 'blur(8px)' },
      { opacity: 1, y: 0, filter: 'blur(0px)', duration: 0.65, stagger: 0.1, ease: 'power3.out' }
    )
      .fromTo(
        heroSubRef.current,
        { opacity: 0, y: 15 },
        { opacity: 1, y: 0, duration: 0.55, ease: 'power2.out' },
        '-=0.3'
      )
      .fromTo(
        heroBtnRef.current,
        { opacity: 0, y: 15 },
        { opacity: 1, y: 0, duration: 0.5, ease: 'power2.out' },
        '-=0.3'
      )
      .fromTo(
        heroImgRef.current,
        { opacity: 0, scale: 0.94, y: 20 },
        { opacity: 1, scale: 1, y: 0, duration: 0.75, ease: 'power3.out' },
        '-=0.55'
      );
  }, []);
  const challengeCards = [
    {
      img: cardAccess,
      icon: Users,
      title: 'Limited Access',
      desc: 'Some learners face barriers in accessing quality educational support due to geography, infrastructure or limited nearby resources.',
    },
    {
      img: cardAffordability,
      icon: IndianRupee,
      title: 'Affordability',
      desc: 'Education-related costs can be a significant challenge for many families, including learning resources and additional academic support.',
    },
    {
      img: cardSupport,
      icon: TrendingUp,
      title: 'Need for Additional Learning Support',
      desc: 'Many learners require additional academic support beyond their regular classroom learning to strengthen their understanding.',
    },
    {
      img: cardDigital,
      icon: Wifi,
      title: 'Digital Access',
      desc: 'Online learning can increase opportunities, but device availability, internet connectivity and digital literacy can still be challenges for some learners.',
    },
    {
      img: cardFinancial,
      icon: GraduationCap,
      title: 'Financial Support for Continued Education',
      desc: 'For some eligible learners, scholarship and educational assistance can help reduce financial barriers and support continued learning.',
    },
  ];

  return (
    <div style={{ backgroundColor: 'var(--bg-jet-black)', color: 'var(--text-secondary)' }}>
      {/* ================= 1. HERO SECTION ================= */}
      <section
        style={{
          position: 'relative',
          paddingTop: 'calc(var(--nav-height) + 24px)',
          paddingBottom: '40px',
          overflow: 'hidden',
          background: 'linear-gradient(180deg, rgba(8, 8, 12, 0.9) 0%, rgba(5, 5, 5, 1) 100%)',
        }}
      >
        {/* Ambient Glows */}
        <div
          style={{
            position: 'absolute',
            top: '0',
            right: '10%',
            width: '600px',
            height: '400px',
            background: 'radial-gradient(circle, rgba(59, 10, 107, 0.4) 0%, rgba(0, 229, 255, 0.08) 50%, transparent 70%)',
            filter: 'blur(80px)',
            pointerEvents: 'none',
          }}
        />

        <div className="container" style={{ position: 'relative', zIndex: 1 }}>
          {/* Breadcrumb */}
          <div
            style={{
              display: 'flex',
              alignItems: 'center',
              gap: '8px',
              fontSize: '0.84rem',
              color: 'var(--text-muted)',
              marginBottom: '28px',
            }}
          >
            <Link to="/" style={{ color: 'var(--text-muted)', textDecoration: 'none' }}>
              Home
            </Link>
            <span style={{ fontSize: '0.75rem' }}>&gt;</span>
            <span style={{ color: 'var(--text-secondary)' }}>The Education Challenge</span>
          </div>

          <div
            style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))',
              gap: '40px',
              alignItems: 'center',
            }}
          >
            {/* Left Content */}
            <div style={{ maxWidth: '620px' }}>
              <h1
                style={{
                  fontSize: 'clamp(2.4rem, 4.4vw, 3.8rem)',
                  fontWeight: 800,
                  fontFamily: 'var(--font-heading)',
                  lineHeight: 1.15,
                  color: '#FFFFFF',
                  marginBottom: '20px',
                }}
              >
                {['The', 'Education'].map((w, i) => (
                  <span
                    key={i}
                    ref={(el) => (heroWordsRef.current[i] = el)}
                    style={{ display: 'inline-block', marginRight: '0.28em' }}
                  >
                    {w}
                  </span>
                ))}
                <br />
                <span
                  ref={(el) => (heroWordsRef.current[2] = el)}
                  style={{ color: 'var(--electric-cyan)', display: 'inline-block' }}
                >
                  Challenge in India
                </span>
              </h1>

              <p
                ref={heroSubRef}
                style={{
                  fontSize: '1.05rem',
                  color: 'var(--text-secondary)',
                  lineHeight: 1.68,
                  marginBottom: '32px',
                }}
              >
                Every child has the potential to learn, grow and build a brighter future. However, many learners still face significant barriers to accessing quality educational opportunities. Scholarbee Foundation works to address these challenges through focused educational initiatives.
              </p>

              <div ref={heroBtnRef}>
                <Link
                  to="/programmes"
                  className="btn btn-cyan"
                  style={{
                    display: 'inline-flex',
                    alignItems: 'center',
                    gap: '8px',
                    padding: '13px 28px',
                    fontSize: '0.96rem',
                    fontWeight: 700,
                  }}
                >
                  Explore Our Programmes <ArrowRight size={16} />
                </Link>
              </div>
            </div>

            {/* Right Hero Image with Script Overlay */}
            <div ref={heroImgRef} style={{ position: 'relative', display: 'flex', justifyContent: 'center' }}>
              <div
                style={{
                  position: 'relative',
                  width: '100%',
                  borderRadius: 'var(--radius-container)',
                  overflow: 'hidden',
                  border: '1px solid rgba(0, 229, 255, 0.25)',
                  boxShadow: 'var(--shadow-card), 0 0 45px rgba(0, 229, 255, 0.2)',
                  background: 'var(--surface-2)',
                }}
              >
                <img
                  src={heroGirl}
                  alt="Indian schoolgirl holding pen looking up thoughtfully in classroom"
                  style={{
                    width: '100%',
                    height: 'auto',
                    display: 'block',
                    objectFit: 'cover',
                  }}
                />

                {/* Cursive Tagline Overlay matching the mockup */}
                <div
                  style={{
                    position: 'absolute',
                    top: '28%',
                    right: '6%',
                    fontFamily: 'cursive, "Brush Script MT", Georgia, sans-serif',
                    fontSize: 'clamp(1.4rem, 2.4vw, 2.1rem)',
                    color: '#FFFFFF',
                    textShadow: '0 2px 10px rgba(0,0,0,0.85), 0 0 20px rgba(0,229,255,0.5)',
                    transform: 'rotate(-8deg)',
                    lineHeight: 1.2,
                    textAlign: 'right',
                    pointerEvents: 'none',
                  }}
                >
                  Every Learner<br />
                  <span style={{ fontStyle: 'italic', fontWeight: 300 }}>Deserves</span><br />
                  Opportunity
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ================= 2. WHY THIS MATTERS ================= */}
      <section className="section" style={{ backgroundColor: 'var(--surface-1)' }}>
        <div className="container">
          <div
            style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))',
              gap: '48px',
              alignItems: 'center',
            }}
          >
            {/* Left Narrative */}
            <div>
              <div className="eyebrow">WHY THIS MATTERS</div>
              <h2 className="h2-section" style={{ marginBottom: '18px' }}>
                A Brighter Future Should Be Within<br />
                Reach for <span className="text-cyan">Every Learner</span>
              </h2>
              <p
                style={{
                  fontSize: '1.02rem',
                  color: 'var(--text-secondary)',
                  lineHeight: 1.7,
                }}
              >
                Education opens doors to confidence, opportunity and a better quality of life. Yet, many children in India continue to face multiple barriers that can make it difficult to access and benefit from quality education. Understanding these challenges is the first step towards creating meaningful and lasting change.
              </p>
            </div>

            {/* Right Cards (2 horizontal cards) */}
            <div style={{ display: 'flex', flexDirection: 'column', gap: '18px' }}>
              {/* Card 1 */}
              <div
                className="card-surface-3"
                style={{
                  display: 'flex',
                  gap: '20px',
                  alignItems: 'flex-start',
                  padding: '24px',
                  borderRadius: 'var(--radius-card)',
                  border: '1px solid var(--border-subtle)',
                }}
              >
                <div
                  style={{
                    width: '48px',
                    height: '48px',
                    borderRadius: '50%',
                    border: '1.5px solid var(--electric-cyan)',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    color: 'var(--electric-cyan)',
                    flexShrink: 0,
                    background: 'rgba(0, 229, 255, 0.06)',
                  }}
                >
                  <BookOpen size={22} />
                </div>
                <div>
                  <h4 style={{ fontSize: '1.08rem', color: '#FFFFFF', marginBottom: '8px', fontWeight: 700 }}>
                    Education Builds Stronger Individuals and Communities
                  </h4>
                  <p style={{ fontSize: '0.88rem', color: 'var(--text-muted)', lineHeight: 1.6 }}>
                    When learners have access to quality education, they are better equipped to pursue their goals, contribute to their communities and build a brighter future.
                  </p>
                </div>
              </div>

              {/* Card 2 */}
              <div
                className="card-surface-3"
                style={{
                  display: 'flex',
                  gap: '20px',
                  alignItems: 'flex-start',
                  padding: '24px',
                  borderRadius: 'var(--radius-card)',
                  border: '1px solid var(--border-subtle)',
                }}
              >
                <div
                  style={{
                    width: '48px',
                    height: '48px',
                    borderRadius: '50%',
                    border: '1.5px solid var(--electric-cyan)',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    color: 'var(--electric-cyan)',
                    flexShrink: 0,
                    background: 'rgba(0, 229, 255, 0.06)',
                  }}
                >
                  <Users size={22} />
                </div>
                <div>
                  <h4 style={{ fontSize: '1.08rem', color: '#FFFFFF', marginBottom: '8px', fontWeight: 700 }}>
                    Addressing Barriers Creates Opportunities
                  </h4>
                  <p style={{ fontSize: '0.88rem', color: 'var(--text-muted)', lineHeight: 1.6 }}>
                    By working to reduce educational barriers, we can help more learners develop the skills, knowledge and confidence they need to thrive.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ================= 3. KEY CHALLENGES ================= */}
      <section className="section">
        <div className="container">
          <div style={{ maxWidth: '780px', marginBottom: '44px' }}>
            <div className="eyebrow">KEY CHALLENGES</div>
            <h2 className="h2-section" style={{ marginBottom: '14px' }}>
              Barriers That Can Limit Educational Opportunity
            </h2>
            <p style={{ fontSize: '1.02rem', color: 'var(--text-muted)', lineHeight: 1.65 }}>
              Many learners face a combination of challenges that can affect their ability to access and continue quality education. These barriers can vary by region, community and individual circumstances.
            </p>
          </div>

          {/* 5 Cards Row */}
          <div
            style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fit, minmax(210px, 1fr))',
              gap: '18px',
            }}
          >
            {challengeCards.map((c, i) => {
              const Icon = c.icon;
              return (
                <div
                  key={i}
                  className="card-base"
                  style={{
                    padding: '0',
                    overflow: 'hidden',
                    display: 'flex',
                    flexDirection: 'column',
                    background: 'var(--surface-2)',
                    border: '1px solid var(--border-card)',
                    borderRadius: 'var(--radius-card)',
                  }}
                >
                  {/* Photo Top */}
                  <div style={{ position: 'relative', width: '100%', height: '120px', overflow: 'hidden' }}>
                    <img
                      src={c.img}
                      alt={c.title}
                      style={{
                        width: '100%',
                        height: '100%',
                        objectFit: 'cover',
                        display: 'block',
                      }}
                    />
                    {/* Floating circular icon badge overlapping bottom */}
                    <div
                      style={{
                        position: 'absolute',
                        bottom: '-16px',
                        left: '18px',
                        width: '36px',
                        height: '36px',
                        borderRadius: '50%',
                        backgroundColor: 'var(--surface-3)',
                        border: '2px solid var(--electric-cyan)',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        color: 'var(--electric-cyan)',
                        zIndex: 2,
                        boxShadow: '0 2px 8px rgba(0,0,0,0.5)',
                      }}
                    >
                      <Icon size={18} />
                    </div>
                  </div>

                  {/* Card Content */}
                  <div style={{ padding: '28px 18px 22px 18px', display: 'flex', flexDirection: 'column', flex: 1 }}>
                    <h3
                      style={{
                        fontSize: '1rem',
                        fontWeight: 700,
                        color: '#FFFFFF',
                        marginBottom: '10px',
                        lineHeight: 1.35,
                      }}
                    >
                      {c.title}
                    </h3>
                    <p
                      style={{
                        fontSize: '0.82rem',
                        color: 'var(--text-muted)',
                        lineHeight: 1.55,
                        flex: 1,
                      }}
                    >
                      {c.desc}
                    </p>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* ================= 4. SCHOLARBEE FOUNDATION'S RESPONSE ================= */}
      <section
        className="section"
        style={{
          backgroundColor: 'var(--surface-1)',
          position: 'relative',
          overflow: 'hidden',
        }}
      >
        {/* Subtle Ambient Glow */}
        <div
          style={{
            position: 'absolute',
            bottom: '0',
            right: '0',
            width: '500px',
            height: '350px',
            background: 'radial-gradient(circle, rgba(59, 10, 107, 0.45) 0%, rgba(0, 229, 255, 0.06) 60%, transparent 80%)',
            filter: 'blur(70px)',
            pointerEvents: 'none',
          }}
        />

        <div className="container" style={{ position: 'relative', zIndex: 1 }}>
          <div
            style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))',
              gap: '48px',
              alignItems: 'center',
            }}
          >
            {/* Left Photo of Students Smiling Together Over Books */}
            <div
              style={{
                borderRadius: 'var(--radius-card-lg)',
                overflow: 'hidden',
                border: '1px solid var(--border-card)',
                boxShadow: 'var(--shadow-card), 0 0 35px rgba(0, 229, 255, 0.15)',
              }}
            >
              <img
                src={responseStudents}
                alt="Indian school students reading together over books in classroom"
                style={{
                  width: '100%',
                  height: 'auto',
                  display: 'block',
                  objectFit: 'cover',
                }}
              />
            </div>

            {/* Right Narrative */}
            <div>
              <div className="eyebrow">SCHOLARBEE FOUNDATION'S RESPONSE</div>
              <h2 className="h2-section" style={{ marginBottom: '18px' }}>
                From Challenges to <span className="text-cyan">Opportunities</span>
              </h2>
              <p
                style={{
                  fontSize: '1.02rem',
                  color: 'var(--text-secondary)',
                  lineHeight: 1.7,
                  marginBottom: '28px',
                }}
              >
                Scholarbee Foundation works to address these challenges through free online learning opportunities, scholarship initiatives and appropriate academic support for eligible learners. Our programmes are designed to help more children access learning, build confidence and move towards a brighter future.
              </p>
              <div>
                <Link
                  to="/programmes"
                  className="btn btn-cyan"
                  style={{
                    display: 'inline-flex',
                    alignItems: 'center',
                    gap: '8px',
                    padding: '13px 28px',
                    fontSize: '0.96rem',
                    fontWeight: 700,
                  }}
                >
                  Explore Our Programmes <ArrowRight size={16} />
                </Link>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
