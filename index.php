<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? htmlspecialchars($_SESSION['username']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="HabitForge — Build unbreakable habits with streak tracking, progress visualization, and daily goal management. Your personal habit tracker.">
  <title>HabitForge — Build Better Habits, One Day at a Time</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css">

  <!-- Favicon -->
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔥</text></svg>">
</head>
<body>

  <!-- Background Particles -->
  <div class="bg-particles">
    <div class="orb"></div>
    <div class="orb"></div>
    <div class="orb"></div>
  </div>

  <!-- ===== NAVBAR ===== -->
  <nav class="navbar navbar-expand-lg navbar-forge" id="mainNav">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <span class="brand-icon">🔥</span>HabitForge
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
          <li class="nav-item"><a class="nav-link" href="#how-it-works">How It Works</a></li>
          <li class="nav-item"><a class="nav-link" href="#showcase">Showcase</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
        </ul>
        <div class="nav-auth d-flex align-items-center gap-2">
          <?php if ($isLoggedIn): ?>
            <a href="dashboard.php" class="btn-forge btn-forge-sm btn-forge-primary">Dashboard</a>
            <a href="auth/logout.php" class="btn-login">Logout</a>
          <?php else: ?>
            <a href="auth/login.php" class="btn-login">Log In</a>
            <a href="auth/register.php" class="btn-forge btn-forge-sm btn-forge-primary">Sign Up</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>

  <!-- ===== HERO SECTION ===== -->
  <section class="hero-section" id="home">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <div class="hero-badge">
            <span class="pulse-dot"></span>
            Build habits that stick
          </div>
          <h1 class="hero-title">
            Forge Your <span class="gradient-text">Best Self</span>, One Habit at a Time
          </h1>
          <p class="hero-subtitle">
            Track your daily habits, visualize your progress, and build unstoppable momentum with streaks and smart insights. HabitForge makes consistency feel effortless.
          </p>
          <div class="hero-cta">
            <a href="auth/register.php" class="btn-forge btn-forge-primary" id="hero-cta-signup">
              <i class="bi bi-rocket-takeoff"></i> Start Forging
            </a>
            <a href="#features" class="btn-forge btn-forge-outline" id="hero-cta-features">
              <i class="bi bi-play-circle"></i> See Features
            </a>
          </div>
          <div class="hero-stats">
            <div class="stat">
              <div class="stat-number" data-count="2500" data-suffix="+">0</div>
              <div class="stat-label">Active Users</div>
            </div>
            <div class="stat">
              <div class="stat-number" data-count="50000" data-suffix="+">0</div>
              <div class="stat-label">Habits Tracked</div>
            </div>
            <div class="stat">
              <div class="stat-number" data-count="95" data-suffix="%">0</div>
              <div class="stat-label">Success Rate</div>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="hero-illustration">
            <!-- Floating badges -->
            <div class="floating-badge streak-badge">
              🔥 12-Day Streak!
            </div>
            <div class="floating-badge completion-badge">
              ✅ 87% Today
            </div>

            <!-- Preview Card -->
            <div class="hero-card-preview">
              <div class="preview-header">
                <span class="preview-title">Today's Habits</span>
                <span class="preview-date" id="heroDate"></span>
              </div>

              <div class="preview-habit">
                <div class="habit-icon purple">🧘</div>
                <div class="habit-info">
                  <div class="habit-name">Morning Meditation</div>
                  <div class="habit-streak">🔥 12 day streak</div>
                </div>
                <div class="habit-check checked" data-bs-toggle="tooltip" title="Click to toggle">✓</div>
              </div>

              <div class="preview-habit">
                <div class="habit-icon green">📖</div>
                <div class="habit-info">
                  <div class="habit-name">Read 20 Pages</div>
                  <div class="habit-streak">🔥 8 day streak</div>
                </div>
                <div class="habit-check checked" data-bs-toggle="tooltip" title="Click to toggle">✓</div>
              </div>

              <div class="preview-habit">
                <div class="habit-icon amber">💪</div>
                <div class="habit-info">
                  <div class="habit-name">Workout 30min</div>
                  <div class="habit-streak">🔥 5 day streak</div>
                </div>
                <div class="habit-check" data-bs-toggle="tooltip" title="Click to toggle"></div>
              </div>

              <div class="preview-habit">
                <div class="habit-icon blue">💧</div>
                <div class="habit-info">
                  <div class="habit-name">Drink 8 Glasses</div>
                  <div class="habit-streak">🔥 15 day streak</div>
                </div>
                <div class="habit-check" data-bs-toggle="tooltip" title="Click to toggle"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== FEATURES SECTION ===== -->
  <section class="section" id="features">
    <div class="container">
      <div class="section-header reveal">
        <div class="section-tag"><i class="bi bi-stars"></i> Features</div>
        <h2 class="section-title">Everything You Need to <span class="text-gradient">Build Habits</span></h2>
        <p class="section-subtitle">Powerful tools designed to help you create, track, and maintain habits that transform your life.</p>
      </div>

      <!-- Filter Buttons -->
      <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap reveal" id="featureFilters">
        <button class="filter-btn active" data-filter="all">All Features</button>
        <button class="filter-btn" data-filter="tracking">Tracking</button>
        <button class="filter-btn" data-filter="analytics">Analytics</button>
        <button class="filter-btn" data-filter="motivation">Motivation</button>
      </div>

      <div class="row g-4 stagger-children" id="featureCards">
        <div class="col-md-6 col-lg-4" data-category="tracking">
          <div class="feature-card">
            <div class="feature-icon purple"><i class="bi bi-check2-circle"></i></div>
            <h3>Smart Habit Tracking</h3>
            <p>Create custom habits with flexible scheduling — daily, weekly, or custom patterns. Check off completions with a single tap.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4" data-category="analytics">
          <div class="feature-card">
            <div class="feature-icon green"><i class="bi bi-graph-up-arrow"></i></div>
            <h3>Progress Visualization</h3>
            <p>Beautiful charts and heatmaps show your consistency over time. See your growth trajectory and identify patterns.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4" data-category="motivation">
          <div class="feature-card">
            <div class="feature-icon amber"><i class="bi bi-fire"></i></div>
            <h3>Streak Counter</h3>
            <p>Build momentum with streak tracking. Watch your flame grow as you maintain consistency day after day.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4" data-category="tracking">
          <div class="feature-card">
            <div class="feature-icon blue"><i class="bi bi-bullseye"></i></div>
            <h3>Goal Setting</h3>
            <p>Set SMART goals for each habit with milestones and deadlines. Break big goals into achievable daily actions.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4" data-category="analytics">
          <div class="feature-card">
            <div class="feature-icon rose"><i class="bi bi-calendar-week"></i></div>
            <h3>Weekly Reports</h3>
            <p>Get comprehensive weekly summaries of your habit performance, with insights and recommendations for improvement.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4" data-category="motivation">
          <div class="feature-card">
            <div class="feature-icon cyan"><i class="bi bi-trophy"></i></div>
            <h3>Achievement Badges</h3>
            <p>Earn badges for milestones like 7-day streaks, 30-day consistency, and habit mastery. Gamify your growth journey.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== HOW IT WORKS ===== -->
  <section class="section" id="how-it-works" style="background: var(--gradient-card);">
    <div class="container">
      <div class="section-header reveal">
        <div class="section-tag"><i class="bi bi-gear"></i> How It Works</div>
        <h2 class="section-title">Get Started in <span class="text-gradient">3 Simple Steps</span></h2>
        <p class="section-subtitle">Building habits shouldn't be complicated. Here's how HabitForge makes it dead simple.</p>
      </div>

      <div class="row g-4">
        <div class="col-md-4 reveal">
          <div class="step-card">
            <div style="position: relative;">
              <div class="step-number">1</div>
              <div class="step-connector"></div>
            </div>
            <h3>Create Your Habits</h3>
            <p>Choose from templates or create custom habits. Set your frequency, reminders, and goals in seconds.</p>
          </div>
        </div>
        <div class="col-md-4 reveal">
          <div class="step-card">
            <div style="position: relative;">
              <div class="step-number">2</div>
              <div class="step-connector"></div>
            </div>
            <h3>Track Daily</h3>
            <p>Check off habits as you complete them. Watch your streaks grow and your progress bars fill up.</p>
          </div>
        </div>
        <div class="col-md-4 reveal">
          <div class="step-card">
            <div class="step-number">3</div>
            <h3>See Your Growth</h3>
            <p>Review analytics, earn badges, and celebrate milestones. Your consistency builds your success story.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== IMAGE SLIDER / SHOWCASE ===== -->
  <section class="section slider-section" id="showcase">
    <div class="container">
      <div class="section-header reveal">
        <div class="section-tag"><i class="bi bi-collection"></i> Showcase</div>
        <h2 class="section-title">Explore <span class="text-gradient">HabitForge</span> in Action</h2>
        <p class="section-subtitle">See how our features come together to create a seamless habit-building experience.</p>
      </div>

      <div class="image-slider reveal" id="mainSlider">
        <div class="slider-container">
          <div class="slide slide-1 active">
            <div class="slide-content">
              <div class="slide-icon">📊</div>
              <h3>Dashboard Overview</h3>
              <p>Your personalized command center. See today's habits, weekly progress, and streak stats all in one beautiful view.</p>
            </div>
          </div>
          <div class="slide slide-2">
            <div class="slide-content">
              <div class="slide-icon">🔥</div>
              <h3>Streak Tracking</h3>
              <p>Watch your streaks grow with satisfying visual counters. Don't break the chain — every day counts toward building unbreakable habits.</p>
            </div>
          </div>
          <div class="slide slide-3">
            <div class="slide-content">
              <div class="slide-icon">📈</div>
              <h3>Progress Analytics</h3>
              <p>Dive deep into your habit data with weekly bar charts, completion heatmaps, and trend analysis. Knowledge is power.</p>
            </div>
          </div>
          <div class="slide slide-4">
            <div class="slide-content">
              <div class="slide-icon">🏆</div>
              <h3>Achievements & Badges</h3>
              <p>Earn milestone badges as you hit 7, 14, 30, and 100-day streaks. Gamification keeps you motivated and engaged.</p>
            </div>
          </div>
        </div>

        <!-- Slider Controls -->
        <button class="slider-btn prev" id="sliderPrev" aria-label="Previous slide"><i class="bi bi-chevron-left"></i></button>
        <button class="slider-btn next" id="sliderNext" aria-label="Next slide"><i class="bi bi-chevron-right"></i></button>

        <!-- Dots -->
        <div class="slider-dots">
          <button class="slider-dot active" data-slide="0" aria-label="Go to slide 1"></button>
          <button class="slider-dot" data-slide="1" aria-label="Go to slide 2"></button>
          <button class="slider-dot" data-slide="2" aria-label="Go to slide 3"></button>
          <button class="slider-dot" data-slide="3" aria-label="Go to slide 4"></button>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== CONTACT SECTION ===== -->
  <section class="section" id="contact">
    <div class="container">
      <div class="section-header reveal">
        <div class="section-tag"><i class="bi bi-envelope"></i> Contact</div>
        <h2 class="section-title">Get In <span class="text-gradient">Touch</span></h2>
        <p class="section-subtitle">Have questions, feedback, or just want to say hi? We'd love to hear from you.</p>
      </div>

      <div class="row g-4">
        <div class="col-lg-5 reveal-left">
          <div class="contact-info-card">
            <h3 class="mb-4">Let's Connect</h3>
            <div class="contact-info-item">
              <div class="info-icon"><i class="bi bi-envelope-fill"></i></div>
              <div>
                <h4>Email Us</h4>
                <p>support@habitforge.app</p>
              </div>
            </div>
            <div class="contact-info-item">
              <div class="info-icon"><i class="bi bi-geo-alt-fill"></i></div>
              <div>
                <h4>Location</h4>
                <p>Colombo, Sri Lanka</p>
              </div>
            </div>
            <div class="contact-info-item">
              <div class="info-icon"><i class="bi bi-clock-fill"></i></div>
              <div>
                <h4>Response Time</h4>
                <p>We typically respond within 24 hours</p>
              </div>
            </div>

            <div class="footer-socials mt-4">
              <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
              <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
              <a href="#" aria-label="GitHub"><i class="bi bi-github"></i></a>
              <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
            </div>
          </div>
        </div>

        <div class="col-lg-7 reveal-right">
          <div class="form-card">
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
              <div class="alert-forge success">
                <i class="bi bi-check-circle-fill"></i>
                Your message has been sent successfully! We'll get back to you soon.
              </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
              <div class="alert-forge error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <?php echo htmlspecialchars($_GET['error']); ?>
              </div>
            <?php endif; ?>

            <form id="contactForm" method="POST" action="contact.php" novalidate>
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="contact-name">Full Name</label>
                    <input type="text" class="form-control-forge" id="contact-name" name="name" placeholder="Your name" required>
                    <div class="error-message">Name is required</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="contact-email">Email Address</label>
                    <input type="email" class="form-control-forge" id="contact-email" name="email" placeholder="you@example.com" required>
                    <div class="error-message">Valid email is required</div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="contact-subject">Subject</label>
                <input type="text" class="form-control-forge" id="contact-subject" name="subject" placeholder="What's this about?">
              </div>
              <div class="form-group">
                <label for="contact-message">Message</label>
                <textarea class="form-control-forge" id="contact-message" name="message" rows="5" placeholder="Tell us what's on your mind..." required></textarea>
                <div class="error-message">Please enter your message (at least 10 characters)</div>
              </div>
              <button type="submit" class="btn-forge btn-forge-primary w-100" id="contactSubmitBtn">
                <i class="bi bi-send"></i> Send Message
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== FOOTER ===== -->
  <footer class="footer-forge">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="footer-brand">🔥 HabitForge</div>
          <p class="footer-desc">Build unbreakable habits, track your progress, and transform your life — one day at a time.</p>
          <div class="footer-socials">
            <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
            <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" aria-label="GitHub"><i class="bi bi-github"></i></a>
          </div>
        </div>
        <div class="col-lg-2 col-md-4">
          <div class="footer-title">Product</div>
          <ul class="footer-links">
            <li><a href="#features">Features</a></li>
            <li><a href="#how-it-works">How It Works</a></li>
            <li><a href="#showcase">Showcase</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
          </ul>
        </div>
        <div class="col-lg-2 col-md-4">
          <div class="footer-title">Account</div>
          <ul class="footer-links">
            <li><a href="auth/login.php">Log In</a></li>
            <li><a href="auth/register.php">Sign Up</a></li>
            <li><a href="auth/logout.php">Logout</a></li>
          </ul>
        </div>
        <div class="col-lg-2 col-md-4">
          <div class="footer-title">Support</div>
          <ul class="footer-links">
            <li><a href="#contact">Contact Us</a></li>
            <li><a href="#">FAQ</a></li>
            <li><a href="#">Privacy Policy</a></li>
          </ul>
        </div>
        <div class="col-lg-2">
          <div class="footer-title">Connect</div>
          <ul class="footer-links">
            <li><a href="#">Twitter</a></li>
            <li><a href="#">Instagram</a></li>
            <li><a href="#">GitHub</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2026 HabitForge. Built with 🔥 and consistency.</p>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom JS -->
  <script src="js/main.js"></script>

  <script>
    // Set hero date dynamically
    const heroDateEl = document.getElementById('heroDate');
    if (heroDateEl) {
      const now = new Date();
      heroDateEl.textContent = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    }
  </script>
</body>
</html>
