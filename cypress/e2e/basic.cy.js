describe("Homepage", () => {
  it("should load successfully", () => {
    cy.visit("/munext/");
    cy.contains("MUNext").should("be.visible");
  });

  it("should display login link", () => {
    cy.visit("/munext/login.php");
    cy.contains("Login").should("be.visible");
  });
});
