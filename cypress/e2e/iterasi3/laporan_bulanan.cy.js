describe('US-009 - Modul Laporan Penjualan Bulanan', () => {
  beforeEach(() => {
    cy.login()
    cy.visit('/report')
    cy.get('[x-show="loading"]', { timeout: 10000 }).should('not.be.visible')
  })

  it('Owner dapat melihat informasi profit, pemasukan, dan pengeluaran', () => {
    cy.contains('Laba').should('be.visible')
    cy.contains('Total Penjualan').should('be.visible')
    cy.contains('Total Pengeluaran').should('be.visible')
  })

  it('Owner dapat memfilter laporan berdasarkan kasir tertentu', () => {
    cy.get('select').last().select('Kasir 1')
    cy.get('[x-show="loading"]').should('not.be.visible')
    cy.contains('Total Penjualan').should('be.visible')
    cy.contains('Kasir 1').should('be.visible')
  })

  it('Owner dapat memfilter laporan berdasarkan tanggal tertentu', () => {
    cy.get('select#selectedMonth').select('3')
    cy.get('select#selectedYear').select('2026')
    cy.get('[x-show="loading"]').should('not.be.visible')
    cy.contains('Maret 2026').should('be.visible')
  })

  it('Owner dapat melihat kolom tabel produk terlaris dan pembeli terbanyak', () => {
    cy.contains('Produk Terlaris').closest('.bg-white').within(() => {
      cy.contains('Nama').should('be.visible')
      cy.contains('Omzet').should('be.visible')
      cy.contains('Jml').should('be.visible')
    })
    cy.contains('5 Pembeli Terbanyak').closest('.bg-white').within(() => {
      cy.contains('Nama').should('be.visible')
      cy.contains('Frekuensi Beli').should('be.visible')
      cy.contains('Total Rp').should('be.visible')
    })
  })

  it('Owner tidak melihat data bulanan jika belum ada transaksi', () => {
    cy.get('select#selectedMonth').select('1')
    cy.get('select#selectedYear').select('2024')
    cy.get('[x-show="loading"]').should('not.be.visible')
    cy.contains('Produk Terlaris').closest('.bg-white').within(() => {
      cy.contains('Belum ada data').should('be.visible')
    })
  })


  it('Owner melihat pesan kosong jika tidak ada data pembeli', () => {
    cy.get('select#selectedMonth').select('1')
    cy.get('select#selectedYear').select('2023')
    cy.get('[x-show="loading"]').should('not.be.visible')
    cy.contains('5 Pembeli Terbanyak').closest('.bg-white').within(() => {
      cy.contains('Belum ada pelanggan').should('be.visible')
    })
  })

})