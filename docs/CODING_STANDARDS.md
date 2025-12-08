# Coding Standards - MUNext Project

## Table of Contents

- [Overview](#overview)
- [General Principles](#general-principles)
- [PHP Standards](#php-standards)
- [JavaScript Standards](#javascript-standards)
- [CSS/SCSS Standards](#cssscss-standards)
- [HTML Standards](#html-standards)
- [Database Standards](#database-standards)
- [Git Commit Standards](#git-commit-standards)
- [File Organization](#file-organization)
- [Documentation Standards](#documentation-standards)
- [Security Standards](#security-standards)
- [Testing Standards](#testing-standards)
- [Code Review Checklist](#code-review-checklist)

---

## Overview

This document outlines the coding standards and naming conventions for the MUNext project. All team members must adhere to these guidelines to ensure code consistency, maintainability, and quality.

**Last Updated:** December 2024  
**Team:** Group 7  
**Project:** MUNext Job Board Platform

---

## General Principles

### Core Values

1. **Readability over Cleverness** - Write code that is easy to understand
2. **Consistency** - Follow established patterns throughout the codebase
3. **Security First** - Always consider security implications
4. **DRY (Don't Repeat Yourself)** - Avoid code duplication
5. **KISS (Keep It Simple, Stupid)** - Favor simple solutions over complex ones
6. **YAGNI (You Aren't Gonna Need It)** - Don't build features you don't need yet

### Code Quality Metrics

- **Lines per Function:** Max 50 lines (ideally 20-30)
- **Function Parameters:** Max 5 parameters (use objects for more)
- **Nesting Depth:** Max 4 levels
- **Cyclomatic Complexity:** Max 10
- **File Length:** Max 500 lines (split larger files)

---

## PHP Standards

### PSR-12 Extended Coding Style

MUNext follows [PSR-12](https://www.php-fig.org/psr/psr-12/) with additional project-specific conventions.

### Naming Conventions

#### Classes

```php
// PascalCase for class names
class JobApplication {}
class UserProfile {}
class EmailNotification {}

// Abstract classes prefixed with "Abstract"
abstract class AbstractModel {}

// Interfaces suffixed with "Interface"
interface AuthenticationInterface {}

// Traits suffixed with "Trait"
trait ValidationTrait {}
```

#### Methods and Functions

```php
// camelCase for methods and functions
public function getUserById($id) {}
public function validateEmailAddress($email) {}
public function sendNotificationEmail($user) {}

// Boolean methods prefixed with is/has/can/should
public function isAuthenticated() {}
public function hasPermission() {}
public function canEditJob() {}
public function shouldSendEmail() {}

// Getters and setters
public function getUsername() {}
public function setUsername($username) {}
```

#### Variables

```php
// snake_case for variables
$user_id = 123;
$job_title = "Software Developer";
$application_date = date('Y-m-d');
$is_active = true;

// Descriptive names, avoid abbreviations
// Good
$user_email_address = "user@example.com";
$maximum_file_size = 5242880;

// Bad
$usr_eml = "user@example.com";
$max_sz = 5242880;
```

#### Constants

```php
// UPPER_SNAKE_CASE for constants
define('MAX_LOGIN_ATTEMPTS', 5);
define('SESSION_TIMEOUT', 3600);
define('UPLOAD_DIR', '/uploads/');

// Class constants
class Config {
    const DB_HOST = 'localhost';
    const DB_NAME = 'munext';
    const DB_CHARSET = 'utf8mb4';
}
```

### File Naming

```
browse-jobs.php          // kebab-case for page files
job-detail.php
apply-job.php

UserModel.php            // PascalCase for class files
JobController.php
AuthenticationService.php

helper.php               // lowercase for utility files
config.php
database.php
```

---

## JavaScript Standards

### Airbnb JavaScript Style Guide

MUNext follows the [Airbnb JavaScript Style Guide](https://github.com/airbnb/javascript) with ESLint enforcement.

### Naming Conventions

```javascript
// camelCase for variables and functions
const userName = 'John Doe';
const jobTitle = 'Software Developer';

function getUserById(id) {}
function validateEmailAddress(email) {}

// Boolean variables prefixed with is/has/can/should
const isAuthenticated = true;
const hasPermission = false;
const canEdit = checkPermissions();

// UPPER_SNAKE_CASE for constants
const MAX_FILE_SIZE = 5242880;
const API_ENDPOINT = '/api/v1';

// PascalCase for classes
class JobApplication {
    constructor(data) {
        this.jobId = data.jobId;
    }
}
```

---

## CSS/SCSS Standards

### BEM Methodology

MUNext uses the [BEM (Block Element Modifier)](http://getbem.com/) naming convention.

```css
/* Block: Standalone component */
.job-card {}

/* Element: Part of a block */
.job-card__title {}
.job-card__description {}

/* Modifier: Variation of a block or element */
.job-card--featured {}
.button--primary {}

/* Utility classes (Tailwind-style) */
.text-center {}
.flex {}
.mt-4 {}
```

---

## Database Standards

### Table Naming

```sql
-- Plural, snake_case
users
jobs
job_applications
companies
job_categories
```

### Column Naming

```sql
-- snake_case for all columns
user_id
username
email_address
created_at
updated_at
is_active
```

### Primary Keys

```sql
-- Always use 'id' as primary key
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL
);
```

### Foreign Keys

```sql
-- Use {table}_id format
CREATE TABLE job_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    job_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## Git Commit Standards

### Conventional Commits

```
<type>(<scope>): <subject>

Types:
feat:     New feature
fix:      Bug fix
docs:     Documentation
style:    Code style
refactor: Code refactoring
test:     Testing
chore:    Maintenance

Examples:
feat(auth): add password reset functionality
fix(jobs): resolve pagination issue
docs(readme): update installation guide
```

### Branch Naming

```
feature/user-authentication
bugfix/login-redirect-issue
hotfix/security-patch
docs/api-documentation
```

---

## Security Standards

```php
// ALWAYS use prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);

// ALWAYS validate input
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

// ALWAYS escape output
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// ALWAYS hash passwords
$hash = password_hash($password, PASSWORD_BCRYPT);
```

---

## Testing Standards

### Test File Naming

```
tests/Unit/UserModelTest.php
tests/Integration/AuthFlowTest.php
cypress/e2e/login.cy.js
```

### Test Method Naming

```php
test_getUserById_WithValidId_ReturnsUser()
test_createUser_WithDuplicateEmail_ThrowsException()
```

---

## Code Review Checklist

### Before Submitting PR

- [ ] Code follows PSR-12 (PHP) / Airbnb (JS) standards
- [ ] All linters pass
- [ ] All tests pass
- [ ] Documentation updated
- [ ] Security reviewed
- [ ] No console.log or var_dump
- [ ] Commit messages follow conventions

---

## Resources

- [PSR-12 Standard](https://www.php-fig.org/psr/psr-12/)
- [Airbnb JavaScript Guide](https://github.com/airbnb/javascript)
- [BEM Methodology](http://getbem.com/)
- [Conventional Commits](https://www.conventionalcommits.org/)

---

**Maintained by:** Group 7 - MUNext Team  
**Last Updated:** December 2024  
**Repository:** https://github.com/Group-7-6905/MUNext
