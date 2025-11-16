class ShoppingCart {
    constructor() {
        this.items = this.getCartFromStorage();
        this.SHIPPING_COST = 20000;
        this.init();
    }
    init() {
        if (window.location.pathname.includes('cart')) {
            this.updateCartUI();
        }
        this.updateCartCount();
        this.attachEventListeners();
        this.handlePayButtonClick();
    }
    getCartFromStorage() {
        try {
            return JSON.parse(localStorage.getItem('shopping_cart')) || [];
        } catch (error) {
            console.error('Error getting cart from local storage', error);
            return [];
        }
    }
    saveCartToStoreStorage() {
        try {
            localStorage.setItem('shopping_cart', JSON.stringify(this.items));
            this.updateCartCount();
        } catch (error) {
            console.error('Error saving cart to local storage   ', error);
            this.showNotification('Error saving cart to local storage');
        }
    }

    updateCartCount() {
        const cartCount = document.getElementById('cart-count');
        if (!cartCount) return;

        const totalItems = this.items.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;
        cartCount.style.display = totalItems > 0 ? 'flex' : 'none';
    }

    addItem(product) {
        if (!product?.id) return;
        try {
            const existingItem = this.items.find(item => item.id === parseInt(product.id));
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                this.items.push({
                    id: parseInt(product.id),
                    name: product.name,
                    price: parseFloat(product.price),
                    image: product.image,
                    category: product.category_name,
                    quantity: 1
                });
            }
            this.saveCartToStoreStorage();
            this.updateCartCount();
            this.updateCartUI();
            this.showNotification(`1 ${product.name} added to cart`);
        } catch (error) {
            console.error(error);
            this.showNotification('Error adding item to cart');
        }
    }



    removeItem(productId) {
        try {
            this.items = this.items.filter(item => item.id !== parseInt(productId));
            this.saveCartToStoreStorage();
            this.updateCartCount();
            this.updateCartUI(); // Tambahkan ini untuk memperbarui UI secara langsung
            this.showNotification('Item removed from cart');
        } catch (error) {
            console.error(error);
            this.showNotification('Error removing item from cart');
        }
    }

    updateQuantity(productId, changeAmount) {
        try {
            const item = this.items.find(item => item.id === parseInt(productId));
            if (!item) return;

            const newQuantity = item.quantity + changeAmount;
            if (newQuantity < 1) {
                this.removeItem(productId);
                return;
            }
            item.quantity = newQuantity;
            this.saveCartToStoreStorage();
            this.updateCartCount();
            this.updateCartUI(); // Tambahkan ini untuk memperbarui UI secara langsung
        } catch (error) {
            console.error(error);
            this.showNotification('Error updating cart count');
        }
    }

    formatPrice(price) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(price);
    }
    calculateSubTotal() {
        return this.items.reduce((total, item) => total + (parseFloat(item.price) * item.quantity), 0);
    }

    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.classList.add(
            'fixed',
            'bottom-4',
            'right-4',
            'px-6',
            'py-3',
            'rounded-lg',
            'shadow-lg',
            'transform',
            'transition-transform',
            'duration-300',
            'translate-y-0',
            'z-50',
            type === 'success' ? 'bg-emerald-500' : 'bg-red-500',
            'text-white'
        );
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('translate-y-full');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    updateOrderSummary(subtotal = 0, shipping = this.SHIPPING_COST) {
        const elements = {
            subtotal: document.querySelector('[data-summary="subtotal"]'),
            shipping: document.querySelector('[data-summary="shipping"]'),
            total: document.querySelector('[data-summary="total"]'),
            checkout: document.querySelector('[data-summary="checkout"]'),
        };

        if (elements.subtotal) elements.subtotal.textContent = this.formatPrice(subtotal);
        if (elements.shipping) elements.shipping.textContent = this.formatPrice(shipping);
        if (elements.total) elements.total.textContent = this.formatPrice(subtotal + shipping);

        if (elements.checkout) {
            const isDisabled = this.items.length === 0;
            elements.checkout.disabled = isDisabled;
            elements.checkout.className = isDisabled
                ? 'mt-6 w-full bg-gray-300 cursor-not-allowed text-white py-3 px-4 rounded-lg'
                : 'mt-6 w-full bg-emerald-600 text-white py-3 px-4 rounded-lg hover:bg-emerald-700';

            elements.checkout.addEventListener('click', () => this.showShippingForm(elements.checkout));
        }
    }




    createCartItemElement(item) {
        const div = document.createElement('div');
        div.className = 'p-6 border-b border-gray-200';
        div.innerHTML = `
        <div class="flex items-center">
            <img src="${item.image}" alt="${item.name}" class="w-20 h-20 object-cover rounded-lg">
            <div class="ml-4 flex-1">
                <h3 class="text-lg font-medium text-gray-900">${item.name}</h3>
                <p class="mt-1 text-sm text-gray-500">${item.category}</p>
                <div class="mt-2 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
    <button type="button" class="quantity-btn p-1 rounded-md hover:bg-gray-100" data-action="decrease" data-product-id="${item.id}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
        </svg>
    </button>
    <span class="quantity-value text-gray-700 font-medium" id="quantity-${item.id}">${item.quantity}</span>
    <button type="button" class="quantity-btn p-1 rounded-md hover:bg-gray-100" data-action="increase" data-product-id="${item.id}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
    </button>
</div>

                    <span class="font-medium text-gray-900">
                        ${this.formatPrice(item.price * item.quantity)}
                    </span>
                </div>
            </div>
            <button class="ml-4 text-gray-400 hover:text-red-500" data-action="remove" data-product-id="${item.id}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>`;
        this.attachItemEventListeners(div, item.id);
        return div;
    }

    showShippingForm(checkoutButton) {
        const cartItemsContainer = document.querySelector('.cart-items');
        if (!cartItemsContainer) {
            console.error('Cart items container not found!');
            return;
        }

        const quantityButtons = cartItemsContainer.querySelectorAll('.quantity-btn');
        const removeButtons = cartItemsContainer.querySelectorAll('[data-action="remove"]');
        quantityButtons.forEach(button => button.disabled = true);
        removeButtons.forEach(button => button.disabled = true);

        const formContainerId = 'shipping-form-container';
        let formContainer = document.getElementById(formContainerId);

        if (!formContainer) {
            formContainer = document.createElement('div');
            formContainer.id = formContainerId;
            formContainer.className = 'mt-6 bg-gray-50 p-6 rounded-lg shadow-md';
            formContainer.innerHTML = `
                <h2 class="text-lg font-medium text-gray-900 mb-4">Shipping Information</h2>
                <form id="checkoutForm" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" id="name" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" id="phone" name="phone" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label for="shipping_address" class="block text-sm font-medium text-gray-700">Shipping Address</label>
                        <textarea id="shipping_address" name="shipping_address" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                        <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                    </div>
                </form>
            `;
            cartItemsContainer.appendChild(formContainer);
        }

        const payButton = document.getElementById('payButton');
        if (payButton) {
            payButton.hidden = false;
        }
        checkoutButton.hidden = true;
    }



    updateCartUI() {
        const cartContainer = document.querySelector('.cart-items');
        if (!cartContainer) return;

        cartContainer.innerHTML = '';
        if (this.items.length === 0) {
            cartContainer.innerHTML = `
            <div class="p-5 text-center text-gray-500">
                <p class="text-2xl">Your cart is currently empty.</p>
                <a href="/" class="text-emerald-600 hover:text-emerald-700">
                    Continue shopping
                </a>
            </div>
            `;
            this.updateOrderSummary(0, 0);
            return;
        }

        const cartContent = document.createElement('div');
        cartContent.className = 'cart-content';

        this.items.forEach(item => {
            cartContent.appendChild(this.createCartItemElement(item));

        });

        cartContainer.appendChild(cartContent);

        // Hitung subtotal dan panggil updateOrderSummary
        const subtotal = this.calculateSubTotal();
        const shipping = this.SHIPPING_COST;
        this.updateOrderSummary(subtotal, shipping);
    }


    attachItemEventListeners(elements, productId) {
        elements.addEventListener('click', (e) => {
            const target = e.target.closest('[data-action]');
            if (!target) return;


            e.preventDefault();
            const action = target.dataset.action;

            switch (action) {
                case 'decrease':
                    this.updateQuantity(productId, -1);
                    break;
                case 'increase':
                    this.updateQuantity(productId, 1);
                    break;
                case 'remove':
                        this.removeItem(productId);
                        break;

            }
        });
    }

    attachEventListeners() {
        document.addEventListener('click', (e) => {
            const addToCartButton = e.target.closest('.add-to-cart-btn');
            if (!addToCartButton) return;

            e.preventDefault();
            const productCard = addToCartButton.closest('.product-card');
            if (!productCard) return;

            const product = {
                id: productCard.dataset.id,
                name: productCard.dataset.name,
                price: productCard.dataset.price,
                image: productCard.dataset.image,
                category_name: productCard.dataset.category
            };
            this.addItem(product);
            this.updateCartUI(); // Pastikan UI diperbarui
        });
    }

    async processPayment() {
        const form = document.getElementById('checkoutForm');
        if(!form || !form.checkValidity()) {
            form?.reportvalidity();
            return;
        }
        const formData = new FormData(form);
        const payButton = document.getElementById('payButton');
        if(payButton) {
            payButton.disabled = true;
            payButton.textContent = 'Processing Payment...';
        }

        try{
            const response = await fetch('/checkout/process', {
                method: 'POST',
                headers:{
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    phone: formData.get('phone'),
                    shipping_address: formData.get('shipping_address'),
                    notes: formData.get('notes'),
                    cart: this.items
                })
            });
            if(!response.ok){
                const error = await response.json();
                throw new Error(error.message || 'Payment Processing failed');
            }

            const data = await response.json();
            if(!data.status === 'success' || !data.snap_token){
                throw new Error(error.message || 'Invalid Payment Response');
            }
            this.handlePayment(data.snap_token, data.order_id, payButton);
        }catch(error){
            console.error('payment error',error);
            this.showNotification(error.message || 'gagal proses bayar', 'error');
            if(payButton){
                payButton.disabled = false
                payButton.textContent = 'Pay Now';
            }
        }
    }
    handlePayButtonClick() {
        const payButton = document.getElementById('payButton');
        payButton.addEventListener('click', async () => {
            await this.processPayment();
        });
    }

    handlePayment(snapToken, orderId, payButton) {
        if (!window.snap || typeof window.snap.pay !== 'function') {
            console.error('Midtrans Snap is not initialized.');
            this.showNotification('Payment gateway not initialized. Please refresh the page.', 'error');
            payButton.disabled = false;
            payButton.textContent = 'Pay Now';
            return;
        }

        window.snap.pay(snapToken, {
            onSuccess: async (result) => {
                await this.updateOrderStatus(result, 'paid');
                this.items = [];
                this.saveCartToStoreStorage();
                window.location.href = '/user/orders';
            },
            onPending: async (result) => {
                await this.updateOrderStatus(result, 'pending');
                this.items = [];
                this.saveCartToStoreStorage();
                window.location.href = '/user/orders';
            },
            onError: async (result) => {
                await this.updateOrderStatus(result, 'failed');
                this.showNotification('Payment failed', 'error');
                payButton.disabled = false;
                payButton.textContent = 'Pay Now';
            },
            onClose: () => {
                if (confirm('Do you want to continue the payment?')) {
                    window.location.href = '/user/orders';
                } else {
                    payButton.disabled = false;
                    payButton.textContent = 'Pay Now';
                }
            },
        });

    }

    updateOrderStatus(paymentResult, status) {
        fetch('/payments/update-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                order_id: paymentResult.order_id,
                transaction_id: paymentResult.transaction_id,
                payment_type: paymentResult.payment_type,
                status: status
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('Order status updated successfully:', data.message);
                setTimeout(() => {
                    window.location.href = '/';
                }, 2000);
            } else {
                console.error('Failed to update order status:', data.message);
            }
        })
        .catch(error => console.error('Error updating order status:', error));
    }



}

export default ShoppingCart;
