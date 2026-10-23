import React from 'react';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Navbar from './components/layout/Navbar';
import Footer from './components/layout/Footer';

// Pages
import Home from './pages/Home';
import About from './pages/About';
import EducationChallenge from './pages/EducationChallenge';
import Programmes from './pages/Programmes';
import FreeOnlineLearning from './pages/FreeOnlineLearning';
import Scholarships from './pages/Scholarships';
import AcademicSupport from './pages/AcademicSupport';
import Tutoring from './pages/Tutoring';
import Impact from './pages/Impact';
import Governance from './pages/Governance';
import Donate from './pages/Donate';
import Volunteer from './pages/Volunteer';
import Careers from './pages/Careers';
import Contact from './pages/Contact';
import Legal from './pages/Legal';
import NotFound from './pages/NotFound';

export default function App() {
  return (
    <BrowserRouter>
      <div style={{ display: 'flex', flexDirection: 'column', minHeight: '100vh', backgroundColor: 'var(--bg-jet-black)' }}>
        <Navbar />
        <main style={{ flex: 1 }}>
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/education-challenge" element={<EducationChallenge />} />
            <Route path="/about" element={<About />} />
            <Route path="/our-story" element={<About />} />
            <Route path="/who-we-are" element={<About />} />
            <Route path="/programmes" element={<Programmes />} />
            <Route path="/programmes/free-online-learning" element={<FreeOnlineLearning />} />
            <Route path="/programmes/scholarships" element={<Scholarships />} />
            <Route path="/programmes/academic-support" element={<AcademicSupport />} />
            <Route path="/tutoring" element={<Tutoring />} />
            <Route path="/impact" element={<Impact />} />
            <Route path="/governance" element={<Governance />} />
            <Route path="/donate" element={<Donate />} />
            <Route path="/volunteer" element={<Volunteer />} />
            <Route path="/careers" element={<Careers />} />
            <Route path="/contact" element={<Contact />} />
            <Route path="/privacy" element={<Legal />} />
            <Route path="/terms" element={<Legal />} />
            <Route path="/cookies" element={<Legal />} />
            <Route path="*" element={<NotFound />} />
          </Routes>
        </main>
        <Footer />
      </div>
    </BrowserRouter>
  );
}
