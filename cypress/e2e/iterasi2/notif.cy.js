describe("Notifikasi Stok Rendah", () => {
    beforeEach(() => {
        cy.login();
        cy.visit("/products");
    });

    it("Sistem menampilkan notifikasi jumlah produk stok kurang dari 30", () => {
        cy.get("table tbody tr")
            .first()
            .within(() => {
                cy.contains("button", "Edit").click();
            });
        cy.contains("Edit Produk").should("be.visible");
        cy.get('input[x-model="form.stock"]').clear().type("9");
        cy.contains("button", "Update").click();
        cy.visit("/dashboard");
        cy.get(".bg-amber-50").should("be.visible");
        cy.contains("Stok Rendah").should("be.visible");
    });
    it("Sistem tidak menampilkan notifikasi stok rendah jika stok mencukupi", () => {
        cy.get("table tbody tr")
            .first()
            .within(() => {
                cy.contains("button", "Edit").click();
            });
        cy.contains("Edit Produk").should("be.visible");
        cy.get('input[x-model="form.stock"]').clear().type("50");
        cy.contains("button", "Update").click();
        cy.visit("/dashboard");
        cy.contains("Stok Rendah").should("not.exist");
    });
});
