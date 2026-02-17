function loginForm() {
    return {
        form: {
            email: '',
            password: '',
            remember: false,
        },
        errors: {},
        loading: false,
        showPassword: false,

        async submit() {
            this.loading = true;
            this.errors = {};

            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    window.location.href = data.redirect;
                } else if (response.status === 422) {
                    this.errors = data.errors || {};
                    this.loading = false;
                } else {
                    this.loading = false;
                }
            } catch (error) {
                this.loading = false;
                console.error('Login error:', error);
            }
        }
    };
}