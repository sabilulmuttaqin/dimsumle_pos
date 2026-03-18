function posPage() {
    return {
        cart: [],
        search: '',
        total: 0,
        paid: 0,
        change: 0,
        paymentMethod: 'cash',
        customerId: '',
        showPaymentModal: false,
        showSuccessModal: false,
        lastInvoice: '',
        selectedTransaction: null,
        customerId: '',
        loading: false,
        toast: { show: false, message: '' },

        isProductVisible(name) {
            if (!this.search) return true;
            return name.toLowerCase().includes(this.search.toLowerCase());
        },

        addToCart(id, name, price, stock) {
            const existing = this.cart.find(item => item.id === id);

            if (existing) {
                if (existing.quantity < stock) {
                    existing.quantity++;
                    existing.subtotal = existing.quantity * existing.price;
                }
            } else {
                this.cart.push({
                    id: id,
                    name: name,
                    price: price,
                    quantity: 1,
                    subtotal: price,
                    stock: stock,
                });
            }

            this.calculateTotal();
        },

        updateQuantity(index, action) {
            const item = this.cart[index];
            if (!item) return;

            if (action === 'increase' && item.quantity < item.stock) {
                item.quantity++;
            } else if (action === 'decrease' && item.quantity > 1) {
                item.quantity--;
            }

            item.subtotal = item.quantity * item.price;
            this.calculateTotal();
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
            this.calculateTotal();
        },

        calculateTotal() {
            this.total = this.cart.reduce((sum, item) => sum + item.subtotal, 0);
        },

        formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID').format(amount);
        },

        openPaymentModal() {
            if (this.cart.length === 0) return;
            this.paid = 0;
            this.change = 0;
            this.showPaymentModal = true;
        },

        closePaymentModal() {
            this.showPaymentModal = false;
        },

        calculateChange() {
            const paidNum = parseFloat(this.paid) || 0;
            this.change = Math.max(0, paidNum - this.total);
        },

        async processPayment() {
            const paidNum = parseFloat(this.paid) || 0;
            if (paidNum < this.total) return;

            this.loading = true;

            try {
                const response = await fetch('/pos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        cart: this.cart.map(item => ({
                            id: item.id,
                            quantity: item.quantity,
                            price: item.price,
                            subtotal: item.subtotal,
                        })),
                        paid_amount: paidNum,
                        payment_method: this.paymentMethod,
                        customer_id: this.customerId ? this.customerId : null,
                    }),
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.lastInvoice = data.invoice;
                    this.showPaymentModal = false;
                    this.showSuccessModal = true;
                    this.selectedTransaction = data.id;
                } else {
                    this.showToast(data.message || 'Terjadi kesalahan.');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showToast('Terjadi kesalahan saat memproses pembayaran.');
            } finally {
                this.loading = false;
            }
        },

        resetTransaction() {
            this.cart = [];
            this.total = 0;
            this.paid = 0;
            this.change = 0;
            this.paymentMethod = 'cash';
            this.customerId = '';
        },

        closeSuccessModal() {
            this.showSuccessModal = false;
            this.resetTransaction();
            location.reload();
        },

        showToast(message) {
            this.toast.message = message;
            this.toast.show = true;
            setTimeout(() => this.toast.show = false, 3000);
        },
    };
}
