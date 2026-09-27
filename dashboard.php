<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username']);

// Fetch user's habits from database
$habits = [];
$stmt = $conn->prepare("SELECT * FROM habits WHERE user_id = ? ORDER BY created_at ASC");
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $habits[] = $row;
    }
    $stmt->close();
}

// Check today's completions
$today = date('Y-m-d');
$completedHabits = [];
$stmt = $conn->prepare("SELECT habit_id FROM habit_logs WHERE user_id = ? AND log_date = ?");
if ($stmt) {
    $stmt->bind_param("is", $userId, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $completedHabits[] = $row['habit_id'];
    }
    $stmt->close();
}

// Calculate stats
$totalHabits = count($habits);
$completedToday = count($completedHabits);
$completionPercentage = $totalHabits > 0 ? round(($completedToday / $totalHabits) * 100) : 0;

// Get best streak
$bestStreak = 0;
$stmt = $conn->prepare("SELECT MAX(current_streak) as best FROM habits WHERE user_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $bestStreak = $row['best'] ?? 0;
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="HabitForge Dashboard — Track and manage your daily habits, view progress, and maintain streaks.">
  <title>Dashboard — HabitForge</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/dashboard.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔥</text></svg>">
</head>
<body>
  <div class="bg-particles">
    <div class="orb"></div>
    <div class="orb"></div>
    <div class="orb"></div>
  </div>

  <!-- Dashboard Navbar -->
  <nav class="navbar navbar-expand-lg navbar-forge scrolled" id="dashNav">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <span class="brand-icon">🔥</span>HabitForge
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#dashNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="dashNavbar">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php#contact">Contact</a></li>
        </ul>
        <div class="nav-auth d-flex align-items-center gap-3">
          <span class="text-secondary" style="font-size: 0.9rem;">
            Hey, <strong class="text-gradient"><?php echo $username; ?></strong>
          </span>
          <a href="auth/logout.php" class="btn-login btn-forge-sm">
            <i class="bi bi-box-arrow-right"></i> Logout
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Dashboard Content -->
  <div class="dashboard-wrapper">
    <div class="container">

      <!-- Welcome Banner -->
      <div class="welcome-banner reveal">
        <h2>Welcome back, <span class="text-gradient"><?php echo $username; ?></span> 👋</h2>
        <p class="welcome-subtitle">Let's make today count. You've got this!</p>
        <p class="welcome-date"></p>
      </div>

      <!-- Success/Error Messages -->
      <?php if (isset($_GET['habit_added'])): ?>
        <div class="alert-forge success"><i class="bi bi-check-circle-fill"></i> New habit added successfully!</div>
      <?php endif; ?>
      <?php if (isset($_GET['habit_deleted'])): ?>
        <div class="alert-forge success"><i class="bi bi-check-circle-fill"></i> Habit removed.</div>
      <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>
        <div class="alert-forge error"><i class="bi bi-exclamation-circle-fill"></i> <?php echo htmlspecialchars($_GET['error']); ?></div>
      <?php endif; ?>

      <!-- Stat Cards -->
      <div class="row g-4 mb-4">
        <div class="col-6 col-lg-3 reveal">
          <div class="stat-card">
            <div class="stat-icon purple"><i class="bi bi-list-check"></i></div>
            <div class="stat-value" style="color: var(--primary-light);"><?php echo $totalHabits; ?></div>
            <div class="stat-label">Total Habits</div>
          </div>
        </div>
        <div class="col-6 col-lg-3 reveal">
          <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
            <div class="stat-value" style="color: var(--accent);"><?php echo $completedToday; ?></div>
            <div class="stat-label">Completed Today</div>
          </div>
        </div>
        <div class="col-6 col-lg-3 reveal">
          <div class="stat-card">
            <div class="stat-icon amber"><i class="bi bi-fire"></i></div>
            <div class="stat-value" style="color: var(--streak);"><?php echo $bestStreak; ?></div>
            <div class="stat-label">Best Streak</div>
          </div>
        </div>
        <div class="col-6 col-lg-3 reveal">
          <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-percent"></i></div>
            <div class="stat-value" style="color: #60a5fa;"><?php echo $completionPercentage; ?>%</div>
            <div class="stat-label">Today's Progress</div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <!-- Left Column: Today's Habits -->
        <div class="col-lg-8">
          <div class="habits-panel reveal">
            <div class="panel-header">
              <h3><i class="bi bi-journal-check"></i> Today's Habits</h3>
              <button class="panel-action" data-bs-toggle="modal" data-bs-target="#addHabitModal">
                <i class="bi bi-plus-circle"></i> Add Habit
              </button>
            </div>

            <!-- Category Filters -->
            <div class="d-flex gap-2 mb-3 flex-wrap">
              <button class="filter-btn dash-filter-btn active" data-filter="all">All</button>
              <button class="filter-btn dash-filter-btn" data-filter="health">Health</button>
              <button class="filter-btn dash-filter-btn" data-filter="fitness">Fitness</button>
              <button class="filter-btn dash-filter-btn" data-filter="learning">Learning</button>
              <button class="filter-btn dash-filter-btn" data-filter="mindfulness">Mindfulness</button>
              <button class="filter-btn dash-filter-btn" data-filter="productivity">Productivity</button>
            </div>

            <!-- Habits List -->
            <div class="habits-list">
              <?php if (empty($habits)): ?>
                <div class="text-center py-4">
                  <p class="text-secondary">No habits yet. Start by adding your first habit!</p>
                </div>
              <?php else: ?>
                <?php foreach ($habits as $habit): ?>
                  <?php $isCompleted = in_array($habit['id'], $completedHabits); ?>
                  <div class="habit-item" data-category="<?php echo htmlspecialchars($habit['category']); ?>" data-habit-id="<?php echo $habit['id']; ?>">
                    <div class="habit-emoji"><?php echo htmlspecialchars($habit['emoji']); ?></div>
                    <div class="habit-details">
                      <div class="habit-title"><?php echo htmlspecialchars($habit['name']); ?></div>
                      <div class="habit-meta">
                        <span><?php echo htmlspecialchars(ucfirst($habit['category'])); ?></span>
                        <span>•</span>
                        <span>🔥 <?php echo $habit['current_streak']; ?> day streak</span>
                      </div>
                    </div>
                    <button class="habit-toggle <?php echo $isCompleted ? 'completed' : ''; ?>" 
                            type="button" 
                            data-habit-id="<?php echo $habit['id']; ?>"
                            onclick="toggleHabit(this, <?php echo $habit['id']; ?>)"
                            aria-label="Mark as complete">
                      <?php echo $isCompleted ? '✓' : ''; ?>
                    </button>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>

              <!-- Add Habit Button -->
              <div class="add-habit-inline">
                <i class="bi bi-plus-lg"></i> Add a new habit
              </div>
            </div>

            <!-- Daily Progress Bar -->
            <div class="mt-3">
              <div class="d-flex justify-content-between mb-1">
                <span class="text-secondary" style="font-size: 0.85rem;">Daily Progress</span>
                <span class="text-gradient" style="font-size: 0.85rem; font-weight: 600;" id="completionPercentage"><?php echo $completionPercentage; ?>%</span>
              </div>
              <div class="progress-bar-forge">
                <div class="progress-fill daily-progress-fill" style="width: <?php echo $completionPercentage; ?>%;"></div>
              </div>
            </div>
          </div>

          <!-- Weekly Chart -->
          <div class="habits-panel mt-4 reveal">
            <div class="panel-header">
              <h3><i class="bi bi-bar-chart-line"></i> Weekly Performance</h3>
            </div>
            <div class="bar-chart" id="weeklyChart"></div>
          </div>
        </div>

        <!-- Right Column: Stats & Weekly View -->
        <div class="col-lg-4">
          <!-- Completion Ring -->
          <div class="dash-card mb-4 reveal">
            <div class="dash-card-title">Today's Completion</div>
            <div class="completion-ring-wrapper">
              <div class="progress-ring-container">
                <svg width="140" height="140">
                  <circle cx="70" cy="70" r="60" fill="none" stroke="var(--bg-input)" stroke-width="10"/>
                  <circle class="progress-ring-circle" cx="70" cy="70" r="60" fill="none" 
                          stroke="url(#ringGradient)" stroke-width="10" stroke-linecap="round"
                          transform="rotate(-90 70 70)"
                          style="stroke-dasharray: 377; stroke-dashoffset: 377;"/>
                  <defs>
                    <linearGradient id="ringGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" stop-color="#7c3aed"/>
                      <stop offset="100%" stop-color="#06d6a0"/>
                    </linearGradient>
                  </defs>
                </svg>
                <div class="ring-label">
                  <span class="ring-value text-gradient" id="completionPercentage"><?php echo $completionPercentage; ?>%</span>
                  <span class="ring-text">Complete</span>
                </div>
              </div>
            </div>
            <div class="text-center">
              <span class="text-secondary" style="font-size: 0.85rem;">
                <span id="habitsCompletedCount"><?php echo $completedToday; ?></span> of <?php echo $totalHabits; ?> habits done
              </span>
            </div>
          </div>

          <!-- This Week -->
          <div class="dash-card mb-4 reveal">
            <div class="dash-card-title">This Week</div>
            <div class="week-grid" id="weekGrid"></div>
          </div>

          <!-- Recent Activity -->
          <div class="dash-card reveal">
            <div class="dash-card-title">Recent Activity</div>
            <div class="activity-item">
              <div class="activity-dot completed"></div>
              <div>
                <div class="activity-text"><strong>Morning Meditation</strong> completed</div>
                <div class="activity-time">2 hours ago</div>
              </div>
            </div>
            <div class="activity-item">
              <div class="activity-dot streak"></div>
              <div>
                <div class="activity-text"><strong>🔥 7-day streak</strong> achieved!</div>
                <div class="activity-time">Yesterday</div>
              </div>
            </div>
            <div class="activity-item">
              <div class="activity-dot added"></div>
              <div>
                <div class="activity-text">New habit <strong>"Read 20 Pages"</strong> added</div>
                <div class="activity-time">2 days ago</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== ADD HABIT MODAL ===== -->
  <div class="modal fade modal-forge" id="addHabitModal" tabindex="-1" aria-labelledby="addHabitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addHabitModalLabel"><i class="bi bi-plus-circle"></i> Add New Habit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="addHabitForm" method="POST" action="includes/add_habit.php">
          <div class="modal-body">
            <div class="form-group">
              <label for="habit-name">Habit Name</label>
              <input type="text" class="form-control-forge" id="habit-name" name="name" placeholder="e.g., Morning Meditation" required>
              <div class="error-message">Habit name is required</div>
            </div>

            <div class="form-group">
              <label for="habit-category">Category</label>
              <select class="form-control-forge" id="habit-category" name="category" required>
                <option value="">Select a category</option>
                <option value="health">🍎 Health</option>
                <option value="fitness">💪 Fitness</option>
                <option value="learning">📚 Learning</option>
                <option value="mindfulness">🧘 Mindfulness</option>
                <option value="productivity">⚡ Productivity</option>
                <option value="social">🤝 Social</option>
                <option value="creative">🎨 Creative</option>
              </select>
              <div class="error-message">Please select a category</div>
            </div>

            <div class="form-group">
              <label>Choose an Icon</label>
              <input type="hidden" id="habit-emoji" name="emoji" value="✨">
              <div class="emoji-picker">
                <div class="emoji-option selected" data-emoji="✨">✨</div>
                <div class="emoji-option" data-emoji="🧘">🧘</div>
                <div class="emoji-option" data-emoji="📖">📖</div>
                <div class="emoji-option" data-emoji="💪">💪</div>
                <div class="emoji-option" data-emoji="💧">💧</div>
                <div class="emoji-option" data-emoji="🏃">🏃</div>
                <div class="emoji-option" data-emoji="🎯">🎯</div>
                <div class="emoji-option" data-emoji="🍎">🍎</div>
                <div class="emoji-option" data-emoji="📝">📝</div>
                <div class="emoji-option" data-emoji="🎵">🎵</div>
                <div class="emoji-option" data-emoji="🌅">🌅</div>
                <div class="emoji-option" data-emoji="😴">😴</div>
              </div>
            </div>

            <div class="form-group">
              <label for="habit-frequency">Frequency</label>
              <select class="form-control-forge" id="habit-frequency" name="frequency">
                <option value="daily">Daily</option>
                <option value="weekdays">Weekdays Only</option>
                <option value="weekends">Weekends Only</option>
                <option value="custom">Custom</option>
              </select>
            </div>

            <div class="form-group">
              <label for="habit-goal">Daily Goal (optional)</label>
              <input type="text" class="form-control-forge" id="habit-goal" name="goal" placeholder="e.g., 30 minutes, 8 glasses">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-forge btn-forge-outline btn-forge-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-forge btn-forge-primary btn-forge-sm">
              <i class="bi bi-plus-lg"></i> Add Habit
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer-forge" style="margin-top: 60px;">
    <div class="container">
      <div class="footer-bottom" style="margin-top: 0; padding-top: 0; border-top: none;">
        <p>&copy; 2026 HabitForge. Built with 🔥 and consistency.</p>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/main.js"></script>
  <script src="js/dashboard.js"></script>

  <script>
    // AJAX habit toggle — syncs with backend
    function toggleHabit(btn, habitId) {
      const isCompleting = !btn.classList.contains('completed');

      fetch('includes/toggle_habit.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          habit_id: habitId,
          completed: isCompleting
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          // Visual state already handled by event listener in main.js
          // Update streak display
          const habitItem = btn.closest('.habit-item');
          if (habitItem && data.streak !== undefined) {
            const metaEl = habitItem.querySelector('.habit-meta');
            if (metaEl) {
              const streakSpan = metaEl.querySelectorAll('span');
              if (streakSpan[2]) {
                streakSpan[2].textContent = `🔥 ${data.streak} day streak`;
              }
            }
          }
        }
      })
      .catch(err => {
        console.error('Toggle error:', err);
      });
    }
  </script>
</body>
</html>
