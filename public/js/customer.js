function customerPage() {
    return {
        showModal: false,
        isEdit: false,
        loading: false,
        form: { id: null, name: '' },
        errors: {},
        toast: { show: false, message: '' },
        
        openCreateModal() {
            this.isEdit = false;
            this.form = { id: null, name: '' };
            this.errors = {};
            this.showModal = true;
        },
        
        async openEditModal(id) {
            this.isEdit = true;
            this.errors = {};
            try {
                const response = await fetch(`/customers/${id}`);
                const data = await response.json();
                if (data.success) {
                    this.form = { id: data.customer.id, name: data.customer.name };
                    this.showModal = true;
                }
            } catch (error) {
                this.showToast('Gagal memuat data pelanggan', 'error');
            }
        },
        
        closeModal() {
            this.showModal = false;
            this.form = { id: null, name: '' };
        },
        
        async save() {
            if (this.loading) return;
            this.loading = true;
            this.errors = {};
            
            const url = this.isEdit ? `/customers/${this.form.id}` : '/customers';
            const method = this.isEdit ? 'PUT' : 'POST';
            
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });
                
                const data = await response.json();
                if (data.success) {
                    this.closeModal(); // Tutup modal duluan biar tidak membingungkan
                    this.showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 2000); // Reload data baru
                } else if (response.status === 422) {
                    this.errors = data.errors;
                } else {
                    this.showToast(data.message || 'Terjadi kesalahan', 'error');
                }
            } catch (error) {
                this.showToast('Terjadi kesalahan sistem', 'error');
            } finally {
                this.loading = false;
            }
        },

        deleteId: null,
        showDeleteModal: false,
        deleteTitle: '',
        deleteDescription: '',
        
        openDeleteModal(id) {
            this.deleteId = id;
            this.deleteTitle = 'Hapus Pelanggan';
            this.deleteDescription = 'Apakah Anda yakin? Pelanggan ini mungkin mempunyai riwayat transaksi. Lanjutkan?';
            this.showDeleteModal = true;
        },

        async destroy() {
            this.loading = true;
            try {
                const response = await fetch(`/customers/${this.deleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    this.showDeleteModal = false;
                    this.showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    this.showToast(data.message, 'error');
                }
            } catch (error) {
                this.showToast('Gagal menghapus pelanggan', 'error');
            } finally {
                this.loading = false;
            }
        },

        // Menggunakan in-page toast bawaan alpine
        showToast(message, type) {
            this.toast.message = message;
            this.toast.show = true;
            setTimeout(() => {
                this.toast.show = false;
            }, 3000);
        }
    }
}