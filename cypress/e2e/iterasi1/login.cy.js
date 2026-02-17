describe('Fitur Login', () => {
    beforeEach(() => {
        cy.visit('/');
    });

    it('Owner login dengan kredensial valid', () => {
        cy.get('input#email').type('owner@gmail.com');
        cy.get('input#password').type('owner123');
        cy.get('button[type="submit"]').click();
        cy.url({ timeout: 10000 }).should('include', '/dashboard');
    });

    it('Kasir login dengan kredensial valid', () => {
        cy.get('input#email').type('kasir@gmail.com');
        cy.get('input#password').type('akunkasir');
        cy.get('button[type="submit"]').click();
        cy.url({ timeout: 10000 }).should('include', '/pos');
    });

    it('Owner gagal login dengan password salah', () => {
        cy.get('input#email').type('owner@gmail.com');
        cy.get('input#password').type('akuowner');
        cy.get('button[type="submit"]').click();
        cy.contains('Password salah').should('be.visible');
    });
    
    it('Kasir gagal login dengan email kosong', () => {
        cy.get('input#password').type('akunkasir');
        cy.get('button[type="submit"]').click();
        cy.contains('Email wajib diisi').should('be.visible');
    });
    
   it('Kasir gagal login dengan email tidak valid', () => {
        cy.get('input#email').type('kasir.gmail.com');
        cy.get('input#password').type('akunkasir');
        cy.get('button[type="submit"]').click();
        cy.contains('Email harus valid').should('be.visible');
    });
});
