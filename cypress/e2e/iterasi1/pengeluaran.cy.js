describe('US-008 - Modul Pengeluaran', () => {
    beforeEach(() => {
        cy.login();
        cy.visit('/expenses');
    });

    it('Owner tambah pengeluaran dengan data valid', () => {
        cy.contains('Tambah Pengeluaran').click();
        cy.get('input[x-model="form.amount"]').type('20000');
        cy.get('textarea[x-model="form.description"]').type('beli ayam');
        cy.contains('button', 'Simpan').click();
        cy.contains("Pengeluaran berhasil ditambahkan", { timeout: 15000 }).should('be.visible');
    });
    it('Owner gagal tambah pengeluaran jika jumlah negatif', () => {
        cy.contains('Tambah Pengeluaran').click();
        cy.get('input[x-model="form.amount"]').type('-50000');
        cy.contains('button', 'Simpan').click();
        cy.contains("Jumlah tidak boleh negatif", { timeout: 15000 }).should('be.visible');
    });
    it('Owner gagal tambah pengeluaran jika deskripsi kosong', () => {
        cy.contains('Tambah Pengeluaran').click();
        cy.get('input[x-model="form.amount"]').type('50000');
        cy.contains('button', 'Simpan').click();
        cy.contains("Deskripsi wajib diisi", { timeout: 15000 }).should('be.visible');
    });

    it('Owner dapat memfilter pengeluaran berdasarkan tanggal valid', () => {
        cy.get('input[name="date_from"]').type('2026-02-28');
        cy.get('input[name="date_to"]').type('2026-03-01');
        cy.contains('button', 'Filter').click();

        cy.get('table tbody').should('be.visible');
        cy.contains('10.000').should('be.visible');
    });

    it('Owner gagal memfilter pengeluaran jika rentang tanggal tidak valid', () => {
        cy.get('input[name="date_from"]').type('2026-03-01');
        cy.get('input[name="date_to"]').type('2026-02-28');
        cy.contains('button', 'Filter').click();

        cy.contains('Tanggal "Dari" tidak boleh lebih besar dari Tanggal "Sampai".', { timeout: 15000 }).should('be.visible');
    });
    it('Owner mengubah data pengeluaran dengan data valid', () => {
        cy.get('table tbody tr').first().within(() => {
            cy.get('button').first().click();
        });
        cy.contains('Edit Pengeluaran').should('be.visible');
        cy.get('input[x-model="form.amount"]').clear().type('60000');
        cy.contains('button', 'Perbarui').click();
        cy.contains("Pengeluaran berhasil diperbarui", { timeout: 15000 }).should('be.visible');
    });

    it('Owner menghapus pengeluaran', () => {
        cy.get('table tbody tr').first().within(() => {
            cy.get('button').last().click();
        });
        cy.contains('button', 'Ya, Hapus').click();
        cy.contains('Pengeluaran berhasil dihapus', { timeout: 15000 }).should('be.visible');
    });
});