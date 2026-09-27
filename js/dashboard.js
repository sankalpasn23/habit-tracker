/* ============================================
   HabitForge — Dashboard JavaScript
   Manages dashboard-specific interactions,
   chart rendering, and habit CRUD operations.
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {
  initDashboardCharts();
  initWeeklyView();
  initAddHabitModal();
  initDashboardFilters();
  updateDashboardClock();
  initProgressRing();
  initHabitCompletion();
});

/* ==============================
   PROGRESS RING (SVG)
   ============================== */
function initProgressRing() {
  const ring = document.querySelector('.progress-ring-circle');
  if (!ring) return;

  const radius = ring.r.baseVal.value;
  const circumference = 2 * Math.PI * radius;

  ring.style.strokeDasharray = circumference;
  ring.style.strokeDashoffset = circumference;

  // Animate to current completion
  setTimeout(() => {
    const toggles = document.querySelectorAll('.habit-toggle');
    const completed = document.querySelectorAll('.habit-toggle.completed');
    const percentage = toggles.length > 0 ? (completed.length / toggles.length) * 100 : 0;
    
    const offset = circumference - (percentage / 100) * circumference;
    ring.style.transition = 'stroke-dashoffset 1s cubic-bezier(0.4, 0, 0.2, 1)';
    ring.style.strokeDashoffset = offset;

    const valueEl = document.getElementById('completionPercentage');
    if (valueEl) {
      animateValue(valueEl, 0, Math.round(percentage), 1000, '%');
    }
  }, 500);
}

function animateValue(el, start, end, duration, suffix = '') {
  const range = end - start;
  let current = start;
  const increment = range / (duration / 16);

  const step = () => {
    current += increment;
    if (current >= end) {
      el.textContent = end + suffix;
      return;
    }
    el.textContent = Math.floor(current) + suffix;
    requestAnimationFrame(step);
  };

  step();
}

/* ==============================
   BAR CHART — Weekly Habits
   ============================== */
function initDashboardCharts() {
  const barChart = document.querySelector('.bar-chart');
  if (!barChart) return;

  // Sample data — replace with real data from PHP when integrated
  const weekData = [
    { day: 'Mon', value: 80 },
    { day: 'Tue', value: 65 },
    { day: 'Wed', value: 90 },
    { day: 'Thu', value: 45 },
    { day: 'Fri', value: 75 },
    { day: 'Sat', value: 100 },
    { day: 'Sun', value: 55 }
  ];

  barChart.innerHTML = '';

  weekData.forEach((data, i) => {
    const group = document.createElement('div');
    group.className = 'bar-group';

    const bar = document.createElement('div');
    bar.className = 'bar';
    bar.style.height = '0px';
    bar.setAttribute('data-bs-toggle', 'tooltip');
    bar.setAttribute('data-bs-placement', 'top');
    bar.setAttribute('title', `${data.value}% completed`);

    const label = document.createElement('span');
    label.className = 'bar-label';
    label.textContent = data.day;

    group.appendChild(bar);
    group.appendChild(label);
    barChart.appendChild(group);

    // Animate in with stagger
    setTimeout(() => {
      bar.style.height = `${data.value}%`;
    }, 200 + i * 100);
  });

  // Re-init tooltips for dynamic elements
  const tooltipTriggers = barChart.querySelectorAll('[data-bs-toggle="tooltip"]');
  tooltipTriggers.forEach(el => new bootstrap.Tooltip(el));
}

/* ==============================
   WEEKLY VIEW
   ============================== */
function initWeeklyView() {
  const weekGrid = document.querySelector('.week-grid');
  if (!weekGrid) return;

  const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
  const today = new Date().getDay();
  // Convert JS day (0=Sun) to Mon-based index
  const todayIndex = today === 0 ? 6 : today - 1;

  // Sample completion data
  const completionData = [true, true, true, false, true, true, false];

  weekGrid.innerHTML = '';
  days.forEach((day, i) => {
    const cell = document.createElement('div');
    cell.className = 'week-day';
    if (i === todayIndex) cell.classList.add('today');
    if (completionData[i] && i <= todayIndex) cell.classList.add('completed');

    cell.innerHTML = `
      <span class="day-label">${day}</span>
      <span>${completionData[i] && i <= todayIndex ? '✓' : i > todayIndex ? '—' : '✗'}</span>
    `;

    weekGrid.appendChild(cell);
  });
}

/* ==============================
   ADD HABIT MODAL
   ============================== */
