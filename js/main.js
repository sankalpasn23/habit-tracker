/* ============================================
   HabitForge — Main JavaScript
   Features:
     1. Dynamic Content Updates (toggling, filtering)
     2. Interactive Image Slider (auto + manual)
     3. Form Validation (email, required, password strength)
     4. Smooth Scrolling (nav links)
     5. Event Handling (hover, tooltips, click events, pop-ups)
     6. Custom Animations (fade-ins, scroll-triggered reveals)
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {
  // Initialize all features
  initNavbar();
  initSmoothScrolling();
  initImageSlider();
  initScrollReveal();
  initDynamicContent();
  initFormValidation();
  initEventHandling();
  initCountUpAnimation();
  initTooltips();
});

/* ==============================
   1. NAVBAR — Scroll effect
   ============================== */
function initNavbar() {
  const navbar = document.querySelector('.navbar-forge');
  if (!navbar) return;

  const handleScroll = () => {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  };

  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll(); // Run on load

  // Close mobile menu on link click
  const navLinks = navbar.querySelectorAll('.nav-link');
  const navCollapse = navbar.querySelector('.navbar-collapse');
  navLinks.forEach(link => {
    link.addEventListener('click', () => {
      if (navCollapse && navCollapse.classList.contains('show')) {
        const bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
        if (bsCollapse) bsCollapse.hide();
      }
    });
  });

  // Active link highlighting based on scroll position
  const sections = document.querySelectorAll('section[id]');
  if (sections.length > 0) {
    window.addEventListener('scroll', () => {
      let current = '';
      sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        if (window.scrollY >= sectionTop) {
          current = section.getAttribute('id');
        }
      });
      navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) {
          link.classList.add('active');
        }
      });
    }, { passive: true });
  }
}

/* ==============================
   4. SMOOTH SCROLLING
   ============================== */
function initSmoothScrolling() {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId === '#') return;
      
      const targetEl = document.querySelector(targetId);
      if (targetEl) {
        e.preventDefault();
        const headerOffset = 80;
        const elementPosition = targetEl.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.scrollY - headerOffset;

        window.scrollTo({
          top: offsetPosition,
          behavior: 'smooth'
        });
      }
    });
  });
}

/* ==============================
   2. INTERACTIVE IMAGE SLIDER
   ============================== */
function initImageSlider() {
  const slider = document.querySelector('.image-slider');
  if (!slider) return;

  const slides = slider.querySelectorAll('.slide');
  const dots = slider.querySelectorAll('.slider-dot');
  const prevBtn = slider.querySelector('.slider-btn.prev');
  const nextBtn = slider.querySelector('.slider-btn.next');
  let currentSlide = 0;
  let autoplayInterval;
  const AUTOPLAY_DELAY = 5000;

  function showSlide(index) {
    // Wrap around
    if (index >= slides.length) index = 0;
    if (index < 0) index = slides.length - 1;

    slides.forEach(s => s.classList.remove('active'));
    dots.forEach(d => d.classList.remove('active'));

    slides[index].classList.add('active');
    if (dots[index]) dots[index].classList.add('active');
    currentSlide = index;
  }

  function nextSlide() {
    showSlide(currentSlide + 1);
  }

  function prevSlide() {
    showSlide(currentSlide - 1);
  }

  function startAutoplay() {
    stopAutoplay();
    autoplayInterval = setInterval(nextSlide, AUTOPLAY_DELAY);
  }

  function stopAutoplay() {
    if (autoplayInterval) clearInterval(autoplayInterval);
  }

  // Controls
  if (nextBtn) nextBtn.addEventListener('click', () => { nextSlide(); startAutoplay(); });
  if (prevBtn) prevBtn.addEventListener('click', () => { prevSlide(); startAutoplay(); });

  dots.forEach((dot, i) => {
    dot.addEventListener('click', () => { showSlide(i); startAutoplay(); });
  });

  // Pause on hover
  slider.addEventListener('mouseenter', stopAutoplay);
  slider.addEventListener('mouseleave', startAutoplay);

  // Keyboard navigation
  document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') { prevSlide(); startAutoplay(); }
    if (e.key === 'ArrowRight') { nextSlide(); startAutoplay(); }
  });

  // Start autoplay
  showSlide(0);
  startAutoplay();
}

