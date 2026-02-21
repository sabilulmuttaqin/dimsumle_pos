describe('US-005 - Modul Produk', () => {
    beforeEach(() => {
        cy.login();
        cy.visit('/products');
    });

    it('Owner tambah produk dengan data valid', () => {
        cy.contains('span', 'Tambah Produk').click();
        cy.get('input[x-model="form.name"]').type('Dimsum mentai');
        cy.get('input[x-model="form.stock"]').type('10');
        cy.get('input[x-model="form.price"]').type('4000');
        cy.contains('button', 'Simpan').click();
        cy.contains('Produk berhasil ditambahkan', { timeout: 15000 }).should('be.visible');
    }); 
    it('Owner gagal tambah produk jika field nama kosong ', () => {
        cy.contains('span', 'Tambah Produk').click();
        cy.get('input[x-model="form.stock"]').type('10');
        cy.get('input[x-model="form.price"]').type('4000');
        cy.contains('button', 'Simpan').click();
        cy.contains('Nama produk wajib diisi', { timeout: 15000 }).should('be.visible');
    });
     it('Owner gagal tambah produk jika field harga dan stok negatif ', () => {
        cy.contains('span', 'Tambah Produk').click();
        cy.get('input[x-model="form.name"]').type('Dimsum mentai');
        cy.get('input[x-model="form.stock"]').type('-10');
        cy.get('input[x-model="form.price"]').type('-4000');
        cy.contains('button', 'Simpan').click();
        cy.contains('Stok tidak boleh minus', { timeout: 15000 }).should('be.visible');
        cy.contains('Harga tidak boleh minus', { timeout: 15000 }).should('be.visible');
    });
    it('Owner gagal tambah produk jika gambar lebih dari 2mb', () => {
    cy.contains('span', 'Tambah Produk').click()
    cy.get('input[x-model="form.name"]').type('Produk Gambar Besar')
    cy.get('input[x-model="form.price"]').type('10000')
    cy.get('input[x-model="form.stock"]').type('10')

    const blob = new Blob([new ArrayBuffer(3 * 1024 * 1024)], { type: 'image/jpeg' })
    const dt = new DataTransfer()
    dt.items.add(new File([blob], 'large.jpg', { type: 'image/jpeg' }))

    cy.get('input[type="file"]').then(($input) => {
        $input[0].files = dt.files
        $input[0].dispatchEvent(new Event('change', { bubbles: true }))
    })

    cy.contains('button', 'Simpan').click()
    cy.contains('2MB', { timeout: 10000 }).should('be.visible')
    })  
    it('Owner mengubah produk dengan data valid', () => {
        cy.get('table tbody tr').first().within(() => {
            cy.contains('button', 'Edit').click();
        });
        cy.contains('Edit Produk').should('be.visible');
        cy.get('input[x-model="form.name"]').clear().type('Dimsum Spesial Baru');
        cy.get('input[x-model="form.stock"]').clear().type('10');
        cy.get('input[x-model="form.price"]').clear().type('2000');
        
        cy.contains('button', 'Update').click();
        cy.contains("Produk berhasil diperbaharui", { timeout: 15000 }).should('be.visible');
    });

    it('Owner menghapus produk', () => {
        cy.get('table tbody tr').first().within(() => {
            cy.contains('button', 'Hapus').click();
        });
        cy.contains("Ya, Hapus").click();
        cy.contains('Produk berhasil dihapus', { timeout: 15000 }).should('be.visible');
    });
});