# 🔥 HabitForge — Interactive Habit Tracker

A full-stack web application for building, tracking, and visualizing daily habits. Built with **HTML, CSS, Bootstrap, JavaScript, PHP, and MySQL**.

---

## 📸 Features

### Frontend
- **Responsive Design** — Adapts seamlessly to desktop, tablet, and mobile screens using Bootstrap 5 and custom CSS.
- **Dynamic Content Updates** — Feature filtering by category with animated transitions.
- **Interactive Image Slider** — Automatic and manual slide navigation with keyboard support.
- **Form Validation** — Real-time client-side validation with email checks, password strength meter, and error feedback.
- **Smooth Scrolling** — All navigation links scroll smoothly to their target sections.
- **Event Handling** — Hover effects, click events, tooltips, and micro-confetti animations on habit completion.
- **Custom Animations** — Scroll-triggered reveal animations, fade-ins, staggered children, and floating elements.

### Backend
- **User Authentication** — Secure registration and login with hashed passwords (`password_hash()`), session management, and session ID regeneration.
- **Habit CRUD** — Create, track, and manage habits with emoji icons, categories, and frequency settings.
- **Streak Tracking** — Automatic streak calculation based on consecutive daily completions.
- **Contact Form** — Server-side validated contact form with database storage.
- **AJAX Habit Toggle** — Toggle habit completion without page reload, with real-time streak updates.

---

## 🗂 Project Structure

```
habitforge/
├── css/
│   ├── style.css           # Main stylesheet (design system)
│   └── dashboard.css       # Dashboard-specific styles
├── js/
│   ├── main.js             # Core JavaScript (all 6 features)
│   └── dashboard.js        # Dashboard interactions & charts
├── images/                 # Image assets
├── includes/
│   ├── db.php              # Database connection
│   ├── functions.php       # Helper/utility functions
│   ├── add_habit.php       # Add habit handler
│   └── toggle_habit.php    # AJAX habit toggle endpoint
├── auth/
│   ├── register.php        # User registration page + logic
│   ├── login.php           # User login page + logic
│   └── logout.php          # Session destruction
├── contact.php             # Contact form handler
├── index.php               # Main landing page (Home)
├── dashboard.php           # User dashboard (requires login)
├── database.sql            # MySQL database dump
└── README.md               # This file
```

---

## ⚙️ Setup Instructions

### Prerequisites
- **XAMPP** or **WAMP** installed on your machine
- A modern web browser (Chrome, Firefox, Edge)

### Step 1: Clone or Download
```bash
git clone https://github.com/your-username/habitforge.git
```
Or download and extract the ZIP into your XAMPP `htdocs` folder.

### Step 2: Place in Web Server
Copy the project folder into:
- **XAMPP:** `C:/xampp/htdocs/habitforge/`
- **WAMP:** `C:/wamp64/www/habitforge/`

### Step 3: Import the Database
1. Start **Apache** and **MySQL** from the XAMPP/WAMP control panel.
2. Open **phpMyAdmin** at `http://localhost/phpmyadmin`
3. Click **"Import"** in the top menu.
4. Select the `database.sql` file from the project root.
5. Click **"Go"** to execute. This creates the `habit_tracker` database with all required tables and sample data.

### Step 4: Configure Database Connection (if needed)
Open `includes/db.php` and verify these settings match your environment:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');              // Default XAMPP password is empty
define('DB_NAME', 'habit_tracker');
```

### Step 5: Run the Application
Open your browser and navigate to:
```
http://localhost/habitforge/
```

### Test Account
A sample user is included in the database:
- **Email:** `test@habitforge.app`
- **Password:** `Test1234`

---

## 🗃 Database Schema

### `users` Table
| Column     | Type         | Description               |
|------------|--------------|---------------------------|
| id         | INT (PK)     | Auto-increment ID         |
| username   | VARCHAR(30)  | Unique username           |
| email      | VARCHAR(255) | Unique email address      |
| password   | VARCHAR(255) | Hashed password           |
| created_at | DATETIME     | Registration timestamp    |

### `habits` Table
| Column         | Type          | Description                |
|----------------|---------------|----------------------------|
| id             | INT (PK)      | Auto-increment ID          |
| user_id        | INT (FK)      | References users.id        |
| name           | VARCHAR(100)  | Habit name                 |
| category       | VARCHAR(50)   | Category (health, fitness…)|
| emoji          | VARCHAR(10)   | Display emoji              |
| frequency      | VARCHAR(20)   | daily, weekdays, etc.      |
| goal           | VARCHAR(100)  | Optional goal text         |
| current_streak | INT           | Current consecutive days   |
| created_at     | DATETIME      | Creation timestamp         |

### `habit_logs` Table
| Column     | Type     | Description                         |
|------------|----------|-------------------------------------|
| id         | INT (PK) | Auto-increment ID                  |
| habit_id   | INT (FK) | References habits.id               |
| user_id    | INT (FK) | References users.id                |
| log_date   | DATE     | Date of completion                 |
| created_at | DATETIME | Timestamp                          |

### `messages` Table
| Column     | Type          | Description                |
|------------|---------------|----------------------------|
| id         | INT (PK)      | Auto-increment ID          |
| name       | VARCHAR(100)  | Sender's name              |
| email      | VARCHAR(255)  | Sender's email             |
| subject    | VARCHAR(200)  | Message subject            |
| message    | TEXT          | Message body               |
| created_at | DATETIME      | Submission timestamp       |

---

## 🎨 JavaScript Features Implemented

| # | Feature                  | Implementation                                          |
|---|--------------------------|--------------------------------------------------------|
| 1 | Dynamic Content Updates  | Feature card filtering by category with animations     |
| 2 | Interactive Image Slider | Auto-play slider with manual controls & keyboard nav   |
| 3 | Form Validation          | Email/password validation, password strength meter     |
| 4 | Smooth Scrolling         | Navigation links scroll smoothly to sections           |
| 5 | Event Handling           | Hover effects, tooltips, click events, confetti        |
| 6 | Custom Animations        | Scroll-triggered reveals, fade-ins, stagger effects    |

---

## 📝 Technologies Used

- **HTML5** — Semantic structure
- **CSS3** — Custom properties, gradients, glassmorphism, animations
- **Bootstrap 5.3** — Responsive grid, components, modals
- **JavaScript (ES6+)** — DOM manipulation, Intersection Observer, Fetch API
- **PHP 8+** — Server-side logic, session management
- **MySQL** — Relational database with InnoDB engine
- **Bootstrap Icons** — Icon library

---

## 👤 Author

Built as a mini-project for ICT2206 Web Development.

---

## 📄 License

This project is for educational purposes only.
