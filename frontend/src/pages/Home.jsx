import React, { useState, useEffect, useRef } from 'react';
import { Link } from 'react-router-dom';
import gsap from 'gsap';
import { 
  GraduationCap, BookOpen, Award, Users, HeartHandshake, ShieldCheck, 
  ArrowRight, Sparkles, CheckCircle2, ChevronRight, Globe, Compass, 
  MapPin, Heart, Briefcase, Mail, Phone, ChevronLeft, Building2,
  FileText, Shield, Laptop, IndianRupee, Layers, Check
} from 'lucide-react';

// Animation Components based on MadeToScale Guide
import CountUpStats from '../components/animations/CountUpStats';
import ReviewShuffle from '../components/animations/ReviewShuffle';
import ProcessDraw from '../components/animations/ProcessDraw';

// Assets
import heroGirl from '../assets/home_hero_girl.jpg';
import challengeBoy from '../assets/challenge_boy.jpg';
import progOnlineLearning from '../assets/prog_online_learning.jpg';
import progScholarships from '../assets/prog_scholarships.jpg';
import progMentoring from '../assets/prog_mentoring.jpg';
import progTutoring from '../assets/prog_tutoring.jpg';
import communityGirls from '../assets/community_girls.jpg';
import studentHero from '../assets/student_hero.jpg';