/* ==============================
   6. SCROLL-TRIGGERED ANIMATIONS
   ============================== */
function initScrollReveal() {
  const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .stagger-children');
  if (revealElements.length === 0) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        // Optionally stop observing after reveal
        // observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  });

  revealElements.forEach(el => observer.observe(el));
}

/* ==============================
   1. DYNAMIC CONTENT UPDATES
   ============================== */
function initDynamicContent() {
  // Feature filtering on home page
  initFeatureFilter();
  // FAQ accordion
  initFAQAccordion();
}

function initFeatureFilter() {
  const filterBtns = document.querySelectorAll('.filter-btn[data-filter]');
  const filterItems = document.querySelectorAll('[data-category]');

  if (filterBtns.length === 0) return;

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const filter = btn.dataset.filter;

      // Update active button
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      // Filter items with animation
      filterItems.forEach(item => {
        if (filter === 'all' || item.dataset.category === filter) {
          item.style.display = '';
          item.style.opacity = '0';
          item.style.transform = 'translateY(10px)';
          requestAnimationFrame(() => {
            item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
          });
        } else {
          item.style.opacity = '0';
          item.style.transform = 'translateY(10px)';
          setTimeout(() => { item.style.display = 'none'; }, 300);
        }
      });
    });
  });
}

function initFAQAccordion() {
  const faqItems = document.querySelectorAll('.faq-item');
  faqItems.forEach(item => {
    const question = item.querySelector('.faq-question');
    if (!question) return;

    question.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');
      
      // Close all
      faqItems.forEach(fi => fi.classList.remove('open'));
      
      // Toggle clicked
      if (!isOpen) {
        item.classList.add('open');
      }
    });
  });
}

/* ==============================
   3. FORM VALIDATION
   ============================== */
function initFormValidation() {
  // Registration Form
  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    setupRegistrationValidation(registerForm);
  }

  // Login Form
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    setupLoginValidation(loginForm);
  }

  // Contact Form
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    setupContactValidation(contactForm);
  }

  // Add Habit Form
  const addHabitForm = document.getElementById('addHabitForm');
  if (addHabitForm) {
    setupHabitFormValidation(addHabitForm);
  }
}

// --- Validation Helpers ---
function showError(input, message) {
  input.classList.remove('success');
  input.classList.add('error');
  const errorEl = input.parentElement.querySelector('.error-message');
  if (errorEl) {
    errorEl.textContent = message;
    errorEl.classList.add('visible');
  }
}

function showSuccess(input) {
  input.classList.remove('error');
  input.classList.add('success');
  const errorEl = input.parentElement.querySelector('.error-message');
  if (errorEl) {
    errorEl.classList.remove('visible');
  }
}

function clearValidation(input) {
  input.classList.remove('error', 'success');
  const errorEl = input.parentElement.querySelector('.error-message');
  if (errorEl) {
    errorEl.classList.remove('visible');
  }
}

function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function getPasswordStrength(password) {
  let strength = 0;
  if (password.length >= 8) strength++;
  if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
  if (/[0-9]/.test(password)) strength++;
  if (/[^a-zA-Z0-9]/.test(password)) strength++;
  return strength; // 0-4
}

