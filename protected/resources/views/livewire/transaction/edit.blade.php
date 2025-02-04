<div class="container box" x-data="{
    searchTerm: '',
    products: [],
    filteredProducts: [],
    cart: @entangle('cart'),
    transactionCode: @entangle('transactionCode'),
    transactionNote: @entangle('transactionNote'),
    customerList: @entangle('customerList'),
    selectedCustomer: @entangle('selectedCustomer'),
    transactionStatus: @entangle('transactionStatus'),
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

    addToCart(product, selectedVariantId = null) {
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
                item.price = variant.price;
                item.selectedVariant = variant.id;
            }
        } else if (field === 'quantity') {
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
        // Save cart data to the server
        $wire.saveCart()
            .then((result) => {
                if (result) {
                    // Handle success, e.g., show a success message
                    alert('Cart saved successfully!');
                    window.location.href = '/penjualan';
                } else {
                    // Handle error, e.g., show an error message
                    alert(result);
                }
            })
            .catch((error) => {
                console.error('Error saving cart:', error);
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
                <template x-for="product in filteredProducts" :key="product.id">
                    <a href="javascript:void(0)" class="list-group-item" x-on:click="addToCart(product)">
                        <p class="text-bold text-uppercase" x-text="product.nama"></p>
                        <p style="margin: 0">Supplier: <i x-text="product.supplier.name"></i></p>
                        <p>Harga mitra: <span x-text="formatPrice(product.harga_jual)"></span></p>
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
                                        this.select2Customer.select2().val(this.selectedCustomer).trigger('change')
                                    }, 1000)
                                }
                            }">
                                <label for="transactionCode">Customer</label>
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
                            <div class="form-group" x-data="{
                                init() {
                                    setTimeout(() => {
                                        this.select2Status = $(this.$refs.status).select2();
                                        this.select2Status.on('select2:select', (event) => {
                                            this.transactionStatus = event.params.data.id;
                                        });
                                        this.select2Status.on('select2:unselect', (event) => {
                                            this.transactionStatus = null
                                        });
                                        this.$watch('transactionStatus', (value) => {
                                            this.select2Status.select2().val(this.transactionStatus).trigger('change')
                                        });
                                        this.select2Status.select2().val(this.transactionStatus).trigger('change')
                                    }, 1000)
                                }
                            }">
                                <label for="transactionStatus">Status</label>
                                <select name="status" id="status" x-model="transactionStatus" class="form-control"
                                    x-ref="status">
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    {{-- <option value="cancel">Cancel</option> --}}
                                    {{-- <option value="return">Return</option> --}}
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="transactionDate">Date</label>
                                <input type="date" name="transactionDate" id="transactionDate" class="form-control"
                                    x-model="transactionDate">
                            </div>
                            <div class="col-md-6">
                                <label for="transactionCode">Nomor Resi</label>
                                <input type="text" name="transactionCode" id="transactionCode" class="form-control"
                                    x-model="transactionCode">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="transactionNote">Note (No Transaction, etc)</label>
                        <textarea name="transactionNote" id="transactionNote" class="form-control" rows="5" x-model="transactionNote"></textarea>
                    </div>
                </div>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product Name</th>
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
                                <input type="number" class="form-control" x-model.lazy="item.quantity"
                                    x-on:input="updateCartItem(index, 'quantity', $event.target.value)"
                                    min="1" />
                            </td>

                            <!-- Price Input -->
                            <td>
                                <input type="number" class="form-control" x-model.lazy="item.price"
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
                <button class="btn btn-primary" x-on:click="saveCart()">Save</button>
            </div>
        </div>
    </div>
</div>
