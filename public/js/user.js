function userPage() {
    return {
        showModal: false,
        showDeleteModal: false,
        isEdit: false,
        editId: null,
        deleteId: null,
        deleteName: "",
        deleteTitle: "",
        deleteDescription: "",
        loading: false,
        errors: {},
        toast: { show: false, message: "", type: "success" },
        form: { name: "", email: "", password: "", role: "kasir" },

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
                const response = await fetch(`/users/${id}`, {
                    headers: { Accept: "application/json" },
                });
                const data = await response.json();
                if (data.success) {
                    this.form.name = data.user.name;
                    this.form.email = data.user.email;
                    this.form.role = data.user.role;
                    this.showModal = true;
                }
            } catch (error) {
                console.error("Error:", error);
            }
        },

        openDeleteModal(id, name) {
            this.deleteId = id;
            this.deleteName = name;
            this.deleteTitle = "Hapus User?";
            this.deleteDescription = "Akun ini akan dihapus permanen.";
            this.showDeleteModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.resetForm();
        },

        resetForm() {
            this.form = { name: "", email: "", password: "", role: "kasir" };
            this.errors = {};
            this.editId = null;
        },

        getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.content : '';
        },

        reloadPage() {
            setTimeout(() => window.location.reload(), 2000);
        },

        async save() {
            this.loading = true;
            this.errors = {};

            let url = "/users";
            let body = { ...this.form };

            if (this.isEdit) {
                url = `/users/${this.editId}`;
                body._method = "PUT";
            }

            try {
                const response = await fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": this.getCsrfToken(),
                        Accept: "application/json",
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
                console.error("Error:", error);
            } finally {
                this.loading = false;
            }
        },

        async destroy() {
            this.loading = true;

            try {
                const response = await fetch(`/users/${this.deleteId}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": this.getCsrfToken(),
                        Accept: "application/json",
                    },
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showDeleteModal = false;
                    this.showToast(data.message);
                    this.reloadPage();
                } else {
                    this.showDeleteModal = false;
                    this.showToast(data.message, "error");
                }
            } catch (error) {
                console.error("Error:", error);
            } finally {
                this.loading = false;
            }
        },

        showToast(message, type = "success") {
            this.toast = { show: true, message, type };
            setTimeout(() => (this.toast.show = false), 3000);
        },
    };
}
