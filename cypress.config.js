const { defineConfig } = require("cypress");

module.exports = defineConfig({
    e2e: {
        baseUrl: "http://127.0.0.1:8000",
        supportFile: "cypress/support/e2e.js",
        viewportWidth: 1280,
        viewportHeight: 720,
        defaultCommandTimeout: 10000,
    },
});
