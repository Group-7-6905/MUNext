const { defineConfig } = require("cypress");

module.exports = defineConfig({
  e2e: {
    baseUrl: "http://localhost/MUNext-TEST",
    viewportWidth: 1280,
    viewportHeight: 720,
    video: false,
    screenshotOnRunFailure: true,
    videosFolder: "cypress/videos",
    screenshotsFolder: "cypress/screenshots",

    // Support file for E2E tests
    supportFile: "cypress/support/e2e.js",
    specPattern: "cypress/e2e/**/*.cy.{js,jsx,ts,tsx}",

    // Timeouts
    defaultCommandTimeout: 10000,
    pageLoadTimeout: 30000,
    requestTimeout: 10000,
    responseTimeout: 30000,

    // Retries
    retries: {
      runMode: 2,
      openMode: 0,
    },

    setupNodeEvents(on, config) {
      // implement node event listeners here
      on("task", {
        log(message) {
          console.log(message);
          return null;
        },
      });
    },

    env: {
      apiUrl: "http://localhost/MUNext-TEST",
      testUser: "demo",
      testPassword: "demo123",
    },
  },

  // Disable component testing (not needed for PHP projects)
  component: {
    supportFile: false,
  },
});