function updatePasswordStrength(password) {
  const strength = getPasswordStrength(password);
  const bars = document.querySelectorAll('.strength-bar');
  const label = document.querySelector('.strength-label');
  
  if (bars.length === 0) return;

  const levels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
  const classes = ['', 'weak', 'medium', 'medium', 'strong'];

  bars.forEach((bar, i) => {
    bar.classList.remove('active', 'weak', 'medium', 'strong');
    if (i < strength) {
      bar.classList.add('active', classes[strength]);
    }
  });

  if (label) {
    label.textContent = password.length > 0 ? levels[strength] : '';
    label.style.color = strength <= 1 ? 'var(--danger-light)' : 
                        strength <= 2 ? 'var(--streak)' : 'var(--accent)';
  }
}

// --- Registration Validation ---
function setupRegistrationValidation(form) {
  const username = form.querySelector('#reg-username');
  const email = form.querySelector('#reg-email');
  const password = form.querySelector('#reg-password');
  const confirmPassword = form.querySelector('#reg-confirm-password');

  // Real-time password strength
  if (password) {
    password.addEventListener('input', () => {
      updatePasswordStrength(password.value);
    });
  }

  // Real-time validation on blur
  if (username) {
    username.addEventListener('blur', () => {
      if (username.value.trim().length < 3) {
        showError(username, 'Username must be at least 3 characters');
      } else if (!/^[a-zA-Z0-9_]+$/.test(username.value)) {
        showError(username, 'Only letters, numbers and underscores allowed');
      } else {
        showSuccess(username);
      }
    });
    username.addEventListener('input', () => clearValidation(username));
  }

  if (email) {
    email.addEventListener('blur', () => {
      if (!email.value.trim()) {
        showError(email, 'Email is required');
      } else if (!isValidEmail(email.value)) {
        showError(email, 'Please enter a valid email address');
      } else {
        showSuccess(email);
      }
    });
    email.addEventListener('input', () => clearValidation(email));
  }

  if (confirmPassword) {
    confirmPassword.addEventListener('blur', () => {
      if (confirmPassword.value !== password.value) {
        showError(confirmPassword, 'Passwords do not match');
      } else if (confirmPassword.value.length > 0) {
        showSuccess(confirmPassword);
      }
    });
    confirmPassword.addEventListener('input', () => clearValidation(confirmPassword));
  }

  // Form submit
  form.addEventListener('submit', (e) => {
    let isValid = true;

    if (!username.value.trim() || username.value.trim().length < 3) {
      showError(username, 'Username must be at least 3 characters');
      isValid = false;
    }

    if (!isValidEmail(email.value)) {
      showError(email, 'Please enter a valid email address');
      isValid = false;
    }

    if (getPasswordStrength(password.value) < 2) {
      showError(password, 'Password is too weak — use 8+ chars with mixed case and numbers');
      isValid = false;
    }

    if (confirmPassword && confirmPassword.value !== password.value) {
      showError(confirmPassword, 'Passwords do not match');
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
      // Shake the form
      form.closest('.form-card')?.classList.add('shake');
      setTimeout(() => form.closest('.form-card')?.classList.remove('shake'), 500);
    }
  });
}

// --- Login Validation ---
function setupLoginValidation(form) {
  const email = form.querySelector('#login-email');
  const password = form.querySelector('#login-password');

  form.addEventListener('submit', (e) => {
    let isValid = true;

    if (!email.value.trim()) {
      showError(email, 'Email is required');
      isValid = false;
    } else if (!isValidEmail(email.value)) {
      showError(email, 'Please enter a valid email');
      isValid = false;
    }

    if (!password.value) {
      showError(password, 'Password is required');
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
    }
  });

  [email, password].forEach(input => {
    if (input) {
      input.addEventListener('input', () => clearValidation(input));
    }
  });
}

