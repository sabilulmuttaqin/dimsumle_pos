describe('Modul History Transaksi (US-002)', () => {

  beforeEach(() => {
    cy.loginKasir()
    cy.visit('/history')
  })

  it('Kasir dapat mengakses halaman history transaksi', () => {
    cy.contains('Riwayat Transaksi').should('be.visible')
    cy.get('table tbody tr').should('have.length.greaterThan', 0)
    cy.contains('th', 'Invoice').should('be.visible')
    cy.contains('th', 'Tanggal').should('be.visible')
    cy.contains('th', 'Total').should('be.visible')
  })

  it('Kasir dapat melihat detail transaksi', () => {
    cy.get('table tbody tr').first().within(() => {
      cy.contains('button', 'Detail').click()
    })
    cy.contains('Detail Transaksi').should('be.visible')
    cy.contains('Invoice').should('be.visible')
    cy.contains('Item Produk').should('be.visible')
    cy.contains('Subtotal').should('be.visible')
    cy.contains('Total').should('be.visible')
    cy.contains('Dibayar').should('be.visible')
  })


  it('Kasir dapat memfilter history transaksi berdasarkan tanggal valid', () => {
    cy.get('input[name="date_from"]').type('2026-02-28')
    cy.get('input[name="date_to"]').type('2026-03-01')

    cy.contains('button', 'Filter').click()

    cy.contains('Rp 39.000').should('be.visible')
  })

  it('Kasir gagal memfilter history transaksi jika rentang tanggal tidak valid', () => {
    cy.get('input[name="date_from"]').type('2026-03-01')
    cy.get('input[name="date_to"]').type('2026-02-28')

    cy.contains('button', 'Filter').click()

    cy.contains('Tanggal "Dari" tidak boleh lebih besar dari Tanggal "Sampai').should('be.visible')
  })

  it('Kasir dapat melihat total pendapatan transaksi harian', () => {
    cy.get('input[name="date_from"]').type('2026-03-01')
    cy.get('input[name="date_to"]').type('2026-03-01')

    cy.contains('button', 'Filter').click()

    cy.contains('Rp 26.000').should('be.visible')
  })
  it('Kasir dapat menghapus transaksi', () => {
    cy.visit('/pos')
    cy.get('.grid.grid-cols-2 > div').first().click()
    cy.contains('button', 'Bayar').click()
    cy.contains('button', 'Cash').click()
    cy.get('input#paid').type('500000')
    cy.contains('button', 'Proses Pembayaran').click()
    cy.contains('Pembayaran Berhasil!', { timeout: 15000 }).should('be.visible')
    cy.visit('/history')
    cy.get('table tbody tr').first().within(() => {
      cy.contains('button', 'Hapus').click()
    })
    cy.contains('button', 'Ya, Hapus').click()
    cy.contains('Transaksi berhasil dihapus').should('be.visible')
  })
})
