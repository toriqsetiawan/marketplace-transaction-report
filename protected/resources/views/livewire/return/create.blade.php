<div class="container box" x-data="{
    loading: false,
    searchTerm: '',
    products: [],
    filteredProducts: [],
    cart: @js($cart),
    transactionNote: @entangle('transactionNote'),
    customerList: @entangle('customerList'),
    selectedCustomer: @entangle('selectedCustomer'),
    transactionDate: @entangle('transactionDate'),

    searchProduct() {
        if (!this.selectedCustomer) {
            alert('Tolong pilih customer terlebih dahulu.');
            this.searchTerm = '';
            return;
        }

        const term = this.searchTerm.toLowerCase();

        if (!term) {
            this.filteredProducts = [];
            return;
        }

        // Simulate a fetch call to search products
        this.filteredProducts = $wire.searchProduct(term)
            .then((result) => {
                return result ?? [];
            })
            .catch((error) => {
                console.error('Error searching products:', error);
            })
    },

    addToCart(product, selectedVariantId = null, isTransaction = false) {
        if (isTransaction) {
            product.items.forEach((item) => {
                this.cart.push({
                    id: item.id,
                    nama: item.nama,
                    price: item.price,
                    quantity: item.quantity,
                    selectedVariant: selectedVariantId ? selectedVariantId : item.selectedVariant,
                    variantAttributes: item.variantAttributes,
                    variants: item.variants.map(v => ({
                        id: v.id,
                        attributes: v.attributes, // Store dynamic attributes
                        price: v.price,
                        stock: v.stock
                    }))
                });
            });

            this.transactionNote = product.note;
            this.selectedCustomer = product.user.id;
        } else {
            const hasVariants = Array.isArray(product.variants) && product.variants.length > 0;

            let selectedVariant = hasVariants ?
                product.variants.find(v => v.id == selectedVariantId) || product.variants[0] :
                null;

            // Extract dynamic attributes
            let variantAttributes = hasVariants ?
                selectedVariant.attributes || {} : {};

            // Check if item exists in cart based on product and selected variant
            const existingItem = this.cart.find(item =>
                item.id === product.id && item.selectedVariant === (selectedVariant?.id || null)
            );

            if (!existingItem) {
                this.cart.push({
                    id: product.id,
                    nama: product.nama,
                    price: selectedVariant ? selectedVariant.price : product.harga_jual,
                    quantity: 1,
                    selectedVariant: selectedVariant ? selectedVariant.id : null,
                    variantAttributes: variantAttributes, // Store all variant attributes dynamically
                    variants: hasVariants ? product.variants.map(v => ({
                        id: v.id,
                        attributes: v.attributes, // Store dynamic attributes
                        price: v.price,
                        stock: v.stock
                    })) : []
                });
            } else {
                existingItem.quantity++;
            }
        }

        this.searchTerm = '';
        this.filteredProducts = [];
    },

    removeFromCart(index) {
        this.cart.splice(index, 1);
    },

    updateCartItem(index, field, value) {
        let item = this.cart[index];

        if (field === 'selectedVariant') {
            let variant = item.variants.find(v => v.id == value);
            if (variant) {
                item.selectedVariant = variant.id;
                item.price = variant.price;
            }
        } else if (field === 'quantity') {
            let stock = item.variants.find(v => v.id == item.selectedVariant).stock;
            item.quantity = Math.max(1, value); // Ensure at least 1 quantity
        }
    },

    calculateTotal() {
        return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    },

    formatPrice(price) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(price);
    },

    saveCart() {
        this.$wire.set('cart', this.cart);
        // Save cart data to the server
        $wire.saveCart()
            .then((result) => {
                if (result) {
                    alert(result.message);
                    if (result.status) {
                        window.location.href = '/return';
                    }
                } else {
                    alert(result);
                }
                this.loading = false
            })
            .catch((error) => {
                console.error('Error saving cart:', error);
                this.loading = false
            });
    }
}">
    <div class="row">
        <!-- Product Search Section -->
        <div class="col-md-6">
            <h4>Product Search</h4>
            <input type="text" class="form-control" placeholder="Search product or scan barcode..." x-model="searchTerm"
                x-on:input.debounce.300ms="searchProduct" />

            <div class="list-group" style="max-height: 300px; overflow-y: auto;" wire:ignore>
                <template x-for="(product, index) in filteredProducts" :key="index">
                    <a href="javascript:void(0)" class="list-group-item"
                        x-on:click="addToCart(product, null, product.transaction_code ? true : false)">
                        <template x-if="product.transaction_code">
                            <div>
                                <p class="text-bold text-uppercase" x-text="product.transaction_code"></p>
                                <p><span x-text="product.user.name"></span></p>
                                <p>Total: <span x-text="formatPrice(product.total_price)"></span></p>
                            </div>
                        </template>
                        <template x-if="product.nama">
                            <div>
                                <p class="text-bold text-uppercase" x-text="product.nama"></p>
                                <p style="margin: 0">Supplier: <i x-text="product.supplier?.name || 'Unknown'"></i></p>
                                <p>Harga mitra: <span x-text="formatPrice(product.harga_jual)"></span></p>
                            </div>
                        </template>
                    </a>
                </template>
            </div>
        </div>
    </div>

    <div class="row" wire:ignore>
        <div class="col-md-12">
            <h4>Cart</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" x-data="{
                                init() {
                                    setTimeout(() => {
                                        this.select2Customer = $(this.$refs.customer).select2();
                                        this.select2Customer.on('select2:select', (event) => {
                                            this.selectedCustomer = event.params.data.id;
                                        });
                                        this.select2Customer.on('select2:unselect', (event) => {
                                            this.selectedCustomer = null
                                        });
                                        this.$watch('selectedCustomer', (value) => {
                                            this.select2Customer.select2().val(this.selectedCustomer).trigger('change')
                                        });
                                    }, 1000)
                                }
                            }">
                                <label for="customer">Customer</label>
                                <select name="customer" id="customer" x-model="selectedCustomer" class="form-control"
                                    x-ref="customer">
                                    <option value="" hidden>Select Customer</option>
                                    <template x-for="customer in customerList" :key="customer.id">
                                        <option :value="customer.id" x-text="customer.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="transactionDate">Date</label>
                            <input type="date" name="transactionDate" id="transactionDate" class="form-control"
                                x-model="transactionDate">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="transactionNote">Note (No Transaction, etc)</label>
                        <textarea name="transactionNote" id="transactionNote" class="form-control" rows="5" x-model="transactionNote" readonly></textarea>
                    </div>
                </div>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Stock</th>
                        <th>Variant</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in cart" :key="index">
                        <tr>
                            <!-- Product Name -->
                            <td x-text="item.nama"></td>
                            <td
                                {{-- :class="{ 'bg-danger': item.variants.find(v => v.id == item.selectedVariant).stock < 1 }" --}}
                                >
                                <div style="display: flex;justify-content: space-between;align-items: center;">
                                    <span x-text="item.variants.find(v => v.id == item.selectedVariant).stock"></span>
                                    <i class="fa fa-arrow-right"></i>
                                    <span
                                        x-text="parseInt(item.variants.find(v => v.id == item.selectedVariant).stock) + parseInt(item.quantity)"
                                        class="label label-success"></span>
                                </div>
                            </td>
                            <!-- Variant Selector -->
                            <td>
                                <template x-if="item.variants.length">
                                    <select class="form-control" x-model="item.selectedVariant"
                                        x-on:change="updateCartItem(index, 'selectedVariant', $event.target.value)">
                                        <template x-for="variant in item.variants" :key="variant.id">
                                            <option :value="variant.id"
                                                x-text="Object.values(variant.attributes).join(' / ')"
                                                :selected="item.selectedVariant == variant.id"></option>
                                        </template>
                                    </select>
                                </template>
                                <template x-if="!item.variants.length">
                                    <span>-</span>
                                </template>
                            </td>

                            <!-- Quantity Input -->
                            <td>
                                <input type="text" class="form-control" x-model="item.quantity"
                                    x-on:input="updateCartItem(index, 'quantity', $event.target.value)"
                                    min="1" />
                            </td>

                            <!-- Price Input -->
                            <td>
                                <input type="number" class="form-control" x-model="item.price"
                                    x-on:input="updateCartItem(index, 'price', $event.target.value)" step="100" />
                            </td>

                            <!-- Subtotal -->
                            <td>Rp <span x-text="(item.price * item.quantity).toLocaleString()"></span></td>

                            <!-- Remove Button -->
                            <td>
                                <button class="btn btn-danger btn-xs" x-on:click="removeFromCart(index)">Remove</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <!-- Cart Total -->
            <div class="text-right">
                <strong>Total: Rp <span x-text="calculateTotal().toLocaleString()"></span></strong>
            </div>

            <!-- Submit / Save / Buy Now Buttons -->
            <div class="mt-3 text-right" style="margin: 2rem 0">
                <a href="{{ route('penjualan.index') }}" class="btn btn-default" style="margin-right: 1rem">Cancel</a>
                <button class="btn btn-primary" x-on:click="saveCart()" :disabled="loading"
                    x-text="loading ? 'Loading...' : 'Save'"></button>
            </div>
        </div>
    </div>
</div>