// --- Contact Form Validation ---
function setupContactValidation(form) {
  const name = form.querySelector('#contact-name');
  const email = form.querySelector('#contact-email');
  const message = form.querySelector('#contact-message');

  // Real-time validation
  if (name) {
    name.addEventListener('blur', () => {
      if (!name.value.trim()) showError(name, 'Name is required');
      else showSuccess(name);
    });
    name.addEventListener('input', () => clearValidation(name));
  }

  if (email) {
    email.addEventListener('blur', () => {
      if (!email.value.trim()) showError(email, 'Email is required');
      else if (!isValidEmail(email.value)) showError(email, 'Invalid email format');
      else showSuccess(email);
    });
    email.addEventListener('input', () => clearValidation(email));
  }

  if (message) {
    message.addEventListener('blur', () => {
      if (!message.value.trim()) showError(message, 'Please enter your message');
      else if (message.value.trim().length < 10) showError(message, 'Message must be at least 10 characters');
      else showSuccess(message);
    });
    message.addEventListener('input', () => clearValidation(message));
  }

  form.addEventListener('submit', (e) => {
    let isValid = true;

    if (!name.value.trim()) { showError(name, 'Name is required'); isValid = false; }
    if (!isValidEmail(email.value)) { showError(email, 'Valid email is required'); isValid = false; }
    if (!message.value.trim() || message.value.trim().length < 10) { 
      showError(message, 'Message must be at least 10 characters'); isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
    }
  });
}

// --- Add Habit Form Validation ---
function setupHabitFormValidation(form) {
  form.addEventListener('submit', (e) => {
    const name = form.querySelector('#habit-name');
    const category = form.querySelector('#habit-category');
    let isValid = true;

    if (name && !name.value.trim()) {
      showError(name, 'Habit name is required');
      isValid = false;
    }

    if (category && !category.value) {
      showError(category, 'Please select a category');
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
    }
  });
}

/* ==============================
   5. EVENT HANDLING
   ============================== */
function initEventHandling() {
  // Hero card interactive checks
  initHeroCardChecks();
  
  // Dashboard habit toggles
  initHabitToggles();

  // Emoji picker
  initEmojiPicker();

  // Hover effects on feature cards
  initCardHoverEffects();
}

function initHeroCardChecks() {
  const checks = document.querySelectorAll('.preview-habit .habit-check');
  checks.forEach(check => {
    check.addEventListener('click', () => {
      check.classList.toggle('checked');
      if (check.classList.contains('checked')) {
        check.innerHTML = '✓';
        // Confetti micro-animation
        createMicroConfetti(check);
      } else {
        check.innerHTML = '';
      }
    });
  });
}

function initHabitToggles() {
  const toggles = document.querySelectorAll('.habit-toggle');
  toggles.forEach(toggle => {
    toggle.addEventListener('click', (e) => {
      e.stopPropagation();
      toggle.classList.toggle('completed');
      
      if (toggle.classList.contains('completed')) {
        toggle.innerHTML = '✓';
        showToast('Habit completed! Keep it up! 🔥', 'success');
        createMicroConfetti(toggle);
        updateCompletionStats();
      } else {
        toggle.innerHTML = '';
        updateCompletionStats();
      }
    });
  });
}

function initEmojiPicker() {
  const emojiOptions = document.querySelectorAll('.emoji-option');
  const emojiInput = document.getElementById('habit-emoji');

  emojiOptions.forEach(option => {
    option.addEventListener('click', () => {
      emojiOptions.forEach(o => o.classList.remove('selected'));
      option.classList.add('selected');
      if (emojiInput) {
        emojiInput.value = option.dataset.emoji;
      }
    });
  });
}

function initCardHoverEffects() {
  const cards = document.querySelectorAll('.feature-card, .stat-card');
  cards.forEach(card => {
    card.addEventListener('mouseenter', () => {
      card.style.transform = 'translateY(-6px)';
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
    });
  });
}

/* ==============================
   COUNT-UP ANIMATION
   ============================== */
function initCountUpAnimation() {
  const counters = document.querySelectorAll('[data-count]');
  if (counters.length === 0) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const target = parseInt(entry.target.dataset.count);
        animateCount(entry.target, 0, target, 1500);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  counters.forEach(counter => observer.observe(counter));
}

