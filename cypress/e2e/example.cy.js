describe("MUNext - Basic Tests", () => {
  beforeEach(() => {
    // Visit homepage before each test
    cy.visit("/munext");
  });

  it("should load the homepage", () => {
    cy.contains("MUNext").should("be.visible");
    cy.title().should("include", "MUNext");
  });

  it("should have a navigation menu", () => {
    cy.get("nav").should("be.visible");
    cy.contains("Home").should("be.visible");
    cy.contains("Jobs").should("be.visible");
  });

  //login modal test
  it("should open login modal", () => {
    cy.get('a[role="login"]').click({ force: true });
    // the modal uses Bootstrap classes like "modal fade show" when visible; assert it's shown and visible
    cy.get("#login").should("have.class", "show").and("be.visible");
  });

  it("should navigate to jobs page", () => {
    cy.contains("Jobs").click();
    cy.url().should("include", "browse-jobs.php");
  });

  it("should have a search functionality", () => {
    cy.get('input[name="search"]').should("exist");
    cy.get('button[type="submit"]').should("exist");
  });
});
