import React, { useState, useEffect } from 'react';
import { 
  Sparkles, Star, ShieldCheck, Video, Calendar, Clock, 
  ArrowRight, CheckCircle, Users, BookOpen, Laptop, MessageSquare, Play 
} from 'lucide-react';
import RoleSwitcher from '../components/tutoring/RoleSwitcher';
import TeacherSearch from '../components/tutoring/TeacherSearch';
import DemoBookingModal from '../components/tutoring/DemoBookingModal';
import { api } from '../services/api';
import studentImg from '../assets/student_hero.jpg';
import parentImg from '../assets/parent_hero.jpg';
import teacherImg from '../assets/teacher_hero.jpg';

export default function Tutoring() {
  const [activeRole, setActiveRole] = useState('student');
  const [teachers, setTeachers] = useState([]);
  const [loadingTeachers, setLoadingTeachers] = useState(true);
  const [bookingModalOpen, setBookingModalOpen] = useState(false);
  const [selectedTeacher, setSelectedTeacher] = useState(null);

  useEffect(() => {
    async function loadTeachers() {
      setLoadingTeachers(true);
      try {
        const res = await api.getInstructors();
        if (res.ok && Array.isArray(res.data) && res.data.length > 0) {
          setTeachers(res.data.slice(0, 8));
        } else {
          // Curated fallback teachers if remote API returns empty or custom structure
          setTeachers([
            { id: 101, name: 'Dr. Ananya Roy', subject: 'Advanced Mathematics & Calculus', rating: 4.95, reviews: 38, experience: '9+ Years', mode: 'Online 1-on-1', price: '₹750 / hr', bio: 'Former college lecturer specializing in board exam preparations and competitive mathematics.' },
            { id: 102, name: 'Prof. Rajesh Menon', subject: 'Physics & Mechanics', rating: 4.9, reviews: 42, experience: '12+ Years', mode: 'Online 1-on-1', price: '₹800 / hr', bio: 'Passionate about conceptual physics and helping high-schoolers achieve top percentile ranks.' },
            { id: 103, name: 'Sneha Kulkarni', subject: 'Chemistry (Organic & Physical)', rating: 4.88, reviews: 29, experience: '7+ Years', mode: 'Group Live', price: '₹650 / hr', bio: 'Interactive visual notes and problem sets making complex chemical equations intuitive.' },
            { id: 104, name: 'Vikramaditya Sen', subject: 'Computer Science & Python', rating: 4.92, reviews: 51, experience: '8+ Years', mode: 'Online 1-on-1', price: '₹900 / hr', bio: 'Software engineer and certified coding educator guiding students through algorithms and projects.' },
          ]);
        }
      } catch (err) {
        console.error('Error fetching instructors:', err);
      } finally {
        setLoadingTeachers(false);
      }
    }
    loadTeachers();
  }, []);

  const openBooking = (teacher = null) => {
    setSelectedTeacher(teacher);
    setBookingModalOpen(true);
  };

  // Content configuration for each role
  const roleData = {
    student: {
      eyebrow: 'FOR STUDENTS',
      headlinePrefix: 'Find the Right Teacher for a',
      headlineAccent: 'Brighter Future',
      description: 'Learn from verified teachers through personalized 1-to-1 or group live classes. Take a free trial demo and choose the educator that matches your pace.',
      primaryCta: 'Book Free Demo',
      secondaryCta: 'Explore Verified Teachers',
      benefits: ['Verified Subject Experts', 'Score Higher in Board Exams', 'Self-Paced Flexible Timings', 'Interactive 1-on-1 Whiteboard'],
    },
    parent: {
      eyebrow: 'FOR PARENTS',
      headlinePrefix: 'Give Your Child the',
      headlineAccent: 'Learning They Deserve',
      description: 'Find verified educators, monitor attendance, audit test performance reports, and ensure your child receives individualized attention in a safe online classroom.',
      primaryCta: 'Find Support for My Child',
      secondaryCta: 'Book Free Demo Session',
      benefits: ['100% Background-Checked Tutors', 'Real-Time Attendance Tracking', 'Bi-Weekly Progress Reports', 'Direct Educator Coordination'],
    },
    teacher: {
      eyebrow: 'FOR TEACHERS',
      headlinePrefix: 'Teach More. Earn More.',
      headlineAccent: 'Grow With ScholarB',
      description: 'Connect with motivated learners, manage class bookings on an automated calendar, build your public teaching brand, and receive automated weekly payouts.',
      primaryCta: 'Join as a Teacher',
      secondaryCta: 'Explore Teacher Benefits',
      benefits: ['Zero Upfront Listing Fee', 'Automated Calendar & Escrow', 'Set Your Own Hourly Rate', 'Integrated MS Teams & Classroom'],
    },
  };

  const current = roleData[activeRole];

  return (
    <div style={{ backgroundColor: 'var(--bg-jet-black)', color: 'var(--text-secondary)' }}>
      {/* ================= HERO SECTION WITH ROLE SWITCHER ================= */}
      <section
        style={{
          position: 'relative',
          paddingTop: 'calc(var(--nav-height) + 50px)',
          paddingBottom: '80px',
          overflow: 'hidden',
        }}
      >
        {/* Ambient Glow */}
        <div
          style={{
            position: 'absolute',
            top: '10%',
            right: '10%',
            width: '600px',
            height: '400px',
            background: 'radial-gradient(circle, rgba(59, 10, 107, 0.4) 0%, rgba(0, 229, 255, 0.08) 50%, transparent 70%)',
            filter: 'blur(70px)',
            pointerEvents: 'none',
          }}
        />

        <div className="container" style={{ position: 'relative', zIndex: 1 }}>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '48px', alignItems: 'center' }}>
            {/* Left Content */}
            <div style={{ maxWidth: '640px' }}>
              <div className="eyebrow">
                <Sparkles size={14} />
                {current.eyebrow}
              </div>

              <h1 className="h1-display" style={{ marginBottom: '14px' }}>
                {current.headlinePrefix}{' '}
                <span className="text-cyan">{current.headlineAccent}</span>
              </h1>

              {/* MANDATORY: Role Switcher Directly Under H1 */}
              <RoleSwitcher activeRole={activeRole} onRoleChange={setActiveRole} />

              <p style={{ fontSize: '1.15rem', color: 'var(--text-secondary)', lineHeight: 1.6, marginBottom: '28px' }}>
                {current.description}
              </p>

              <div style={{ display: 'flex', flexWrap: 'wrap', gap: '14px', marginBottom: '32px' }}>
                <button onClick={() => openBooking(null)} className="btn btn-cyan" style={{ fontSize: '1rem', padding: '12px 28px' }}>
                  {current.primaryCta}
                  <ArrowRight size={18} />
                </button>
                <a href="#teachers-section" className="btn btn-outline" style={{ fontSize: '1rem', padding: '12px 28px' }}>
                  {current.secondaryCta}
                </a>
              </div>

              {/* Role Benefits Badges */}
              <div style={{ display: 'flex', flexWrap: 'wrap', gap: '10px', marginBottom: '36px' }}>
                {current.benefits.map((b, i) => (
                  <div
                    key={i}
                    style={{
                      display: 'flex',
                      alignItems: 'center',
                      gap: '6px',
                      fontSize: '0.8rem',
                      color: 'var(--text-primary)',
                      backgroundColor: 'var(--surface-3)',
                      border: '1px solid var(--border-subtle)',
                      padding: '6px 12px',
                      borderRadius: 'var(--radius-pill)',
                    }}
                  >
                    <CheckCircle size={14} color="var(--electric-cyan)" />
                    <span>{b}</span>
                  </div>
                ))}
              </div>

              {/* Teacher Search Form */}
              <TeacherSearch onSearch={(filters) => console.log('Searching teachers with filters:', filters)} />
            </div>

            {/* Right Visual: Dynamic Hero Person */}
            <div style={{ display: 'flex', justifyContent: 'center', position: 'relative' }}>
              <div
                style={{
                  position: 'relative',
                  width: '100%',
                  maxWidth: '440px',
                  borderRadius: 'var(--radius-container)',
                  overflow: 'hidden',
                  border: '1px solid var(--border-cyan)',
                  boxShadow: 'var(--shadow-card), 0 0 40px rgba(0, 229, 255, 0.25)',
                  background: 'var(--surface-2)',
                }}
              >
                <img
                  src={activeRole === 'student' ? studentImg : activeRole === 'parent' ? parentImg : teacherImg}
                  alt={`${activeRole} Hero Representation`}
                  style={{
                    width: '100%',
                    height: 'auto',
                    display: 'block',
                    objectFit: 'cover',
                    transition: 'opacity 0.3s ease',
                  }}
                />

                {/* Floating Trust Pill */}
                <div
                  style={{
                    position: 'absolute',
                    bottom: '20px',
                    left: '20px',
                    right: '20px',
                    backgroundColor: 'rgba(13, 13, 20, 0.88)',
                    backdropFilter: 'blur(12px)',
                    border: '1px solid rgba(0, 229, 255, 0.3)',
                    borderRadius: 'var(--radius-md)',
                    padding: '12px 16px',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '12px',
                  }}
                >
                  <div style={{ width: '10px', height: '10px', borderRadius: '50%', backgroundColor: '#10B981', boxShadow: '0 0 8px #10B981' }} />
                  <div>
                    <strong style={{ color: '#FFF', fontSize: '0.85rem', display: 'block' }}>
                      {activeRole === 'student' ? '1-on-1 Interactive Demo' : activeRole === 'parent' ? 'Audited Child Progress' : 'Verified Educator Profile'}
                    </strong>
                    <span style={{ color: 'var(--text-muted)', fontSize: '0.75rem' }}>
                      Active Live Classes on ScholarB
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ================= TOP RATED TEACHERS SECTION ================= */}
      <section id="teachers-section" className="section" style={{ backgroundColor: 'var(--surface-1)' }}>
        <div className="container">
          <div style={{ display: 'flex', flexWrap: 'wrap', alignItems: 'flex-end', justifyContent: 'space-between', gap: '20px', marginBottom: '48px' }}>
            <div>
              <div className="eyebrow">VERIFIED EDUCATORS</div>
              <h2 className="h2-section">Top Rated Subject Teachers</h2>
              <p style={{ color: 'var(--text-muted)', fontSize: '1rem', marginTop: '8px' }}>
                Book a risk-free demonstration class with vetted instructors.
              </p>
            </div>
            <button onClick={() => openBooking(null)} className="btn btn-outline">
              Request Customized Match
            </button>
          </div>

          <div
            style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))',
              gap: '24px',
            }}
          >
            {teachers.map((t) => (
              <div
                key={t.id}
                className="card-base"
                style={{
                  display: 'flex',
                  flexDirection: 'column',
                  padding: '24px',
                }}
              >
                <div style={{ display: 'flex', alignItems: 'center', gap: '16px', marginBottom: '16px' }}>
                  <div
                    style={{
                      width: '60px',
                      height: '60px',
                      borderRadius: '50%',
                      background: 'linear-gradient(135deg, var(--surface-3), var(--surface-4))',
                      border: '2px solid var(--border-cyan)',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      fontSize: '1.2rem',
                      fontWeight: 700,
                      color: 'var(--electric-cyan)',
                      flexShrink: 0,
                    }}
                  >
                    {t.name.split(' ').map(n => n[0]).join('')}
                  </div>
                  <div>
                    <h4 style={{ fontSize: '1.1rem', color: '#FFF', display: 'flex', alignItems: 'center', gap: '6px' }}>
                      {t.name}
                      <ShieldCheck size={16} color="var(--electric-cyan)" />
                    </h4>
                    <span style={{ fontSize: '0.82rem', color: 'var(--text-muted)' }}>{t.subject}</span>
                  </div>
                </div>

                <p style={{ fontSize: '0.86rem', color: 'var(--text-secondary)', lineHeight: 1.5, marginBottom: '20px', flex: 1 }}>
                  {t.bio || 'Experienced educator focused on analytical thinking and curriculum mastery.'}
                </p>

                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '12px 0', borderTop: '1px solid var(--border-subtle)', marginBottom: '16px', fontSize: '0.85rem' }}>
                  <div style={{ display: 'flex', alignItems: 'center', gap: '4px', color: '#FFB800' }}>
                    <Star size={15} fill="#FFB800" />
                    <span style={{ fontWeight: 700, color: '#FFF' }}>{t.rating || '4.9'}</span>
                    <span style={{ color: 'var(--text-muted)' }}>({t.reviews || 25})</span>
                  </div>
                  <span style={{ color: 'var(--electric-cyan)', fontWeight: 700 }}>
                    {t.price || 'Free Demo'}
                  </span>
                </div>

                <button
                  onClick={() => openBooking(t)}
                  className="btn btn-cyan"
                  style={{ width: '100%', minHeight: '42px', fontSize: '0.9rem' }}
                >
                  Book Free Demo
                </button>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ================= PRODUCT DASHBOARD PREVIEW ================= */}
      <section className="section">
        <div className="container">
          <div style={{ textAlign: 'center', maxWidth: '700px', margin: '0 auto 50px auto' }}>
            <div className="eyebrow" style={{ margin: '0 auto 16px auto' }}>STUDENT & PARENT HUB</div>
            <h2 className="h2-section" style={{ marginBottom: '16px' }}>Personalized Learning. Real Progress.</h2>
            <p style={{ color: 'var(--text-muted)', fontSize: '1rem' }}>
              Full visibility over live upcoming sessions, assignments, attendance records, and direct educator feedback.
            </p>
          </div>

          {/* HTML/CSS Dashboard Mockup */}
          <div
            className="card-base"
            style={{
              maxWidth: '960px',
              margin: '0 auto',
              background: 'var(--surface-2)',
              border: '1px solid var(--border-cyan)',
              padding: '28px',
              borderRadius: 'var(--radius-card-lg)',
              boxShadow: 'var(--shadow-card), 0 0 50px rgba(0, 229, 255, 0.1)',
            }}
          >
            {/* Top Bar of Mockup */}
            <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', borderBottom: '1px solid var(--border-subtle)', paddingBottom: '16px', marginBottom: '24px' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                <div style={{ width: '10px', height: '10px', borderRadius: '50%', backgroundColor: '#10B981' }} />
                <span style={{ fontSize: '0.85rem', color: '#FFF', fontWeight: 600 }}>Active Student Learning Session</span>
              </div>
              <span style={{ fontSize: '0.8rem', color: 'var(--text-muted)' }}>CBSE Class 10 Portal</span>
            </div>

            {/* Dashboard Content Grid */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(260px, 1fr))', gap: '20px' }}>
              {/* Upcoming Class Card */}
              <div className="card-surface-3" style={{ borderLeft: '4px solid var(--electric-cyan)' }}>
                <span style={{ fontSize: '0.72rem', textTransform: 'uppercase', letterSpacing: '0.08em', color: 'var(--electric-cyan)', fontWeight: 700 }}>Upcoming Today</span>
                <h4 style={{ fontSize: '1.1rem', color: '#FFF', margin: '8px 0 4px 0' }}>Calculus & Trigonometry</h4>
                <p style={{ fontSize: '0.82rem', color: 'var(--text-muted)', marginBottom: '16px' }}>Educator: Dr. Ananya Roy • 60 mins</p>
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                  <span style={{ fontSize: '0.82rem', color: '#FFF' }}>Starts in 25 mins</span>
                  <button className="btn btn-cyan" style={{ minHeight: '32px', padding: '4px 14px', fontSize: '0.8rem' }}>
                    <Play size={12} fill="#050505" /> Join Live
                  </button>
                </div>
              </div>

              {/* Progress Summary Card */}
              <div className="card-surface-3">
                <span style={{ fontSize: '0.72rem', textTransform: 'uppercase', letterSpacing: '0.08em', color: '#FFF', fontWeight: 700 }}>Syllabus Coverage</span>
                <div style={{ display: 'flex', alignItems: 'baseline', gap: '8px', margin: '8px 0' }}>
                  <span style={{ fontSize: '1.8rem', fontWeight: 800, color: 'var(--electric-cyan)', fontFamily: 'var(--font-heading)' }}>84%</span>
                  <span style={{ fontSize: '0.8rem', color: '#10B981' }}>+12% this month</span>
                </div>
                <div style={{ width: '100%', height: '6px', backgroundColor: 'var(--surface-4)', borderRadius: '3px', overflow: 'hidden' }}>
                  <div style={{ width: '84%', height: '100%', backgroundColor: 'var(--electric-cyan)' }} />
                </div>
              </div>

              {/* Attendance Card */}
              <div className="card-surface-3">
                <span style={{ fontSize: '0.72rem', textTransform: 'uppercase', letterSpacing: '0.08em', color: '#FFF', fontWeight: 700 }}>Attendance Audit</span>
                <div style={{ display: 'flex', alignItems: 'baseline', gap: '8px', margin: '8px 0' }}>
                  <span style={{ fontSize: '1.8rem', fontWeight: 800, color: '#FFF', fontFamily: 'var(--font-heading)' }}>96%</span>
                  <span style={{ fontSize: '0.8rem', color: 'var(--text-muted)' }}>24/25 Classes</span>
                </div>
                <span style={{ fontSize: '0.8rem', color: 'var(--text-muted)' }}>Verified parent notification log</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Demo Booking Modal Component */}
      <DemoBookingModal
        isOpen={bookingModalOpen}
        onClose={() => setBookingModalOpen(false)}
        preselectedTeacher={selectedTeacher}
      />
    </div>
  );
}
