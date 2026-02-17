import "./commands";

Cypress.on("uncaught:exception", (err) => {
    if (err.message.includes("Script error")) {
        return false;
    }
});
