// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************

// -- Custom Commands for MUNext --

/**
 * Login command
 * Usage: cy.login('username', 'password')
 */
Cypress.Commands.add("login", (username, password) => {
  cy.visit("/login.php");
  cy.get('input[name="username"]').type(username);
  cy.get('input[name="password"]').type(password);
  cy.get('button[type="submit"]').click();

  // Wait for redirect to dashboard
  cy.url().should("include", "dashboard");
});

/**
 * Login as applicant
 * Usage: cy.loginAsApplicant()
 */
Cypress.Commands.add("loginAsApplicant", () => {
  cy.login(
    Cypress.env("testUser") || "demo",
    Cypress.env("testPassword") || "demo123"
  );
});

/**
 * Login as employer
 * Usage: cy.loginAsEmployer()
 */
Cypress.Commands.add("loginAsEmployer", () => {
  cy.login(
    Cypress.env("employerUser") || "employer",
    Cypress.env("employerPassword") || "employer123"
  );
});

/**
 * Logout command
 * Usage: cy.logout()
 */
Cypress.Commands.add("logout", () => {
  cy.visit("/logout.php");
  cy.url().should("include", "index.php");
});

/**
 * Apply to a job
 * Usage: cy.applyToJob(jobId)
 */
Cypress.Commands.add("applyToJob", (jobId) => {
  cy.visit(`/apply-job.php?jobid=${jobId}`);
  cy.get('textarea[name="cover_letter"]').type(
    "This is my cover letter for this position."
  );

  // Upload resume (if file input exists)
  const fileName = "sample-resume.pdf";
  cy.get('input[type="file"]').attachFile(fileName);

  cy.get('button[type="submit"]').click();

  // Verify application submitted
  cy.contains("Application submitted successfully").should("be.visible");
});

/**
 * Visit a page and wait for it to load
 * Usage: cy.visitAndWait('/jobs')
 */
Cypress.Commands.add("visitAndWait", (url) => {
  cy.visit(url);
  cy.get("body").should("be.visible");
});

/**
 * Check if logged in
 * Usage: cy.isLoggedIn()
 */
Cypress.Commands.add("isLoggedIn", () => {
  cy.getCookie("PHPSESSID").should("exist");
});

/**
 * Check if logged out
 * Usage: cy.isLoggedOut()
 */
Cypress.Commands.add("isLoggedOut", () => {
  cy.getCookie("PHPSESSID").should("not.exist");
});

/**
 * Fill job search form
 * Usage: cy.searchJobs('Software Engineer', 'St. Johns')
 */
Cypress.Commands.add("searchJobs", (keyword, location = "") => {
  if (keyword) {
    cy.get('input[name="keyword"]').clear().type(keyword);
  }
  if (location) {
    cy.get('input[name="location"]').clear().type(location);
  }
  cy.get('button[type="submit"]').contains("Search").click();
});

/**
 * Select a filter option
 * Usage: cy.selectFilter('category', 'IT')
 */
Cypress.Commands.add("selectFilter", (filterType, value) => {
  cy.get(`select[name="${filterType}"]`).select(value);
});

// -- Overwrite existing commands (optional) --

/**
 * Override type command to add small delay for better stability
 */
Cypress.Commands.overwrite("type", (originalFn, element, text, options) => {
  return originalFn(element, text, { ...options, delay: 10 });
});
