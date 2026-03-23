describe("US-007 - Laporan harian", () => {
    beforeEach(() => {
        cy.login();
        cy.visit("/dashboard");
    });

    it("Sistem menampilkan laporan penjualan harian pada dashboard", () => {
        cy.contains("Transaksi Hari Ini").should("be.visible");
        cy.contains("Pendapatan Hari Ini").should("be.visible");
    });

    it("Dashboard menampilkan data laporan penjualan harian sesuai filter kasir", () => {
        cy.get('select[name="kasir_id"]').select("Kasir 1");
        cy.get("#transaction-list").within(() => {
            cy.contains("Kasir 1").should("be.visible");
        });
    });
});
