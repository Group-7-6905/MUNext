# MUNext - Memorial University Job Board

[![CI/CD Pipeline](https://github.com/Group-7-6905/MUNext/workflows/CI%2FCD%20Pipeline/badge.svg)](https://github.com/Group-7-6905/MUNext/actions)
[![PHP Version](https://img.shields.io/badge/PHP-7.4+-blue.svg)](https://php.net)
[![Code Quality](https://img.shields.io/badge/code%20quality-A-brightgreen.svg)](https://github.com/Group-7-6905/MUNext)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

> Professional job board platform for Memorial University community 

## 🧪 Testing & Code Quality

### Running Tests

\`\`\`bash
# Run all linters
npm run lint:all

# Run PHP unit tests
npm run test:php

# Run E2E tests
npm run test:e2e

# Run everything
npm run ci
\`\`\`

### Code Quality Tools

- **PHP_CodeSniffer**: Enforces PHP coding standards (PSR-12)
- **ESLint**: JavaScript linting and code quality
- **Stylelint**: CSS linting and best practices
- **PHPUnit**: Unit and integration testing
- **Cypress**: End-to-end testing
- **PHPStan**: Static analysis for PHP

### Continuous Integration

Every push and pull request automatically runs:
✅ Code quality checks (linters)
✅ Unit tests
✅ Integration tests
✅ E2E tests
✅ Security scans

See [CI/CD Pipeline](.github/workflows/ci.yml) for details.










<div align="center">

**Connecting MUN Talent with Opportunity**

[![Live Demo](https://img.shields.io/badge/demo-live-success)](https://munext.ca/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/license-Academic-lightgrey)](LICENSE)

[Live Demo](https://munext.ca/) • [Report Bug](https://github.com/Group-7-6905/MUNext/issues) • [Request Feature](https://github.com/Group-7-6905/MUNext/issues)

</div>

---

## 📋 Table of Contents

- [About The Project](#about-the-project)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Getting Started](#getting-started)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Demo Accounts](#demo-accounts)
- [Contributing](#contributing)
- [Team](#team)
- [License](#license)

---

## 🎯 About The Project

**MUNext** is a dedicated job board platform designed specifically for Memorial University of Newfoundland (MUN) students, alumni, and employers. The platform bridges the gap between MUN talent and employment opportunities by providing a campus-specific, tailored job search experience.

### The Problem

MUN community members currently rely on generic external job boards that:
- Lack campus-specific tailoring
- Don't cater to new graduates and entry-level positions
- Miss opportunities within the MUN network
- Provide limited connection to local employers

### Our Solution

MUNext provides:
- **Campus-Focused**: Exclusively for MUN students, alumni, and employers
- **Tailored Opportunities**: Jobs suited for new graduates and students
- **Direct Connection**: Employers get direct access to MUN talent pool
- **Comprehensive Tools**: Application tracking, notifications, and analytics

---

## ✨ Features

### For Job Seekers (Students & Alumni)

- 🔐 **User Authentication**: Secure registration and login system
- 👤 **Profile Management**: Create and maintain professional profiles
- 🔍 **Advanced Job Search**: Filter by location, job type, salary, and more
- 📄 **Resume Upload**: Attach CV/resume to applications
- 📝 **Application Tracking**: Monitor application status in real-time
- 🔔 **Smart Notifications**: Get alerts for new relevant opportunities
- 💼 **Saved Jobs**: Bookmark positions for later review
- 📊 **Application History**: Track all past applications

### For Employers

- 🏢 **Company Profiles**: Showcase your organization
- 📢 **Job Posting Management**: Create, edit, and manage job listings
- 👥 **Applicant Management**: Review and filter applications
- ❓ **Screening Questions**: Set custom questions for applicants
- 📈 **Application Analytics**: Track posting performance
- ⭐ **Applicant Ratings**: Rate and categorize candidates
- 📧 **Direct Communication**: Message applicants through the platform

### For Administrators

- 📊 **Analytics Dashboard**: Platform usage statistics and insights
- 👥 **User Management**: Manage students, alumni, and employers
- 📋 **Job Moderation**: Review and approve job postings
- 🔍 **Reporting Tools**: Generate platform reports
- ⚙️ **System Configuration**: Platform settings management
- 📝 **Activity Logs**: Track all system activities

---

## 🛠️ Tech Stack

### Backend
- **PHP 7.4+** - Core PHP (no framework)
- **MySQL 5.7+** - Relational database

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Styling with modern features
- **Bootstrap 5** - Responsive UI framework
- **TailwindCSS** - Utility-first CSS
- **JavaScript (ES6+)** - Client-side interactivity
- **jQuery 3.x** - DOM manipulation and AJAX

### Development Tools
- **Git** - Version control
- **GitHub** - Code repository and collaboration
- **XAMPP/WAMP/MAMP** - Local development environment

### Deployment
- **Shared Hosting** - Production environment
- **Apache** - Web server
- **phpMyAdmin** - Database management

---

## 🚀 Getting Started

### Prerequisites

Before you begin, ensure you have the following installed:

```bash
# Check PHP version (7.4 or higher required)
php -v

# Check MySQL version (5.7 or higher required)
mysql --version
```

**Required Software:**
- PHP 7.4+
- MySQL 5.7+ or MariaDB 10.3+
- Apache 2.4+ or Nginx
- Web browser (Chrome, Firefox, Safari, Edge)

### Installation

#### Step 1: Clone the Repository

```bash
git clone https://github.com/Group-7-6905/MUNext.git
cd MUNext
```

#### Step 2: Set Up Web Server

**Option A: Using XAMPP (Recommended)**

1. Download and install [XAMPP](https://www.apachefriends.org/)
2. Copy the `MUNext` folder to:
   - **Windows**: `C:\xampp\htdocs\MUNext`
   - **Mac**: `/Applications/XAMPP/htdocs/MUNext`
   - **Linux**: `/opt/lampp/htdocs/MUNext`
3. Start Apache and MySQL from XAMPP Control Panel

**Option B: Using PHP Built-in Server (Development Only)**

```bash
cd MUNext
php -S localhost:8000
```

#### Step 3: Create Database

**Using phpMyAdmin:**
1. Open `http://localhost/phpmyadmin`
2. Click "New" to create a database
3. Name it: `erisdb`
4. Collation: `utf8mb4_general_ci`
5. Click "Create"

**Using MySQL Command Line:**

```bash
mysql -u root -p
```

```sql
CREATE DATABASE erisdb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
EXIT;
```

#### Step 4: Import Database Schema

**Using phpMyAdmin:**
1. Select the `erisdb` database
2. Click "Import" tab
3. Choose file: `db/erisdb.sql`
4. Click "Go"

**Using Command Line:**

```bash
mysql -u root -p erisdb < db/erisdb.sql
```

### Configuration

#### Locate Your Configuration File

The database configuration is typically located in one of these files:
- `includes/config.php`
- `includes/db_connect.php`
- `config.php` (root directory)

#### Update Database Credentials

Edit your configuration file with your local database credentials:

```php
<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'erisdb');
define('DB_USER', 'root');
define('DB_PASSWORD', 'your_password_here'); // UPDATE THIS
define('DB_PORT', '3306');

// Application Configuration
define('APP_URL', 'http://localhost/MUNext');
define('APP_NAME', 'MUNext');
?>
```

**Important**: Never commit your actual credentials to version control!

#### File Permissions (Linux/Mac)

```bash
# Make upload directories writable
chmod -R 755 uploads/
chmod -R 755 resumes_cv/
```

---

## 💡 Usage

### Accessing the Application

**Local Development:**
```
http://localhost/MUNext
```

**Live Demo:**
```
https://munext.ca/
```

### Demo Accounts

#### Applicant Account
- **Username**: `demo`
- **Password**: `demo123`
- **Use for**: Testing job search, applications, profile management

#### Admin Account
- **Username**: `admin`
- **Password**: `1111`
- **Use for**: Testing admin dashboard, user management, analytics

### Quick User Guide

#### For Job Seekers:

1. **Register**: Create your account as a student or alumni
2. **Complete Profile**: Add your education, skills, and experience
3. **Upload Resume**: Attach your CV for applications
4. **Search Jobs**: Use filters to find relevant opportunities
5. **Apply**: Submit applications with cover letters
6. **Track**: Monitor your application status

#### For Employers:

1. **Register**: Create your employer account
2. **Company Profile**: Add your organization details
3. **Post Jobs**: Create detailed job listings
4. **Set Screening**: Add custom screening questions
5. **Review Applications**: Filter and review applicants
6. **Manage**: Update job status and communicate with candidates

#### For Administrators:

1. **Login**: Use admin credentials
2. **Dashboard**: View platform analytics
3. **Moderate**: Review and approve job postings
4. **Manage Users**: Handle user accounts and permissions
5. **Generate Reports**: Create usage and activity reports

---

## 📁 Project Structure

```
MUNext/
├── .github/                        # GitHub configuration
│
├── admin/
│   ├── include/
│   │   ├── session.php
│   │   ├── phpcode.php
│   │   └── functions.php
│   │
│   ├── assets/
│   │   ├── js/
│   │   ├── css/
│   │   └── img/
│   │
│   ├── index.php - 18 KB
│   ├── admin-active-jobs.php - 31 KB
│   ├── admin-activity-logs.php - 23 KB
│   ├── admin-administrators.php - 32 KB
│   ├── admin-all-applications.php - 35 KB
│   ├── admin-all-companies.php - 27 KB
│   ├── admin-all-jobs.php - 33 KB
│   ├── admin-analytics-dashboard.php - 36 KB
│   ├── admin-application-reports.php - 42 KB
│   ├── admin-application-statistics.php - 44 KB
│   ├── admin-change-password.php - 20 KB
│   ├── admin-company-statistics.php - 38 KB
│   ├── admin-email-settings.php - 34 KB
│   ├── admin-employers.php - 28 KB
│   ├── admin-expired-jobs.php - 29 KB
│   ├── admin-export-reports.php - 36 KB
│   ├── admin-general-settings.php - 28 KB
│   ├── admin-get-application-details.php - 19 KB
│   ├── admin-get-company-details.php - 16 KB
│   ├── admin-get-job-details.php - 22 KB
│   ├── admin-get-user-details.php - 21 KB
│   ├── admin-help-support.php - 25 KB
│   ├── admin-job-category.php - 35 KB
│   ├── admin-job-details.php - 35 KB
│   ├── admin-job-reports.php - 46 KB
│   ├── admin-job-seekers.php - 31 KB
│   ├── admin-pending-applications.php - 32 KB
│   ├── admin-pending-jobs.php - 36 KB
│   ├── admin-profile.php - 25 KB
│   ├── admin-user-reports.php - 41 KB
│   ├── admin-verify-companies.php - 30 KB
│   ├── get_company.php - 1 KB
│   ├── header.php - 3 KB
│   ├── sidenav.php - 17 KB
│   ├── footer.php - 1 KB
│   └── logout.php - 2 KB
│
├── assets/                         # Static assets
│   ├── css/                       # Stylesheets
│   │   ├── bootstrap.min.css     # Bootstrap framework
│   │   ├── style.css             # Custom styles
│   │   ├── slick.css             # Slider styles
│   │   └── snackbar.min.css      # Notification styles
│   ├── js/                        # JavaScript files
│   │   ├── jquery.min.js         # jQuery library
│   │   ├── popper.min.js         # Popper.js
│   │   ├── bootstrap.min.js      # Bootstrap JS
│   │   ├── slick.js              # Carousel/slider
│   │   ├── custom.js             # Custom JavaScript
│   │   └── snackbar.min.js       # Notifications
│   └── images/                    # Images and graphics
│       ├── logo.png              # Application logo
│       └── banners/              # Banner images
│
├── avater/                         # User avatar uploads
│   └── default-avatar.png         # Default user avatar
│
├── company_logo/                   # Company logo uploads
│   └── default-company.png        # Default company logo
│
├── dashboard/
│   │
│   ├── applicant/
│   │   ├── assets/
│   │   │   ├── css/
│   │   │   ├── js/
│   │   │   └── img/
│   │   │
│   │   ├── include/
│   │   │   ├── session.php
│   │   │   ├── phpcode.php
│   │   │   └── function.php
│   │   │
│   │   ├── index.php - 33 KB
│   │   ├── dashboard-applied-jobs.php - 35 KB
│   │   ├── dashboard-alert-job.php - 26 KB
│   │   ├── dashboard-messages.php - 22 KB
│   │   ├── dashboard-my-profile.php - 67 KB
│   │   ├── dashboard-saved-jobs.php - 33 KB
│   │   ├── dashboard-add-profile.php - 67 KB
│   │   ├── dashboard-change-password.php - 17 KB
│   │   ├── candidate-detail.php - 30 KB
│   │   ├── header.php - 4 KB
│   │   ├── sidenav.php - 7 KB
│   │   ├── footer.php - 1 KB
│   │   ├── logout.php - 2 KB
│   │   └── .htaccess - 1 KB
│   │
│   └── employer/
│       ├── assets/
│       │   ├── css/
│       │   ├── js/
│       │   └── img/
│       │
│       ├── include/
│       │   ├── session.php
│       │   ├── phpcode.php
│       │   └── function.php
│       │
│       ├── index.php - 33 KB
│       ├── index-1.php - 49 KB
│       ├── dashboard-shortlisted-resume.php - 31 KB
│       ├── dashboard-screening-questions.php - 37 KB
│       ├── dashboard-post-job.php - 31 KB
│       ├── dashboard-messages.php - 19 KB
│       ├── dashboard-manage-jobs.php - 35 KB
│       ├── dashboard-manage-applications.php - 37 KB
│       ├── dashboard-jobs-edit.php - 33 KB
│       ├── dashboard-job-details.php - 29 KB
│       ├── dashboard-company-edit.php - 57 KB
│       ├── dashboard-company-add.php - 74 KB
│       ├── dashboard-change-password.php - 16 KB
│       ├── dashboard-candidate-detail.php - 27 KB
│       ├── company-detail.php - 24 KB
│       ├── view-screening-answers.php - 11 KB
│       ├── view-resume.php - 2 KB
│       ├── download-resume.php - 1 KB
│       ├── ajax-get-messages.php - 1 KB
│       ├── ajax_toggle_job_status.php - 4 KB
│       ├── header.php - 2 KB
│       ├── sidenav.php - 5 KB
│       ├── footer.php - 839 bytes
│       ├── logout.php - 1 KB
│       └── .htaccess - 707 bytes
│
├── db/                             # Database files
│   └── erisdb.sql                 # Complete database schema
│
├── include/                        # PHP includes
│   ├── header.php                 # Common header
│   ├── footer.php                 # Common footer
│   ├── head.php                   # HTML head section
│   ├── navigation.php             # Navigation menus
│   ├── helper.php                 # Helper functions
│   └── db_functions.php           # Database helper functions
│
├── profile/                        # User profile management
│   ├── applicant_profile.php     # Job seeker profile
│   ├── employer_profile.php      # Employer profile
│   ├── edit_profile.php          # Profile editing
│   └── view_profile.php          # Public profile view
│
├── resumes_cv/                     # Resume/CV uploads
│   └── .htaccess                  # Security configuration
│
├── apply-job.php                   # Job application form
├── browse-category.php             # Browse jobs by category
├── browse-employers.php            # Browse employer listings
├── browse-jobs.php                 # Browse all job listings
├── employer-detail.php             # Employer profile details
├── index.php                       # Landing/home page
├── job-detail.php                  # Individual job details
├── job-list-v1.php                 # Job listing view (variant 1)
├── job-search-v1.php               # Job search interface
├── login.php                       # User login and registration
├── logout.php                      # Logout handler
├── mysqli_connect.php              # Database configuration
├── notification-mail.php           # Email notification handler
├── session_check.php               # Session management
├── settings-manager.php
├── maintenance.php 
├── forgot-password.php 
├── email-usage-examples.php 
└── email-test.php 
├── .gitignore                      # Git ignore configuration
└── README.md                       # Project documentation
```

### Key Directories Explained

**`/.github`** - GitHub-specific configuration files and workflows.

**`/admin`** - Administrative interface for platform management, user moderation, job approval, and analytics.

**`/assets`** - All static resources including CSS (Bootstrap, custom styles), JavaScript (jQuery, custom scripts), and images organized by type.

**`/avater`** - Storage for user profile pictures with default avatar fallback.

**`/company_logo`** - Storage for employer company logos with default logo fallback.

**`/dashboard`** - Separate dashboard areas for applicants and employers with role-specific functionality:
  - `/applicant` - Job seeker dashboard, application tracking, saved jobs
  - `/employer` - Employer dashboard, job posting, applicant management

**`/db`** - Database schema file (`erisdb.sql`) containing all table structures, relationships, and sample data.

**`/include`** - Reusable PHP components including headers, footers, navigation, and helper functions.

**`/profile`** - User profile management for both job seekers and employers, including public profile views.

**`/resumes_cv`** - Secure storage for uploaded resumes and CVs with `.htaccess` access controls.

### Core Application Files

**Root-Level PHP Files:**

- **`apply-job.php`** - Job application submission form
- **`browse-category.php`** - Browse jobs by category
- **`browse-employers.php`** - Browse employer/company listings
- **`browse-jobs.php`** - Main job browsing interface
- **`employer-detail.php`** - Detailed employer profile view
- **`index.php`** - Landing page and home
- **`job-detail.php`** - Individual job posting details
- **`job-list-v1.php`** - Job listing display (version 1)
- **`job-search-v1.php`** - Job search with filters
- **`login.php`** - Combined login and registration page
- **`logout.php`** - Logout and session cleanup
- **`mysqli_connect.php`** - Database configuration and connection
- **`notification-mail.php`** - Email notification system
- **`session_check.php`** - Session validation and authentication

### Configuration Files

**`mysqli_connect.php`** - Central database configuration containing:
- Database host, name, and credentials
- Connection initialization
- Configuration constants (paths, upload settings)

**`session_check.php`** - Session management including:
- User authentication checking
- Session validation
- User ID tracking

**`.gitignore`** - Version control exclusions for sensitive files and temporary data.

---

## 🗄️ Database Schema

### Core Tables

#### Users Table
Stores all user accounts (students, alumni, employers, admins)

#### Jobs Table
Contains all job postings

#### Applications Table
Tracks job applications

#### Employers Table
Employer profile information

#### Notifications Table
User notifications

### Database Relationships

```
Users (1) ─── (Many) Applications
Users (1) ─── (Many) Jobs (as employer)
Jobs (1) ─── (Many) Applications
Users (1) ─── (Many) Notifications
```

**Complete Schema**: See `db/erisdb.sql` for full schema with indexes and constraints.

---

## 🎭 Demo Accounts

### Test Credentials

| Role | Username | Password | Purpose |
|------|----------|----------|---------|
| **Applicant** | demo | demo123 | Test job seeker features |
| **Admin** | admin | 1111 | Test admin features |

---

## 🤝 Contributing

We welcome contributions! Here's how:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Commit Message Convention

We follow [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` - New feature
- `fix:` - Bug fix
- `docs:` - Documentation changes
- `style:` - Code formatting
- `refactor:` - Code restructuring
- `test:` - Adding tests
- `chore:` - Maintenance tasks

---

## 👥 Team

**Group 7 - COMP 6905 Software Engineering**

| Name | Student ID | Role | GitHub |
|------|------------|------|--------|
| **Victor Chisom Muojeke** | 202480408 | Team Leader | [@portalzone](https://github.com/portalzone) |
| Chiemerie Obijiaku | 202492457 | Full Stack Developer | [@emeldo39](https://github.com/emeldo39) |
| Akinfenwa Oluwaseun | 202382513 | Frontend Developer | [@osakinfenwa](https://github.com/osakinfenwa) |
| Kelechukwu Ede | 202580616 | Backend Developer | [@Kelechiede](https://github.com/Kelechiede) |
| Akefar Islam | 202093086 | Database Admin | [@Akefar](https://github.com/Akefar) |
| Adiba Ekpunobi | 202234225 | UI/UX Designer | [@AdibaKayla](https://github.com/AdibaKayla) |
| Joshua Oseimobor | 202492785 | Quality Assurance | [@walkwithoj](https://github.com/walkwithoj) |
| Victor Onwosi | 202488814 | DevOps | [@Adimvicky](https://github.com/Adimvicky) |

### Contact

- **Project Lead**: Victor Chisom Muojeke - vcmuojeke@mun.ca
- **Project Repository**: [https://github.com/Group-7-6905/MUNext](https://github.com/Group-7-6905/MUNext)
- **Live Demo**: [https://munext.ca/](https://munext.ca/)

---

## 🙏 Acknowledgments

- **Memorial University of Newfoundland** - For the learning environment
- **Course Instructor** - For guidance throughout the project
- **Open Source Community** - For amazing tools and libraries

### Built With

- [Bootstrap](https://getbootstrap.com) - Frontend framework
- [TailwindCSS](https://tailwindcss.com) - Utility-first CSS
- [jQuery](https://jquery.com) - JavaScript library
- [Font Awesome](https://fontawesome.com) - Icon library

---

## 📄 License

This project is developed as part of COMP 6905 - Software Engineering course at Memorial University of Newfoundland.

**Academic Use Only** - For educational purposes.

```
Copyright (c) 2025 Group 7 - MUNext
Memorial University of Newfoundland
All rights reserved.
```

---

## 📞 Support

Need help?

- Search [existing issues](https://github.com/Group-7-6905/MUNext/issues)
- Create a [new issue](https://github.com/Group-7-6905/MUNext/issues/new)
- Email: vcmuojeke@mun.ca

---

<div align="center">

**[⬆ back to top](#munext---memorial-university-job-board-platform)**

Made with ❤️ by Group 7

**Memorial University of Newfoundland** | **COMP 6905** | **Fall 2025**

</div>
