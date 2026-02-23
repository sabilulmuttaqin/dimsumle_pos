function expensePage() {
    const today = new Date().toISOString().split('T')[0];

    return {
        showModal: false,
        showDeleteModal: false,
        isEdit: false,
        editId: null,
        deleteId: null,
        deleteTitle: '',
        deleteDescription: '',
        deleteType: '',
        loading: false,
        errors: {},
        toast: { show: false, message: '' },
        form: {
            amount: '',
            description: '',
            expense_date: today,
        },

        openCreateModal() {
            this.resetForm();
            this.isEdit = false;
            this.showModal = true;
        },

        async openEditModal(id) {
            this.resetForm();
            this.isEdit = true;
            this.editId = id;

            try {
                const response = await fetch(`/expenses/${id}`, { headers: { 'Accept': 'application/json' } });
                const data = await response.json();
                if (data.success) {
                    this.form.amount = data.expense.amount;
                    this.form.description = data.expense.description || '';
                    this.form.expense_date = data.expense.expense_date;
                    this.showModal = true;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        },

        openDeleteModal(id) {
            this.deleteId = id;
            this.deleteType = 'expense';
            this.deleteTitle = 'Hapus Pengeluaran?';
            this.deleteDescription = 'Pengeluaran ini akan dihapus permanen.';
            this.showDeleteModal = true;
        },



        closeModal() {
            this.showModal = false;
            this.resetForm();
        },

        resetForm() {
            this.form = { amount: '', description: '', expense_date: today };
            this.errors = {};
            this.editId = null;
        },

        getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]').content;
        },

        reloadPage() {
            setTimeout(() => window.location.reload(), 2000);
        },

        async save() {
            this.loading = true;
            this.errors = {};

            let url = '/expenses';
            let body = { ...this.form };

            if (this.isEdit) {
                url = `/expenses/${this.editId}`;
                body._method = 'PUT';
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(body),
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.closeModal();
                    this.showToast(data.message);
                    this.reloadPage();
                } else if (response.status === 422) {
                    this.errors = data.errors || {};
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.loading = false;
            }
        },

        async destroy() {
            this.loading = true;
            const url = `/expenses/${this.deleteId}`;

            try {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showDeleteModal = false;
                    this.showToast(data.message);
                    this.reloadPage();
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.loading = false;
            }
        },



        showToast(message) {
            this.toast = { show: true, message };
            setTimeout(() => this.toast.show = false, 3000);
        },
    };
}