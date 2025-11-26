// ***********************************************************
// This example support/e2e.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import commands.js using ES2015 syntax:
import "./commands";

// Alternatively you can use CommonJS syntax:
// require('./commands')

// Hide fetch/XHR requests in command log (optional - makes tests cleaner)
const app = window.top;
if (!app.document.head.querySelector("[data-hide-command-log-request]")) {
  const style = app.document.createElement("style");
  style.innerHTML =
    ".command-name-request, .command-name-xhr { display: none }";
  style.setAttribute("data-hide-command-log-request", "");
  app.document.head.appendChild(style);
}

// Example: Add custom error handling
Cypress.on("uncaught:exception", (err, runnable) => {
  // Returning false here prevents Cypress from failing the test
  // You can customize this to handle specific errors

  // Ignore specific errors (example)
  if (err.message.includes("ResizeObserver")) {
    return false;
  }

  // Let other errors fail the test
  return true;
});
