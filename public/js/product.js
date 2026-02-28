function productPage() {
    return {
        showModal: false,
        showDeleteModal: false,
        isEdit: false,
        editId: null,
        deleteId: null,
        deleteName: '',
        deleteTitle: '',
        deleteDescription: '',
        loading: false,
        errors: {},
        imageFile: null,
        imagePreview: null,
        toast: { show: false, message: '' },
        form: {
            name: '',
            price: '',
            stock: '',
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
                const response = await fetch(`/products/${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                if (data.success) {
                    this.form.name = data.product.name;
                    this.form.price = data.product.price;
                    this.form.stock = data.product.stock;
                    if (data.product.image_url) {
                        this.imagePreview = data.product.image_url;
                    }
                    this.showModal = true;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        },

        openDeleteModal(id, name) {
            this.deleteId = id;
            this.deleteName = name;
            this.deleteTitle = 'Hapus Produk?';
            this.deleteDescription = 'Produk ini akan dihapus permanen.';
            this.showDeleteModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.resetForm();
        },

        resetForm() {
            this.form = { name: '', price: '', stock: '' };
            this.errors = {};
            this.imageFile = null;
            this.imagePreview = null;
            this.editId = null;
        },

        getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.content : '';
        },

        reloadPage() {
            setTimeout(() => window.location.reload(), 2000);
        },

        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.imageFile = file;
                this.imagePreview = URL.createObjectURL(file);
            }
        },

        async save() {
            this.loading = true;
            this.errors = {};

            const formData = new FormData();
            formData.append('name', this.form.name);
            formData.append('price', this.form.price);
            formData.append('stock', this.form.stock);

            if (this.imageFile) {
                formData.append('image', this.imageFile);
            }

            let url = '/products';
            if (this.isEdit) {
                url = `/products/${this.editId}`;
                formData.append('_method', 'PUT');
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                        'Accept': 'application/json',
                    },
                    body: formData,
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

            try {
                const response = await fetch(`/products/${this.deleteId}`, {
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
            this.toast.message = message;
            this.toast.show = true;
            setTimeout(() => this.toast.show = false, 3000);
        },
    };
}