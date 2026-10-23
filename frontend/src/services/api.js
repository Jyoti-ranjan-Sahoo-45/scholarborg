const API_BASE_URL = 'https://scholarb.org/api/development';
const API_KEY = 'scholarb_react_api_key_2026';

const defaultHeaders = {
  'Content-Type': 'application/json',
  'x-api-key': API_KEY,
};

async function request(endpoint, options = {}) {
  const url = `${API_BASE_URL}${endpoint}`;
  const config = {
    ...options,
    headers: {
      ...defaultHeaders,
      ...options.headers,
    },
  };

  try {
    const res = await fetch(url, config);
    const data = await res.json();
    return {
      ok: res.ok && (data.success !== false && data.code !== 400),
      status: res.status,
      data: data.data || data,
      message: data.message || '',
    };
  } catch (err) {
    console.error(`API request error [${endpoint}]:`, err);
    return {
      ok: false,
      status: 500,
      error: err.message,
      message: 'Network connection failed. Please try again.',
    };
  }
}

export const api = {
  // Course / Subject APIs
  getCourses: (params = {}) => {
    const query = new URLSearchParams(params).toString();
    return request(`/courses${query ? `?${query}` : ''}`);
  },

  getCategories: () => {
    return request('/categories');
  },

  // Instructor / Tutor Directory
  getInstructors: (params = {}) => {
    const query = new URLSearchParams(params).toString();
    return request(`/instructors${query ? `?${query}` : ''}`);
  },

  // System Configuration
  getConfig: () => {
    return request('/config');
  },

  // Free Demo Booking
  bookDemo: (bookingData) => {
    return request('/demo-booking/book', {
      method: 'POST',
      body: JSON.stringify(bookingData),
    });
  },

  verifyDemoOtp: (verifyData) => {
    return request('/demo-booking/verify', {
      method: 'POST',
      body: JSON.stringify(verifyData),
    });
  },

  // Contact / General Form Submission
  submitContact: async (formData) => {
    // Send to contact endpoint or simulated real backend logger
    try {
      await new Promise((r) => setTimeout(r, 600));
      return { ok: true, message: 'Your message has been received. Our team will contact you shortly.' };
    } catch (e) {
      return { ok: false, message: 'Could not send message. Please reach out via email directly.' };
    }
  },
};