function animateCount(el, start, end, duration) {
  const range = end - start;
  let current = start;
  const increment = range / (duration / 16);
  const suffix = el.dataset.suffix || '';

  const step = () => {
    current += increment;
    if (current >= end) {
      el.textContent = end.toLocaleString() + suffix;
      return;
    }
    el.textContent = Math.floor(current).toLocaleString() + suffix;
    requestAnimationFrame(step);
  };

  step();
}

/* ==============================
   TOOLTIPS (Custom)
   ============================== */
function initTooltips() {
  // Bootstrap tooltips init
  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  tooltipTriggerList.forEach(el => {
    new bootstrap.Tooltip(el);
  });
}

/* ==============================
   TOAST NOTIFICATIONS
   ============================== */
function showToast(message, type = 'info') {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const icons = {
    success: '✅',
    error: '❌',
    info: 'ℹ️',
    warning: '⚠️'
  };

  const toast = document.createElement('div');
  toast.className = `toast-forge ${type}`;
  toast.innerHTML = `<span>${icons[type] || ''}</span><span>${message}</span>`;
  container.appendChild(toast);

  // Auto-remove after 3s
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(20px)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

/* ==============================
   MICRO CONFETTI EFFECT
   ============================== */
function createMicroConfetti(element) {
  const rect = element.getBoundingClientRect();
  const colors = ['#7c3aed', '#06d6a0', '#f59e0b', '#3b82f6', '#f43f5e'];

  for (let i = 0; i < 8; i++) {
    const particle = document.createElement('div');
    particle.style.cssText = `
      position: fixed;
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: ${colors[Math.floor(Math.random() * colors.length)]};
      left: ${rect.left + rect.width / 2}px;
      top: ${rect.top + rect.height / 2}px;
      pointer-events: none;
      z-index: 9999;
      transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    `;
    document.body.appendChild(particle);

    requestAnimationFrame(() => {
      particle.style.left = `${rect.left + (Math.random() - 0.5) * 80}px`;
      particle.style.top = `${rect.top + (Math.random() - 0.5) * 80 - 20}px`;
      particle.style.opacity = '0';
      particle.style.transform = `scale(0)`;
    });

    setTimeout(() => particle.remove(), 700);
  }
}

/* ==============================
   COMPLETION STATS UPDATE
   ============================== */
function updateCompletionStats() {
  const toggles = document.querySelectorAll('.habit-toggle');
  const completed = document.querySelectorAll('.habit-toggle.completed');
  const total = toggles.length;
  const done = completed.length;
  const percentage = total > 0 ? Math.round((done / total) * 100) : 0;

  // Update completion text
  const completionText = document.getElementById('completionPercentage');
  if (completionText) completionText.textContent = `${percentage}%`;

  // Update progress ring if exists
  const ring = document.querySelector('.progress-ring-circle');
  if (ring) {
    const radius = ring.r.baseVal.value;
    const circumference = 2 * Math.PI * radius;
    ring.style.strokeDasharray = circumference;
    ring.style.strokeDashoffset = circumference - (percentage / 100) * circumference;
  }

  // Update counter
  const counterEl = document.getElementById('habitsCompletedCount');
  if (counterEl) counterEl.textContent = done;

  // Update progress bar
  const progressFill = document.querySelector('.daily-progress-fill');
  if (progressFill) progressFill.style.width = `${percentage}%`;
}

/* ==============================
   UTILITY — Shake animation
   ============================== */
const shakeStyle = document.createElement('style');
shakeStyle.textContent = `
  @keyframes shake {
    0%, 100% { transform: translateX(0); }
    20% { transform: translateX(-8px); }
    40% { transform: translateX(8px); }
    60% { transform: translateX(-4px); }
    80% { transform: translateX(4px); }
  }
  .shake {
    animation: shake 0.5s ease;
  }
`;
document.head.appendChild(shakeStyle);
