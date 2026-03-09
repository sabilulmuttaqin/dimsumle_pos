describe('US-003 - Modul Struk Transaksi', () => {

  beforeEach(() => {
    cy.loginKasir()
  })

  it('Kasir dapat mencetak struk setelah transaksi berhasil.', () => {
    cy.visit('/pos')
    cy.get('.grid.grid-cols-2 > div').first().click()
    cy.contains('button', 'Bayar').click()
    cy.get('input#paid').type('500000')
    cy.contains('button', 'Proses Pembayaran').click()
    cy.contains('Pembayaran Berhasil!', { timeout: 15000 }).should('be.visible')
    cy.contains('No. Invoice').should('be.visible')
    cy.contains('button', 'Cetak Struk').should('be.visible')
    cy.contains('button', 'Cetak Struk').click()
    cy.get('iframe#struk-frame')
      .should('exist')
      .invoke('attr', 'src')
      .should('include', '/struk/')
  })
})
