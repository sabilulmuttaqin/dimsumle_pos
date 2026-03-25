describe('US-010 - Modul Ringkasan Teks', () => {

  beforeEach(() => {
    cy.login()
    cy.visit('/report')
    cy.get('[x-show="loading"]', { timeout: 10000 }).should('not.be.visible')
  })

  it('Owner dapat melihat ringkasan teks pada halaman laporan berdasarkan tanggal', () => {
    cy.get('#selectedMonth').select('3')
    cy.get('#selectedYear').select('2026')
    cy.contains('Ringkasan Teks').should('be.visible')
    cy.contains('Ringkasan periode ini').should('be.visible')
  })

  it('Owner tidak dapat melihat ringkasan teks pada halaman laporan jika tidak ada transaksi', () => {
    cy.get('#selectedMonth').select('1')
    cy.get('#selectedYear').select('2023')
     cy.get('[x-show="loading"]').should('not.be.visible')
    cy.contains('Ringkasan Teks').should('be.visible')
    cy.contains('Belum ada data untuk dianalisis').should('be.visible')
  })

  it('Owner dapat memfilter ringkasan berdasarkan kasir tertentu', () => {
  cy.get('select').last().select('Kasir 1')
  cy.get('[x-show="loading"]').should('not.be.visible')
  cy.contains('Ringkasan Teks').should('be.visible')
  cy.contains('Kasir 1').should('be.visible')
})

  it('Owner dapat melihat informasi perbandingan omzet , profit bulanan dan jam ramai', () => {
    cy.get('select#selectedMonth').select(String(2))
    cy.get('select#selectedYear').select(String(2026))
    cy.contains('Ringkasan Teks').closest('.bg-white').within(() => {
      cy.contains('Jam ramai').should('be.visible')
      cy.contains('Perbandingan Omzet').should('be.visible')
      cy.contains('Profit').should('be.visible')
    })
  })

})
