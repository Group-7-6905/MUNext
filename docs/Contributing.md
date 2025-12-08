# Contributing to MUNext

Thank you for contributing to MUNext! This document provides guidelines for contributing to the project.

---

## Table of Contents

1. [Getting Started](#getting-started)
2. [Development Workflow](#development-workflow)
3. [Coding Standards](#coding-standards)
4. [Commit Guidelines](#commit-guidelines)
5. [Pull Request Process](#pull-request-process)
6. [Code Review](#code-review)
7. [Testing](#testing)

---

## Getting Started

### Prerequisites

Before contributing, ensure you have:

- PHP 7.4+ installed
- MySQL 5.7+ installed
- Git installed
- XAMPP/WAMP/MAMP or similar environment
- Code editor (VS Code recommended)

### Setting Up Development Environment

1. **Clone the repository**

   ```bash
   git clone https://github.com/Group-7-6905/MUNext.git
   cd MUNext
   ```

2. **Set up database**

   ```bash
   mysql -u root -p
   CREATE DATABASE erisdb;
   USE erisdb;
   SOURCE db/erisdb.sql;
   ```

3. **Configure database connection**

   - Copy `mysqli_connect.php` template
   - Update database credentials
   - Never commit actual credentials

4. **Start development server**

   ```bash
   # Option 1: Using XAMPP
   # Place project in htdocs and access via http://localhost/MUNext

   # Option 2: PHP built-in server
   php -S localhost:8000
   ```

---

## Development Workflow

### Branching Strategy

We follow a simplified Git workflow:

```
main (production)
  ↑
  └── feature/feature-name
  └── fix/bug-description
  └── docs/documentation-update
```

### Branch Naming Conventions

- **Feature branches:** `feature/job-application-form`
- **Bug fixes:** `fix/login-validation-error`
- **Documentation:** `docs/update-readme`
- **Hotfixes:** `hotfix/security-patch`

### Creating a New Branch

```bash
# Update main branch
git checkout main
git pull origin main

# Create new feature branch
git checkout -b feature/your-feature-name

# Work on your feature
# Make commits

# Push your branch
git push origin feature/your-feature-name
```

---

## Coding Standards

### PHP Coding Standards

#### File Structure

```php
<?php
/**
 * File description
 *
 * Purpose of this file
 *
 * @author Your Name
 * @date YYYY-MM-DD
 */

// Include dependencies
require_once('mysqli_connect.php');
require_once('session_check.php');

// Constants
define('MAX_UPLOAD_SIZE', 5242880);

// Main code
// ...
?>
```

#### Naming Conventions

**Files:**

- Use lowercase with hyphens: `browse-jobs.php`, `job-detail.php`
- Be descriptive: `notification-mail.php` not `nm.php`

**Variables:**

- Use snake_case: `$user_id`, `$job_title`
- Be descriptive: `$employer_name` not `$en`
- Avoid single letters except in loops

```php
// Good
$user_email = "user@example.com";
$job_applications = [];

// Avoid
$ue = "user@example.com";
$ja = [];
```

**Functions:**

- Use camelCase: `getUserById()`, `createJobPosting()`
- Verb + noun: `getApplications()`, `validateInput()`

```php
// Good
function getJobById($job_id) {
    // ...
}

// Avoid
function job($id) {
    // ...
}
```

**Constants:**

- Use UPPER_CASE: `MAX_FILE_SIZE`, `DB_HOST`

```php
define('UPLOAD_PATH', 'resumes_cv/');
define('SESSION_TIMEOUT', 3600);
```

#### Code Formatting

**Indentation:**

- Use 4 spaces (not tabs)
- Consistent indentation throughout

**Braces:**

```php
// Control structures - opening brace on same line
if ($condition) {
    // code
} else {
    // code
}

// Functions - opening brace on same line
function doSomething() {
    // code
}
```

**Spacing:**

```php
// Good spacing
$result = $value1 + $value2;
if ($x == $y) {
    echo "Equal";
}

// Avoid
$result=$value1+$value2;
if($x==$y){echo "Equal";}
```

#### Comments

```php
// Single-line comment for brief explanations

/*
 * Multi-line comment for longer explanations
 * that span multiple lines
 */

/**
 * Function documentation
 *
 * @param int $user_id The user ID
 * @param string $email The user email
 * @return bool Success status
 */
function updateUser($user_id, $email) {
    // Implementation
}
```

### HTML/CSS Standards

**HTML:**

```html
<!-- Use semantic HTML5 -->
<header>
  <nav>...</nav>
</header>

<main>
  <section>...</section>
</main>

<footer>...</footer>

<!-- Proper indentation -->
<div class="container">
  <div class="row">
    <div class="col-md-6">Content</div>
  </div>
</div>
```

**CSS:**

```css
/* Use meaningful class names */
.job-listing-card {
  padding: 20px;
  margin-bottom: 15px;
}

/* Avoid generic names */
.box1 {
  /* Avoid */
}
```

### JavaScript Standards

```javascript
// Use camelCase for variables and functions
const jobTitle = "Software Developer";
const applicationCount = 0;

function submitApplication() {
  // Implementation
}

// Use const/let, avoid var
const API_URL = "https://api.example.com";
let isValid = false;

// Avoid var
var x = 5; // Don't use
```

---

## Commit Guidelines

### Commit Message Format

We follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>: <description>

[optional body]

[optional footer]
```

### Commit Types

- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation changes
- **style**: Code formatting (no functional changes)
- **refactor**: Code restructuring
- **test**: Adding or updating tests
- **chore**: Maintenance tasks

### Examples

```bash
# Feature
git commit -m "feat: add job application form with resume upload"

# Bug fix
git commit -m "fix: resolve SQL injection vulnerability in login"

# Documentation
git commit -m "docs: update README with installation instructions"

# Style
git commit -m "style: format code according to PSR standards"

# Refactor
git commit -m "refactor: extract database functions into helper file"
```

### Writing Good Commit Messages

**Do:**

- Use present tense ("add feature" not "added feature")
- Be specific and descriptive
- Reference issue numbers when applicable
- Keep subject line under 50 characters
- Add body for complex changes

```bash
# Good
git commit -m "feat: add email notification for job applications"

# Better with body
git commit -m "feat: add email notification for job applications

- Send email to employer when application submitted
- Include applicant name and job title
- Use template from notification-mail.php"
```

**Don't:**

```bash
# Too vague
git commit -m "update file"

# Not descriptive
git commit -m "fix bug"

# Multiple changes in one commit
git commit -m "add feature and fix bug and update docs"
```

---

## Pull Request Process

### Before Creating a Pull Request

1. **Update your branch with main**

   ```bash
   git checkout main
   git pull origin main
   git checkout your-feature-branch
   git merge main
   # Resolve any conflicts
   ```

2. **Test your changes**

   - Manually test all modified features
   - Check for PHP errors
   - Verify database interactions
   - Test on multiple browsers (if UI changes)

3. **Review your changes**
   ```bash
   git diff main
   ```

### Creating a Pull Request

1. **Push your branch**

   ```bash
   git push origin feature/your-feature-name
   ```

2. **Open PR on GitHub**
   - Go to repository on GitHub
   - Click "New Pull Request"
   - Select your branch
   - Fill in the template

### Pull Request Template

```markdown
## Description

Brief description of what this PR does

## Type of Change

- [ ] New feature
- [ ] Bug fix
- [ ] Documentation update
- [ ] Code refactoring

## Changes Made

- List specific changes
- What was added/modified/removed

## Testing Done

- How was this tested?
- What scenarios were covered?

## Screenshots (if applicable)

Add screenshots for UI changes

## Checklist

- [ ] Code follows project style guidelines
- [ ] Comments added for complex code
- [ ] Documentation updated (if needed)
- [ ] No console errors
- [ ] Tested on multiple browsers (if UI)
- [ ] Database changes documented
```

### Pull Request Guidelines

- **One feature per PR**: Don't mix multiple features
- **Keep it small**: Easier to review (< 500 lines if possible)
- **Descriptive title**: Clear and concise
- **Add reviewers**: Tag team lead and relevant developers
- **Link issues**: Reference related issues with `#issue-number`

---

## Code Review

### For Reviewers

**What to Check:**

1. **Functionality**

   - Does it work as intended?
   - Are edge cases handled?
   - No breaking changes?

2. **Code Quality**

   - Follows coding standards?
   - Proper naming conventions?
   - Adequate comments?
   - No code duplication?

3. **Security**

   - SQL injection prevention (prepared statements)?
   - XSS prevention (input sanitization)?
   - CSRF protection where needed?
   - Proper authentication/authorization?

4. **Performance**
   - Efficient database queries?
   - No unnecessary loops?
   - Proper indexing used?

### Review Comments

```markdown
# Constructive feedback

Consider using prepared statements here to prevent SQL injection:
\`\`\`php
$stmt = $con->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
\`\`\`

# Positive feedback

Great job implementing proper error handling here! 👍

# Questions

Could you explain the reasoning behind this approach?
```

### Responding to Feedback

- Be open to suggestions
- Ask questions if unclear
- Make requested changes
- Mark conversations as resolved
- Thank reviewers

---

## Testing

### Manual Testing Checklist

Before submitting a PR, test:

**Authentication:**

- [ ] Login with valid credentials
- [ ] Login with invalid credentials
- [ ] Registration with all fields
- [ ] Registration with missing fields
- [ ] Logout functionality

**Job Browsing:**

- [ ] View all jobs
- [ ] Search with keywords
- [ ] Filter by category
- [ ] View job details
- [ ] Browse employers

**Job Application:**

- [ ] Apply with resume
- [ ] Apply without resume (if optional)
- [ ] View application status
- [ ] Receive confirmation

**File Uploads:**

- [ ] Upload valid file
- [ ] Upload invalid file type
- [ ] Upload oversized file
- [ ] View uploaded file

**Cross-Browser Testing:**

- [ ] Chrome
- [ ] Firefox
- [ ] Safari (if available)
- [ ] Edge

### Security Testing

**Always test for:**

- SQL injection attempts
- XSS vulnerabilities
- CSRF attacks
- Session hijacking
- File upload exploits
- Authorization bypasses

### Test Data

Use the demo accounts:

- **Applicant:** demo / demo123
- **Admin:** admin / 1111

Or create test data in your local database.

---

## Security Best Practices

### Database Security

**Always use prepared statements:**

```php
// Good - Prepared statement
$stmt = $con->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();

// Bad - Direct concatenation
$query = "SELECT * FROM jobs WHERE id = " . $job_id; // NEVER DO THIS
```

### Input Validation

```php
// Validate and sanitize all inputs
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');

// Check for required fields
if (empty($email) || empty($username)) {
    die("Required fields missing");
}
```

### File Uploads

```php
// Validate file type
$allowed_types = ['pdf', 'doc', 'docx'];
$file_extension = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);

if (!in_array($file_extension, $allowed_types)) {
    die("Invalid file type");
}

// Check file size
if ($_FILES['resume']['size'] > MAX_FILE_SIZE) {
    die("File too large");
}

// Generate unique filename
$new_filename = uniqid() . '.' . $file_extension;
```

### Session Security

```php
// Start session securely
session_start();

// Regenerate session ID on login
session_regenerate_id(true);

// Check session validity
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
```

---

## Documentation

### Code Documentation

Document:

- Complex functions
- Business logic
- Security measures
- API endpoints (if any)
- Database schema changes

---

## License

By contributing to MUNext, you agree that your contributions will be part of the project under the same license.

---

**Thank you for contributing to MUNext!**

_Last Updated: November 2025_