function initAddHabitModal() {
  const addBtn = document.querySelector('.add-habit-inline');
  const modal = document.getElementById('addHabitModal');
  
  if (addBtn && modal) {
    addBtn.addEventListener('click', () => {
      const bsModal = new bootstrap.Modal(modal);
      bsModal.show();
    });
  }

  // Handle form submission
  const form = document.getElementById('addHabitForm');
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      const name = form.querySelector('#habit-name')?.value;
      const category = form.querySelector('#habit-category')?.value;
      const emoji = form.querySelector('#habit-emoji')?.value || '✨';
      const frequency = form.querySelector('#habit-frequency')?.value || 'daily';

      if (!name || !category) return;

      // Add habit to the list dynamically
      addHabitToList(name, category, emoji, frequency);
      
      // Close modal
      const bsModal = bootstrap.Modal.getInstance(modal);
      if (bsModal) bsModal.hide();

      // Reset form
      form.reset();
      document.querySelectorAll('.emoji-option').forEach(o => o.classList.remove('selected'));

      showToast(`"${name}" added to your habits! 🎯`, 'success');
    });
  }
}

function addHabitToList(name, category, emoji, frequency) {
  const habitsList = document.querySelector('.habits-list');
  if (!habitsList) return;

  const item = document.createElement('div');
  item.className = 'habit-item';
  item.setAttribute('data-category', category);
  item.innerHTML = `
    <div class="habit-emoji">${emoji}</div>
    <div class="habit-details">
      <div class="habit-title">${name}</div>
      <div class="habit-meta">
        <span>${category}</span>
        <span>•</span>
        <span>${frequency}</span>
      </div>
    </div>
    <button class="habit-toggle" type="button" aria-label="Mark as complete">
    </button>
  `;

  // Insert before the "add" button
  const addBtn = habitsList.querySelector('.add-habit-inline');
  if (addBtn) {
    habitsList.insertBefore(item, addBtn);
  } else {
    habitsList.appendChild(item);
  }

  // Attach toggle event
  const toggle = item.querySelector('.habit-toggle');
  toggle.addEventListener('click', (e) => {
    e.stopPropagation();
    toggle.classList.toggle('completed');
    if (toggle.classList.contains('completed')) {
      toggle.innerHTML = '✓';
      if (typeof showToast === 'function') showToast('Habit completed! 🔥', 'success');
      if (typeof createMicroConfetti === 'function') createMicroConfetti(toggle);
    } else {
      toggle.innerHTML = '';
    }
    if (typeof updateCompletionStats === 'function') updateCompletionStats();
  });

  // Animate in
  item.style.opacity = '0';
  item.style.transform = 'translateY(10px)';
  requestAnimationFrame(() => {
    item.style.transition = 'all 0.3s ease';
    item.style.opacity = '1';
    item.style.transform = 'translateY(0)';
  });
}

/* ==============================
   DASHBOARD FILTERS
   ============================== */
function initDashboardFilters() {
  const filterBtns = document.querySelectorAll('.dash-filter-btn');
  const habits = document.querySelectorAll('.habits-list .habit-item');

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const filter = btn.dataset.filter;

      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      habits.forEach(habit => {
        const category = habit.dataset.category;
        if (filter === 'all' || category === filter) {
          habit.style.display = '';
          habit.style.opacity = '1';
        } else {
          habit.style.display = 'none';
        }
      });
    });
  });
}

/* ==============================
   CLOCK / DATE DISPLAY
   ============================== */
function updateDashboardClock() {
  const dateEl = document.querySelector('.welcome-date');
  if (!dateEl) return;

  const now = new Date();
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  dateEl.textContent = now.toLocaleDateString('en-US', options);
}

/* ==============================
   HABIT COMPLETION PERSISTENCE
   ============================== */
function initHabitCompletion() {
  // Check localStorage for today's completions
  const today = new Date().toISOString().split('T')[0];
  const savedData = localStorage.getItem(`habitforge_${today}`);
  
  if (savedData) {
    const completed = JSON.parse(savedData);
    const toggles = document.querySelectorAll('.habit-toggle');
    
    toggles.forEach((toggle, i) => {
      if (completed.includes(i)) {
        toggle.classList.add('completed');
        toggle.innerHTML = '✓';
      }
    });
  }

  // Save on change
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('habit-toggle') || e.target.closest('.habit-toggle')) {
      saveHabitCompletions();
    }
  });
}

function saveHabitCompletions() {
  const today = new Date().toISOString().split('T')[0];
  const toggles = document.querySelectorAll('.habit-toggle');
  const completed = [];
  
  toggles.forEach((toggle, i) => {
    if (toggle.classList.contains('completed')) {
      completed.push(i);
    }
  });

  localStorage.setItem(`habitforge_${today}`, JSON.stringify(completed));
}
