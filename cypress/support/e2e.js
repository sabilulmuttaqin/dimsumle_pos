import "./commands";

Cypress.on("uncaught:exception", (err) => {
    if (err instanceof TypeError) {
        return false;
    }
    if (err.message.includes("Script error")) {
        return false;
    }
    if (err.message.includes("Cannot read properties of null")) {
        return false;
    }
});
