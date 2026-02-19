describe("Modul Manajemen User", () => {
    beforeEach(() => {
        cy.login();
        cy.visit("/users");
    });

    it("Owner dapat tambah akun kasir dengan data valid", () => {
        cy.contains("button", "Tambah User").click();
        cy.get('input[x-model="form.name"]').type("Budi");
        cy.get('input[x-model="form.email"]').type("Budi@gmail.com");
        cy.get('select[x-model="form.role"]').select("kasir");
        cy.get('input[x-model="form.password"]').type("Rahasia");
        cy.contains("button", "Simpan").click();
        cy.contains("User berhasil ditambahkan", { timeout: 15000 }).should(
            "be.visible",
        );
    });
    it("Owner gagal tambah akun kasir dengan data email sudah ada", () => {
        cy.contains("button", "Tambah User").click();
        cy.get('input[x-model="form.name"]').type("Budi");
        cy.get('input[x-model="form.email"]').type("Budi@gmail.com");
        cy.get('select[x-model="form.role"]').select("kasir");
        cy.get('input[x-model="form.password"]').type("Rahasia");
        cy.contains("button", "Simpan").click();
        cy.contains("Email sudah terdaftar", { timeout: 15000 }).should(
            "be.visible",
        );
    });
    it("Owner gagal tambah akun kasir dengan email kosong", () => {
        cy.contains("button", "Tambah User").click();
        cy.get('input[x-model="form.name"]').type("Budi");
        cy.get('select[x-model="form.role"]').select("kasir");
        cy.get('input[x-model="form.password"]').type("Rahasia");
        cy.contains("button", "Simpan").click();
        cy.contains("Email harus diisi", { timeout: 15000 }).should(
            "be.visible",
        );
    });
    it("Owner gagal tambah akun kasir dengan password kurang dari 6 karakter", () => {
        cy.contains("button", "Tambah User").click();
        cy.get('input[x-model="form.name"]').type("Budi");
        cy.get('input[x-model="form.email"]').type("budi@gmail.com");
        cy.get('select[x-model="form.role"]').select("kasir");
        cy.get('input[x-model="form.password"]').type("123");
        cy.contains("button", "Simpan").click();
        cy.contains("Password minimal 6 karakter", { timeout: 15000 }).should(
            "be.visible",
        );
    });

    it("Owner mengubah akun kasir dengan data valid", () => {
        cy.get("table tbody tr")
            .first()
            .within(() => {
                cy.contains("button", "Edit").click();
            });
        cy.contains("Edit User").should("be.visible");
        cy.get('input[x-model="form.name"]').clear().type("Budi baru");
        cy.contains("button", "Update").click();
        cy.contains("User berhasil diupdate", { timeout: 15000 }).should(
            "be.visible",
        );
    });

    it("Owner menghapus akun kasir ", () => {
        cy.get("table tbody tr").contains("button", "Hapus").first().click();
        cy.contains("button", "Ya, Hapus").click();
        cy.contains("User berhasil dihapus", { timeout: 15000 }).should(
            "be.visible",
        );
    });
});