export default function Home() {
  const [openFaq, setOpenFaq] = useState(0);
  const heroWordsRef = useRef([]);
  const heroSubRef = useRef(null);
  const heroButtonsRef = useRef(null);
  const heroCardRef = useRef(null);

  // 01 / 12 — Hero Reveal Animation (under 2.2s total)
  useEffect(() => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    const words = heroWordsRef.current.filter(Boolean);
    if (words.length === 0) return;

    const tl = gsap.timeline();
    tl.fromTo(
      words,
      { opacity: 0, y: 35, filter: 'blur(8px)' },
      { opacity: 1, y: 0, filter: 'blur(0px)', duration: 0.65, stagger: 0.08, ease: 'power3.out' }
    )
      .fromTo(
        heroSubRef.current,
        { opacity: 0, y: 15 },
        { opacity: 1, y: 0, duration: 0.6, ease: 'power2.out' },
        '-=0.3'
      )
      .fromTo(
        heroButtonsRef.current,
        { opacity: 0, y: 15 },
        { opacity: 1, y: 0, duration: 0.5, ease: 'power2.out' },
        '-=0.3'
      )
      .fromTo(
        heroCardRef.current,
        { opacity: 0, scale: 0.94, y: 20 },
        { opacity: 1, scale: 1, y: 0, duration: 0.8, ease: 'power3.out' },
        '-=0.6'
      );
  }, []);

  const faqs = [
    {
      q: 'What is Scholarbee Foundation?',
      a: 'Scholarbee Foundation is a Section 8 nonprofit organisation in India working towards improving access to education through free online learning, scholarship initiatives, and appropriate academic support for eligible learners.'
    },
    {
      q: 'Who can apply for the free learning programmes?',
      a: 'Any student enrolled in recognized school education across India can access our open digital modules. Targeted grant and mentorship programmes are open to eligible students based on merit and financial need.'
    },
    {
      q: 'How do scholarships work?',
      a: 'Our scholarships provide direct financial assistance to reduce educational expenses, covering tuition fees and academic supplies directly with recognized institutions.'
    },
    {
      q: 'Do you provide tutoring services?',
      a: 'Yes, we provide supplementary private 1-on-1 and group online tutoring with verified independent teachers. This is an optional service clearly separated from our charitable programmes.'
    },
    {
      q: 'How can I volunteer?',
      a: 'Educators, university scholars, and professionals can contribute their time to mentor students, review curriculum, or conduct doubt resolution clinics.'
    },
    {
      q: 'How can I donate?',
      a: 'Individuals and CSR organizations can contribute through our secure online donation portal or via direct bank transfer to fund student scholarships.'
    },
    {
      q: 'Where can I find governance information?',
      a: 'Our statutory filings, annual disclosures, and organizational charter are publicly accessible on our Governance & Transparency page.'
    }
  ];

  return (
    <div style={{ backgroundColor: 'var(--bg-jet-black)', color: 'var(--text-secondary)' }}>
      {/* ================= 1. HERO SECTION ================= */}
      <section
        style={{
          position: 'relative',
          paddingTop: 'calc(var(--nav-height) + 40px)',
          paddingBottom: '60px',
          overflow: 'hidden',
        }}
      >
        {/* Ambient Glows */}
        <div
          style={{
            position: 'absolute',
            top: '5%',
            left: '30%',
            width: '700px',
            height: '400px',
            background: 'radial-gradient(circle, rgba(59, 10, 107, 0.45) 0%, rgba(0, 229, 255, 0.08) 50%, transparent 70%)',
            filter: 'blur(90px)',
            pointerEvents: 'none',
          }}
        />

        <div className="container" style={{ position: 'relative', zIndex: 1 }}>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '48px', alignItems: 'center', marginBottom: '60px' }}>
            {/* Left Hero Text */}
            <div style={{ maxWidth: '620px' }}>
              <div className="eyebrow">
                <Sparkles size={14} />
                EDUCATION • ACCESS • OPPORTUNITY
              </div>

              <h1 className="h1-display" style={{ marginBottom: '18px' }}>
                {['Making', 'Quality', 'Education'].map((w, i) => (
                  <span
                    key={i}
                    ref={(el) => (heroWordsRef.current[i] = el)}
                    style={{ display: 'inline-block', marginRight: '0.28em' }}
                  >
                    {w}
                  </span>
                ))}
                <span
                  ref={(el) => (heroWordsRef.current[3] = el)}
                  className="text-cyan"
                  style={{ display: 'inline-block' }}
                >
                  Accessible to Every Child
                </span>
              </h1>

              <p
                ref={heroSubRef}
                style={{ fontSize: '1.1rem', color: 'var(--text-secondary)', lineHeight: 1.65, marginBottom: '32px' }}
              >
                Scholarbee Foundation is a Section 8 nonprofit organisation working to improve access to education through free online learning, scholarship initiatives and appropriate academic support for eligible learners.
              </p>

              <div ref={heroButtonsRef} style={{ display: 'flex', flexWrap: 'wrap', gap: '14px' }}>
                <Link to="/programmes" className="btn btn-cyan" style={{ fontSize: '0.98rem', padding: '12px 26px' }}>
                  Explore Our Programmes <ArrowRight size={16} />
                </Link>
                <Link to="/contact" className="btn btn-outline" style={{ fontSize: '0.98rem', padding: '12px 26px' }}>
                  Contact the Foundation
                </Link>
              </div>
            </div>

            {/* Right Hero Image with Script Overlay */}
            <div ref={heroCardRef} style={{ display: 'flex', justifyContent: 'center', position: 'relative' }}>
              <div
                style={{
                  position: 'relative',
                  width: '100%',
                  maxWidth: '520px',
                  borderRadius: 'var(--radius-container)',
                  overflow: 'hidden',
                  border: '1px solid rgba(0, 229, 255, 0.3)',
                  boxShadow: 'var(--shadow-card), 0 0 50px rgba(0, 229, 255, 0.25)',
                  background: 'var(--surface-2)',
                }}
              >
                <img
                  src={heroGirl}
                  alt="Young Indian Schoolgirl Smiling With Hope"
                  style={{
                    width: '100%',
                    height: 'auto',
                    display: 'block',
                    objectFit: 'cover',
                  }}
                />

                {/* Cursive Tagline Overlay */}
                <div
                  style={{
                    position: 'absolute',
                    top: '24px',
                    right: '24px',
                    fontFamily: 'cursive, "Brush Script MT", Georgia, sans-serif',
                    fontSize: '1.4rem',
                    color: '#00E5FF',
                    textShadow: '0 2px 10px rgba(0,0,0,0.8), 0 0 15px rgba(0,229,255,0.6)',
                    transform: 'rotate(-6deg)',
                    lineHeight: 1.2,
                    textAlign: 'right',
                  }}
                >
                  A Brighter<br />Tomorrow Together
                </div>
              </div>
            </div>
          </div>

          {/* 5 Foundation Principles Strip */}
          <div
            style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))',
              gap: '14px',
              borderTop: '1px solid var(--border-subtle)',
              paddingTop: '28px',
            }}
          >
            {[
              { icon: Users, title: 'Inclusive Opportunities' },
              { icon: BookOpen, title: 'Learner-Centred Support' },
              { icon: ShieldCheck, title: 'Transparent & Accountable' },
              { icon: Globe, title: 'Community Driven' },
              { icon: Award, title: 'Brighter Futures' },
            ].map((item, idx) => {
              const Icon = item.icon;
              return (
                <div
                  key={idx}
                  className="card-surface-3"
                  style={{
                    display: 'flex',
                    alignItems: 'center',
                    gap: '12px',
                    padding: '16px 20px',
                    borderRadius: 'var(--radius-md)',
                  }}
                >
                  <div style={{ color: 'var(--electric-cyan)', flexShrink: 0 }}>
                    <Icon size={22} />
                  </div>
                  <span style={{ fontSize: '0.9rem', color: '#FFF', fontWeight: 600 }}>{item.title}</span>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* ================= 2. WHO WE ARE ================= */}
      <section className="section" style={{ backgroundColor: 'var(--surface-1)' }}>
        <div className="container">
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '48px', alignItems: 'center' }}>
            <div>
              <div className="eyebrow">WHO WE ARE</div>
              <h2 className="h2-section" style={{ marginBottom: '18px' }}>
                An Indian Nonprofit Committed to Educational Opportunity
              </h2>
              <p style={{ fontSize: '1.05rem', color: 'var(--text-secondary)', lineHeight: 1.7, marginBottom: '20px' }}>
                Scholarbee Foundation is a Section 8 organisation working towards improved access to educational opportunities for learners from underprivileged backgrounds. We believe that every child, regardless of their financial circumstances, should have the opportunity to learn, grow and build a brighter future.
              </p>
              <Link to="/about" className="btn btn-cyan">
                Learn More About Us <ArrowRight size={16} />
              </Link>
            </div>

            {/* 3 Pillars Cards */}
            <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
              <div className="card-surface-3" style={{ borderLeft: '3px solid var(--electric-cyan)' }}>
                <h4 style={{ fontSize: '1.1rem', color: '#FFF', marginBottom: '6px' }}>Our Mission</h4>
                <p style={{ fontSize: '0.88rem', color: 'var(--text-muted)', lineHeight: 1.6 }}>
                  To improve access to education through free online learning, scholarship support and appropriate academic assistance.
                </p>
              </div>

              <div className="card-surface-3" style={{ borderLeft: '3px solid var(--royal-amethyst)' }}>
                <h4 style={{ fontSize: '1.1rem', color: '#FFF', marginBottom: '6px' }}>Our Vision</h4>
                <p style={{ fontSize: '0.88rem', color: 'var(--text-muted)', lineHeight: 1.6 }}>
                  A more inclusive learning environment where financial circumstances are less likely to limit educational opportunity.
                </p>
              </div>

              <div className="card-surface-3" style={{ borderLeft: '3px solid var(--electric-cyan)' }}>
                <h4 style={{ fontSize: '1.1rem', color: '#FFF', marginBottom: '6px' }}>Our Values</h4>
                <div style={{ display: 'flex', flexWrap: 'wrap', gap: '8px 16px', fontSize: '0.85rem', color: 'var(--text-secondary)', marginTop: '8px' }}>
                  <span>✓ Access & Inclusion</span>
                  <span>✓ Learner-Centred Support</span>
                  <span>✓ Integrity & Transparency</span>
                  <span>✓ Responsible Tech</span>
                  <span>✓ Accountability</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ================= 3. THE EDUCATION CHALLENGE ================= */}
      <section className="section">
        <div className="container">
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '48px', alignItems: 'center', marginBottom: '48px' }}>
            {/* Image of Schoolboy Writing */}
            <div style={{ borderRadius: 'var(--radius-card-lg)', overflow: 'hidden', border: '1px solid var(--border-card)', boxShadow: 'var(--shadow-card)' }}>
              <img
                src={challengeBoy}
                alt="Indian student at classroom desk"
                style={{ width: '100%', height: 'auto', display: 'block', objectFit: 'cover' }}
              />
            </div>

            {/* Right text */}
            <div>
              <div className="eyebrow">THE EDUCATION CHALLENGE</div>
              <h2 className="h2-section" style={{ marginBottom: '18px' }}>
                Every Learner Deserves a Fair Chance to Learn
              </h2>
              <p style={{ fontSize: '1.05rem', color: 'var(--text-secondary)', lineHeight: 1.7, marginBottom: '28px' }}>
                Many children in India continue to face barriers to quality education. Financial constraints, limited access to learning support and unequal opportunities can make it difficult for learners to reach their full potential.
              </p>
              <Link to="/education-challenge" className="btn btn-cyan">
                Learn More <ArrowRight size={16} />
              </Link>
            </div>
          </div>

          {/* 4 Barrier Cards */}
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(240px, 1fr))', gap: '20px' }}>
            <div className="card-surface-3">
              <div style={{ color: 'var(--electric-cyan)', marginBottom: '12px' }}><BookOpen size={24} /></div>
              <h4 style={{ fontSize: '1.1rem', color: '#FFF', marginBottom: '8px' }}>Limited Access</h4>
              <p style={{ fontSize: '0.86rem', color: 'var(--text-muted)', lineHeight: 1.6 }}>Quality learning opportunities are not equally available to all learners.</p>
            </div>

            <div className="card-surface-3">
              <div style={{ color: 'var(--electric-cyan)', marginBottom: '12px' }}><IndianRupee size={24} /></div>
              <h4 style={{ fontSize: '1.1rem', color: '#FFF', marginBottom: '8px' }}>Affordability</h4>
              <p style={{ fontSize: '0.86rem', color: 'var(--text-muted)', lineHeight: 1.6 }}>Education-related costs can be a significant challenge for many families.</p>
            </div>

            <div className="card-surface-3">
              <div style={{ color: 'var(--electric-cyan)', marginBottom: '12px' }}><Users size={24} /></div>
              <h4 style={{ fontSize: '1.1rem', color: '#FFF', marginBottom: '8px' }}>Need for Support</h4>
              <p style={{ fontSize: '0.86rem', color: 'var(--text-muted)', lineHeight: 1.6 }}>Many learners benefit from additional academic support beyond regular schooling.</p>
            </div>

            <div className="card-surface-3">
              <div style={{ color: 'var(--electric-cyan)', marginBottom: '12px' }}><Globe size={24} /></div>
              <h4 style={{ fontSize: '1.1rem', color: '#FFF', marginBottom: '8px' }}>Digital Divide</h4>
              <p style={{ fontSize: '0.86rem', color: 'var(--text-muted)', lineHeight: 1.6 }}>Access to technology and reliable internet can remain a barrier for many learners.</p>
            </div>
          </div>
        </div>
      </section>

      {/* ================= 4. OUR PROGRAMMES ================= */}
      <section className="section" style={{ backgroundColor: 'var(--surface-1)' }}>
        <div className="container">
          <div style={{ display: 'flex', flexWrap: 'wrap', alignItems: 'flex-end', justifyContent: 'space-between', gap: '20px', marginBottom: '48px' }}>
            <div>
              <div className="eyebrow">OUR PROGRAMMES</div>
              <h2 className="h2-section">Real Support. Real Opportunities.</h2>
              <p style={{ color: 'var(--text-muted)', fontSize: '1rem', marginTop: '8px' }}>
                Our programmes are designed to help learners overcome educational barriers and build a brighter future.
              </p>
            </div>
            <Link to="/programmes" className="btn btn-cyan">
              Explore All Programmes <ArrowRight size={16} />
            </Link>
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))', gap: '24px' }}>
            {/* Card 1 */}
            <div className="card-base" style={{ padding: 0, overflow: 'hidden', display: 'flex', flexDirection: 'column' }}>
              <img src={progOnlineLearning} alt="Free Online Learning" style={{ width: '100%', height: '170px', objectFit: 'cover' }} />
              <div style={{ padding: '24px', display: 'flex', flexDirection: 'column', flex: 1 }}>
                <span style={{ fontSize: '0.72rem', textTransform: 'uppercase', letterSpacing: '0.1em', color: 'var(--electric-cyan)', fontWeight: 700, marginBottom: '6px' }}>
                  CHARITABLE PROGRAMME
                </span>
                <h3 style={{ fontSize: '1.25rem', color: '#FFF', marginBottom: '14px' }}>Free Online Learning</h3>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '8px', fontSize: '0.84rem', color: 'var(--text-muted)', marginBottom: '24px', flex: 1 }}>
                  <div>✓ Learning support for eligible learners</div>
                  <div>✓ Subject-based classes</div>
                  <div>✓ Individual or group learning</div>
                  <div>✓ Learning resources and guidance</div>
                </div>
                <Link to="/programmes/free-online-learning" style={{ color: 'var(--electric-cyan)', textDecoration: 'none', fontWeight: 600, fontSize: '0.9rem', display: 'flex', alignItems: 'center', gap: '4px' }}>
                  Learn More <ArrowRight size={14} />
                </Link>
              </div>
            </div>

            {/* Card 2 */}
            <div className="card-base" style={{ padding: 0, overflow: 'hidden', display: 'flex', flexDirection: 'column' }}>
              <img src={progScholarships} alt="Scholarships & Assistance" style={{ width: '100%', height: '170px', objectFit: 'cover' }} />
              <div style={{ padding: '24px', display: 'flex', flexDirection: 'column', flex: 1 }}>
                <span style={{ fontSize: '0.72rem', textTransform: 'uppercase', letterSpacing: '0.1em', color: 'var(--electric-cyan)', fontWeight: 700, marginBottom: '6px' }}>
                  CHARITABLE PROGRAMME
                </span>
                <h3 style={{ fontSize: '1.25rem', color: '#FFF', marginBottom: '14px' }}>Scholarships & Assistance</h3>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '8px', fontSize: '0.84rem', color: 'var(--text-muted)', marginBottom: '24px', flex: 1 }}>
                  <div>✓ Support to reduce financial barriers</div>
                  <div>✓ Assistance for eligible learners</div>
                  <div>✓ Help with continued education</div>
                  <div>✓ Guidance through the application process</div>
                </div>
                <Link to="/programmes/scholarships" style={{ color: 'var(--electric-cyan)', textDecoration: 'none', fontWeight: 600, fontSize: '0.9rem', display: 'flex', alignItems: 'center', gap: '4px' }}>
                  Learn More <ArrowRight size={14} />
                </Link>
              </div>
            </div>

            {/* Card 3 */}
            <div className="card-base" style={{ padding: 0, overflow: 'hidden', display: 'flex', flexDirection: 'column' }}>
              <img src={progMentoring} alt="Academic Support & Mentoring" style={{ width: '100%', height: '170px', objectFit: 'cover' }} />
              <div style={{ padding: '24px', display: 'flex', flexDirection: 'column', flex: 1 }}>
                <span style={{ fontSize: '0.72rem', textTransform: 'uppercase', letterSpacing: '0.1em', color: 'var(--electric-cyan)', fontWeight: 700, marginBottom: '6px' }}>
                  CHARITABLE PROGRAMME
                </span>
                <h3 style={{ fontSize: '1.25rem', color: '#FFF', marginBottom: '14px' }}>Academic Support & Mentoring</h3>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '8px', fontSize: '0.84rem', color: 'var(--text-muted)', marginBottom: '24px', flex: 1 }}>
                  <div>✓ Additional learning support</div>
                  <div>✓ Mentoring and guidance</div>
                  <div>✓ Helps build confidence</div>
                  <div>✓ Designed for better learning outcomes</div>
                </div>
                <Link to="/programmes/academic-support" style={{ color: 'var(--electric-cyan)', textDecoration: 'none', fontWeight: 600, fontSize: '0.9rem', display: 'flex', alignItems: 'center', gap: '4px' }}>
                  Learn More <ArrowRight size={14} />
                </Link>
              </div>
            </div>

            {/* Card 4: Supplementary Tutoring */}
            <div className="card-base" style={{ padding: 0, overflow: 'hidden', display: 'flex', flexDirection: 'column', borderColor: 'var(--border-cyan)' }}>
              <img src={progTutoring} alt="Supplementary Tutoring Services" style={{ width: '100%', height: '170px', objectFit: 'cover' }} />
              <div style={{ padding: '24px', display: 'flex', flexDirection: 'column', flex: 1 }}>
                <span style={{ fontSize: '0.72rem', textTransform: 'uppercase', letterSpacing: '0.1em', color: '#C084FC', fontWeight: 700, marginBottom: '6px' }}>
                  SUPPLEMENTARY SERVICE
                </span>
                <h3 style={{ fontSize: '1.25rem', color: '#FFF', marginBottom: '14px' }}>Supplementary Tutoring Services</h3>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '8px', fontSize: '0.84rem', color: 'var(--text-muted)', marginBottom: '24px', flex: 1 }}>
                  <div>✓ Additional subject support</div>
                  <div>✓ Flexible learning options</div>
                  <div>✓ Online classes with qualified teachers</div>
                  <div>✓ Designed for interested learners</div>
                </div>
                <Link to="/tutoring" style={{ color: 'var(--electric-cyan)', textDecoration: 'none', fontWeight: 600, fontSize: '0.9rem', display: 'flex', alignItems: 'center', gap: '4px' }}>
                  Learn More <ArrowRight size={14} />
                </Link>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ================= 5. HOW TO GET SUPPORT (08 Process Draw) ================= */}
      <section className="section">
        <div className="container">
          <div style={{ textAlign: 'center', maxWidth: '720px', margin: '0 auto 40px auto' }}>
            <div className="eyebrow" style={{ margin: '0 auto 16px auto' }}>HOW TO GET SUPPORT</div>
            <h2 className="h2-section" style={{ marginBottom: '14px' }}>A Simple and Transparent Process</h2>
            <p style={{ color: 'var(--text-muted)', fontSize: '1rem' }}>
              We aim to make it easy for eligible learners and families to explore and apply for support.
            </p>
          </div>

          {/* Animated Process Draw Component */}
          <ProcessDraw />
        </div>
      </section>

      {/* ================= 6. OUR IMPACT (05 Count-up Stats) ================= */}
      <section className="section" style={{ backgroundColor: 'var(--surface-1)' }}>
        <div className="container">
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '48px', alignItems: 'center' }}>
            <div>
              <div className="eyebrow">OUR IMPACT</div>
              <h2 className="h2-section" style={{ marginBottom: '18px' }}>
                Creating Meaningful Opportunities
              </h2>
              <p style={{ fontSize: '1.05rem', color: 'var(--text-secondary)', lineHeight: 1.7, marginBottom: '28px' }}>
                Through our programmes, we work towards stronger learning outcomes, greater confidence and brighter futures for learners.
              </p>
              <Link to="/impact" className="btn btn-cyan">
                View Our Impact <ArrowRight size={16} />
              </Link>
            </div>

            {/* Animated Count-up Stats Component */}
            <CountUpStats />
          </div>
        </div>
      </section>

      {/* ================= 7. BUILDING AN EDUCATION COMMUNITY ================= */}
      <section className="section">
        <div className="container">
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '48px', alignItems: 'center' }}>
            {/* Image of Schoolgirls Studying Together */}
            <div style={{ borderRadius: 'var(--radius-card-lg)', overflow: 'hidden', border: '1px solid var(--border-card)', boxShadow: 'var(--shadow-card)' }}>
              <img
                src={communityGirls}
                alt="Indian schoolgirls studying in classroom"
                style={{ width: '100%', height: 'auto', display: 'block', objectFit: 'cover' }}
              />
            </div>

            {/* Right text & pills */}
            <div>
              <div className="eyebrow">BUILDING AN EDUCATION COMMUNITY</div>
              <h2 className="h2-section" style={{ marginBottom: '18px' }}>Stronger Together</h2>
              <p style={{ fontSize: '1.05rem', color: 'var(--text-secondary)', lineHeight: 1.7, marginBottom: '28px' }}>
                We work with learners, families, volunteers, educators and partners to build a supportive education community. Together, we can create more opportunities and a more inclusive learning environment.
              </p>

              <div style={{ display: 'flex', flexDirection: 'column', gap: '14px', marginBottom: '32px' }}>
                <div className="card-surface-3" style={{ padding: '16px 20px', display: 'flex', alignItems: 'center', gap: '14px' }}>
                  <Users size={22} color="var(--electric-cyan)" />
                  <div>
                    <strong style={{ color: '#FFF', fontSize: '0.92rem', display: 'block' }}>Volunteer Network</strong>
                    <span style={{ color: 'var(--text-muted)', fontSize: '0.82rem' }}>Individuals who contribute their time and skills.</span>
                  </div>
                </div>

                <div className="card-surface-3" style={{ padding: '16px 20px', display: 'flex', alignItems: 'center', gap: '14px' }}>
                  <Award size={22} color="var(--electric-cyan)" />
                  <div>
                    <strong style={{ color: '#FFF', fontSize: '0.92rem', display: 'block' }}>Educator Community</strong>
                    <span style={{ color: 'var(--text-muted)', fontSize: '0.82rem' }}>Teachers and mentors who support our learners.</span>
                  </div>
                </div>

                <div className="card-surface-3" style={{ padding: '16px 20px', display: 'flex', alignItems: 'center', gap: '14px' }}>
                  <HeartHandshake size={22} color="var(--electric-cyan)" />
                  <div>
                    <strong style={{ color: '#FFF', fontSize: '0.92rem', display: 'block' }}>Partnerships</strong>
                    <span style={{ color: 'var(--text-muted)', fontSize: '0.82rem' }}>Collaborations to strengthen educational opportunities.</span>
                  </div>
                </div>
              </div>

              <Link to="/contact" className="btn btn-cyan">
                Be Part of the Community <ArrowRight size={16} />
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* ================= 8. STORIES (04 Review Shuffle) ================= */}
      <section className="section" style={{ backgroundColor: 'var(--surface-1)' }}>
        <div className="container">
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '48px', alignItems: 'center' }}>
            <div>
              <div className="eyebrow">STORIES</div>
              <h2 className="h2-section" style={{ marginBottom: '18px' }}>
                Learners. Families. Brighter Futures.
              </h2>
              <p style={{ fontSize: '1.05rem', color: 'var(--text-secondary)', lineHeight: 1.7, marginBottom: '28px' }}>
                Real stories from our community show the difference that educational support can make. These stories highlight the potential that exists when learners receive the right opportunities.
              </p>
              <Link to="/about" className="btn btn-cyan">
                More Stories <ArrowRight size={16} />
              </Link>
            </div>

            {/* Animated Review Shuffle Component */}
            <ReviewShuffle />
          </div>
        </div>
      </section>

      {/* ================= 9. GET INVOLVED ================= */}
      <section className="section">
        <div className="container">
          <div style={{ display: 'flex', flexWrap: 'wrap', alignItems: 'flex-end', justifyContent: 'space-between', gap: '20px', marginBottom: '48px' }}>
            <div>
              <div className="eyebrow">GET INVOLVED</div>
              <h2 className="h2-section">Different Ways to Make a Difference</h2>
              <p style={{ color: 'var(--text-muted)', fontSize: '1rem', marginTop: '8px' }}>
                You can support Scholarbee Foundation in many ways.
              </p>
            </div>
            <Link to="/donate" className="btn btn-cyan">
              Get Involved <ArrowRight size={16} />
            </Link>
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: '24px' }}>
            <div className="card-base" style={{ display: 'flex', flexDirection: 'column' }}>
              <div style={{ color: 'var(--electric-cyan)', marginBottom: '16px' }}><Heart size={32} /></div>
              <h3 style={{ fontSize: '1.3rem', color: '#FFF', marginBottom: '12px' }}>Support Education</h3>
              <p style={{ fontSize: '0.9rem', color: 'var(--text-muted)', lineHeight: 1.6, marginBottom: '24px', flex: 1 }}>
                Your contribution can help strengthen educational opportunities for eligible learners.
              </p>
              <Link to="/donate" className="btn btn-cyan" style={{ width: '100%' }}>
                Donate <ArrowRight size={14} />
              </Link>
            </div>

            <div className="card-base" style={{ display: 'flex', flexDirection: 'column' }}>
              <div style={{ color: 'var(--electric-cyan)', marginBottom: '16px' }}><Users size={32} /></div>
              <h3 style={{ fontSize: '1.3rem', color: '#FFF', marginBottom: '12px' }}>Volunteer Your Skills</h3>
              <p style={{ fontSize: '0.9rem', color: 'var(--text-muted)', lineHeight: 1.6, marginBottom: '24px', flex: 1 }}>
                Share your time and skills to support our educational programmes and learners.
              </p>
              <Link to="/volunteer" className="btn btn-outline" style={{ width: '100%' }}>
                Volunteer <ArrowRight size={14} />
              </Link>
            </div>

            <div className="card-base" style={{ display: 'flex', flexDirection: 'column' }}>
              <div style={{ color: 'var(--electric-cyan)', marginBottom: '16px' }}><Briefcase size={32} /></div>
              <h3 style={{ fontSize: '1.3rem', color: '#FFF', marginBottom: '12px' }}>Work With Us</h3>
              <p style={{ fontSize: '0.9rem', color: 'var(--text-muted)', lineHeight: 1.6, marginBottom: '24px', flex: 1 }}>
                Explore employment and internship opportunities with Scholarbee Foundation.
              </p>
              <Link to="/careers" className="btn btn-outline" style={{ width: '100%' }}>
                View Opportunities <ArrowRight size={14} />
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* ================= 10. GOVERNANCE & TRANSPARENCY ================= */}
      <section className="section" style={{ backgroundColor: 'var(--surface-1)' }}>
        <div className="container">
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '48px', alignItems: 'center' }}>
            <div>
              <div className="eyebrow">GOVERNANCE & TRANSPARENCY</div>
              <h2 className="h2-section" style={{ marginBottom: '18px' }}>
                Built on Trust and Accountability
              </h2>
              <p style={{ fontSize: '1.05rem', color: 'var(--text-secondary)', lineHeight: 1.7, marginBottom: '28px' }}>
                We are committed to maintaining transparency in our work, governance and use of resources. Our organisational identity, statutory information and key policies are available for review.
              </p>
              <Link to="/governance" className="btn btn-cyan">
                View Governance & Transparency <ArrowRight size={16} />
              </Link>
            </div>

            {/* 4 Governance Cards */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(2, 1fr)', gap: '16px' }}>
              <div className="card-surface-3" style={{ padding: '24px' }}>
                <div style={{ color: 'var(--electric-cyan)', marginBottom: '8px' }}><Building2 size={24} /></div>
                <h4 style={{ fontSize: '1rem', color: '#FFF', marginBottom: '4px' }}>Legal Identity</h4>
                <p style={{ fontSize: '0.82rem', color: 'var(--text-muted)' }}>Section 8 Company</p>
              </div>

              <div className="card-surface-3" style={{ padding: '24px' }}>
                <div style={{ color: 'var(--electric-cyan)', marginBottom: '8px' }}><Compass size={24} /></div>
                <h4 style={{ fontSize: '1rem', color: '#FFF', marginBottom: '4px' }}>Learning Objectives</h4>
                <p style={{ fontSize: '0.82rem', color: 'var(--text-muted)' }}>Education & non-profit activities</p>
              </div>

              <div className="card-surface-3" style={{ padding: '24px' }}>
                <div style={{ color: 'var(--electric-cyan)', marginBottom: '8px' }}><ShieldCheck size={24} /></div>
                <h4 style={{ fontSize: '1rem', color: '#FFF', marginBottom: '4px' }}>Statutory Registrations</h4>
                <p style={{ fontSize: '0.82rem', color: 'var(--text-muted)' }}>As applicable by law</p>
              </div>

              <div className="card-surface-3" style={{ padding: '24px' }}>
                <div style={{ color: 'var(--electric-cyan)', marginBottom: '8px' }}><FileText size={24} /></div>
                <h4 style={{ fontSize: '1rem', color: '#FFF', marginBottom: '4px' }}>Reports & Policies</h4>
                <p style={{ fontSize: '0.82rem', color: 'var(--text-muted)' }}>Annual reports and policies</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ================= 11. FAQ ACCORDION ================= */}
      <section className="section">
        <div className="container">
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '48px', alignItems: 'flex-start' }}>
            <div>
              <div className="eyebrow">FREQUENTLY ASKED QUESTIONS</div>
              <h2 className="h2-section" style={{ marginBottom: '18px' }}>Common Questions</h2>
              <p style={{ fontSize: '1.05rem', color: 'var(--text-secondary)', lineHeight: 1.7, marginBottom: '28px' }}>
                Find quick answers to common questions about our programmes and organisation.
              </p>
              <Link to="/contact" className="btn btn-cyan">
                View All FAQs <ArrowRight size={16} />
              </Link>
            </div>

            {/* Accordion */}
            <div style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
              {faqs.map((f, i) => {
                const isOpen = openFaq === i;
                return (
                  <div
                    key={i}
                    className="card-surface-3"
                    style={{
                      padding: 0,
                      overflow: 'hidden',
                      border: isOpen ? '1px solid var(--border-cyan)' : '1px solid var(--border-subtle)',
                      transition: 'all 0.2s',
                    }}
                  >
                    <button
                      onClick={() => setOpenFaq(isOpen ? -1 : i)}
                      style={{
                        width: '100%',
                        padding: '18px 20px',
                        background: 'none',
                        border: 'none',
                        color: '#FFF',
                        fontFamily: 'var(--font-heading)',
                        fontSize: '0.96rem',
                        fontWeight: 600,
                        textAlign: 'left',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'space-between',
                        cursor: 'pointer',
                      }}
                    >
                      <span>{f.q}</span>
                      <span style={{ color: 'var(--electric-cyan)', fontSize: '1.2rem', marginLeft: '12px' }}>
                        {isOpen ? '−' : '+'}
                      </span>
                    </button>
                    {isOpen && (
                      <div style={{ padding: '0 20px 20px 20px', color: 'var(--text-muted)', fontSize: '0.9rem', lineHeight: 1.6 }}>
                        {f.a}
                      </div>
                    )}
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </section>

      {/* ================= 12. CONTACT US ================= */}
      <section className="section" style={{ backgroundColor: 'var(--surface-1)' }}>
        <div className="container">
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: '36px', alignItems: 'center' }}>
            <div>
              <div className="eyebrow">CONTACT US</div>
              <h2 className="h2-section" style={{ marginBottom: '18px' }}>We'd Love to Hear From You</h2>
              <p style={{ fontSize: '1.02rem', color: 'var(--text-secondary)', lineHeight: 1.65, marginBottom: '28px' }}>
                For programme enquiries, volunteering, donations, partnerships or general information, please get in touch.
              </p>
              <div style={{ display: 'flex', flexWrap: 'wrap', gap: '14px' }}>
                <Link to="/contact" className="btn btn-cyan">
                  Contact the Foundation <ArrowRight size={16} />
                </Link>
                <Link to="/contact" className="btn btn-outline">
                  View Contact Details
                </Link>
              </div>
            </div>

            {/* Contact Details Card */}
            <div className="card-base" style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
              <div style={{ display: 'flex', gap: '14px', alignItems: 'flex-start' }}>
                <Mail size={20} color="var(--electric-cyan)" style={{ marginTop: '3px' }} />
                <div>
                  <strong style={{ color: '#FFF', display: 'block', fontSize: '0.88rem' }}>Email</strong>
                  <span style={{ fontSize: '0.85rem', color: 'var(--text-muted)' }}>info@scholarbeefoundation.org</span>
                </div>
              </div>

              <div style={{ display: 'flex', gap: '14px', alignItems: 'flex-start' }}>
                <Phone size={20} color="var(--electric-cyan)" style={{ marginTop: '3px' }} />
                <div>
                  <strong style={{ color: '#FFF', display: 'block', fontSize: '0.88rem' }}>Phone</strong>
                  <span style={{ fontSize: '0.85rem', color: 'var(--text-muted)' }}>+91 98765 43210</span>
                </div>
              </div>

              <div style={{ display: 'flex', gap: '14px', alignItems: 'flex-start' }}>
                <Building2 size={20} color="var(--electric-cyan)" style={{ marginTop: '3px' }} />
                <div>
                  <strong style={{ color: '#FFF', display: 'block', fontSize: '0.88rem' }}>Registered Office</strong>
                  <span style={{ fontSize: '0.85rem', color: 'var(--text-muted)' }}>New Delhi, India (Exact address as per official records)</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
