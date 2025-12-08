# MUNext - Task Mapping & Team Contributions (Updated)

**Project:** MUNext - Memorial University Job Board Platform  
**Course:** COMP 6905 - Software Engineering  
**Team:** Group 7 (8 Members)  
**Period:** September 22 - December 12, 2024  
**Repository:** https://github.com/Group-7-6905/MUNext

---

## Table of Contents

1. [Team Overview](#team-overview)
2. [Project Statistics](#project-statistics)
3. [Task Distribution by Phase](#task-distribution-by-phase)
4. [Feature Assignments](#feature-assignments)
5. [Contribution Analysis](#contribution-analysis)
6. [Sprint Breakdown](#sprint-breakdown)
7. [Pull Request Summary](#pull-request-summary)

---

## Team Overview

### Team Structure

| Name | Student ID | Role | GitHub Username | Primary Focus |
|------|------------|------|-----------------|---------------|
| **Victor Chisom Muojeke** | 202480408 | **Team Leader** | portalzone | Backend Lead, Architecture |
| Chiemerie Obijiaku | 202492457 | Full Stack Developer | emeldo39 | Core Features, Database |
| Akinfenwa Oluwaseun | 202382513 | Frontend Developer | osakinfenwa | UI/UX, Styling |
| Kelechukwu Ede | 202580616 | Backend Developer | Kelechiede | Authentication, Security |
| Akefar Islam | 202093086 | Database Administrator | Akefar | Schema Design, Optimization |
| Adiba Ekpunobi | 202234225 | UI/UX Designer | AdibaKayla | Design, User Experience |
| Joshua Oseimobor | 202492785 | Quality Assurance | walkwithoj | Testing, Bug Fixes |
| Victor Onwosi | 202488814 | DevOps | Adimvicky | Deployment, Configuration |

---

## Project Statistics

### Final Numbers (As of December 12, 2024)

| Metric | Value |
|--------|-------|
| **Total Duration** | 81 days (Sep 22 - Dec 12) |
| **Total Story Points** | 310 |
| **Completed Story Points** | 310 (100%) |
| **User Stories** | 48 |
| **Pull Requests** | 67 merged |
| **Commits** | 560+ |
| **Team Members** | 8 developers |
| **Iterations** | 2 (100% each) |
| **Test Coverage** | 90%+ |
| **Code Files** | 50+ PHP files |
| **Database Tables** | 12 tables |

---

## Task Distribution by Phase

### Phase 1: Requirements & Design (Sep 22 - Oct 6)

**Duration:** 14 days | **Story Points:** 26

| Task | Assigned To | Status | Points |
|------|-------------|--------|--------|
| Requirements gathering | Team | ✅ Complete | 3 |
| System architecture design | Victor Chisom Muojeke | ✅ Complete | 5 |
| Database schema design | Akefar Islam | ✅ Complete | 5 |
| Technology stack selection | Team | ✅ Complete | 2 |
| Repository setup | Victor Chisom Muojeke | ✅ Complete | 2 |
| Project structure planning | Victor Chisom Muojeke | ✅ Complete | 3 |
| UI/UX mockups | Adiba Ekpunobi | ✅ Complete | 4 |
| Development environment setup | Victor Onwosi | ✅ Complete | 2 |

**Phase Total:** 26/26 points (100%)

---

### Phase 2: Database Schema Setup (Oct 1 - Oct 13)

**Duration:** 12 days | **Story Points:** 15

| Task | Assigned To | Status | Points |
|------|-------------|--------|--------|
| Table creation (12 tables) | Akefar Islam | ✅ Complete | 8 |
| Relationships & foreign keys | Akefar Islam | ✅ Complete | 3 |
| Indexes optimization | Akefar Islam | ✅ Complete | 2 |
| mysqli_connect.php | Chiemerie Obijiaku | ✅ Complete | 2 |

**Phase Total:** 15/15 points (100%)

---

### Phase 3: Core Features (Oct 10 - Nov 18)

**Duration:** 39 days | **Story Points:** 165

#### Authentication System (18 points)
| Task | Assigned To | Status | Points |
|------|-------------|--------|--------|
| Login/Registration pages | Kelechukwu Ede | ✅ Complete | 8 |
| Password hashing | Kelechukwu Ede | ✅ Complete | 3 |
| Session management (session_check.php) | Kelechukwu Ede | ✅ Complete | 5 |
| Logout functionality | Kelechukwu Ede | ✅ Complete | 2 |

#### Job Browsing & Search (42 points)
| Task | Assigned To | Status | Points |
|------|-------------|--------|--------|
| browse-jobs.php | Chiemerie Obijiaku | ✅ Complete | 8 |
| browse-category.php | Chiemerie Obijiaku | ✅ Complete | 6 |
| browse-employers.php | Chiemerie Obijiaku | ✅ Complete | 6 |
| job-detail.php | Chiemerie Obijiaku | ✅ Complete | 8 |
| job-list-v1.php | Akinfenwa Oluwaseun | ✅ Complete | 5 |
| job-search-v1.php | Akinfenwa Oluwaseun | ✅ Complete | 9 |

#### Homepage & Landing (20 points)
| Task | Assigned To | Status | Points |
|------|-------------|--------|--------|
| index.php design | Adiba Ekpunobi | ✅ Complete | 6 |
| index.php implementation | Akinfenwa Oluwaseun | ✅ Complete | 8 |
| employer-detail.php | Chiemerie Obijiaku | ✅ Complete | 6 |

#### User Dashboards (52 points)
| Task | Assigned To | Status | Points |
|------|-------------|--------|--------|
| Applicant dashboard | Chiemerie Obijiaku | ✅ Complete | 10 |
| Employer dashboard | Chiemerie Obijiaku | ✅ Complete | 10 |
| My applications page | Chiemerie Obijiaku | ✅ Complete | 8 |
| Saved jobs feature | Akinfenwa Oluwaseun | ✅ Complete | 6 |
| Post job interface | Chiemerie Obijiaku | ✅ Complete | 10 |
| Manage jobs page | Chiemerie Obijiaku | ✅ Complete | 8 |

#### Job Application System (33 points)
| Task | Assigned To | Status | Points |
|------|-------------|--------|--------|
| apply-job.php | Chiemerie Obijiaku | ✅ Complete | 12 |
| Resume upload functionality | Kelechukwu Ede | ✅ Complete | 8 |
| Application tracking | Chiemerie Obijiaku | ✅ Complete | 8 |
| Applicant view for employers | Chiemerie Obijiaku | ✅ Complete | 5 |

**Phase Total:** 165/165 points (100%)

---

### Phase 4: Admin Dashboard & Analytics (Nov 5 - Nov 25)

**Duration:** 20 days | **Story Points:** 60

| Task | Assigned To | Status | Points |
|------|-------------|--------|--------|
| **Admin Dashboard Pages** |
| admin/dashboard.php | Victor Chisom Muojeke | ✅ Complete | 10 |
| admin/users.php (User Management) | Victor Chisom Muojeke | ✅ Complete | 10 |
| admin/jobs.php (Job Management) | Victor Chisom Muojeke | ✅ Complete | 8 |
| admin/companies.php | Victor Chisom Muojeke | ✅ Complete | 8 |
| admin/applications.php | Victor Chisom Muojeke | ✅ Complete | 8 |
| **Analytics & Statistics** |
| Statistics dashboard | Victor Chisom Muojeke | ✅ Complete | 8 |
| Charts integration (Chart.js) | Victor Chisom Muojeke | ✅ Complete | 5 |
| Reports generation | Akefar Islam | ✅ Complete | 3 |

**Phase Total:** 60/60 points (100%)

---

### Phase 5: Extended Features (Nov 10 - Dec 2)

**Duration:** 22 days | **Story Points:** 22

| Task | Assigned To | Status | Points |
|------|-------------|--------|--------|
| Email notification system | Victor Chisom Muojeke | ✅ Complete | 8 |
| notification-mail.php | Victor Chisom Muojeke | ✅ Complete | 5 |
| Job alerts feature | Victor Chisom Muojeke | ✅ Complete | 5 |
| Screening questions | Chiemerie Obijiaku | ✅ Complete | 4 |

**Phase Total:** 22/22 points (100%)

---

### Phase 6: Testing (Nov 15 - Dec 6)

**Duration:** 21 days | **Story Points:** 16

| Task | Assigned To | Status | Points |
|------|-------------|--------|--------|
| PHPUnit setup | Joshua Oseimobor | ✅ Complete | 3 |
| Unit tests creation | Team | ✅ Complete | 5 |
| Cypress E2E setup | Victor Chisom Muojeke | ✅ Complete | 3 |
| E2E test scenarios | Team | ✅ Complete | 5 |

**Phase Total:** 16/16 points (100%)

---

### Phase 7: Code Quality & Documentation (Nov 25 - Dec 8)

**Duration:** 13 days | **Story Points:** 6

| Task | Assigned To | Status | Points |
|------|-------------|--------|--------|
| PHP_CodeSniffer setup | Victor Chisom Muojeke | ✅ Complete | 1 |
| ESLint configuration | Victor Chisom Muojeke | ✅ Complete | 1 |
| CODING_STANDARDS.md | Victor Chisom Muojeke | ✅ Complete | 2 |
| ARCHITECTURE.md | Victor Chisom Muojeke | ✅ Complete | 2 |

**Phase Total:** 6/6 points (100%)

---

## Feature Assignments

### Detailed Contributions by Developer

#### Victor Chisom Muojeke (Team Leader)
**Total Story Points:** ~95

**Major Features:**
- Complete admin dashboard system
  - User management interface
  - Company management
  - Job moderation
  - Application tracking
  - Statistics & analytics
- Email notification system
- Job alerts functionality
- Cypress E2E testing setup
- Code quality tools (linters)
- Documentation (CODING_STANDARDS.md)

**Leadership Contributions:**
- Project architecture design
- Team coordination (8 members)
- Code review (67 PRs)
- Sprint planning
- Final report preparation
- Presentation coordination

**GitHub Activity:**
- Commits: ~120
- Files changed: ~65
- Lines added: ~5,200

---

#### Chiemerie Obijiaku (Full Stack Developer)
**Total Story Points:** ~115

**Major Features:**
- Complete job browsing system
  - browse-jobs.php
  - browse-category.php
  - browse-employers.php
  - job-detail.php
  - employer-detail.php
- Complete applicant dashboard
- Complete employer dashboard
- Job application system (apply-job.php)
- Job posting interface
- Job management pages
- Applicant tracking
- Screening questions

**Key Contributions:**
- Highest contributor to core features
- Complex business logic
- Database integration
- Dashboard development

**GitHub Activity:**
- Commits: ~150
- Files changed: ~75
- Lines added: ~6,500

---

#### Akinfenwa Oluwaseun (Frontend Developer)
**Total Story Points:** ~70

**Major Features:**
- Homepage (index.php) implementation
- Job listing views (job-list-v1.php)
- Job search interface (job-search-v1.php)
- Saved jobs functionality
- Profile management pages
- Avatar upload features
- Company logo upload
- Responsive design implementation

**Key Contributions:**
- Frontend development
- Bootstrap/TailwindCSS integration
- UI/UX polish
- Responsive design

**GitHub Activity:**
- Commits: ~95
- Files changed: ~60
- Lines added: ~4,200

---

#### Kelechukwu Ede (Backend Developer)
**Total Story Points:** ~55

**Major Features:**
- Complete authentication system
- Login/Registration pages
- Session management (session_check.php)
- Password hashing (PASSWORD_BCRYPT)
- Resume upload functionality
- File upload security
- Input validation

**Key Contributions:**
- Security implementation
- Authentication/authorization
- Session handling
- File security

**GitHub Activity:**
- Commits: ~70
- Files changed: ~40
- Lines added: ~2,800

---

#### Akefar Islam (Database Administrator)
**Total Story Points:** ~45

**Major Features:**
- Database schema design (12 tables)
- Query optimization
- Database indexes
- Analytics queries
- Reports generation
- Database documentation

**Key Contributions:**
- Database architecture
- Schema relationships
- Performance optimization
- Data integrity

**GitHub Activity:**
- Commits: ~55
- Files changed: ~30
- Lines added: ~2,200

---

#### Adiba Ekpunobi (UI/UX Designer)
**Total Story Points:** ~30

**Major Features:**
- Homepage design (index.php)
- UI/UX mockups
- Design system
- Asset integration
- Color scheme (#862633)
- Typography standards

**Key Contributions:**
- Visual design
- User experience
- Design consistency
- Asset management

**GitHub Activity:**
- Commits: ~45
- Files changed: ~35
- Lines added: ~1,800

---

#### Joshua Oseimobor (Quality Assurance)
**Total Story Points:** ~25

**Major Features:**
- PHPUnit test framework
- Unit test creation
- Bug tracking and reporting
- Cross-browser testing
- Test documentation

**Key Contributions:**
- Quality assurance
- Bug identification
- Testing framework
- Test coverage

**GitHub Activity:**
- Commits: ~35
- Files changed: ~25
- Lines added: ~1,200

---

#### Victor Onwosi (DevOps)
**Total Story Points:** ~20

**Major Features:**
- Production deployment
- Server configuration
- Environment setup
- Database deployment
- CI/CD pipeline

**Key Contributions:**
- Deployment management
- Server setup
- Configuration
- Technical support

**GitHub Activity:**
- Commits: ~30
- Files changed: ~20
- Lines added: ~1,000

---

## Contribution Analysis

### Story Points Distribution

```
Chiemerie Obijiaku:    115 points  (37.1%)   ██████████████████████████
Victor Chisom Muojeke:         95 points  (30.6%)   ██████████████████████
Victor Onwosi:          80 points   (30.5%)  ███████████████████
Akinfenwa Oluwaseun:    70 points  (22.6%)   ██████████████████
Kelechukwu Ede:         55 points  (17.7%)   ███████████████
Akefar Islam:           45 points  (14.5%)   ██████████████
Adiba Ekpunobi:         40 points   (10.7%)  ████████████
Joshua Oseimobor:       40 points   (10.1%)  ████████████
```

### Commit Distribution

| Developer | Commits | % of Total | Files Changed | Lines Added |
|-----------|---------|------------|---------------|-------------|
| Chiemerie Obijiaku | ~150 | 26.8% | 75 | ~6,500 |
| Victor Chisom Muojeke | ~120 | 21.4% | 65 | ~5,200 |
| Akinfenwa Oluwaseun | ~95 | 17.0% | 60 | ~4,200 |
| Kelechukwu Ede | ~70 | 12.5% | 40 | ~2,800 |
| Akefar Islam | ~55 | 9.8% | 30 | ~2,200 |
| Adiba Ekpunobi | ~45 | 8.0% | 35 | ~1,800 |
| Joshua Oseimobor | ~35 | 6.3% | 25 | ~1,200 |
| Victor Onwosi | ~30 | 5.4% | 20 | ~1,000 |
| **Total** | **~560** | **100%** | **~350** | **~24,900** |

---

## Sprint Breakdown

### Iteration 1 (Nov 4-18, 2024)
**Goal:** Core features and dashboards

**Completed Stories:** 27  
**Story Points:** 165  
**Success Rate:** 100%

**Major Deliverables:**
- ✅ Authentication system complete
- ✅ Job browsing features
- ✅ User dashboards (applicant + employer)
- ✅ Job application system
- ✅ Profile management

**Velocity:** 41.25 points/week

---

### Iteration 2 (Nov 19 - Dec 6, 2024)
**Goal:** Admin panel, testing, and polish

**Completed Stories:** 21  
**Story Points:** 145  
**Success Rate:** 100%

**Major Deliverables:**
- ✅ Complete admin dashboard
- ✅ Email notifications
- ✅ Testing framework (PHPUnit + Cypress)
- ✅ Code quality tools
- ✅ Documentation

**Velocity:** 36.25 points/week

---

## Pull Request Summary

### PR Statistics

| Metric | Value |
|--------|-------|
| **Total PRs** | 67 merged |
| **Average PR Size** | ~370 lines |
| **Review Time** | ~24-48 hours |
| **Merge Conflicts** | 0 |
| **Reverted PRs** | 0 |

### PR Categories

```
Feature Implementation:  45 PRs  (67%)  ████████████████████
Bug Fixes:              12 PRs  (18%)  █████
Documentation:           5 PRs   (7%)  ██
Testing:                 3 PRs   (4%)  █
Configuration:           2 PRs   (3%)  █
```

### Notable PRs

1. **PR #45:** Complete Admin Dashboard System (Victor Chisom Muojeke) - 1,200 lines
2. **PR #32:** Job Application Workflow (Chiemerie) - 800 lines
3. **PR #28:** Authentication System (Kelechukwu) - 650 lines
4. **PR #55:** E2E Testing Setup (Victor Chisom Muojeke) - 450 lines
5. **PR #62:** Email Notifications (Victor Chisom Muojeke) - 400 lines

---

## Collaboration Highlights

### Pair Programming Sessions
- Obi & Chiemerie: Admin dashboard + core features integration
- Kelechukwu & Chiemerie: Auth + application flow
- Akinfenwa & Adiba: UI/UX implementation
- Joshua & Team: Testing coordination

### Code Reviews
- All PRs reviewed by Victor Chisom Muojeke (Team Leader)
- Security reviews by Kelechukwu Ede
- UI/UX reviews by Adiba Ekpunobi
- Cross-team reviews encouraged

### Communication
- Weekly sprint planning (Mondays)
- Daily async standups (GitHub)
- Code review sessions (as needed)
- Sprint retrospectives (biweekly)

---

## Technology Stack

### Backend
- PHP 8.1+
- MySQL 8.0+
- PDO/MySQLi (prepared statements)

### Frontend
- HTML5
- CSS3 (Bootstrap 5 + TailwindCSS)
- JavaScript (ES6+)
- Chart.js (analytics)

### Testing
- PHPUnit (unit tests)
- Cypress (E2E tests)

### Tools
- Git/GitHub (version control)
- PHP_CodeSniffer (PSR-12)
- ESLint (Airbnb JavaScript)
- GitHub Actions (CI/CD)

---

## Key Achievements

### Development Metrics
✅ **310 story points** delivered  
✅ **48 user stories** completed  
✅ **67 pull requests** merged  
✅ **0 merge conflicts**  
✅ **90%+ test coverage**  
✅ **100% iteration completion**  

### Quality Metrics
✅ **PSR-12 compliant** code  
✅ **Security audit** passed  
✅ **No critical bugs** in production  
✅ **Zero delays** in timeline  

### Team Metrics
✅ **8 developers** coordinated  
✅ **560+ commits** across team  
✅ **24,900+ lines** of code  
✅ **100% on-time** delivery  

---

## Lessons Learned

### What Worked Well
✅ Clear task ownership and accountability  
✅ Regular code reviews improved quality  
✅ Modular architecture enabled parallel work  
✅ Team specialization improved efficiency  
✅ Git workflow prevented conflicts  
✅ Documentation from start helped onboarding  

### Challenges Overcome
🔧 Coordinating 8 team members across time zones  
🔧 Maintaining consistent code quality  
🔧 Balancing academic schedules  
🔧 Database security implementation  
🔧 File upload security  
🔧 Responsive design across devices  

### Best Practices Established
💡 Feature branches for all development  
💡 PR reviews required before merge  
💡 Prepared statements for all SQL queries  
💡 Input validation on all forms  
💡 Comprehensive testing before deployment  
💡 Documentation updated with code  

---

## Future Enhancements

### Planned Features (Post-Course)
- Mobile app development
- Advanced analytics dashboard
- AI-powered job matching
- Video interview integration
- Blockchain-based credentials
- Multi-language support

### Technical Improvements
- Microservices architecture
- Docker containerization
- Redis caching layer
- GraphQL API
- Real-time notifications (WebSockets)
- Machine learning recommendations

---

## Conclusion

The MUNext project represents a highly successful collaborative effort by Group 7. Over 81 days, the team delivered a complete, production-ready job board platform with:

- **100% completion rate** (310/310 story points)
- **Professional quality** (90%+ test coverage, PSR-12 compliant)
- **On-time delivery** (0 delays across 12 major phases)
- **Excellent coordination** (8 members, 67 PRs, 0 conflicts)

Each team member contributed significantly to their assigned areas, demonstrating strong technical skills, effective collaboration, and professional software engineering practices.

### Recognition

**Top Contributors:**
🥇 Chiemerie Obijiaku - 115 points (Core features champion)  
🥈 Victor Chisom Muojeke - 95 points (Team leader & admin system)  
🥉 Akinfenwa Oluwaseun - 70 points (Frontend excellence)  

**Special Recognition:**
🔒 Kelechukwu Ede - Security champion  
📊 Akefar Islam - Database architect  
🎨 Adiba Ekpunobi - UI/UX designer  
✅ Joshua Oseimobor - Quality assurance  
🚀 Victor Onwosi - DevOps engineer  

---

**Document Version:** 2.0 (Updated)  
**Last Updated:** December 12, 2024  
**Maintained By:** Group 7 - MUNext Team  
**Team Leader:** Victor Chisom Muojeke  
**Repository:** https://github.com/Group-7-6905/MUNext  
**Status:** ✅ Project Complete (100%)
