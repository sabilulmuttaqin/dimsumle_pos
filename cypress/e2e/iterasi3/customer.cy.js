describe('US-012 - Modul Customer', () => {

  beforeEach(() => {
    cy.login()
    cy.visit('/customers')
  })

  it('Owner tambah data pelanggan dengan data valid', () => {
    cy.contains('button', '+ Tambah Pelanggan').click()
    cy.get('input[x-model="form.name"]').type("budi")
    cy.contains('button', 'Simpan').click()
    cy.contains('Pelanggan berhasil ditambahkan!', { timeout: 15000 }).should('be.visible')
  })

  it('Owner gagal tambah data pelanggan dengan field nama kosong', () => {
    cy.contains('button', '+ Tambah Pelanggan').click()
    cy.contains('button', 'Simpan').click()
    cy.contains('Nama pelanggan wajib diisi', { timeout: 15000 }).should('be.visible')
  })

  it('Owner mengubah data pelanggan', () => {
    cy.get('table tbody tr').first().within(() => {
      cy.get('button').first().click()
    })
    cy.contains('Edit Pelanggan').should('be.visible')
    cy.get('input[x-model="form.name"]').clear().type("Budi baru")
    cy.contains('button', 'Perbarui').click()
    cy.contains('Pelanggan berhasil diperbarui!', { timeout: 15000 }).should('be.visible')
  })

  it('Owner menghapus data pelanggan', () => {
    cy.get('table tbody tr').first().within(() => {
      cy.get('button').eq(1).click()
    })
    cy.contains('Hapus Pelanggan').should('be.visible')
    cy.contains('button', 'Ya, Hapus').click()
    cy.contains('Pelanggan berhasil dihapus!', { timeout: 15000 }).should('be.visible')
  })
  it('Owner dapat melihat frekuesi pembelian pelanggan', () => {
    cy.visit('/report')
    cy.contains('Pembeli Terbanyak', { timeout: 15000 }).should('be.visible')
  })
})
