describe('US-013 - Fitur Pemilihan Pelanggan pada Transaksi (POS)', () => {

  beforeEach(() => {
    cy.loginKasir()
    cy.visit('/pos')
  })

  it('Kasir dapat mencatat transaksi dengan nama pelanggan', () => {
    cy.get('.grid.grid-cols-2 > div').first().click()
    cy.contains('button', 'Bayar').click()
    cy.contains('Pembayaran').should('be.visible')
    cy.get('select[x-model="customerId"]').select(1)
    cy.get('.text-3xl.font-bold').invoke('text').then((totalText) => {
      const total = parseInt(totalText.replace(/\D/g, ''))
      cy.get('input#paid').type((total + 1000).toString())
    })
    cy.contains('button', 'Proses Pembayaran').should('not.have.class', 'opacity-50')
    cy.contains('button', 'Proses Pembayaran').click()
    cy.contains('Pembayaran Berhasil!', { timeout: 15000 }).should('be.visible')
  })

  it('Kasir dapat mencatat transaksi tanpa nama pelanggan', () => {
    cy.get('.grid.grid-cols-2 > div').first().click()
    cy.contains('button', 'Bayar').click()
    cy.contains('Pembayaran').should('be.visible')
    cy.get('select[x-model="customerId"]').should('have.value', '')
    cy.get('.text-3xl.font-bold').invoke('text').then((totalText) => {
      const total = parseInt(totalText.replace(/\D/g, ''))
      cy.get('input#paid').type((total + 1000).toString())
    })
    cy.contains('button', 'Proses Pembayaran').should('not.have.class', 'opacity-50')
    cy.contains('button', 'Proses Pembayaran').click()
    cy.contains('Pembayaran Berhasil!', { timeout: 15000 }).should('be.visible')
  })

})
