function historyPage() {
    return {
        showDetailModal: false,
        showDeleteModal: false,
        deleteId: null,
        deleteTitle: '',
        deleteDescription: '',
        loading: false,
        selectedTransaction: null,
        toast: { show: false, message: '' },

        async viewDetail(id) {
            this.loading = true;
            try {
                const response = await fetch(`/history/${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                if (data.success) {
                    this.selectedTransaction = data.transaction;
                    console.log("Isi dari data transaksi:", this.selectedTransaction);
                    
                    this.showDetailModal = true;
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.loading = false;
            }
        },

        closeDetailModal() {
            this.showDetailModal = false;
            this.selectedTransaction = null;
        },

        openDeleteModal(id) {
            this.deleteId = id;
            this.deleteTitle = 'Hapus Transaksi?';
            this.deleteDescription = 'Transaksi ini akan dihapus permanen.';
            this.showDeleteModal = true;
        },

        async destroy() {
            if (!this.deleteId) return;
            this.loading = true;

            try {
                const response = await fetch(`/history/${this.deleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showDeleteModal = false;
                    this.showToast(data.message);
                    setTimeout(() => location.reload(), 500);
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.loading = false;
            }
        },

        formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID').format(amount);
        },

        showToast(message) {
            this.toast.message = message;
            this.toast.show = true;
            setTimeout(() => this.toast.show = false, 3000);
        },
    };
}
