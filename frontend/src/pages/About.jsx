import React, { useEffect, useRef } from 'react';
import { Link, useLocation } from 'react-router-dom';
import gsap from 'gsap';
import { 
  ArrowRight, 
  Target, 
  Eye, 
  Gem, 
  Check, 
  Building2, 
  FileText, 
  Calendar, 
  MapPin, 
  Users, 
  UserPlus 
} from 'lucide-react';

import heroImg from '../assets/about_hero_students.jpg';
import storyImg from '../assets/about_story_girl.jpg';
import leader1 from '../assets/about_leader_1.jpg';
import leader2 from '../assets/about_leader_2.jpg';
import leader3 from '../assets/about_leader_3.jpg';
import leader4 from '../assets/about_leader_4.jpg';

export default function About() {
  // GSAP Animation Refs
  const heroWordsRef = useRef([]);
  const cyanWordsRef = useRef([]);
  const heroSubRef = useRef(null);
  const heroBtnRef = useRef(null);
  const heroBgRef = useRef(null);
  const heroMobileImgRef = useRef(null);
  const cursiveRef = useRef(null);

  // Section Refs for Scroll-Triggered Reveal
  const purposeSectionRef = useRef(null);
  const purposeCardsRef = useRef([]);

  const identitySectionRef = useRef(null);
  const identityBadgesRef = useRef([]);

  const storySectionRef = useRef(null);
  const storyImgRef = useRef(null);
  const storyNarrativeRef = useRef(null);
  const milestoneLineRef = useRef(null);
  const milestoneItemsRef = useRef([]);

  const leadershipSectionRef = useRef(null);
  const leadershipCardsRef = useRef([]);

  const location = useLocation();

  useEffect(() => {
    if (location.hash === '#story' || location.pathname === '/our-story') {
      setTimeout(() => {
        const el = document.getElementById('story');
        if (el) {
          el.scrollIntoView({ behavior: 'smooth' });
        }
      }, 150);
    }
  }, [location]);

  useEffect(() => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    // 1. MadeToScale 01: Hero Reveal Master Timeline
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

    const titleWords = heroWordsRef.current.filter(Boolean);
    const cyanWords = cyanWordsRef.current.filter(Boolean);

    // Initial states
    gsap.set([...titleWords, ...cyanWords], { opacity: 0, y: 35, filter: 'blur(8px)' });
    gsap.set(heroSubRef.current, { opacity: 0, y: 18 });
    gsap.set(heroBtnRef.current, { opacity: 0, scale: 0.92, y: 12 });
    if (heroBgRef.current) gsap.set(heroBgRef.current, { opacity: 0, scale: 0.96 });
    if (heroMobileImgRef.current) gsap.set(heroMobileImgRef.current, { opacity: 0, y: 20 });
    if (cursiveRef.current) gsap.set(cursiveRef.current, { opacity: 0, scale: 0.85, rotate: -15 });

    // Sequence
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

    // 2. IntersectionObserver for Purpose Cards
    const purposeObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const cards = purposeCardsRef.current.filter(Boolean);
            gsap.fromTo(
              cards,
              { opacity: 0, y: 35, scale: 0.95 },
              { opacity: 1, y: 0, scale: 1, duration: 0.65, stagger: 0.14, ease: 'power2.out' }
            );
            purposeObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15 }
    );
    if (purposeSectionRef.current) purposeObserver.observe(purposeSectionRef.current);

    // 3. IntersectionObserver for Identity Statutory Badges
    const identityObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const badges = identityBadgesRef.current.filter(Boolean);
            gsap.fromTo(
              badges,
              { opacity: 0, y: 25, scale: 0.92 },
              { opacity: 1, y: 0, scale: 1, duration: 0.55, stagger: 0.09, ease: 'back.out(1.3)' }
            );
            identityObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15 }
    );
    if (identitySectionRef.current) identityObserver.observe(identitySectionRef.current);

    // 4. IntersectionObserver for Story Section & Milestones
    const storyObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            // Animate photo slide-in
            if (storyImgRef.current) {
              gsap.fromTo(
                storyImgRef.current,
                { opacity: 0, x: -30, scale: 0.96 },
                { opacity: 1, x: 0, scale: 1, duration: 0.7, ease: 'power2.out' }
              );
            }
            // Animate narrative fade-in
            if (storyNarrativeRef.current) {
              gsap.fromTo(
                storyNarrativeRef.current,
                { opacity: 0, y: 20 },
                { opacity: 1, y: 0, duration: 0.65, delay: 0.15, ease: 'power2.out' }
              );
            }
            // Animate timeline drawing
            if (milestoneLineRef.current) {
              gsap.fromTo(
                milestoneLineRef.current,
                { scaleY: 0, transformOrigin: 'top center' },
                { scaleY: 1, duration: 0.75, delay: 0.25, ease: 'power2.inOut' }
              );
            }
            // Stagger milestone items
            const mItems = milestoneItemsRef.current.filter(Boolean);
            gsap.fromTo(
              mItems,
              { opacity: 0, x: 20 },
              { opacity: 1, x: 0, duration: 0.55, stagger: 0.16, delay: 0.35, ease: 'power2.out' }
            );
            storyObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15 }
    );
    if (storySectionRef.current) storyObserver.observe(storySectionRef.current);

    // 5. IntersectionObserver for Leadership Cards
    const leadershipObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const cards = leadershipCardsRef.current.filter(Boolean);
            gsap.fromTo(
              cards,
              { opacity: 0, y: 30, scale: 0.93 },
              { opacity: 1, y: 0, scale: 1, duration: 0.6, stagger: 0.1, ease: 'back.out(1.25)' }
            );
            leadershipObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1 }
    );
    if (leadershipSectionRef.current) leadershipObserver.observe(leadershipSectionRef.current);

    return () => {
      purposeObserver.disconnect();
      identityObserver.disconnect();
      storyObserver.disconnect();
      leadershipObserver.disconnect();
    };
  }, []);

  return (
    <div style={{ backgroundColor: 'var(--bg-jet-black)', color: 'var(--text-secondary)', overflowX: 'hidden' }}>
      
      {/* =========================================================================
          1. HERO SECTION (People. Purpose. Possibility.)
          ========================================================================= */}
      <section className="about-hero-section">
        {/* Desktop Background photo & gradient overlay */}
        <div 
          ref={heroBgRef}
          className="about-hero-desktop-bg"
          style={{ backgroundImage: `url(${heroImg})` }}
        >
          {/* Radial + horizontal fade into Jet Black */}
          <div style={{
            position: 'absolute',
            inset: 0,
            background: 'linear-gradient(90deg, #050505 0%, rgba(5,5,5,0.85) 25%, rgba(5,5,5,0.3) 60%, transparent 100%), linear-gradient(180deg, rgba(5,5,5,0.4) 0%, transparent 40%, #050505 100%)'
          }} />
        </div>

        {/* Desktop Cursive script in top-right with subtle continuous floating */}
        <div ref={cursiveRef} className="about-hero-cursive anim-cursive-float">
          Better<br />
          <span style={{ color: '#00E5FF' }}>Learners</span><br />
          Brighter<br />
          <span style={{ color: '#FFFFFF' }}>Futures</span>
        </div>

        <div className="container" style={{ position: 'relative', zIndex: 2, width: '100%' }}>
          {/* Breadcrumb */}
          <div style={{ display: 'flex', alignItems: 'center', gap: '8px', fontSize: '0.85rem', color: 'rgba(255,255,255,0.6)', marginBottom: '18px' }}>
            <Link to="/" style={{ color: 'rgba(255,255,255,0.6)', textDecoration: 'none' }} className="hover:text-white">Home</Link>
            <span style={{ color: 'var(--electric-cyan)' }}>&gt;</span>
            <span style={{ color: '#FFFFFF', fontWeight: 500 }}>Who We Are</span>
          </div>

          <div style={{ maxWidth: '620px' }}>
            <h1 style={{ 
              fontSize: 'clamp(2.2rem, 5vw, 3.8rem)', 
              fontWeight: 800, 
              color: '#FFFFFF', 
              lineHeight: 1.1, 
              letterSpacing: '-0.02em',
              marginBottom: '8px'
            }}>
              {['Who', 'We', 'Are'].map((word, idx) => (
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
              fontSize: 'clamp(1.8rem, 4.2vw, 3.3rem)', 
              fontWeight: 800, 
              color: 'var(--electric-cyan)', 
              lineHeight: 1.15, 
              letterSpacing: '-0.02em',
              marginBottom: '20px'
            }}>
              {['People.', 'Purpose.', 'Possibility.'].map((word, idx) => (
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
              Scholarbee Foundation is a Section 8 nonprofit organisation committed to improving access to education through free online learning opportunities, scholarship initiatives and appropriate academic support for eligible learners.
            </p>

            <div ref={heroBtnRef}>
              <Link to="/programmes" className="btn btn-cyan" style={{ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '14px 28px', fontSize: '0.95rem', fontWeight: 600 }}>
                Our Programmes <ArrowRight size={18} />
              </Link>
            </div>

            {/* Mobile Visual Banner (renders on screens <= 900px) */}
            <div ref={heroMobileImgRef} className="about-hero-mobile-visual">
              <img src={heroImg} alt="Scholarbee Foundation learners smiling outdoors" />
              <div className="about-hero-mobile-cursive anim-mobile-cursive-float">
                Better <span style={{ color: '#00E5FF' }}>Learners</span><br />
                Brighter <span style={{ color: '#FFFFFF' }}>Futures</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* =========================================================================
          2. OUR PURPOSE SECTION (Education for a Brighter, More Inclusive Tomorrow)
          ========================================================================= */}
      <section ref={purposeSectionRef} style={{ padding: 'clamp(50px, 8vw, 80px) 0', borderBottom: '1px solid rgba(255,255,255,0.06)' }}>
        <div className="container">
          <div className="about-purpose-grid">
            {/* Left Narrative */}
            <div>
              <div className="eyebrow" style={{ color: 'var(--electric-cyan)', marginBottom: '12px' }}>
                OUR PURPOSE
              </div>
              <h2 style={{ fontSize: 'clamp(1.7rem, 3vw, 2.5rem)', fontWeight: 800, lineHeight: 1.2, color: '#FFFFFF', marginBottom: '18px' }}>
                Education for a Brighter,<br />
                <span style={{ color: 'var(--electric-cyan)' }}>More Inclusive Tomorrow</span>
              </h2>
              <p style={{ fontSize: 'clamp(0.9rem, 1.8vw, 0.96rem)', color: 'rgba(255,255,255,0.72)', lineHeight: 1.7 }}>
                We believe every child deserves the opportunity to learn, grow and build a confident future. Scholarbee Foundation works to reduce educational barriers and create meaningful opportunities for learners from underprivileged backgrounds.
              </p>
            </div>

            {/* Right Cards: Mission, Vision, Values */}
            <div style={{ 
              display: 'grid', 
              gridTemplateColumns: 'repeat(auto-fit, minmax(260px, 1fr))', 
              gap: '20px' 
            }}>
              {/* Card 1: Our Mission */}
              <div 
                ref={(el) => (purposeCardsRef.current[0] = el)}
                style={{
                  backgroundColor: 'rgba(16, 20, 30, 0.65)',
                  border: '1px solid rgba(255,255,255,0.08)',
                  borderRadius: '16px',
                  padding: 'clamp(20px, 4vw, 28px) clamp(18px, 3vw, 24px)',
                  display: 'flex',
                  flexDirection: 'column',
                  transition: 'transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease'
                }}
                className="hover:border-cyan"
              >
                <div style={{
                  width: '44px',
                  height: '44px',
                  borderRadius: '50%',
                  border: '1.5px solid var(--electric-cyan)',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  color: 'var(--electric-cyan)',
                  marginBottom: '18px',
                  boxShadow: '0 0 16px rgba(0, 229, 255, 0.15)'
                }}>
                  <Target size={22} />
                </div>
                <h3 style={{ fontSize: '1.2rem', fontWeight: 700, color: '#FFFFFF', marginBottom: '10px' }}>
                  Our Mission
                </h3>
                <p style={{ fontSize: '0.88rem', color: 'rgba(255,255,255,0.68)', lineHeight: 1.65 }}>
                  Improve access to education through free online learning, scholarship support and appropriate academic assistance for eligible learners.
                </p>
              </div>

              {/* Card 2: Our Vision */}
              <div 
                ref={(el) => (purposeCardsRef.current[1] = el)}
                style={{
                  backgroundColor: 'rgba(16, 20, 30, 0.65)',
                  border: '1px solid rgba(255,255,255,0.08)',
                  borderRadius: '16px',
                  padding: 'clamp(20px, 4vw, 28px) clamp(18px, 3vw, 24px)',
                  display: 'flex',
                  flexDirection: 'column',
                  transition: 'transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease'
                }}
                className="hover:border-cyan"
              >
                <div style={{
                  width: '44px',
                  height: '44px',
                  borderRadius: '50%',
                  border: '1.5px solid var(--electric-cyan)',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  color: 'var(--electric-cyan)',
                  marginBottom: '18px',
                  boxShadow: '0 0 16px rgba(0, 229, 255, 0.15)'
                }}>
                  <Eye size={22} />
                </div>
                <h3 style={{ fontSize: '1.2rem', fontWeight: 700, color: '#FFFFFF', marginBottom: '10px' }}>
                  Our Vision
                </h3>
                <p style={{ fontSize: '0.88rem', color: 'rgba(255,255,255,0.68)', lineHeight: 1.65 }}>
                  A more inclusive learning environment where financial circumstances are less likely to limit a child's access to educational opportunities.
                </p>
              </div>

              {/* Card 3: Our Values */}
              <div 
                ref={(el) => (purposeCardsRef.current[2] = el)}
                style={{
                  backgroundColor: 'rgba(16, 20, 30, 0.65)',
                  border: '1px solid rgba(255,255,255,0.08)',
                  borderRadius: '16px',
                  padding: 'clamp(20px, 4vw, 28px) clamp(18px, 3vw, 24px)',
                  display: 'flex',
                  flexDirection: 'column',
                  transition: 'transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease'
                }}
                className="hover:border-cyan"
              >
                <div style={{
                  width: '44px',
                  height: '44px',
                  borderRadius: '50%',
                  border: '1.5px solid var(--electric-cyan)',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  color: 'var(--electric-cyan)',
                  marginBottom: '18px',
                  boxShadow: '0 0 16px rgba(0, 229, 255, 0.15)'
                }}>
                  <Gem size={22} />
                </div>
                <h3 style={{ fontSize: '1.2rem', fontWeight: 700, color: '#FFFFFF', marginBottom: '12px' }}>
                  Our Values
                </h3>
                <ul style={{ listStyle: 'none', padding: 0, margin: 0, display: 'flex', flexDirection: 'column', gap: '8px' }}>
                  {[
                    'Access & Inclusion',
                    'Learner-Centred Support',
                    'Integrity & Transparency',
                    'Responsible Use of Technology',
                    'Accountability'
                  ].map((val, idx) => (
                    <li key={idx} style={{ display: 'flex', alignItems: 'center', gap: '8px', fontSize: '0.84rem', color: 'rgba(255,255,255,0.78)' }}>
                      <Check size={14} color="var(--electric-cyan)" strokeWidth={2.6} />
                      <span>{val}</span>
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* =========================================================================
          3. OUR IDENTITY SECTION (A Section 8 Nonprofit Organisation)
          ========================================================================= */}
      <section 
        ref={identitySectionRef}
        style={{ 
          padding: 'clamp(50px, 8vw, 70px) 0', 
          position: 'relative', 
          background: 'linear-gradient(135deg, rgba(20, 5, 40, 0.7) 0%, rgba(5, 5, 15, 0.95) 100%)',
          borderBottom: '1px solid rgba(255,255,255,0.06)'
        }}
      >
        {/* Subtle purple radial glow */}
        <div style={{
          position: 'absolute',
          top: '50%',
          left: '10%',
          width: '500px',
          height: '300px',
          background: 'radial-gradient(circle, rgba(100, 20, 180, 0.15) 0%, transparent 70%)',
          transform: 'translateY(-50%)',
          pointerEvents: 'none'
        }} />

        <div className="container" style={{ position: 'relative', zIndex: 2 }}>
          <div className="about-identity-grid">
            {/* Left Column */}
            <div>
              <div className="eyebrow" style={{ color: 'var(--electric-cyan)', marginBottom: '10px' }}>
                OUR IDENTITY
              </div>
              <h2 style={{ fontSize: 'clamp(1.6rem, 2.6vw, 2.2rem)', fontWeight: 800, color: '#FFFFFF', lineHeight: 1.25, marginBottom: '14px' }}>
                A Section 8<br />Nonprofit Organisation
              </h2>
              <p style={{ fontSize: 'clamp(0.88rem, 1.6vw, 0.92rem)', color: 'rgba(255,255,255,0.72)', lineHeight: 1.65, marginBottom: '22px' }}>
                Scholarbee Foundation is a legally registered Section 8 company in India, focused on educational and related nonprofit activities.
              </p>
              <Link to="/governance" className="btn btn-cyan" style={{ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '12px 22px', fontSize: '0.88rem', fontWeight: 600 }}>
                View Governance & Transparency <ArrowRight size={16} />
              </Link>
            </div>

            {/* Right Statutory Badges */}
            <div className="about-statutory-badges">
              {[
                { 
                  icon: <Building2 size={24} color="var(--electric-cyan)" />, 
                  title: 'Legal Status', 
                  desc: 'Section 8 Company (Nonprofit)' 
                },
                { 
                  icon: <FileText size={24} color="var(--electric-cyan)" />, 
                  title: 'CIN', 
                  desc: 'To be confirmed (Management to provide)' 
                },
                { 
                  icon: <Calendar size={24} color="var(--electric-cyan)" />, 
                  title: 'Incorporation Date', 
                  desc: 'To be confirmed (Management to provide)' 
                },
                { 
                  icon: <MapPin size={24} color="var(--electric-cyan)" />, 
                  title: 'Registered Office', 
                  desc: 'To be confirmed (Management to provide)' 
                },
                { 
                  icon: <Users size={24} color="var(--electric-cyan)" />, 
                  title: 'Governing Members', 
                  desc: 'To be confirmed (Management to provide)' 
                },
              ].map((item, idx) => (
                <div 
                  key={idx} 
                  ref={(el) => (identityBadgesRef.current[idx] = el)}
                  className="about-statutory-item"
                >
                  <div style={{ marginBottom: '12px' }}>{item.icon}</div>
                  <div style={{ fontSize: '0.88rem', fontWeight: 700, color: '#FFFFFF', marginBottom: '6px' }}>
                    {item.title}
                  </div>
                  <div style={{ fontSize: '0.78rem', color: 'rgba(255,255,255,0.58)', lineHeight: 1.4 }}>
                    {item.desc}
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* =========================================================================
          4. OUR STORY SECTION (Split Photo + Narrative + Key Milestones)
          ========================================================================= */}
      <section 
        id="story"
        ref={storySectionRef}
        style={{ padding: 'clamp(50px, 8vw, 80px) 0', borderBottom: '1px solid rgba(255,255,255,0.06)' }}
      >
        <div className="container">
          <div className="about-story-grid">
            {/* Left Photo of girl studying */}
            <div 
              ref={storyImgRef}
              style={{
                borderRadius: '16px',
                overflow: 'hidden',
                border: '1px solid rgba(255,255,255,0.1)',
                boxShadow: '0 12px 36px rgba(0,0,0,0.6)',
                aspectRatio: '4 / 3',
                width: '100%',
                maxHeight: '340px'
              }}
            >
              <img 
                src={storyImg} 
                alt="Indian schoolgirl studying in classroom" 
                style={{ width: '100%', height: '100%', objectFit: 'cover', display: 'block' }} 
              />
            </div>

            {/* Middle Narrative */}
            <div ref={storyNarrativeRef}>
              <div className="eyebrow" style={{ color: 'var(--electric-cyan)', marginBottom: '12px' }}>
                OUR STORY
              </div>
              <h2 style={{ fontSize: 'clamp(1.5rem, 2.5vw, 2.2rem)', fontWeight: 800, lineHeight: 1.25, color: '#FFFFFF', marginBottom: '16px' }}>
                A Journey Towards<br />
                <span style={{ color: 'var(--electric-cyan)' }}>Greater Educational Opportunity</span>
              </h2>
              <p style={{ fontSize: 'clamp(0.88rem, 1.6vw, 0.92rem)', color: 'rgba(255,255,255,0.72)', lineHeight: 1.7, marginBottom: '22px' }}>
                Scholarbee Foundation was established with the belief that education can open doors to confidence, opportunity and a better quality of life. We work with learners, families, volunteers, educators and partners to address key barriers to education through practical, focused and responsible initiatives.
              </p>
              <a 
                href="#story" 
                onClick={(e) => {
                  e.preventDefault();
                  document.getElementById('story')?.scrollIntoView({ behavior: 'smooth' });
                }} 
                className="btn btn-cyan" 
                style={{ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '12px 24px', fontSize: '0.9rem', fontWeight: 600 }}
              >
                Our Story <ArrowRight size={16} />
              </a>
            </div>

            {/* Right Key Milestones Timeline */}
            <div style={{
              backgroundColor: 'rgba(12, 14, 24, 0.6)',
              border: '1px solid rgba(255,255,255,0.08)',
              borderRadius: '16px',
              padding: 'clamp(20px, 4vw, 28px) clamp(18px, 3vw, 24px)'
            }}>
              <div style={{ fontSize: '0.8rem', fontWeight: 700, letterSpacing: '0.12em', color: 'var(--electric-cyan)', marginBottom: '20px' }}>
                KEY MILESTONES
              </div>

              <div style={{ display: 'flex', flexDirection: 'column', gap: '20px', position: 'relative' }}>
                {/* Vertical timeline line animated with GSAP */}
                <div 
                  ref={milestoneLineRef}
                  style={{
                    position: 'absolute',
                    top: '12px',
                    bottom: '12px',
                    left: '7px',
                    width: '2px',
                    backgroundColor: 'rgba(0, 229, 255, 0.45)',
                    boxShadow: '0 0 8px rgba(0, 229, 255, 0.4)',
                    zIndex: 1
                  }} 
                />

                {[
                  { year: '202X', text: 'Incorporated as a Section 8 company with a focus on education.' },
                  { year: '202X', text: 'Launched initial learning support initiatives.' },
                  { year: '202X', text: 'Expanding programmes and building stronger community partnerships.' }
                ].map((m, idx) => (
                  <div 
                    key={idx} 
                    ref={(el) => (milestoneItemsRef.current[idx] = el)}
                    style={{ display: 'flex', alignItems: 'flex-start', gap: '16px', position: 'relative', zIndex: 2 }}
                  >
                    <div 
                      className="anim-pulse-dot"
                      style={{
                        width: '16px',
                        height: '16px',
                        borderRadius: '50%',
                        backgroundColor: 'var(--electric-cyan)',
                        flexShrink: 0,
                        marginTop: '3px'
                      }} 
                    />
                    <div>
                      <div style={{ fontSize: '0.86rem', fontWeight: 700, color: 'var(--electric-cyan)', marginBottom: '3px' }}>
                        {m.year}
                      </div>
                      <div style={{ fontSize: '0.84rem', color: 'rgba(255,255,255,0.72)', lineHeight: 1.5 }}>
                        {m.text}
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* =========================================================================
          5. OUR LEADERSHIP SECTION (Guided by Experience, Driven by Impact)
          ========================================================================= */}
      <section 
        ref={leadershipSectionRef}
        style={{ padding: 'clamp(50px, 8vw, 80px) 0 clamp(60px, 8vw, 100px) 0' }}
      >
        <div className="container">
          {/* Top Header Row */}
          <div className="about-leadership-header">
            <div style={{ maxWidth: '640px' }}>
              <div className="eyebrow" style={{ color: 'var(--electric-cyan)', marginBottom: '12px' }}>
                OUR LEADERSHIP
              </div>
              <h2 style={{ fontSize: 'clamp(1.7rem, 3vw, 2.5rem)', fontWeight: 800, lineHeight: 1.2, color: '#FFFFFF', marginBottom: '12px' }}>
                Guided by Experience,<br />
                <span style={{ color: 'var(--electric-cyan)' }}>Driven by Impact</span>
              </h2>
              <p style={{ fontSize: 'clamp(0.88rem, 1.6vw, 0.94rem)', color: 'rgba(255,255,255,0.72)', lineHeight: 1.65 }}>
                Our leadership team brings together experience in education, technology, social development and organisational management to guide the Foundation's work.
              </p>
            </div>

            <Link to="/governance" className="btn btn-cyan" style={{ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '12px 24px', fontSize: '0.88rem', fontWeight: 600 }}>
              View Full Leadership & Governance <ArrowRight size={16} />
            </Link>
          </div>

          {/* Leadership Cards Grid (4 portraits + 1 Join Team card) */}
          <div className="about-leadership-grid">
            {[
              { img: leader1, role: 'Founder / Director' },
              { img: leader2, role: 'Director' },
              { img: leader3, role: 'Director' },
              { img: leader4, role: 'Advisory / Governing Member' },
            ].map((leader, idx) => (
              <div 
                key={idx}
                ref={(el) => (leadershipCardsRef.current[idx] = el)}
                style={{
                  position: 'relative',
                  borderRadius: '16px',
                  overflow: 'hidden',
                  aspectRatio: '1 / 1',
                  border: '1px solid rgba(255,255,255,0.08)',
                  backgroundColor: '#10121A',
                  transition: 'transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease'
                }}
                className="hover:scale-102 hover:border-cyan"
              >
                <img 
                  src={leader.img} 
                  alt={leader.role} 
                  style={{ width: '100%', height: '100%', objectFit: 'cover', display: 'block' }} 
                />
                
                {/* Bottom dark gradient overlay with role */}
                <div style={{
                  position: 'absolute',
                  inset: 0,
                  background: 'linear-gradient(180deg, transparent 50%, rgba(5,5,10,0.92) 85%, #05050A 100%)',
                  display: 'flex',
                  flexDirection: 'column',
                  justifyContent: 'flex-end',
                  padding: '14px 12px'
                }}>
                  <div style={{ fontSize: 'clamp(0.82rem, 1.8vw, 0.9rem)', fontWeight: 700, color: '#FFFFFF', marginBottom: '2px' }}>
                    To be confirmed
                  </div>
                  <div style={{ fontSize: 'clamp(0.72rem, 1.5vw, 0.78rem)', color: 'rgba(255,255,255,0.6)' }}>
                    {leader.role}
                  </div>
                </div>
              </div>
            ))}

            {/* 5th Card: Join Our Team */}
            <div 
              ref={(el) => (leadershipCardsRef.current[4] = el)}
              className="about-card-join-team"
              style={{
                borderRadius: '16px',
                padding: 'clamp(18px, 3.5vw, 24px) clamp(16px, 3vw, 20px)',
                background: 'linear-gradient(145deg, #1A0A33 0%, #0E0720 100%)',
                border: '1px solid rgba(138, 43, 226, 0.3)',
                display: 'flex',
                flexDirection: 'column',
                justifyContent: 'space-between',
                minHeight: '200px',
                transition: 'transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease'
              }}
            >
              <div>
                <div style={{
                  width: '40px',
                  height: '40px',
                  borderRadius: '50%',
                  border: '1.5px solid var(--electric-cyan)',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  color: 'var(--electric-cyan)',
                  marginBottom: '14px',
                  boxShadow: '0 0 16px rgba(0, 229, 255, 0.2)'
                }}>
                  <UserPlus size={18} />
                </div>
                <h3 style={{ fontSize: '1.1rem', fontWeight: 700, color: '#FFFFFF', marginBottom: '8px' }}>
                  Join Our Team
                </h3>
                <p style={{ fontSize: '0.8rem', color: 'rgba(255,255,255,0.7)', lineHeight: 1.5 }}>
                  We're always open to passionate individuals who want to contribute to our mission.
                </p>
              </div>

              <Link 
                to="/contact" 
                className="btn btn-cyan" 
                style={{ 
                  display: 'inline-flex', 
                  alignItems: 'center', 
                  justifyContent: 'center', 
                  gap: '6px', 
                  padding: '10px 16px', 
                  fontSize: '0.84rem', 
                  fontWeight: 600,
                  marginTop: '12px',
                  width: '100%'
                }}
              >
                Work With Us <ArrowRight size={14} />
              </Link>
            </div>
          </div>
        </div>
      </section>

    </div>
  );
}
