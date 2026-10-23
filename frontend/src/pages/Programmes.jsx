import React, { useEffect, useRef } from 'react';
import { Link } from 'react-router-dom';
import gsap from 'gsap';
import { 
  ArrowRight, 
  BookOpen, 
  Award, 
  Users, 
  Sparkles, 
  Check, 
  ShieldCheck, 
  Compass, 
  Lightbulb, 
  HeartHandshake, 
  TrendingUp,
  ExternalLink 
} from 'lucide-react';

import heroImg from '../assets/programmes_hero.jpg';
import imgOnline from '../assets/prog_online_learning.jpg';
import imgScholarships from '../assets/prog_scholarships.jpg';
import imgMentoring from '../assets/prog_mentoring.jpg';
import imgTutoring from '../assets/prog_tutoring.jpg';

export default function Programmes() {
  // Animation Refs
  const heroWordsRef = useRef([]);
  const cyanWordsRef = useRef([]);
  const heroSubRef = useRef(null);
  const heroBtnRef = useRef(null);
  const heroBgRef = useRef(null);
  const heroMobileImgRef = useRef(null);
  const cursiveRef = useRef(null);

  const cardsSectionRef = useRef(null);
  const programmeCardsRef = useRef([]);

  const helpSectionRef = useRef(null);
  const helpCardsRef = useRef([]);

  const roadmapSectionRef = useRef(null);
  const roadmapCardsRef = useRef([]);

  useEffect(() => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    // 1. MadeToScale 01: Hero Reveal Master Timeline
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

    const titleWords = heroWordsRef.current.filter(Boolean);
    const cyanWords = cyanWordsRef.current.filter(Boolean);

    gsap.set([...titleWords, ...cyanWords], { opacity: 0, y: 35, filter: 'blur(8px)' });
    gsap.set(heroSubRef.current, { opacity: 0, y: 18 });
    gsap.set(heroBtnRef.current, { opacity: 0, scale: 0.92, y: 12 });
    if (heroBgRef.current) gsap.set(heroBgRef.current, { opacity: 0, scale: 0.96 });
    if (heroMobileImgRef.current) gsap.set(heroMobileImgRef.current, { opacity: 0, y: 20 });
    if (cursiveRef.current) gsap.set(cursiveRef.current, { opacity: 0, scale: 0.85, rotate: -15 });

    tl.to(titleWords, {
      opacity: 1,
      y: 0,
      filter: 'blur(0px)',
      duration: 0.6,
      stagger: 0.08
    })
    .to(cyanWords, {
      opacity: 1,
      y: 0,
      filter: 'blur(0px)',
      duration: 0.6,
      stagger: 0.1
    }, '-=0.35')
    .to(heroSubRef.current, {
      opacity: 1,
      y: 0,
      duration: 0.55
    }, '-=0.3')
    .to(heroBtnRef.current, {
      opacity: 1,
      scale: 1,
      y: 0,
      duration: 0.45,
      ease: 'back.out(1.4)'
    }, '-=0.25')
    .to([heroBgRef.current, heroMobileImgRef.current].filter(Boolean), {
      opacity: 0.88,
      scale: 1,
      y: 0,
      duration: 0.85,
      ease: 'power2.out'
    }, '-=0.6')
    .to(cursiveRef.current, {
      opacity: 1,
      scale: 1,
      rotate: -7,
      duration: 0.7,
      ease: 'back.out(1.6)'
    }, '-=0.5');

    // 2. IntersectionObserver for Programme Cards
    const cardsObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const cards = programmeCardsRef.current.filter(Boolean);
            gsap.fromTo(
              cards,
              { opacity: 0, y: 35, scale: 0.96 },
              { opacity: 1, y: 0, scale: 1, duration: 0.65, stagger: 0.15, ease: 'power2.out' }
            );
            cardsObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1 }
    );
    if (cardsSectionRef.current) cardsObserver.observe(cardsSectionRef.current);

    // 3. IntersectionObserver for "How Our Programmes Help" Pillars
    const helpObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const items = helpCardsRef.current.filter(Boolean);
            gsap.fromTo(
              items,
              { opacity: 0, y: 25, scale: 0.94 },
              { opacity: 1, y: 0, scale: 1, duration: 0.55, stagger: 0.1, ease: 'back.out(1.2)' }
            );
            helpObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15 }
    );
    if (helpSectionRef.current) helpObserver.observe(helpSectionRef.current);

    // 4. IntersectionObserver for Roadmap Steps
    const roadmapObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const steps = roadmapCardsRef.current.filter(Boolean);
            gsap.fromTo(
              steps,
              { opacity: 0, y: 25 },
              { opacity: 1, y: 0, duration: 0.55, stagger: 0.12, ease: 'power2.out' }
            );
            roadmapObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15 }
    );
    if (roadmapSectionRef.current) roadmapObserver.observe(roadmapSectionRef.current);

    return () => {
      cardsObserver.disconnect();
      helpObserver.disconnect();
      roadmapObserver.disconnect();
    };
  }, []);

  const programmes = [
    {
      id: 'online-learning',
      title: 'Free Online Learning',
      badge: '100% Free / Charitable',
      badgeType: 'charitable',
      img: imgOnline,
      desc: 'Syllabus-aligned digital lessons, practice quizzes, and downloadable study guides designed for Indian students to learn at their own pace without cost barriers.',
      link: '/programmes/free-online-learning',
      btnText: 'Access Free Learning',
      icon: BookOpen,
      features: [
        'Curated core subjects across secondary grades',
        'Open to all eligible students with zero registration fees',
        'Optimized for smartphones & low-bandwidth connectivity',
        'Self-paced interactive video modules & revision notes'
      ],
      isMarketplace: false
    },
    {
      id: 'scholarships',
      title: 'Scholarships & Educational Assistance',
      badge: 'Merit & Need Based Aid',
      badgeType: 'charitable',
      img: imgScholarships,
      desc: 'Financial continuity support for motivated secondary learners from low-income households, helping cover essential schooling costs, textbooks, and learning supplies.',
      link: '/programmes/scholarships',
      btnText: 'View Scholarship Criteria',
      icon: Award,
      features: [
        'Direct educational grants & institutional fee remittance',
        'Transparent, audited merit-cum-means selection criteria',
        'Biannual open application cycles for secondary students',
        'Academic advisor paired to monitor educational continuity'
      ],
      isMarketplace: false
    },
    {
      id: 'mentorship',
      title: 'Academic Support & Mentorship',
      badge: 'Community Mentoring',
      badgeType: 'charitable',
      img: imgMentoring,
      desc: 'Focused doubt-clearing clinics and motivational guidance pairing students with verified volunteer mentors and university scholars to build subject confidence.',
      link: '/programmes/academic-support',
      btnText: 'Discover Mentorship',
      icon: Users,
      features: [
        'Weekend live doubt clinics & subject clarification',
        'Career pathway counseling & higher secondary guidance',
        'Safe, monitored digital channels strictly adhering to child safety',
        '100% volunteer-led and free for foundation beneficiaries'
      ],
      isMarketplace: false
    },
    {
      id: 'tutoring',
      title: 'Supplementary Tutoring Services',
      badge: 'Marketplace Service (Paid)',
      badgeType: 'marketplace',
      img: imgTutoring,
      desc: 'Independent paid marketplace offering personalized 1-on-1 and small-group live online classes operated by verified educators with tailored pacing.',
      link: '/tutoring',
      btnText: 'Find Marketplace Tutors',
      icon: Sparkles,
      features: [
        'Personalized 1-to-1 pacing tailored to student learning speed',
        'Interactive live whiteboard & real-time problem solving',
        'Risk-free trial demo class with verified educators',
        'Transparent per-session pricing with audited teacher ratings'
      ],
      isMarketplace: true
    }
  ];

  return (
    <div style={{ backgroundColor: 'var(--bg-jet-black)', color: 'var(--text-secondary)', overflowX: 'hidden' }}>
      
      {/* =========================================================================
          1. HERO SECTION (Our Programmes — Real Support. Real Opportunities.)
          ========================================================================= */}
      <section className="programmes-hero-section">
        {/* Desktop Background photo & gradient overlay */}
        <div 
          ref={heroBgRef}
          className="programmes-hero-desktop-bg"
          style={{ backgroundImage: `url(${heroImg})` }}
        >
          {/* Radial + horizontal fade into Jet Black */}
          <div style={{
            position: 'absolute',
            inset: 0,
            background: 'linear-gradient(90deg, #050505 0%, rgba(5,5,5,0.85) 30%, rgba(5,5,5,0.3) 65%, transparent 100%), linear-gradient(180deg, rgba(5,5,5,0.4) 0%, transparent 40%, #050505 100%)'
          }} />
        </div>

        {/* Desktop Cursive script in top-right with continuous floating */}
        <div ref={cursiveRef} className="programmes-hero-cursive anim-cursive-float">
          Real<br />
          <span style={{ color: '#00E5FF' }}>Support.</span><br />
          Real<br />
          <span style={{ color: '#FFFFFF' }}>Opportunities.</span>
        </div>

        <div className="container" style={{ position: 'relative', zIndex: 2, width: '100%' }}>
          {/* Breadcrumb */}
          <div style={{ display: 'flex', alignItems: 'center', gap: '8px', fontSize: '0.85rem', color: 'rgba(255,255,255,0.6)', marginBottom: '18px' }}>
            <Link to="/" style={{ color: 'rgba(255,255,255,0.6)', textDecoration: 'none' }} className="hover:text-white">Home</Link>
            <span style={{ color: 'var(--electric-cyan)' }}>&gt;</span>
            <span style={{ color: '#FFFFFF', fontWeight: 500 }}>Our Programmes</span>
          </div>

          <div style={{ maxWidth: '640px' }}>
            <h1 style={{ 
              fontSize: 'clamp(2.2rem, 5vw, 3.8rem)', 
              fontWeight: 800, 
              color: '#FFFFFF', 
              lineHeight: 1.1, 
              letterSpacing: '-0.02em',
              marginBottom: '8px'
            }}>
              {['Our', 'Programmes'].map((word, idx) => (
                <span 
                  key={idx} 
                  ref={(el) => (heroWordsRef.current[idx] = el)}
                  style={{ display: 'inline-block', marginRight: '0.25em' }}
                >
                  {word}
                </span>
              ))}
            </h1>
            <h2 style={{ 
              fontSize: 'clamp(1.8rem, 4.2vw, 3.2rem)', 
              fontWeight: 800, 
              color: 'var(--electric-cyan)', 
              lineHeight: 1.15, 
              letterSpacing: '-0.02em',
              marginBottom: '20px'
            }}>
              {['Real', 'Support.', 'Real', 'Opportunities.'].map((word, idx) => (
                <span 
                  key={idx} 
                  ref={(el) => (cyanWordsRef.current[idx] = el)}
                  style={{ display: 'inline-block', marginRight: '0.25em' }}
                >
                  {word}
                </span>
              ))}
            </h2>
            
            <p 
              ref={heroSubRef}
              style={{ 
                fontSize: 'clamp(0.95rem, 1.8vw, 1.05rem)', 
                color: 'rgba(255,255,255,0.78)', 
                lineHeight: 1.7, 
                marginBottom: '28px',
                maxWidth: '560px'
              }}
            >
              Scholarbee Foundation operates charitable educational support initiatives designed to reduce learning barriers for eligible students across India, while maintaining clear institutional transparency with independent supplementary services.
            </p>

            <div ref={heroBtnRef} style={{ display: 'flex', gap: '14px', flexWrap: 'wrap' }}>
              <a href="#charitable-programmes" className="btn btn-cyan" style={{ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '14px 26px', fontSize: '0.94rem', fontWeight: 600 }}>
                Explore Charitable Programmes <ArrowRight size={16} />
              </a>
              <Link to="/tutoring" className="btn btn-outline" style={{ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '14px 24px', fontSize: '0.94rem', fontWeight: 600 }}>
                Paid Tutoring Services <ExternalLink size={15} />
              </Link>
            </div>

            {/* Mobile Visual Banner (screens <= 900px) */}
            <div ref={heroMobileImgRef} className="programmes-hero-mobile-visual">
              <img src={heroImg} alt="Indian school learners engaged in digital study" />
              <div className="about-hero-mobile-cursive anim-mobile-cursive-float">
                Real <span style={{ color: '#00E5FF' }}>Support.</span><br />
                Real <span style={{ color: '#FFFFFF' }}>Opportunities.</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* =========================================================================
          2. INSTITUTIONAL SEPARATION & TRANSPARENCY NOTICE
          ========================================================================= */}
      <section style={{ padding: '24px 0', backgroundColor: 'rgba(10, 12, 22, 0.7)', borderBottom: '1px solid rgba(255,255,255,0.06)' }}>
        <div className="container">
          <div style={{
            display: 'flex',
            alignItems: 'center',
            gap: '16px',
            backgroundColor: 'rgba(0, 229, 255, 0.04)',
            border: '1px solid rgba(0, 229, 255, 0.18)',
            borderRadius: '12px',
            padding: '16px 20px',
            flexWrap: 'wrap'
          }}>
            <div style={{ color: 'var(--electric-cyan)', flexShrink: 0 }}>
              <ShieldCheck size={24} />
            </div>
            <div style={{ flex: 1, minWidth: '260px', fontSize: '0.88rem', color: 'rgba(255,255,255,0.78)', lineHeight: 1.5 }}>
              <strong style={{ color: '#FFFFFF' }}>Institutional Governance Note: </strong>
              Scholarbee Foundation is a Section 8 nonprofit organization. Foundation initiatives (Free Online Learning, Scholarships, Academic Mentorship) are strictly non-commercial and provided free to eligible beneficiaries. Supplementary Tutoring is operated separately as an optional paid marketplace.
            </div>
            <Link to="/governance" style={{ color: 'var(--electric-cyan)', fontSize: '0.85rem', fontWeight: 600, textDecoration: 'none', whiteSpace: 'nowrap' }} className="hover:underline">
              Governance Policy &rarr;
            </Link>
          </div>
        </div>
      </section>

      {/* =========================================================================
          3. CORE PROGRAMMES GRID (4 Cards)
          ========================================================================= */}
      <section id="charitable-programmes" ref={cardsSectionRef} style={{ padding: 'clamp(60px, 8vw, 90px) 0', borderBottom: '1px solid rgba(255,255,255,0.06)' }}>
        <div className="container">
          <div style={{ maxWidth: '720px', marginBottom: '48px' }}>
            <div className="eyebrow" style={{ color: 'var(--electric-cyan)', marginBottom: '10px' }}>
              INTERVENTIONS FOR IMPACT
            </div>
            <h2 style={{ fontSize: 'clamp(1.8rem, 3.2vw, 2.6rem)', fontWeight: 800, color: '#FFFFFF', lineHeight: 1.2, marginBottom: '16px' }}>
              Direct Educational Programmes Designed for <span style={{ color: 'var(--electric-cyan)' }}>Real Growth</span>
            </h2>
            <p style={{ fontSize: 'clamp(0.92rem, 1.8vw, 1rem)', color: 'rgba(255,255,255,0.72)', lineHeight: 1.65 }}>
              Explore how Scholarbee Foundation addresses the key barriers in Indian school education through accessible content, financial grants, and personalized educator support.
            </p>
          </div>

          <div className="programmes-cards-grid">
            {programmes.map((p, idx) => {
              const Icon = p.icon;
              return (
                <div
                  key={idx}
                  ref={(el) => (programmeCardsRef.current[idx] = el)}
                  style={{
                    backgroundColor: p.isMarketplace ? 'rgba(22, 10, 42, 0.7)' : 'rgba(12, 14, 24, 0.75)',
                    border: p.isMarketplace ? '1px solid rgba(138, 43, 226, 0.35)' : '1px solid rgba(255,255,255,0.08)',
                    borderRadius: '20px',
                    overflow: 'hidden',
                    display: 'flex',
                    flexDirection: 'column',
                    transition: 'transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease'
                  }}
                  className="hover:scale-101 hover:border-cyan"
                >
                  {/* Photo Header */}
                  <div style={{ position: 'relative', width: '100%', aspectRatio: '16 / 9', overflow: 'hidden' }}>
                    <img 
                      src={p.img} 
                      alt={p.title} 
                      style={{ width: '100%', height: '100%', objectFit: 'cover', display: 'block' }} 
                    />
                    <div style={{
                      position: 'absolute',
                      inset: 0,
                      background: 'linear-gradient(180deg, transparent 40%, rgba(5,5,10,0.85) 100%)'
                    }} />

                    {/* Badge */}
                    <div style={{
                      position: 'absolute',
                      top: '16px',
                      left: '16px',
                      backgroundColor: p.isMarketplace ? 'rgba(59, 10, 107, 0.9)' : 'rgba(0, 229, 255, 0.15)',
                      border: p.isMarketplace ? '1px solid rgba(138, 43, 226, 0.6)' : '1px solid rgba(0, 229, 255, 0.45)',
                      backdropFilter: 'blur(8px)',
                      color: p.isMarketplace ? '#E0B0FF' : 'var(--electric-cyan)',
                      padding: '5px 12px',
                      borderRadius: '999px',
                      fontSize: '0.74rem',
                      fontWeight: 700,
                      letterSpacing: '0.06em',
                      textTransform: 'uppercase'
                    }}>
                      {p.badge}
                    </div>
                  </div>

                  {/* Card Body */}
                  <div style={{ padding: 'clamp(22px, 3.5vw, 30px)', display: 'flex', flexDirection: 'column', flex: 1 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '12px', marginBottom: '14px' }}>
                      <div style={{
                        width: '38px',
                        height: '38px',
                        borderRadius: '10px',
                        backgroundColor: p.isMarketplace ? 'rgba(138, 43, 226, 0.2)' : 'rgba(0, 229, 255, 0.1)',
                        border: p.isMarketplace ? '1px solid rgba(138, 43, 226, 0.4)' : '1px solid rgba(0, 229, 255, 0.3)',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        color: p.isMarketplace ? '#E0B0FF' : 'var(--electric-cyan)'
                      }}>
                        <Icon size={20} />
                      </div>
                      <h3 style={{ fontSize: 'clamp(1.2rem, 2.2vw, 1.45rem)', fontWeight: 700, color: '#FFFFFF' }}>
                        {p.title}
                      </h3>
                    </div>

                    <p style={{ fontSize: '0.92rem', color: 'rgba(255,255,255,0.7)', lineHeight: 1.65, marginBottom: '24px' }}>
                      {p.desc}
                    </p>

                    {/* Features list */}
                    <div style={{ 
                      display: 'flex', 
                      flexDirection: 'column', 
                      gap: '10px', 
                      marginBottom: '28px',
                      paddingTop: '16px',
                      borderTop: '1px solid rgba(255,255,255,0.06)',
                      flex: 1
                    }}>
                      {p.features.map((feat, i) => (
                        <div key={i} style={{ display: 'flex', alignItems: 'flex-start', gap: '10px', fontSize: '0.86rem', color: 'rgba(255,255,255,0.78)' }}>
                          <Check size={16} color={p.isMarketplace ? '#C084FC' : 'var(--electric-cyan)'} style={{ marginTop: '2px', flexShrink: 0 }} />
                          <span>{feat}</span>
                        </div>
                      ))}
                    </div>

                    {/* CTA */}
                    <Link
                      to={p.link}
                      className={p.isMarketplace ? 'btn btn-purple' : 'btn btn-cyan'}
                      style={{ 
                        display: 'inline-flex', 
                        alignItems: 'center', 
                        justifyContent: 'center', 
                        gap: '8px', 
                        padding: '13px 20px', 
                        fontSize: '0.92rem', 
                        fontWeight: 600,
                        width: '100%'
                      }}
                    >
                      {p.btnText} <ArrowRight size={16} />
                    </Link>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* =========================================================================
          4. HOW OUR PROGRAMMES HELP (4 Pillars)
          ========================================================================= */}
      <section ref={helpSectionRef} style={{ padding: 'clamp(60px, 8vw, 85px) 0', borderBottom: '1px solid rgba(255,255,255,0.06)' }}>
        <div className="container">
          <div style={{ textAlign: 'center', maxWidth: '700px', margin: '0 auto 48px auto' }}>
            <div className="eyebrow" style={{ color: 'var(--electric-cyan)', marginBottom: '10px' }}>
              HOLISTIC IMPACT
            </div>
            <h2 style={{ fontSize: 'clamp(1.8rem, 3.2vw, 2.5rem)', fontWeight: 800, color: '#FFFFFF', lineHeight: 1.2, marginBottom: '14px' }}>
              How Our Programmes Drive <span style={{ color: 'var(--electric-cyan)' }}>Real Progress</span>
            </h2>
            <p style={{ fontSize: 'clamp(0.9rem, 1.8vw, 0.96rem)', color: 'rgba(255,255,255,0.72)', lineHeight: 1.65 }}>
              Each programme is architected to address specific pain points across accessibility, financial continuity, and personalized learning confidence.
            </p>
          </div>

          <div className="programmes-help-grid">
            {[
              {
                icon: <Compass size={24} color="var(--electric-cyan)" />,
                title: 'Access to Learning',
                desc: 'Overcoming geographic and socioeconomic isolation through mobile-first digital learning modules and free study materials.'
              },
              {
                icon: <Lightbulb size={24} color="var(--electric-cyan)" />,
                title: 'Confidence & Support',
                desc: 'One-on-one mentorship clinics and structured doubt-clearing sessions that empower students to master tough subjects.'
              },
              {
                icon: <HeartHandshake size={24} color="var(--electric-cyan)" />,
                title: 'Stronger Communities',
                desc: 'Engaging families, teachers, and university volunteer scholars to establish a nurturing circle of educational accountability.'
              },
              {
                icon: <TrendingUp size={24} color="var(--electric-cyan)" />,
                title: 'Long-Term Opportunity',
                desc: 'Preventing school dropouts through timely scholarship grants, paving smooth pathways toward vocational and university degrees.'
              }
            ].map((col, idx) => (
              <div
                key={idx}
                ref={(el) => (helpCardsRef.current[idx] = el)}
                style={{
                  backgroundColor: 'rgba(14, 16, 26, 0.65)',
                  border: '1px solid rgba(255,255,255,0.08)',
                  borderRadius: '16px',
                  padding: 'clamp(22px, 3.5vw, 26px)',
                  display: 'flex',
                  flexDirection: 'column'
                }}
              >
                <div style={{
                  width: '46px',
                  height: '46px',
                  borderRadius: '12px',
                  backgroundColor: 'rgba(0, 229, 255, 0.08)',
                  border: '1.5px solid var(--electric-cyan)',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  marginBottom: '18px'
                }}>
                  {col.icon}
                </div>
                <h3 style={{ fontSize: '1.15rem', fontWeight: 700, color: '#FFFFFF', marginBottom: '10px' }}>
                  {col.title}
                </h3>
                <p style={{ fontSize: '0.86rem', color: 'rgba(255,255,255,0.68)', lineHeight: 1.6 }}>
                  {col.desc}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* =========================================================================
          5. ROADMAP: HOW TO GET SUPPORT (3 Simple Steps)
          ========================================================================= */}
      <section ref={roadmapSectionRef} style={{ padding: 'clamp(60px, 8vw, 85px) 0', borderBottom: '1px solid rgba(255,255,255,0.06)' }}>
        <div className="container">
          <div style={{ maxWidth: '680px', marginBottom: '44px' }}>
            <div className="eyebrow" style={{ color: 'var(--electric-cyan)', marginBottom: '10px' }}>
              HOW TO ACCESS PROGRAMMES
            </div>
            <h2 style={{ fontSize: 'clamp(1.8rem, 3.2vw, 2.5rem)', fontWeight: 800, color: '#FFFFFF', lineHeight: 1.2, marginBottom: '14px' }}>
              Simple, Transparent Steps to <span style={{ color: 'var(--electric-cyan)' }}>Receive Support</span>
            </h2>
            <p style={{ fontSize: '0.94rem', color: 'rgba(255,255,255,0.72)', lineHeight: 1.65 }}>
              Whether you are a student, parent, or school administrator, accessing Scholarbee Foundation resources is designed to be straightforward.
            </p>
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))', gap: '22px' }}>
            {[
              {
                step: '01',
                title: 'Choose a Programme',
                desc: 'Review the eligibility criteria for Free Online Learning, Scholarships, or Mentorship to find the right fit for your academic stage.'
              },
              {
                step: '02',
                title: 'Submit Simple Application',
                desc: 'Complete an online request form with basic student details and academic verification. No application or evaluation fees required.'
              },
              {
                step: '03',
                title: 'Begin Guided Learning',
                desc: 'Get immediate access to syllabus content or receive scholarship onboarding instructions from our dedicated support coordinators.'
              }
            ].map((s, idx) => (
              <div
                key={idx}
                ref={(el) => (roadmapCardsRef.current[idx] = el)}
                style={{
                  backgroundColor: 'rgba(12, 14, 24, 0.7)',
                  border: '1px solid rgba(255,255,255,0.08)',
                  borderRadius: '16px',
                  padding: '28px 24px',
                  position: 'relative'
                }}
              >
                <div style={{
                  fontSize: '1.8rem',
                  fontWeight: 900,
                  fontFamily: 'var(--font-heading)',
                  color: 'var(--electric-cyan)',
                  opacity: 0.85,
                  marginBottom: '14px'
                }}>
                  {s.step}
                </div>
                <h3 style={{ fontSize: '1.2rem', fontWeight: 700, color: '#FFFFFF', marginBottom: '10px' }}>
                  {s.title}
                </h3>
                <p style={{ fontSize: '0.88rem', color: 'rgba(255,255,255,0.68)', lineHeight: 1.6 }}>
                  {s.desc}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* =========================================================================
          6. FINAL CALL TO ACTION BANNER
          ========================================================================= */}
      <section style={{ padding: 'clamp(60px, 8vw, 90px) 0' }}>
        <div className="container">
          <div style={{
            background: 'linear-gradient(135deg, rgba(20, 5, 40, 0.85) 0%, rgba(8, 10, 20, 0.95) 100%)',
            border: '1px solid rgba(0, 229, 255, 0.25)',
            borderRadius: '24px',
            padding: 'clamp(36px, 6vw, 60px) clamp(24px, 5vw, 48px)',
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
            textAlign: 'center',
            boxShadow: '0 20px 50px rgba(0, 0, 0, 0.5), 0 0 30px rgba(0, 229, 255, 0.1)'
          }}>
            <div className="eyebrow" style={{ color: 'var(--electric-cyan)', marginBottom: '10px' }}>
              MAKE A DIFFERENCE
            </div>
            <h2 style={{ fontSize: 'clamp(1.8rem, 3.5vw, 2.7rem)', fontWeight: 800, color: '#FFFFFF', lineHeight: 1.2, marginBottom: '16px', maxWidth: '680px' }}>
              Help Us Expand Opportunities for <span style={{ color: 'var(--electric-cyan)' }}>Deserving Learners</span>
            </h2>
            <p style={{ fontSize: 'clamp(0.92rem, 1.8vw, 1.02rem)', color: 'rgba(255,255,255,0.78)', lineHeight: 1.65, maxWidth: '580px', marginBottom: '32px' }}>
              Whether through philanthropic grant funding, corporate CSR partnerships, or volunteering your time as an academic mentor, your involvement changes lives.
            </p>

            <div style={{ display: 'flex', gap: '14px', flexWrap: 'wrap', justifyContent: 'center' }}>
              <Link to="/donate" className="btn btn-cyan" style={{ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '14px 28px', fontSize: '0.94rem', fontWeight: 600 }}>
                Support Education (Donate) <ArrowRight size={16} />
              </Link>
              <Link to="/volunteer" className="btn btn-outline" style={{ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '14px 26px', fontSize: '0.94rem', fontWeight: 600 }}>
                Become a Volunteer Mentor <Users size={16} />
              </Link>
            </div>
          </div>
        </div>
      </section>

    </div>
  );
}
