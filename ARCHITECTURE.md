# MUNext - System Architecture Documentation

**Version:** 2.0  
**Status:** Production - Fully Functional  
**Last Updated:** December 2, 2025  
**Project:** MUNext - Memorial University Job Board Platform  
**Live Site:** https://munext.ca/

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [System Architecture](#system-architecture)
3. [Component Architecture](#component-architecture)
4. [Module Architecture](#module-architecture)
5. [Data Architecture](#data-architecture)
6. [Security Architecture](#security-architecture)
7. [Technology Stack](#technology-stack)
8. [Deployment Architecture](#deployment-architecture)
9. [Integration Architecture](#integration-architecture)
10. [Performance Architecture](#performance-architecture)
11. [Testing Architecture](#testing-architecture)
12. [Design Decisions](#design-decisions)

---

## Executive Summary

### Project Overview

MUNext is a production-ready, three-tier web application serving as a dedicated job board platform for Memorial University of Newfoundland. The platform connects students, alumni, and employers through a secure, scalable, and maintainable architecture built with core PHP, MySQL, and modern frontend technologies.

### Architecture Philosophy

The architecture follows these core principles:

- **Simplicity First**: Core PHP without framework overhead for optimal shared hosting compatibility
- **Security by Design**: Multi-layered security with session-based authentication and prepared statements
- **Modular Structure**: Clear separation of concerns enabling independent module development
- **Role-Based Access**: Distinct interfaces for Applicants, Employers, and Administrators
- **Production Ready**: Currently deployed and serving real users at https://munext.ca/

### System Statistics

- **Total PHP Files**: 120+ pages
- **User Roles**: 3 (Applicant, Employer, Administrator)
- **Database Tables**: 15+ core tables
- **File Upload Types**: Resumes, Avatars, Company Logos
- **Supported Browsers**: Chrome, Firefox, Safari, Edge
- **Production Uptime**: 99.9% availability

---

## System Architecture

### Architectural Style

MUNext implements a **Three-Tier Layered Architecture** with additional file storage layer:

1. **Presentation Layer** - Web browser, HTML/CSS/JavaScript
2. **Application Layer** - PHP business logic, session management
3. **Data Layer** - MySQL database
4. **Storage Layer** - File system for uploads and assets

### High-Level System Diagram

```
┌────────────────────────────────────────────────────────────────────────┐
│                         CLIENT TIER (Presentation Layer)                │
├────────────────────────────────────────────────────────────────────────┤
│  Web Browsers                                                           │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐                 │
│  │   Desktop    │  │    Mobile    │  │    Tablet    │                 │
│  │  Chrome, FF  │  │   Safari     │  │    Edge      │                 │
│  └──────────────┘  └──────────────┘  └──────────────┘                 │
│                                                                          │
│  Frontend Technologies                                                  │
│  ├── HTML5 (Semantic Markup)                                           │
│  ├── CSS3 (Bootstrap 5 + TailwindCSS)                                  │
│  ├── JavaScript (ES6+ + jQuery 3.x)                                    │
│  └── AJAX (Dynamic Content Loading)                                    │
└───────────────────────────┬────────────────────────────────────────────┘
                            │ HTTPS (SSL/TLS)
                            │ RESTful-like Request/Response
                            ▼
┌────────────────────────────────────────────────────────────────────────┐
│                    APPLICATION TIER (Business Logic Layer)              │
├────────────────────────────────────────────────────────────────────────┤
│  Apache Web Server (2.4+) with mod_rewrite                             │
│  ├── PHP 7.4+ Runtime Environment                                      │
│  ├── Session Management (session_check.php)                            │
│  ├── Request Routing (.htaccess configurations)                        │
│  └── Error Handling & Logging                                          │
│                                                                          │
│  Application Modules                                                    │
│  ├─┬─ Public Module                                                    │
│  │ ├── Landing Pages (index.php)                                       │
│  │ ├── Job Browsing (browse-jobs.php, job-search-v1.php)             │
│  │ ├── Employer Directory (browse-employers.php)                       │
│  │ ├── Category Browsing (browse-category.php)                         │
│  │ └── Detail Views (job-detail.php, employer-detail.php)             │
│  │                                                                       │
│  ├─┬─ Authentication Module                                            │
│  │ ├── Login System (login.php)                                        │
│  │ ├── Registration System                                              │
│  │ ├── Session Management (session_check.php)                          │
│  │ ├── Password Recovery (forgot-password.php)                         │
│  │ └── Logout Handler (logout.php)                                     │
│  │                                                                       │
│  ├─┬─ Applicant Module (dashboard/applicant/)                         │
│  │ ├── Dashboard (index.php - 33 KB)                                   │
│  │ ├── Profile Management (dashboard-my-profile.php - 67 KB)          │
│  │ ├── Applied Jobs Tracker (dashboard-applied-jobs.php - 35 KB)      │
│  │ ├── Saved Jobs (dashboard-saved-jobs.php - 33 KB)                  │
│  │ ├── Job Alerts (dashboard-alert-job.php - 26 KB)                   │
│  │ ├── Messages (dashboard-messages.php - 22 KB)                       │
│  │ └── Password Management (dashboard-change-password.php - 17 KB)    │
│  │                                                                       │
│  ├─┬─ Employer Module (dashboard/employer/)                           │
│  │ ├── Dashboard (index.php - 33 KB)                                   │
│  │ ├── Enhanced Dashboard (index-1.php - 49 KB)                        │
│  │ ├── Job Posting (dashboard-post-job.php - 31 KB)                   │
│  │ ├── Job Management (dashboard-manage-jobs.php - 35 KB)             │
│  │ ├── Job Editing (dashboard-jobs-edit.php - 33 KB)                  │
│  │ ├── Application Management (dashboard-manage-applications - 37 KB) │
│  │ ├── Screening Questions (dashboard-screening-questions - 37 KB)     │
│  │ ├── Shortlisted Resumes (dashboard-shortlisted-resume - 31 KB)     │
│  │ ├── Company Profile (dashboard-company-add/edit.php - 74/57 KB)   │
│  │ ├── Candidate Details (dashboard-candidate-detail.php - 27 KB)     │
│  │ └── Resume Management (view-resume.php, download-resume.php)       │
│  │                                                                       │
│  ├─┬─ Administrator Module (admin/)                                    │
│  │ ├── Admin Dashboard (index.php - 18 KB)                             │
│  │ ├── Analytics Dashboard (admin-analytics-dashboard.php - 36 KB)    │
│  │ ├── User Management                                                  │
│  │ │   ├── Job Seekers (admin-job-seekers.php - 31 KB)               │
│  │ │   ├── Employers (admin-employers.php - 28 KB)                    │
│  │ │   └── Administrators (admin-administrators.php - 32 KB)          │
│  │ ├── Job Management                                                   │
│  │ │   ├── All Jobs (admin-all-jobs.php - 33 KB)                     │
│  │ │   ├── Active Jobs (admin-active-jobs.php - 31 KB)               │
│  │ │   ├── Pending Jobs (admin-pending-jobs.php - 36 KB)             │
│  │ │   ├── Expired Jobs (admin-expired-jobs.php - 29 KB)             │
│  │ │   └── Job Details (admin-job-details.php - 35 KB)               │
│  │ ├── Application Management                                           │
│  │ │   ├── All Applications (admin-all-applications.php - 35 KB)     │
│  │ │   └── Pending Applications (admin-pending-applications - 32 KB)  │
│  │ ├── Company Management                                               │
│  │ │   ├── All Companies (admin-all-companies.php - 27 KB)           │
│  │ │   └── Company Verification (admin-verify-companies.php - 30 KB) │
│  │ ├── Reporting & Analytics                                           │
│  │ │   ├── Application Reports (admin-application-reports - 42 KB)    │
│  │ │   ├── Application Statistics (admin-application-stats - 44 KB)   │
│  │ │   ├── Job Reports (admin-job-reports.php - 46 KB)               │
│  │ │   ├── User Reports (admin-user-reports.php - 41 KB)             │
│  │ │   ├── Company Statistics (admin-company-statistics - 38 KB)      │
│  │ │   └── Export Reports (admin-export-reports.php - 36 KB)         │
│  │ ├── System Management                                                │
│  │ │   ├── Activity Logs (admin-activity-logs.php - 23 KB)           │
│  │ │   ├── Job Categories (admin-job-category.php - 35 KB)           │
│  │ │   ├── General Settings (admin-general-settings.php - 28 KB)     │
│  │ │   ├── Email Settings (admin-email-settings.php - 34 KB)         │
│  │ │   └── Help & Support (admin-help-support.php - 25 KB)           │
│  │ └── Admin Profile (admin-profile.php - 25 KB)                       │
│  │                                                                       │
│  ├─┬─ Shared Services                                                  │
│  │ ├── Database Connection (mysqli_connect.php)                        │
│  │ ├── Email Notifications (notification-mail.php)                     │
│  │ ├── Settings Manager (settings-manager.php)                         │
│  │ ├── Email Testing (email-test.php, email-usage-examples.php)      │
│  │ └── Maintenance Mode (maintenance.php)                              │
│  │                                                                       │
│  └─┬─ Common Components (include/)                                     │
│    ├── Header (header.php)                                              │
│    ├── Footer (footer.php)                                              │
│    ├── Navigation (navigation.php)                                      │
│    ├── HTML Head (head.php)                                            │
│    ├── Helper Functions (helper.php)                                    │
│    └── Database Functions (db_functions.php)                           │
└───────────────────────────┬────────────────────────────────────────────┘
                            │ MySQLi Protocol
                            │ Prepared Statements
                            ▼
┌────────────────────────────────────────────────────────────────────────┐
│                         DATA TIER (Persistence Layer)                   │
├────────────────────────────────────────────────────────────────────────┤
│  MySQL Database Server (MariaDB 10.4.32) - erisdb                      │
│                                                                          │
│  Core Tables                                                            │
│  ├── tblusers              - User accounts and authentication           │
│  ├── tbljob                - Job postings                               │
│  ├── tbljobapplication     - Job applications                           │
│  ├── tblcompany            - Employer profiles                          │
│  ├── tblapplicants         - Applicant detailed profiles                │
│  ├── tblemployees          - Employer/employee accounts                 │
│  ├── tblnotification       - User notifications                         │
│  ├── tbljobcategory        - Job categorization                         │
│  ├── tbljobsubcategory     - Job subcategories                          │
│  ├── tblbookmarkjob        - Saved/bookmarked jobs                      │
│  ├── tblbookmarkresume     - Bookmarked resumes                         │
│  ├── tblscreening          - Screening question bank                    │
│  ├── tblscreening_answer   - Correct answers for screening              │
│  ├── tbljobscreening_ques  - Job-specific screening questions           │
│  ├── tblscreening_qa       - Applicant screening responses              │
│  ├── tblscreening_score    - Screening test scores                      │
│  ├── tblattachmentfile     - Application attachment files               │
│  ├── tblfeedback           - Feedback system                            │
│  ├── tblautonumbers        - Auto-increment number management           │
│  └── tbljobregistration    - Job registration tracking                  │
│                                                                          │
│  Database Features                                                      │
│  ├── ACID Compliance                                                    │
│  ├── InnoDB Engine                                                      │
│  ├── Latin1 Character Set (latin1_swedish_ci)                          │
│  └── AUTO_INCREMENT Primary Keys                                        │
└────────────────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────────────────┐
│                     FILE STORAGE TIER (Asset Layer)                     │
├────────────────────────────────────────────────────────────────────────┤
│  File System Storage                                                    │
│                                                                          │
│  User Uploads                                                           │
│  ├── /resumes_cv/          - Resume/CV files (.htaccess protected)    │
│  │   ├── PDF, DOC, DOCX formats                                        │
│  │   ├── Maximum 5MB per file                                          │
│  │   └── Virus scanning (planned)                                      │
│  ├── /avater/              - User profile pictures                     │
│  │   ├── JPG, PNG formats                                              │
│  │   ├── 500KB size limit                                              │
│  │   └── Image optimization                                            │
│  └── /company_logo/        - Employer logos                            │
│      ├── JPG, PNG, SVG formats                                         │
│      ├── 1MB size limit                                                │
│      └── Automatic resizing                                            │
│                                                                          │
│  Static Assets                                                          │
│  └── /assets/                                                          │
│      ├── /css/             - Stylesheets (Bootstrap, TailwindCSS)     │
│      ├── /js/              - JavaScript libraries                      │
│      └── /images/          - Static images and icons                  │
│                                                                          │
│  Security Features                                                      │
│  ├── .htaccess rules (prevent direct execution)                        │
│  ├── MIME type validation                                              │
│  ├── Secure filename generation                                         │
│  └── Directory permissions (755)                                       │
└────────────────────────────────────────────────────────────────────────┘
```

### Request Flow Diagram

```
┌──────────┐
│  Client  │
└─────┬────┘
      │ 1. HTTPS Request
      ▼
┌─────────────────┐
│ Apache Server   │
│ + .htaccess     │
└─────┬───────────┘
      │ 2. Route Request
      ▼
┌─────────────────┐
│ session_check   │──► Check authentication status
│                 │    Load user session data
└─────┬───────────┘
      │ 3. Validate Session
      ▼
┌─────────────────┐
│ PHP Controller  │──► Business logic processing
│ (e.g., browse-  │    Input validation
│  jobs.php)      │    Data preparation
└─────┬───────────┘
      │ 4. Query Database
      ▼
┌─────────────────┐
│ mysqli_connect  │──► Execute prepared statement
│ + SQL Query     │    Fetch results
└─────┬───────────┘
      │ 5. Return Data
      ▼
┌─────────────────┐
│ PHP Template    │──► Render HTML
│ + include files │    Inject dynamic data
│   (header.php)  │    Apply formatting
└─────┬───────────┘
      │ 6. HTML Response
      ▼
┌─────────────────┐
│ Client Browser  │──► Display to user
│ + JavaScript    │    Add interactivity
└─────────────────┘
```

---

## Component Architecture

### Component Hierarchy

```
MUNext Platform
│
├─── Public Components (Unauthenticated Access)
│    ├─── Landing Module
│    │    └── index.php (Homepage with job listings, featured employers)
│    │
│    ├─── Job Discovery Module
│    │    ├── browse-jobs.php (All job listings with filters)
│    │    ├── job-search-v1.php (Advanced search interface)
│    │    ├── job-list-v1.php (List view of jobs)
│    │    ├── job-detail.php (Individual job details)
│    │    └── browse-category.php (Category-based browsing)
│    │
│    └─── Employer Discovery Module
│         ├── browse-employers.php (Employer directory)
│         └── employer-detail.php (Company profile view)
│
├─── Authentication Components
│    ├─── Login/Registration Module
│    │    └── login.php (Combined login and signup)
│    │
│    ├─── Password Management
│    │    └── forgot-password.php (Password recovery)
│    │
│    └─── Session Module
│         ├── session_check.php (Validation)
│         └── logout.php (Termination)
│
├─── Applicant Components (Role: Job Seeker)
│    ├─── Dashboard Module (dashboard/applicant/)
│    │    ├── index.php (Overview dashboard)
│    │    ├── dashboard-my-profile.php (Profile view/edit)
│    │    ├── dashboard-add-profile.php (Initial profile creation)
│    │    ├── dashboard-applied-jobs.php (Application tracker)
│    │    ├── dashboard-saved-jobs.php (Bookmarked jobs)
│    │    ├── dashboard-alert-job.php (Job alerts setup)
│    │    ├── dashboard-messages.php (Messaging inbox)
│    │    └── dashboard-change-password.php (Security settings)
│    │
│    ├─── Profile Module
│    │    └── candidate-detail.php (Public profile)
│    │
│    └─── Application Module
│         └── apply-job.php (Application form)
│
├─── Employer Components (Role: Recruiter)
│    ├─── Dashboard Module (dashboard/employer/)
│    │    ├── index.php (Basic dashboard)
│    │    ├── index-1.php (Enhanced analytics dashboard)
│    │    └── dashboard-change-password.php (Security)
│    │
│    ├─── Job Management Module
│    │    ├── dashboard-post-job.php (Create new job)
│    │    ├── dashboard-manage-jobs.php (View all postings)
│    │    ├── dashboard-jobs-edit.php (Edit existing job)
│    │    ├── dashboard-job-details.php (View job details)
│    │    ├── ajax_toggle_job_status.php (Status updates)
│    │    └── dashboard-screening-questions.php (Custom questions)
│    │
│    ├─── Applicant Management Module
│    │    ├── dashboard-manage-applications.php (Review applications)
│    │    ├── dashboard-candidate-detail.php (Candidate profile)
│    │    ├── dashboard-shortlisted-resume.php (Shortlist management)
│    │    ├── view-screening-answers.php (View responses)
│    │    ├── view-resume.php (Resume viewer)
│    │    └── download-resume.php (Resume download)
│    │
│    ├─── Company Profile Module
│    │    ├── dashboard-company-add.php (Create profile)
│    │    ├── dashboard-company-edit.php (Update profile)
│    │    └── company-detail.php (Public view)
│    │
│    └─── Communication Module
│         ├── dashboard-messages.php (Messaging)
│         └── ajax-get-messages.php (Message retrieval)
│
├─── Administrator Components (Role: Admin)
│    ├─── Admin Dashboard Module (admin/)
│    │    ├── index.php (Main admin dashboard)
│    │    ├── admin-analytics-dashboard.php (Platform analytics)
│    │    ├── admin-profile.php (Admin profile)
│    │    └── admin-change-password.php (Security)
│    │
│    ├─── User Management Module
│    │    ├── admin-job-seekers.php (Manage applicants)
│    │    ├── admin-employers.php (Manage employers)
│    │    ├── admin-administrators.php (Manage admins)
│    │    └── admin-get-user-details.php (User details API)
│    │
│    ├─── Job Moderation Module
│    │    ├── admin-all-jobs.php (All job listings)
│    │    ├── admin-active-jobs.php (Active jobs)
│    │    ├── admin-pending-jobs.php (Approval queue)
│    │    ├── admin-expired-jobs.php (Expired listings)
│    │    ├── admin-job-details.php (Job details)
│    │    ├── admin-get-job-details.php (Job details API)
│    │    └── admin-job-category.php (Category management)
│    │
│    ├─── Application Management Module
│    │    ├── admin-all-applications.php (All applications)
│    │    ├── admin-pending-applications.php (Pending review)
│    │    └── admin-get-application-details.php (Application API)
│    │
│    ├─── Company Management Module
│    │    ├── admin-all-companies.php (All companies)
│    │    ├── admin-verify-companies.php (Verification queue)
│    │    └── admin-get-company-details.php (Company API)
│    │
│    ├─── Reporting & Analytics Module
│    │    ├── admin-application-reports.php (Application analytics)
│    │    ├── admin-application-statistics.php (Application stats)
│    │    ├── admin-job-reports.php (Job analytics)
│    │    ├── admin-user-reports.php (User analytics)
│    │    ├── admin-company-statistics.php (Company analytics)
│    │    └── admin-export-reports.php (Data export)
│    │
│    └─── System Management Module
│         ├── admin-activity-logs.php (Audit trail)
│         ├── admin-general-settings.php (System config)
│         ├── admin-email-settings.php (Email config)
│         └── admin-help-support.php (Support tools)
│
├─── Shared Services
│    ├─── Database Service
│    │    ├── mysqli_connect.php (Connection management)
│    │    └── db_functions.php (Database helpers)
│    │
│    ├─── Email Service
│    │    ├── notification-mail.php (Email dispatcher)
│    │    ├── email-test.php (Testing utility)
│    │    └── email-usage-examples.php (Documentation)
│    │
│    ├─── Configuration Service
│    │    └── settings-manager.php (Settings handler)
│    │
│    └─── Utility Services
│         └── maintenance.php (Maintenance mode)
│
└─── Common UI Components (include/)
     ├── header.php (Global header)
     ├── footer.php (Global footer)
     ├── head.php (HTML metadata)
     ├── navigation.php (Navigation menus)
     ├── helper.php (Helper functions)
     └── sidenav.php (Dashboard sidebars)
```

### Component Communication Patterns

#### 1. Page Request Pattern (Traditional)
```
Browser → PHP Controller → Database → PHP Controller → HTML Response → Browser
```

#### 2. AJAX Pattern (Dynamic)
```
Browser (JS) → AJAX Request → PHP Endpoint → Database → JSON Response → Browser (JS)
```

#### 3. File Upload Pattern
```
Browser (Form) → PHP Handler → Validation → File System → Database Record → Confirmation
```

#### 4. Email Notification Pattern
```
Event Trigger → notification-mail.php → SMTP Server → Recipient Email
```

---

## Module Architecture

### Public Module

**Purpose**: Provide unauthenticated access to job listings and company information.

**Key Features**:
- Job browsing and searching
- Employer directory
- Job and company detail views
- Category-based navigation

**Technology**: PHP, MySQL, Bootstrap, JavaScript

**Security**: Public access, no authentication required

---

### Authentication Module

**Purpose**: Manage user authentication and session handling.

**Components**:
```
Authentication Module
├── login.php
│   ├── Login form processing
│   ├── Registration form processing
│   ├── Input validation
│   ├── Password hashing (bcrypt)
│   ├── Session creation
│   └── Role-based redirection
│
├── forgot-password.php
│   ├── Email verification
│   ├── Reset token generation
│   └── Password update
│
├── session_check.php
│   ├── Session validation
│   ├── User authentication check
│   ├── Role verification
│   └── Automatic redirect on failure
│
└── logout.php
    ├── Session destruction
    ├── Cookie cleanup
    └── Redirect to home
```

**Security Features**:
- Password hashing with bcrypt
- Session hijacking prevention
- CSRF token validation
- Rate limiting (IP-based)
- SQL injection protection (prepared statements)

---

### Applicant Module

**Purpose**: Enable job seekers to manage profiles and applications.

**Dashboard Features**:
```
Applicant Dashboard
├── Overview
│   ├── Application statistics
│   ├── Profile completion status
│   ├── Recent applications
│   └── Job recommendations
│
├── Profile Management
│   ├── Personal information
│   ├── Education history
│   ├── Work experience
│   ├── Skills and certifications
│   ├── Resume upload
│   └── Portfolio links
│
├── Job Search & Application
│   ├── Advanced search filters
│   ├── Saved jobs
│   ├── Application tracking
│   ├── Status updates
│   └── Employer messages
│
└── Settings
    ├── Job alerts configuration
    ├── Email preferences
    ├── Privacy settings
    └── Password change
```

**File Structure**: `/dashboard/applicant/`

**Average File Size**: 20-35 KB (indicates rich functionality)

---

### Employer Module

**Purpose**: Enable employers to post jobs and manage applicants.

**Dashboard Features**:
```
Employer Dashboard
├── Analytics
│   ├── Job posting performance
│   ├── Application statistics
│   ├── View counts
│   └── Engagement metrics
│
├── Job Management
│   ├── Create new posting
│   ├── Edit existing jobs
│   ├── Toggle job status
│   ├── View applications
│   └── Screening questions
│
├── Applicant Management
│   ├── Review applications
│   ├── Filter candidates
│   ├── Shortlist resumes
│   ├── View screening answers
│   ├── Download resumes
│   └── Rate applicants
│
├── Company Profile
│   ├── Company information
│   ├── Logo upload
│   ├── Industry details
│   └── Contact information
│
└── Communication
    ├── Message applicants
    ├── Email integration
    └── Notification settings
```

**File Structure**: `/dashboard/employer/`

**Average File Size**: 25-50 KB (most complex module)

**Unique Features**:
- Dual dashboard views (basic and enhanced)
- AJAX-powered status updates
- Real-time application notifications
- Integrated resume viewer

---

### Administrator Module

**Purpose**: Platform management, moderation, and analytics.

**Dashboard Features**:
```
Administrator Dashboard
├── Platform Overview
│   ├── Total users
│   ├── Active jobs
│   ├── Applications today
│   └── System health
│
├── User Management
│   ├── Job seekers (view, edit, suspend)
│   ├── Employers (verify, manage)
│   ├── Administrators (role management)
│   └── Bulk operations
│
├── Content Moderation
│   ├── Job approval queue
│   ├── Company verification
│   ├── Application review
│   └── Flagged content
│
├── Analytics & Reporting
│   ├── Application reports
│   ├── Job statistics
│   ├── User demographics
│   ├── Company analytics
│   ├── Export functionality
│   └── Customizable date ranges
│
├── System Management
│   ├── Activity logs
│   ├── Job categories
│   ├── General settings
│   ├── Email configuration
│   └── Maintenance mode
│
└── Support Tools
    ├── Help documentation
    ├── User support tickets
    └── System diagnostics
```

**File Structure**: `/admin/`

**Average File Size**: 20-46 KB (extensive functionality)

**Advanced Features**:
- Comprehensive audit trail (admin-activity-logs.php)
- Multi-format report export (CSV, PDF, Excel)
- Real-time analytics dashboards
- Granular permission system
- Email template management

---

## Data Architecture

### Database Schema Overview

**Database Name**: `erisdb`  
**Character Set**: `latin1` (latin1_swedish_ci)  
**Engine**: InnoDB  
**Total Tables**: 20  
**Server**: MariaDB 10.4.32

### Entity-Relationship Diagram

```
┌─────────────────────┐
│     tblusers        │
│─────────────────────│
│ PK: USERID          │
│    USERNAME         │
│    PASS             │
│    EMAIL            │
│    ROLE             │──┐
│    FNAME, ONAME     │  │
└─────────────────────┘  │
        │                │
        │ 1              │ 1
        │                │
        │ n              │ n
        ▼                ▼
┌────────────────────┐ ┌──────────────────────────┐
│ tbljobapplication  │ │       tbljob             │
│────────────────────│ │──────────────────────────│
│ PK: ID             │ │ PK: JOBID                │
│ FK: APPLICANTID    │ │ FK: COMPANYID            │──┐
│ FK: JOBID          │─│ FK: JOBCATEGORYID        │  │
│    RESUME_FILE     │ │    JOBTITLE              │  │
│    APPSTATUS       │ │    JOBDESCRIPTION        │  │
│    APPLICATIONDATE │ │    SALARY, JOBTYPE       │  │
│    SCORE           │ │    QUALIFICATION         │  │
└────────────────────┘ │    WORKPLACE_POLICY      │  │
        │              │    DEADLINE, JOBSTATUS   │  │
        │              │    DATEPOSTED            │  │
        │              └──────────────────────────┘  │
        │ 1                                          │ 1
        │                                            │
        │ n                                          │ 1
        ▼                                            ▼
┌─────────────────────┐              ┌────────────────────┐
│  tblscreening_qa    │              │    tblcompany      │
│─────────────────────│              │────────────────────│
│ PK: ID              │              │ PK: COMPANYID      │
│ FK: JOBAPPLICATION  │              │    COMPANYNAME     │
│ FK: APPLICANTID     │              │    COMPANYADDRESS  │
│ FK: JOBID           │              │    COMPANYEMAIL    │
│ FK: QUESTION_ID     │──┐           │    COMPANYINDUSTRY │
│    APPLICANT_ANSWER │  │           │    COMPANYLOGO     │
└─────────────────────┘  │           │    COMPANYSTATUS   │
                         │           └────────────────────┘
                         │ n
                         │ 1
                         ▼
              ┌──────────────────────┐
              │    tblscreening      │
              │──────────────────────│
              │ PK: id               │
              │    q_title           │
              │    question          │
              │    opt_A/B/C/D/E     │
              │    status            │
              └──────────┬───────────┘
                         │ 1
                         │
                         │ 1
                         ▼
              ┌──────────────────────┐
              │ tblscreening_answer  │
              │──────────────────────│
              │ PK: id               │
              │ FK: question_id      │
              │    ideal_ans_opt     │
              │    ideal_ans         │
              └──────────────────────┘

┌─────────────────────┐
│   tblapplicants     │
│─────────────────────│
│ PK: APPLICANTID     │
│ FK: USERID          │
│ FK: JOBCATEGORYID   │
│    FNAME, OTHERNAMES│
│    EMAILADDRESS     │
│    CONTACTNO        │
│    FULLADDRESS, CITY│
│    COUNTRY, SEX     │
│    DEGREE, SKILLS   │
│    APPLICANTPHOTO   │
│    FB_link, LI_link │
└─────────────────────┘

┌─────────────────────┐         ┌────────────────────┐
│  tblbookmarkjob     │         │  tblbookmarkresume │
│─────────────────────│         │────────────────────│
│ PK: ID              │         │ PK: ID             │
│ FK: APPLICANTID     │         │ FK: USERID         │
│ FK: JOBID           │         │ FK: JOBAPPLICATIONID│
│    DATETIME         │         │ FK: JOBRESUMEID    │
└─────────────────────┘         │    DATETIME        │
                                └────────────────────┘

┌─────────────────────┐         ┌────────────────────┐
│ tbljobcategory      │         │ tbljobsubcategory  │
│─────────────────────│         │────────────────────│
│ PK: ID              │         │ PK: ID             │
│    CATEGORY         │◄────────│ FK: CATEGORYID     │
└─────────────────────┘         │    SUBCATEGORY     │
                                └────────────────────┘

┌──────────────────────┐        ┌────────────────────┐
│  tblnotification     │        │ tblattachmentfile  │
│──────────────────────│        │────────────────────│
│ PK: ID               │        │ PK: ID             │
│ FK: USERID           │        │ FK: APPLICANTID    │
│    TYPE, TYPEID      │        │ FK: JOBID          │
│    STATUS, NOTE      │        │    FILE_NAME       │
│    DATETIME          │        │    FILE_LOCATION   │
└──────────────────────┘        └────────────────────┘

┌──────────────────────┐        ┌────────────────────┐
│ tbljobscreening_ques │        │  tblscreening_score│
│──────────────────────│        │────────────────────│
│ PK: id               │        │ PK: id             │
│ FK: job_id           │        │ FK: jobapp_id      │
│ FK: question_id      │        │    score           │
└──────────────────────┘        │    total_ques      │
                                └────────────────────┘

┌──────────────────────┐        ┌────────────────────┐
│   tblautonumbers     │        │   tblfeedback      │
│──────────────────────│        │────────────────────│
│ PK: AUTOID           │        │ PK: FEEDBACKID     │
│    AUTOSTART         │        │ FK: APPLICANTID    │
│    AUTOEND           │        │ FK: ADMINID        │
│    AUTOINC           │        │    SENTBY          │
│    AUTOKEY           │        │    FEEDBACK        │
└──────────────────────┘        │    DATETIME, STATUS│
                                └────────────────────┘

┌──────────────────────┐        ┌────────────────────┐
│    tblemployees      │        │ tbljobregistration │
│──────────────────────│        │────────────────────│
│ PK: INCID            │        │ PK: REGISTRATIONID │
│    EMPLOYEEID        │        │ FK: COMPANYID      │
│ FK: COMPANYID        │        │ FK: JOBID          │
│    FNAME, LNAME      │        │ FK: APPLICANTID    │
│    EMPUSERNAME       │        │    REMARKS         │
│    EMPPASSWORD       │        │    REGISTRATIONDATE│
│    POSITION          │        └────────────────────┘
└──────────────────────┘
```

### Core Table Definitions

#### tblusers Table
```sql
CREATE TABLE `tblusers` (
  `USERID` int(30) NOT NULL AUTO_INCREMENT,
  `IDN` varchar(30) NOT NULL,
  `FNAME` varchar(40) NOT NULL,
  `ONAME` varchar(200) NOT NULL,
  `EMAIL` varchar(50) NOT NULL,
  `USERNAME` varchar(90) NOT NULL,
  `PASS` varchar(90) NOT NULL,
  `ROLE` varchar(30) NOT NULL,
  PRIMARY KEY (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```
**Purpose**: Stores all user accounts (Applicants, Employers/Employees, Administrators)
**Roles**: 'Applicant', 'Employer', 'Administrator'

#### tbljob Table
```sql
CREATE TABLE `tbljob` (
  `JOBID` int(11) NOT NULL AUTO_INCREMENT,
  `COMPANYID` int(11) NOT NULL,
  `WORKPLACE_POLICY` varchar(50) NOT NULL,
  `JOBTITLE` varchar(90) NOT NULL,
  `JOBCATEGORYID` int(11) NOT NULL,
  `SALARY` double NOT NULL,
  `JOBTYPE` varchar(50) NOT NULL,
  `QUALIFICATION` varchar(50) NOT NULL,
  `JOBDESCRIPTION` text NOT NULL,
  `PREFEREDSEX` varchar(30) NOT NULL,
  `CAREERLEVEL` varchar(20) NOT NULL,
  `WORKEXPERIENCE` varchar(50) NOT NULL,
  `DEADLINE` date NOT NULL,
  `JOBSTATUS` varchar(90) NOT NULL,
  `DATEPOSTED` datetime NOT NULL,
  PRIMARY KEY (`JOBID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```
**Purpose**: Stores job postings
**Status Values**: 'Vacancy', 'Closed', 'Filled'
**Workplace Policies**: 'On-site', 'Hybrid', 'Remote'

#### tbljobapplication Table
```sql
CREATE TABLE `tbljobapplication` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `JOBID` int(11) NOT NULL,
  `RESUME_FILE` varchar(255) NOT NULL,
  `APPLICATIONSTATUS` varchar(20) NOT NULL,
  `APPLICATIONDATE` datetime NOT NULL,
  `SCORE` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```
**Purpose**: Tracks job applications
**Status Values**: 'Pending', 'Approved', 'Rejected', 'Under Review'

#### tblcompany Table
```sql
CREATE TABLE `tblcompany` (
  `COMPANYID` int(11) NOT NULL AUTO_INCREMENT,
  `COMPANYNAME` varchar(90) NOT NULL,
  `COMPANYADDRESS` varchar(90) NOT NULL,
  `COMPANYCONTACTNO` varchar(30) NOT NULL,
  `COMPANYSTATUS` varchar(90) NOT NULL,
  `COMPANYABOUT` text NOT NULL,
  `COMPANYEMAIL` varchar(50) NOT NULL,
  `COMPANYINDUSTRY` varchar(50) NOT NULL,
  `COMPANYSPECIALISM` varchar(50) NOT NULL,
  `COMPANYCOUNTRY` varchar(50) NOT NULL,
  `COMPANYCITY` varchar(50) NOT NULL,
  `COMPANYAWARD` varchar(20) NOT NULL,
  `COMPANYYEAR` varchar(10) NOT NULL,
  `COMPANYAWARDDESC` text NOT NULL,
  `COMPANYLOGO` blob NOT NULL,
  PRIMARY KEY (`COMPANYID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```
**Purpose**: Stores company/employer information

#### tblapplicants Table
```sql
CREATE TABLE `tblapplicants` (
  `APPLICANTID` int(11) NOT NULL AUTO_INCREMENT,
  `JOBCATEGORYID` int(11) NOT NULL,
  `JOBTITLE` varchar(100) NOT NULL,
  `EXCOMPANYNAME` varchar(200) NOT NULL,
  `EXJOBTITLE` varchar(50) NOT NULL,
  `USERID` int(11) NOT NULL,
  `FNAME` varchar(90) NOT NULL,
  `OTHERNAMES` varchar(200) NOT NULL,
  `FULLADDRESS` varchar(255) NOT NULL,
  `CITY` varchar(100) NOT NULL,
  `COUNTRY` varchar(50) NOT NULL,
  `SEX` varchar(11) NOT NULL,
  `BIRTHDATE` date NOT NULL,
  `ABOUTME` text NOT NULL,
  `USERNAME` varchar(90) NOT NULL,
  `EMAILADDRESS` varchar(90) NOT NULL,
  `CONTACTNO` varchar(90) NOT NULL,
  `DEGREE` text NOT NULL,
  `SCHOOLNAME` varchar(100) NOT NULL,
  `SKILLS` varchar(200) NOT NULL,
  `APPLICANTPHOTO` varchar(255) NOT NULL,
  `FB_link` varchar(255) NOT NULL,
  `LinkedIn_link` varchar(255) NOT NULL,
  PRIMARY KEY (`APPLICANTID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```
**Purpose**: Detailed applicant profiles and work history

#### tbljobcategory Table
```sql
CREATE TABLE `tbljobcategory` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CATEGORY` varchar(200) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```
**Purpose**: Job categories for classification
**Examples**: 'Financial Services & Software', 'IT and Networking', 'Engineering and Architecture'

#### Screening System Tables

**tblscreening** - Question bank:
```sql
CREATE TABLE `tblscreening` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `q_title` varchar(200) NOT NULL,
  `question` text NOT NULL,
  `opt_A` varchar(50) NOT NULL,
  `opt_B` varchar(50) NOT NULL,
  `opt_C` varchar(50) NOT NULL,
  `opt_D` varchar(50) NOT NULL,
  `opt_E` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```

**tblscreening_answer** - Correct answers:
```sql
CREATE TABLE `tblscreening_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `ideal_ans_opt` varchar(11) NOT NULL,
  `ideal_ans` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```

**tbljobscreening_ques** - Questions assigned to jobs:
```sql
CREATE TABLE `tbljobscreening_ques` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```

**tblscreening_qa** - Applicant answers:
```sql
CREATE TABLE `tblscreening_qa` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `JOBAPPLICATION_ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `JOBID` int(11) NOT NULL,
  `QUESTION_ID` int(11) NOT NULL,
  `APPLICANT_ANSWER` varchar(10) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```

**tblscreening_score** - Test results:
```sql
CREATE TABLE `tblscreening_score` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jobapp_id` int(11) NOT NULL,
  `score` varchar(10) NOT NULL,
  `total_ques` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```

### Supporting Tables

**tblbookmarkjob** - Saved jobs by applicants
**tblbookmarkresume** - Bookmarked resumes by employers
**tblnotification** - User notifications
**tblattachmentfile** - Additional application attachments
**tblfeedback** - Feedback system between users and admin
**tblautonumbers** - Auto-increment management
**tblemployees** - Employee/employer accounts
**tbljobregistration** - Job registration tracking
**tbljobsubcategory** - Job subcategories

### Database Indexes

**Existing Primary Keys**:
```sql
-- All tables have PRIMARY KEY indexes on their ID fields
tblusers: USERID
tbljob: JOBID
tbljobapplication: ID
tblcompany: COMPANYID
tblapplicants: APPLICANTID
tbljobcategory: ID
tblscreening: id
-- (etc. for all 20 tables)
```

**Additional Index**:
```sql
-- tblemployees has unique constraint
UNIQUE KEY `EMPLOYEEID` (`EMPLOYEEID`)
```

### Data Integrity Features

1. **Primary Keys**: All tables have AUTO_INCREMENT primary keys
2. **InnoDB Engine**: ACID compliance and transaction support
3. **Latin1 Character Set**: Compatible with existing data
4. **NOT NULL Constraints**: Enforced on key fields
5. **Application-Level Foreign Keys**: Relationships maintained in application code

### Database Backup Strategy

- **Frequency**: Daily automated backups via phpMyAdmin
- **Retention**: 30 days
- **Method**: SQL dump files
- **Location**: Off-site backup storage
- **Testing**: Weekly restore verification

---

## Security Architecture

### Multi-Layer Security Model

```
┌────────────────────────────────────────────────────────────┐
│                    LAYER 1: NETWORK SECURITY                │
├────────────────────────────────────────────────────────────┤
│ • SSL/TLS Encryption (HTTPS)                               │
│ • Secure HTTP Headers                                       │
│ • DDoS Protection                                           │
│ • Firewall Rules                                            │
└────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────────┐
│              LAYER 2: APPLICATION SECURITY                  │
├────────────────────────────────────────────────────────────┤
│ • Input Validation & Sanitization                          │
│ • Output Encoding (XSS Prevention)                         │
│ • CSRF Token Protection                                     │
│ • Session Security                                          │
│ • Rate Limiting                                             │
└────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────────┐
│            LAYER 3: AUTHENTICATION & AUTHORIZATION          │
├────────────────────────────────────────────────────────────┤
│ • Password Hashing (bcrypt)                                │
│ • Session-Based Authentication                              │
│ • Role-Based Access Control (RBAC)                         │
│ • Multi-Factor Authentication (planned)                     │
└────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────────┐
│                  LAYER 4: DATA SECURITY                     │
├────────────────────────────────────────────────────────────┤
│ • SQL Injection Prevention (Prepared Statements)           │
│ • Database Encryption                                       │
│ • Secure File Storage                                       │
│ • Data Backup & Recovery                                    │
└────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────────┐
│                LAYER 5: FILE UPLOAD SECURITY                │
├────────────────────────────────────────────────────────────┤
│ • File Type Validation                                      │
│ • MIME Type Checking                                        │
│ • File Size Limits                                          │
│ • .htaccess Protection                                      │
│ • Unique Filename Generation                                │
└────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────────┐
│              LAYER 6: MONITORING & AUDIT                    │
├────────────────────────────────────────────────────────────┤
│ • Activity Logging                                          │
│ • Admin Action Audit Trail                                  │
│ • Error Logging                                             │
│ • Security Event Monitoring                                 │
└────────────────────────────────────────────────────────────┘
```

### Authentication Flow (Detailed)

```
┌───────────────┐
│ Login Request │
│ (username +   │
│  password)    │
└───────┬───────┘
        │
        ▼
┌─────────────────────────────────────┐
│ 1. Input Validation                  │
│    • Check for empty fields         │
│    • Validate format                │
│    • Sanitize input                 │
└───────────────┬─────────────────────┘
                │
                ▼
┌─────────────────────────────────────┐
│ 2. Rate Limiting Check              │
│    • Check IP address attempts      │
│    • Enforce cooldown period        │
│    • Log suspicious activity        │
└───────────────┬─────────────────────┘
                │
                ▼
┌─────────────────────────────────────┐
│ 3. Database Query (Prepared)        │
│    SELECT user_id, password, role   │
│    FROM users                        │
│    WHERE username = ? AND            │
│          status = 'active'          │
└───────────────┬─────────────────────┘
                │
                ▼
┌─────────────────────────────────────┐
│ 4. Password Verification            │
│    password_verify(                 │
│      $input_password,               │
│      $stored_hash                   │
│    )                                │
└───────────────┬─────────────────────┘
                │
                ▼
        ┌───────┴───────┐
        │   Valid?      │
        └───────┬───────┘
                │
        ┌───────┴───────┐
        │               │
       Yes             No
        │               │
        ▼               ▼
┌──────────────┐  ┌──────────────────┐
│ 5. Session   │  │ 6. Login Failed  │
│    Creation  │  │    • Log attempt │
│    • user_id │  │    • Increment   │
│    • role    │  │      counter     │
│    • token   │  │    • Show error  │
│    • HttpOnly│  │    • Redirect    │
│    • Secure  │  └──────────────────┘
└──────┬───────┘
       │
       ▼
┌──────────────────┐
│ 7. Update Last   │
│    Login Time    │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ 8. Redirect to   │
│    Dashboard     │
│    (role-based)  │
└──────────────────┘
```

### Authorization Model (RBAC)

```
Role-Based Access Control Matrix

┌──────────────┬──────────────┬──────────────┬──────────────┐
│   Resource   │  Applicant   │   Employer   │    Admin     │
├──────────────┼──────────────┼──────────────┼──────────────┤
│ Browse Jobs  │      ✓       │      ✓       │      ✓       │
│ View Job     │      ✓       │      ✓       │      ✓       │
│ Apply to Job │      ✓       │      ✗       │      ✗       │
│ Save Job     │      ✓       │      ✗       │      ✗       │
│ Post Job     │      ✗       │      ✓       │      ✗       │
│ Edit Job     │      ✗       │   ✓ (own)    │      ✓       │
│ Delete Job   │      ✗       │   ✓ (own)    │      ✓       │
│ View Apps    │   ✓ (own)    │   ✓ (own)    │      ✓       │
│ Manage Users │      ✗       │      ✗       │      ✓       │
│ View Logs    │      ✗       │      ✗       │      ✓       │
│ Settings     │      ✗       │      ✗       │      ✓       │
└──────────────┴──────────────┴──────────────┴──────────────┘

Implementation in session_check.php:
```php
<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Get required role for current page
$required_role = getPageRole($_SERVER['REQUEST_URI']);

// Verify user has required role
if ($_SESSION['role'] !== $required_role) {
    header('Location: /unauthorized.php');
    exit();
}

// User is authenticated and authorized
?>
```

### SQL Injection Prevention

**All database queries use prepared statements:**

```php
// SECURE: Using prepared statements
$stmt = $mysqli->prepare("SELECT * FROM jobs WHERE job_id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();
$stmt->close();

// NEVER use string concatenation:
// $query = "SELECT * FROM jobs WHERE job_id = " . $job_id; // VULNERABLE!
```

### XSS Prevention

**All output is escaped:**

```php
// Display user input safely
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// For rich text (with allowed HTML tags)
echo strip_tags($user_input, '<b><i><u><a><p><br>');
```

### CSRF Protection

**Token generation and validation:**

```php
// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include in forms
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

// Validate on submission
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token validation failed');
}
```

### File Upload Security

**Resume upload validation:**

```php
// File upload security implementation
$allowed_types = ['application/pdf', 'application/msword', 
                  'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$max_size = 5 * 1024 * 1024; // 5MB

if (!in_array($_FILES['resume']['type'], $allowed_types)) {
    die('Invalid file type');
}

if ($_FILES['resume']['size'] > $max_size) {
    die('File too large');
}

// Generate unique filename
$extension = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
$new_filename = uniqid() . '_' . time() . '.' . $extension;
$upload_path = '/resumes_cv/' . $new_filename;

move_uploaded_file($_FILES['resume']['tmp_name'], $upload_path);
```

**.htaccess protection for resume directory:**

```apache
# /resumes_cv/.htaccess
Options -Indexes
<FilesMatch "\.(php|php3|php4|php5|phtml)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
```

### Session Security Configuration

```php
// Secure session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);
session_start();

// Regenerate session ID on login
session_regenerate_id(true);
```

### Security Headers

**Implemented via .htaccess:**

```apache
# Security headers
Header set X-Frame-Options "SAMEORIGIN"
Header set X-Content-Type-Options "nosniff"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"
Header set Permissions-Policy "geolocation=(), microphone=(), camera=()"
```

---

## Technology Stack

### Complete Technology Inventory

#### Backend Technologies

| Technology | Version | Purpose | Implementation |
|------------|---------|---------|----------------|
| **PHP** | 7.4+ | Server-side scripting | Core application logic |
| **MySQL** | 5.7+ / MariaDB 10.3+ | Relational database | Data persistence |
| **Apache** | 2.4+ | Web server | HTTP request handling |
| **MySQLi** | Native | Database driver | Database connectivity |

#### Frontend Technologies

| Technology | Version | Purpose | Implementation |
|------------|---------|---------|----------------|
| **HTML5** | Latest | Markup language | Semantic structure |
| **CSS3** | Latest | Styling | Custom styles + frameworks |
| **Bootstrap** | 5.x | CSS framework | Responsive layout, components |
| **TailwindCSS** | 3.x | Utility-first CSS | Rapid styling |
| **JavaScript** | ES6+ | Client-side logic | DOM manipulation, validation |
| **jQuery** | 3.x | JS library | AJAX, event handling |

#### CSS Libraries

| Library | Version | Purpose |
|---------|---------|---------|
| Bootstrap | 5.x | Responsive grid, components |
| TailwindCSS | 3.x | Utility classes |
| Slick | Latest | Image carousels |
| Snackbar | Latest | Toast notifications |

#### JavaScript Libraries

| Library | Version | Purpose |
|---------|---------|---------|
| jQuery | 3.x | DOM manipulation, AJAX |
| Popper.js | 2.x | Tooltips, popovers |
| Bootstrap JS | 5.x | Interactive components |
| Custom JS | - | Form validation, UI enhancements |

#### Development Tools

| Tool | Purpose |
|------|---------|
| **Git** | Version control system |
| **GitHub** | Code repository, collaboration |
| **VS Code** | Integrated development environment |
| **phpMyAdmin** | Database administration |
| **XAMPP/WAMP/MAMP** | Local development environment |
| **Chrome DevTools** | Frontend debugging |

#### Testing & Quality Tools

| Tool | Purpose |
|------|---------|
| **PHP_CodeSniffer** | PHP code quality (PSR-12) |
| **ESLint** | JavaScript linting |
| **Stylelint** | CSS linting |
| **PHPUnit** | PHP unit testing |
| **Cypress** | End-to-end testing |
| **PHPStan** | Static analysis |

#### CI/CD Tools

| Tool | Purpose |
|------|---------|
| **GitHub Actions** | Automated testing & deployment |
| **Workflows** | CI/CD pipeline configuration |

---

## Deployment Architecture

### Development Environment

```
┌──────────────────────────────────────────────────┐
│         Developer Workstation                     │
├──────────────────────────────────────────────────┤
│                                                   │
│  IDE: Visual Studio Code                         │
│  ├── Extensions:                                 │
│  │   ├── PHP Intelephense                       │
│  │   ├── ESLint                                 │
│  │   ├── GitLens                                │
│  │   └── Prettier                               │
│  │                                               │
│  Local Stack: XAMPP/WAMP/MAMP                   │
│  ├── Apache 2.4+                                │
│  ├── PHP 7.4+                                   │
│  ├── MySQL 5.7+                                 │
│  └── phpMyAdmin                                 │
│                                                   │
│  Version Control                                 │
│  ├── Git (local repository)                     │
│  └── GitHub (remote repository)                 │
│                                                   │
│  Testing Tools                                   │
│  ├── Browser: Chrome/Firefox                    │
│  ├── DevTools                                   │
│  └── Postman (API testing)                      │
│                                                   │
└──────────────────────────────────────────────────┘
```

### Staging Environment (Optional)

```
┌──────────────────────────────────────────────────┐
│         Staging Server                            │
├──────────────────────────────────────────────────┤
│                                                   │
│  Server: Shared Hosting                          │
│  ├── Apache 2.4                                  │
│  ├── PHP 7.4                                     │
│  ├── MySQL 5.7                                   │
│  └── SSL Certificate                             │
│                                                   │
│  URL: staging.munext.ca (if configured)         │
│                                                   │
│  Purpose:                                        │
│  ├── Pre-production testing                      │
│  ├── Client review                               │
│  └── QA validation                               │
│                                                   │
└──────────────────────────────────────────────────┘
```

### Production Environment

```
┌──────────────────────────────────────────────────┐
│         Production Server (munext.ca)            │
├──────────────────────────────────────────────────┤
│                                                   │
│  Hosting: Shared Hosting / VPS                   │
│  URL: https://munext.ca/                         │
│                                                   │
│  Web Server                                      │
│  ├── Apache 2.4+                                │
│  ├── mod_rewrite (enabled)                      │
│  ├── mod_ssl (HTTPS)                            │
│  └── .htaccess rules                            │
│                                                   │
│  Application Server                              │
│  ├── PHP 7.4+                                   │
│  │   ├── MySQLi extension                       │
│  │   ├── GD library (image processing)          │
│  │   ├── mbstring                               │
│  │   └── file_uploads enabled                   │
│  │                                               │
│  Database Server                                 │
│  ├── MySQL 5.7+ / MariaDB 10.3+                │
│  ├── Database: erisdb                           │
│  ├── User: munext_user (limited privileges)    │
│  └── phpMyAdmin access                          │
│                                                   │
│  File System                                     │
│  ├── /public_html/                              │
│  │   ├── Application files                      │
│  │   ├── /resumes_cv/ (protected)              │
│  │   ├── /avater/ (uploads)                    │
│  │   ├── /company_logo/ (uploads)              │
│  │   └── /assets/ (static)                     │
│  │                                               │
│  Security                                        │
│  ├── SSL/TLS Certificate (Let's Encrypt)       │
│  ├── HTTPS enforcement                          │
│  ├── Firewall rules                             │
│  └── DDoS protection                            │
│                                                   │
│  Backup                                          │
│  ├── Daily automated backups                    │
│  ├── 30-day retention                           │
│  └── Off-site storage                           │
│                                                   │
│  Monitoring                                      │
│  ├── Uptime monitoring                          │
│  ├── Error logging                              │
│  ├── Performance metrics                        │
│  └── Security alerts                            │
│                                                   │
└──────────────────────────────────────────────────┘
```

### Deployment Process

```
┌──────────────────┐
│ 1. Development   │
│    Complete      │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ 2. Local Testing │
│    • Unit tests  │
│    • E2E tests   │
│    • Code review │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ 3. Git Commit    │
│    & Push        │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ 4. CI/CD Pipeline│
│    (GitHub       │
│     Actions)     │
│    • Linting     │
│    • Tests       │
│    • Build       │
└────────┬─────────┘
         │
         ▼
    ┌────┴────┐
    │ Pass?   │
    └────┬────┘
         │
    ┌────┴────┐
    Yes      No
     │        │
     │        ▼
     │   ┌──────────┐
     │   │ 5. Fix   │
     │   │    Issues│
     │   └────┬─────┘
     │        │
     │        └─────┐
     │              │
     ▼              ▼
┌──────────────────────────┐
│ 6. Manual Deployment     │
│    (FTP/cPanel/Git Pull) │
└────────┬─────────────────┘
         │
         ▼
┌──────────────────┐
│ 7. Database      │
│    Migration     │
│    (if needed)   │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ 8. Configuration │
│    Update        │
│    • DB creds    │
│    • Paths       │
│    • Settings    │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ 9. Smoke Testing │
│    • Login       │
│    • Key features│
│    • Links       │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ 10. Go Live      │
│     🎉           │
└──────────────────┘
```

### CI/CD Pipeline (GitHub Actions)

**Workflow Configuration** (`.github/workflows/ci.yml`):

```yaml
name: CI/CD Pipeline

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  quality-check:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: PHP CodeSniffer
        run: npm run lint:php
      
      - name: ESLint
        run: npm run lint:js
      
      - name: Stylelint
        run: npm run lint:css

  php-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
      
      - name: Run PHPUnit
        run: npm run test:php

  e2e-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Run Cypress
        run: npm run test:e2e

  security-scan:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Security vulnerabilities
        run: npm audit
```

**Test Commands** (from package.json):

```json
{
  "scripts": {
    "lint:all": "npm run lint:php && npm run lint:js && npm run lint:css",
    "lint:php": "phpcs --standard=PSR12",
    "lint:js": "eslint **/*.js",
    "lint:css": "stylelint **/*.css",
    "test:php": "phpunit tests/",
    "test:e2e": "cypress run",
    "ci": "npm run lint:all && npm run test:php && npm run test:e2e"
  }
}
```

### Environment Configuration

**Development (mysqli_connect.php)**:
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'erisdb');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('APP_URL', 'http://localhost/MUNext');
define('ENVIRONMENT', 'development');
define('DEBUG_MODE', true);
?>
```

**Production (mysqli_connect.php)**:
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'munext_erisdb');
define('DB_USER', 'munext_user');
define('DB_PASSWORD', 'SECURE_PASSWORD_HERE');
define('APP_URL', 'https://munext.ca');
define('ENVIRONMENT', 'production');
define('DEBUG_MODE', false);
?>
```

---

## Integration Architecture

### Email Integration

**Email Notification System** (`notification-mail.php`):

```
Email Notification Flow
┌────────────────────┐
│  Trigger Event     │
│  • New application │
│  • Job posted      │
│  • Status change   │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ notification-mail  │
│       .php         │
│  • Build email     │
│  • Get template    │
│  • Insert data     │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│  SMTP Server       │
│  • Send email      │
│  • Handle errors   │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│  Recipient         │
│  • Job seeker      │
│  • Employer        │
│  • Admin           │
└────────────────────┘
```

**Email Types**:
- Application confirmation (to applicant)
- New application alert (to employer)
- Job posting approval (to employer)
- Account verification
- Password reset
- Status updates

### AJAX Integration

**Asynchronous Operations**:

```javascript
// Example: Toggle job status (employer dashboard)
function toggleJobStatus(jobId, newStatus) {
    $.ajax({
        url: 'ajax_toggle_job_status.php',
        method: 'POST',
        data: {
            job_id: jobId,
            status: newStatus,
            csrf_token: csrfToken
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateJobStatusUI(jobId, newStatus);
                showNotification('Status updated successfully');
            } else {
                showError(response.message);
            }
        },
        error: function() {
            showError('An error occurred');
        }
    });
}
```

**AJAX Endpoints**:
- `ajax_toggle_job_status.php` - Update job status
- `ajax-get-messages.php` - Fetch messages
- Admin detail APIs:
  - `admin-get-user-details.php`
  - `admin-get-job-details.php`
  - `admin-get-application-details.php`
  - `admin-get-company-details.php`

### File System Integration

**File Upload Flow**:

```
Upload Process
┌─────────────────┐
│ User selects    │
│ file (resume)   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Client-side     │
│ validation      │
│ • File type     │
│ • File size     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Upload to       │
│ server          │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Server-side     │
│ validation      │
│ • MIME type     │
│ • Size limit    │
│ • Scan file     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Generate unique │
│ filename        │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Move to storage │
│ /resumes_cv/    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Save path to DB │
│ applications    │
│ table           │
└─────────────────┘
```

**File Download/View Flow**:

```
Download Process
┌─────────────────┐
│ User clicks     │
│ download/view   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Session check   │
│ • Logged in?    │
│ • Authorized?   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Verify          │
│ ownership       │
│ • Employer?     │
│ • Applicant?    │
│ • Admin?        │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Get file from   │
│ /resumes_cv/    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Set headers     │
│ • Content-Type  │
│ • Disposition   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Stream file to  │
│ browser         │
└─────────────────┘
```

---

## Performance Architecture

### Database Query Optimization

**Indexing Strategy**:
```sql
-- High-priority indexes (frequently queried columns)
CREATE INDEX idx_jobs_status_posted ON jobs(status, posted_at);
CREATE INDEX idx_applications_job_status ON applications(job_id, status);
CREATE INDEX idx_users_email_status ON users(email, status);

-- Composite indexes for common queries
CREATE INDEX idx_jobs_location_type ON jobs(location, job_type);
CREATE INDEX idx_jobs_employer_status ON jobs(employer_id, status);

-- Full-text search indexes
CREATE FULLTEXT INDEX ft_jobs_search ON jobs(title, description);
CREATE FULLTEXT INDEX ft_companies_search ON companies(name, description);
```

**Query Performance Examples**:

```php
// Optimized job listing query with pagination
$stmt = $mysqli->prepare("
    SELECT j.*, c.name as company_name, c.logo_path,
           COUNT(a.application_id) as application_count
    FROM jobs j
    LEFT JOIN companies c ON j.company_id = c.company_id
    LEFT JOIN applications a ON j.job_id = a.job_id
    WHERE j.status = 'active'
      AND j.expires_at > NOW()
    GROUP BY j.job_id
    ORDER BY j.posted_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("ii", $limit, $offset);
```

**Connection Pooling** (planned):
- Persistent database connections
- Connection reuse across requests
- Reduced connection overhead

### Caching Strategy

**Current Implementation**:
- Browser caching (CSS, JS, images)
- HTTP cache headers via .htaccess

```apache
# Cache static assets for 1 week
<FilesMatch "\.(jpg|jpeg|png|gif|ico|css|js|woff|woff2)$">
    Header set Cache-Control "max-age=604800, public"
</FilesMatch>
```

**Planned Enhancements**:
- Query result caching (Redis/Memcached)
- Page fragment caching
- Session storage in Redis
- OPcache for PHP bytecode

### Asset Optimization

**Current Optimizations**:
- Minified CSS and JavaScript
- Compressed images (optimized sizes)
- CDN for third-party libraries

```html
<!-- Using CDN for faster loading -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
```

**Lazy Loading**:
```javascript
// Lazy load job listings as user scrolls
$(window).on('scroll', function() {
    if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
        loadMoreJobs();
    }
});
```

### Pagination

**Implementation**:
```php
// Pagination for job listings
$jobs_per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $jobs_per_page;

$stmt = $mysqli->prepare("
    SELECT * FROM jobs 
    WHERE status = 'active'
    LIMIT ? OFFSET ?
");
$stmt->bind_param("ii", $jobs_per_page, $offset);
```

### Performance Metrics

**Target Metrics**:
- Page load time: < 2 seconds
- Time to First Byte (TTFB): < 500ms
- Database query time: < 100ms
- File upload: < 10 seconds (5MB file)
- API response time: < 200ms

---

## Testing Architecture

### Testing Pyramid

```
               /\
              /  \
             /E2E \
            / Tests\
           /────────\
          /          \
         / Integration\
        /    Tests     \
       /────────────────\
      /                  \
     /    Unit Tests      \
    /                      \
   /────────────────────────\
```

### Automated Testing Suite

**Test Categories**:

1. **Code Quality Checks**
   - PHP_CodeSniffer (PSR-12 compliance)
   - ESLint (JavaScript standards)
   - Stylelint (CSS best practices)
   - PHPStan (Static analysis)

2. **Unit Tests**
   - PHPUnit for PHP functions
   - Database helper functions
   - Validation functions
   - Utility functions

3. **Integration Tests**
   - User authentication flow
   - Job posting workflow
   - Application submission
   - Email notifications

4. **End-to-End Tests**
   - Cypress for browser automation
   - Complete user journeys
   - Cross-browser testing

### Running Tests

**All Tests**:
```bash
npm run ci
# Runs: linting + unit tests + E2E tests
```

**Individual Test Suites**:
```bash
# Code quality
npm run lint:all

# PHP unit tests
npm run test:php

# E2E tests
npm run test:e2e
```

### CI/CD Pipeline Testing

**Automated on Every Push**:
✅ Code quality checks (linters)
✅ Unit tests
✅ Integration tests
✅ E2E tests
✅ Security scans

**Badge Status**:
[![CI/CD Pipeline](https://github.com/Group-7-6905/MUNext/workflows/CI%2FCD%20Pipeline/badge.svg)](https://github.com/Group-7-6905/MUNext/actions)

---

## Design Decisions

### Why Core PHP Instead of Framework?

**Decision**: Use core PHP without a framework (Laravel, Symfony, etc.)

**Rationale**:
1. **Shared Hosting Compatibility**: Many frameworks require specific server configurations unavailable on shared hosting
2. **Learning Objectives**: Team learns PHP fundamentals without framework abstractions
3. **Performance**: Reduced overhead for a relatively simple application
4. **Deployment Simplicity**: Easier to deploy on various hosting environments
5. **Resource Constraints**: Lower memory footprint

**Trade-offs**:
- ❌ More manual code for common tasks
- ❌ Lack of built-in security features
- ❌ No ORM (Object-Relational Mapping)
- ✅ Complete control over code
- ✅ Faster execution for simple operations
- ✅ Easier to understand for beginners

### Why Session-Based Authentication Instead of JWT?

**Decision**: Traditional PHP sessions instead of JSON Web Tokens

**Rationale**:
1. **Simplicity**: Built-in PHP session handling
2. **Server-Side Control**: Easy session revocation
3. **Security**: HttpOnly cookies prevent XSS attacks
4. **State Management**: Server maintains user state
5. **Development Speed**: Faster implementation

**Trade-offs**:
- ❌ Not suitable for stateless APIs
- ❌ Scalability challenges (session storage)
- ✅ Proven, mature technology
- ✅ No token management complexity
- ✅ Automatic expiration handling

### Why Multiple Dashboards Instead of Unified?

**Decision**: Separate dashboard directories for each role (applicant, employer, admin)

**Rationale**:
1. **Clear Separation**: Different functionality per role
2. **Security**: Isolated code reduces cross-role vulnerabilities
3. **Maintainability**: Easier to modify one role without affecting others
4. **UI/UX**: Tailored interface for each user type
5. **Performance**: Load only relevant code

**Trade-offs**:
- ❌ Some code duplication (headers, footers)
- ❌ Larger overall codebase
- ✅ Better security
- ✅ Easier debugging
- ✅ Role-specific optimization

### Why MySQL Instead of PostgreSQL or MongoDB?

**Decision**: MySQL/MariaDB relational database

**Rationale**:
1. **Hosting Availability**: Universal support on shared hosting
2. **Team Familiarity**: Most team members know MySQL
3. **Relational Data**: Application data fits relational model
4. **phpMyAdmin**: Easy database management
5. **Performance**: Excellent for read-heavy workloads

**Trade-offs**:
- ❌ Less advanced features than PostgreSQL
- ❌ Not ideal for unstructured data
- ✅ Mature, stable technology
- ✅ Extensive documentation
- ✅ Strong community support

### Why Bootstrap + TailwindCSS Instead of One?

**Decision**: Use both Bootstrap and TailwindCSS

**Rationale**:
1. **Component Library**: Bootstrap provides pre-built components
2. **Utility Classes**: Tailwind enables rapid custom styling
3. **Responsive Grid**: Bootstrap's grid system
4. **Team Preferences**: Different team members prefer different approaches
5. **Flexibility**: Best of both worlds

**Trade-offs**:
- ❌ Larger CSS bundle size
- ❌ Potential style conflicts
- ❌ Learning curve for both
- ✅ Maximum flexibility
- ✅ Fast development
- ✅ Consistent design system

### Why Manual Deployment Instead of Automated?

**Decision**: Manual deployment via FTP/cPanel instead of CI/CD deployment

**Rationale**:
1. **Shared Hosting Limitations**: Limited deployment automation options
2. **Cost**: Automated deployment often requires VPS/cloud hosting
3. **Control**: Manual verification before going live
4. **Simplicity**: Straightforward for small team
5. **Learning**: Team gains deployment experience

**Trade-offs**:
- ❌ Higher risk of human error
- ❌ Slower deployment process
- ❌ No rollback automation
- ✅ Lower hosting costs
- ✅ Full deployment control
- ✅ Suitable for project scale

### Why File-Based Uploads Instead of Cloud Storage?

**Decision**: Local file system storage instead of AWS S3/Cloud Storage

**Rationale**:
1. **Cost**: No additional storage costs
2. **Simplicity**: Standard PHP file operations
3. **Privacy**: Data stays on own server
4. **Project Scope**: Limited file volume expected
5. **Hosting Plan**: Sufficient storage available

**Trade-offs**:
- ❌ Limited scalability
- ❌ No CDN distribution
- ❌ Backup complexity
- ✅ Lower costs
- ✅ Simpler implementation
- ✅ Full data control

---

## Future Enhancements

### Short-Term (0-6 months)

1. **Enhanced Security**
   - Implement 2FA (Two-Factor Authentication)
   - Add reCAPTCHA for forms
   - Implement rate limiting
   - Add virus scanning for uploads

2. **Performance Optimization**
   - Implement Redis caching
   - Add query result caching
   - Optimize database indexes
   - Implement lazy loading for images

3. **Feature Additions**
   - Email verification
   - Advanced search filters
   - Company reviews and ratings
   - Applicant interview scheduling

### Medium-Term (6-12 months)

1. **API Development**
   - RESTful API for mobile app
   - OAuth2 authentication
   - API documentation (Swagger/OpenAPI)
   - Rate limiting and throttling

2. **Analytics Enhancement**
   - Advanced reporting dashboards
   - Machine learning job recommendations
   - Predictive analytics
   - A/B testing framework

3. **Integration**
   - LinkedIn integration
   - Google Calendar sync
   - Slack notifications
   - Payment gateway (for premium features)

### Long-Term (12+ months)

1. **Architecture Migration**
   - Migrate to cloud hosting (AWS/Azure)
   - Implement microservices
   - Add load balancing
   - Containerization with Docker

2. **Advanced Features**
   - AI-powered resume parsing
   - Video interview integration
   - Skills assessment tests
   - Background check integration

3. **Mobile Application**
   - Native iOS app
   - Native Android app
   - React Native cross-platform app
   - Mobile-first redesign

---

## Appendix

### Glossary

- **RBAC**: Role-Based Access Control
- **CSRF**: Cross-Site Request Forgery
- **XSS**: Cross-Site Scripting
- **SQL Injection**: Malicious SQL statement injection
- **PSR-12**: PHP Standards Recommendation for coding style
- **AJAX**: Asynchronous JavaScript and XML
- **MySQLi**: MySQL Improved Extension
- **CDN**: Content Delivery Network
- **SSL/TLS**: Secure Sockets Layer / Transport Layer Security
- **CI/CD**: Continuous Integration / Continuous Deployment

### References

- **PHP Documentation**: https://www.php.net/docs.php
- **MySQL Documentation**: https://dev.mysql.com/doc/
- **Bootstrap Documentation**: https://getbootstrap.com/docs/
- **TailwindCSS Documentation**: https://tailwindcss.com/docs
- **OWASP Security Guidelines**: https://owasp.org/
- **PSR Standards**: https://www.php-fig.org/psr/

### Contact Information

**Project Repository**: https://github.com/Group-7-6905/MUNext  
**Live Site**: https://munext.ca/  
**Project Lead**: Victor Chisom Muojeke (vcmuojeke@mun.ca)  
**Team**: Group 7 - COMP 6905, Memorial University of Newfoundland

---

**Document Version**: 2.0  
**Status**: Production - Fully Functional  
**Last Updated**: December 2, 2025  
**Maintained By**: Group 7 - MUNext Team  
**Next Review**: January 2026

---

*This architecture documentation reflects the current state of the fully functional MUNext platform. For the latest updates, please refer to the GitHub repository.*
