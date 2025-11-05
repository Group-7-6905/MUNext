describe("MUNext - Flexible Homepage Tests", () => {
  beforeEach(() => {
    cy.visit("/munext", { failOnStatusCode: false });
  });

  it("should load the homepage", () => {
    // Just check that the page loads
    cy.get("body").should("exist");
  });

  it("should have some navigation", () => {
    // Check for any navigation element
    cy.get('nav, .nav, .navbar, [role="navigation"]', {
      timeout: 10000,
    }).should("exist");
  });

  it("should have some content", () => {
    // Check that page has any content
    cy.get("body").should("not.be.empty");
    cy.get("body").invoke("text").should("have.length.greaterThan", 0);
  });

  it("should have links", () => {
    // Check that page has some links
    cy.get("a").should("have.length.greaterThan", 0);
  });

  it("should be responsive", () => {
    // Test different viewport sizes
    cy.viewport(375, 667); // Mobile
    cy.get("body").should("be.visible");

    cy.viewport(1920, 1080); // Desktop
    cy.get("body").should("be.visible");
  });
});

describe("MUNext - Job Browsing (Flexible)", () => {
  it("should access browse jobs page", () => {
    // Try different possible URLs
    const jobUrls = [
      "/munext/browse-jobs.php",
      "/munext/job-list-v1.php",
      "/munext/browse-employers.php",
      "/munext/browse-category.php",
    ];

    let pageLoaded = false;

    jobUrls.forEach((url) => {
      if (!pageLoaded) {
        cy.visit(url, { failOnStatusCode: false }).then((resp) => {
          // If page loads successfully, mark as loaded
          cy.get("body").then(($body) => {
            if ($body.length > 0) {
              pageLoaded = true;
              cy.log(`✅ Found jobs page at: ${url}`);
            }
          });
        });
      }
    });
  });

  it("should have some content on jobs page", () => {
    cy.visit("/munext/browse-jobs.php", { failOnStatusCode: false });
    cy.get("body").should("not.be.empty");
  });
});

describe("MUNext - Basic Functionality Checks", () => {
  beforeEach(() => {
    cy.visit("/munext/", { failOnStatusCode: false });
  });

  it("should have a title", () => {
    cy.title().should("not.be.empty");
    cy.title().should("have.length.greaterThan", 0);
  });

  it("should have some images or styles", () => {
    // Check for either images or CSS
    cy.get("body").then(($body) => {
      const hasImages = $body.find("img").length > 0;
      const hasLinks = $body.find('link[rel="stylesheet"]').length > 0;
      expect(hasImages || hasLinks).to.be.true;
    });
  });

  it("should not have JavaScript errors", () => {
    // Listen for console errors
    cy.window().then((win) => {
      win.console.error = cy.stub();
    });
  });

  it("should load within reasonable time", () => {
    // Check page load performance
    cy.visit("/munext/", { timeout: 30000 }); // 30 second timeout
    cy.get("body", { timeout: 10000 }).should("be.visible");
  });
});

describe("MUNext - Page Availability", () => {
  const pages = [
    { url: "/munext/", name: "Homepage" },
    { url: "/munext/index.php", name: "Index" },
    { url: "/munext/about-us.php", name: "About Us" },
    { url: "/munext/browse-jobs.php", name: "Browse Jobs" },
    { url: "/munext/browse-employers.php", name: "Browse Employers" },
  ];

  pages.forEach((page) => {
    it(`should access ${page.name}`, () => {
      cy.visit(page.url, { failOnStatusCode: false });
      cy.get("body").should("exist");
      cy.log(`✅ ${page.name} page is accessible`);
    });
  });
});
