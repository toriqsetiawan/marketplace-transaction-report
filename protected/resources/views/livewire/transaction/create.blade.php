{{-- <div>
    <div class="row">
        <!-- Product Search Section -->
        <div class="col-md-6">
            <h4>Product Search</h4>
            <input type="text" class="form-control" placeholder="Search product or scan barcode..."
                wire:model.debounce.300ms="searchTerm" />

            <div class="list-group" style="max-height: 300px; overflow-y: auto;">
                @foreach ($filteredProducts as $product)
                    <a href="javascript:void(0)" class="list-group-item"
                        wire:click="addToCart({{ $product['id'] }})">
                        <p>Supplier: <i>{{ $product['supplier']['name'] }}</i></p>
                        <strong>{{ $product['nama'] }}</strong>
                        <br />
                        <small>Price: Rp {{ number_format($product['harga_jual']) }}</small>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Cart Section -->
        <div class="col-md-6">
            <h4>Cart</h4>
            <ul class="list-group" style="max-height: 300px; overflow-y: auto;">
                @foreach ($cart as $index => $item)
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-xs-6">
                                <strong>{{ $item['nama'] }}</strong>
                                <div>
                                    <select class="form-control"
                                        wire:model="cart.{{ $index }}.selectedVariant"
                                        wire:change="updateCartItem({{ $index }}, 'selectedVariant', $event.target.value)">
                                        @foreach ($item['variants'] as $variant)
                                            <option value="{{ $variant['id'] }}">
                                                {{ $variant['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <input type="number" class="form-control"
                                    wire:model.debounce.500ms="cart.{{ $index }}.quantity"
                                    wire:change="updateCartItem({{ $index }}, 'quantity', $event.target.value)" min="1" />
                            </div>
                            <div class="col-xs-2">
                                <input type="number" class="form-control"
                                    wire:model.debounce.500ms="cart.{{ $index }}.price"
                                    wire:change="updateCartItem({{ $index }}, 'price', $event.target.value)" step="100" />
                            </div>
                            <div class="col-xs-2">
                                <button class="btn btn-danger btn-xs" wire:click="removeFromCart({{ $index }})">Remove</button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="text-right">
                <strong>Total: Rp {{ $cartTotal }}</strong>
            </div>
        </div>
    </div>
</div> --}}

<div class="container" x-data="{
    searchTerm: '',
    products: [], // This should be loaded dynamically via an API call
    filteredProducts: [],
    cart: [],

    searchProduct() {
        const term = this.searchTerm.toLowerCase();

        // Simulate a fetch call to search products
        this.filteredProducts = $wire.searchProduct(term)
            .then((result) => {
                return result ?? [];
            })
            .catch((error) => {
                console.error('Error searching products:', error);
            })
    },

    addToCart(product) {
        // Check if the product already exists in the cart
        const existingItem = this.cart.find(item => item.id === product.id);

        if (!existingItem) {
            // Handle variants gracefully
            const hasVariants = Array.isArray(product.variants) && product.variants.length > 0;

            console.log(product.variants);

            const cartItem = {
                id: product.id,
                nama: product.nama,
                price: product.harga_jual,
                quantity: 1,
                variants: hasVariants ?
                    product.variants.map(variant => ({
                        id: variant.id,
                        label: `${variant.color ?? 'No Color'} / ${variant.size ?? 'No Size'}`,
                        price: variant.price ?? product.harga_jual, // Fallback to product price if variant price is missing
                        stock: variant.stock ?? 0, // Default stock to 0 if missing
                    })) :
                    [], // If no variants, keep it as an empty array
                selectedVariant: hasVariants ? product.variants[0]?.id : null, // Select the first variant by default
            };

            this.cart.push(cartItem);
        } else {
            // If the product already exists, just increment the quantity
            existingItem.quantity++;
        }
    },

    removeFromCart(index) {
        this.cart.splice(index, 1);
    },

    updateCartItem(index) {
        const item = this.cart[index];
        // You can add logic here for recalculations if needed
    },

    calculateTotal() {
        return this.cart.reduce((total, item) =>
            total + (item.price * item.quantity), 0
        );
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
                        <p>Supplier: <i x-text="product.supplier.name"></i></p>
                        <strong x-text="product.nama"></strong>
                        <br />
                        <small>Price: Rp <span x-text="product.harga_jual"></span></small>
                    </a>
                </template>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Cart Section -->
        <div class="col-md-12">
            <h4>Cart</h4>
            <ul class="list-group" style="max-height: 300px; overflow-y: auto;">
                <template x-for="(item, index) in cart" :key="index">
                    <li class="list-group-item">
                        <div class="row">
                            <!-- Product Name and Variant Selection -->
                            <div class="col-xs-6">
                                <strong x-text="item.nama"></strong>
                                <div>
                                    <select class="form-control" x-model="item.selectedVariant"
                                        x-on:change="updateCartItem(index)">
                                        <template x-for="variant in item.variants" :key="variant.id">
                                            <option :value="variant.id" x-text="variant.label"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <!-- Quantity Input -->
                            <div class="col-xs-2">
                                <input type="number" class="form-control" x-model.number="item.quantity"
                                    x-on:input="updateCartItem(index)" min="1" placeholder="Qty" />
                            </div>

                            <!-- Price Input -->
                            <div class="col-xs-2">
                                <input type="number" class="form-control" x-model.number="item.price"
                                    x-on:input="updateCartItem(index)" step="100" placeholder="Price" />
                            </div>

                            <!-- Remove Button -->
                            <div class="col-xs-2 text-right">
                                <button class="btn btn-danger btn-xs" x-on:click="removeFromCart(index)">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </li>
                </template>
            </ul>

            <!-- Cart Total -->
            <div class="text-right mt-3">
                <strong>Total: Rp <span x-text="calculateTotal()"></span></strong>
            </div>
        </div>
    </div>
</div>
