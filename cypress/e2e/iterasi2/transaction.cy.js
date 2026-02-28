describe('US-001 - Modul Transaksi (POS)', () => {

  beforeEach(() => {
    cy.loginKasir()
    cy.visit('/pos')
  })

  it('Kasir dapat mencatat transaksi dengan data valid', () => {
    cy.get('.grid.grid-cols-2 > div').first().click()
    cy.contains('button', 'Bayar').click()
    cy.contains('Pembayaran').should('be.visible')
    cy.contains('button', 'Cash').click()
    cy.get('input#paid').type('500000')
    cy.contains('button', 'Proses Pembayaran').click()
    cy.contains('Pembayaran Berhasil!', { timeout: 15000 }).should('be.visible')
  })

  it('Kasir gagal mencatat transaksi jika jumlah dibayar negatif', () => {
    cy.get('.grid.grid-cols-2 > div').first().click()
    cy.contains('button', 'Bayar').click()
    cy.contains('Pembayaran').should('be.visible')
    cy.get('input#paid').invoke('val', '-500000').trigger('input')
    cy.contains('Jumlah dibayar tidak boleh negatif').should('be.visible')
})
  it('Kasir melihat perhitungan kembalian dengan benar', () => {
    cy.get('.grid.grid-cols-2 > div').first().click()
    cy.contains('button', 'Bayar').click()
    cy.contains('Pembayaran').should('be.visible')
    cy.contains('button', 'Cash').click()
    cy.get('input#paid').type('500000')
    cy.contains('Kembalian').should('be.visible')
  })
    it('Kasir gagal mencatat transaksi jika jumlah dibayar kurang dari total harga', () => {
    cy.get('.grid.grid-cols-2 > div').first().click()
    cy.contains('button', 'Bayar').click()
    cy.contains('Pembayaran').should('be.visible')
    cy.contains('button', 'Cash').click()
    cy.get('input#paid').type('10')
    cy.contains('Jumlah pembayaran kurang dari total').should('be.visible')
  })
   it('Kasir gagal mencatat transaksi jika jumlah dibayar kosong', () => {
    cy.get('.grid.grid-cols-2 > div').first().click()
    cy.contains('button', 'Bayar').click()
    cy.contains('Pembayaran').should('be.visible')
    cy.contains('button', 'Cash').click()
    cy.contains('button', 'Proses Pembayaran')
    .should('be.disabled')
    .and('have.class', 'opacity-50')
    .and('have.class', 'cursor-not-allowed')
  })

})
