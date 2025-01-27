<div>
    <form wire:submit.prevent="processReturn">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Purchased Quantity</th>
                    <th>Return Quantity</th>
                    <th>Refund Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactionItems as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>
                            <input type="number" wire:model="returns.{{ $item->id }}.quantity" min="0" max="{{ $item->quantity }}">
                        </td>
                        <td>
                            {{ $returns[$item->id]['quantity'] * $item->price ?? 0 }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit">Process Return</button>
    </form>
</div>
