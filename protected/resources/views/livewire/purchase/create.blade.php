<div class="container box" x-data="{
    searchTerm: '',
    products: [],
    filteredProducts: [],
    cart: @entangle('cart'),
    purchaseCode: @entangle('purchaseCode'),
    purchaseNote: @entangle('purchaseNote'),
    purchaseStatus: @entangle('purchaseStatus'),
    purchaseDate: @entangle('purchaseDate'),

    searchProduct() {
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

        product.variants.forEach((variant, index) => {
            let selectedVariant = hasVariants ?
                product.variants.find(v => v.id == selectedVariantId) || product.variants[index] :
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
                    price: product.harga_beli,
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
        })

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
                    window.location.href = '/purchase';
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
                        <p>Harga mitra: <span x-text="formatPrice(product.harga_beli)"></span></p>
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
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" x-data="{
                                    init() {
                                        setTimeout(() => {
                                            this.select2Status = $(this.$refs.status).select2();
                                            this.select2Status.on('select2:select', (event) => {
                                                this.purchaseStatus = event.params.data.id;
                                            });
                                            this.select2Status.on('select2:unselect', (event) => {
                                                this.purchaseStatus = null
                                            });
                                            this.$watch('purchaseStatus', (value) => {
                                                this.select2Status.select2().val(this.purchaseStatus).trigger('change')
                                            });
                                        }, 1000)
                                    }
                                }">
                                    <label for="purchaseStatus">Status</label>
                                    <select name="status" id="status" x-model="purchaseStatus" class="form-control"
                                        x-ref="status">
                                        <option value="pending">Pending</option>
                                        <option value="complete">Complete</option>
                                        <option value="cancel">Cancel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="purchaseDate">Date</label>
                                <input type="date" name="purchaseDate" id="purchaseDate" class="form-control"
                                    x-model="purchaseDate">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="purchaseNote">Note</label>
                        <textarea name="purchaseNote" id="purchaseNote" class="form-control" rows="5" x-model="purchaseNote"></textarea>
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
                                        <option value=""></option>
                                        <template x-for="variant in item.variants" :key="variant.id">
                                            <option :value="variant.id"
                                                x-text="Object.values(variant.attributes).join(' / ')"></option>
                                        </template>
                                    </select>
                                </template>
                                <template x-if="!item.variants.length">
                                    <span>-</span>
                                </template>
                            </td>

                            <!-- Quantity Input -->
                            <td>
                                <input type="number" class="form-control" x-model.debounce.500ms="item.quantity"
                                    x-on:input="updateCartItem(index, 'quantity', $event.target.value)"
                                    min="1" />
                            </td>

                            <!-- Price Input -->
                            <td>
                                <input type="number" class="form-control" x-model.debounce.500ms="item.price"
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
