Cypress.Commands.add(
    "login",
    (email = "owner@gmail.com", password = "owner123") => {
        cy.visit("/");
        cy.get("#email").clear().type(email);
        cy.get("#password").clear().type(password);
        cy.get('button[type="submit"]').click();
        cy.url().should("include", "/dashboard", { timeout: 15000 });
    },
);

Cypress.Commands.add(
    "loginKasir",
    (email = "kasir@gmail.com", password = "akunkasir") => {
        cy.visit("/");
        cy.get("#email").clear().type(email);
        cy.get("#password").clear().type(password);
        cy.get('button[type="submit"]').click();
        cy.url().should("include", "/pos", { timeout: 15000 });
    },
);
