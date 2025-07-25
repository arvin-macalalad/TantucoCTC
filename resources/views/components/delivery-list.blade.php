@if(!empty($deliveries) && $deliveries->count() > 0)
<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Address</th>
                <th>Status</th>
                <th>Total Items</th>
                <th>Total Amount</th>
                <th>Items</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deliveries as $delivery)

            @php
            $fullAddress = $delivery->b2bAddress->full_address ?? 'N/A';
            $shortAddress = strlen($fullAddress) > 20 ? substr($fullAddress, 0, 20) . '...' : $fullAddress;
            @endphp

            <tr>
                <td>{{ $delivery->order_number }}</td>
                <td>{{ $delivery->user->name ?? 'N/A' }}</td>
                <td>
                    <span class="view-full-address text-primary" style="cursor:pointer;" data-address="{{ e($fullAddress) }}" title="Click to view full address">
                        {{ e($shortAddress) }}
                    </span>
                </td>
                <td>
                    <span class="badge bg-warning">
                        {{ $delivery->delivery->status ?? 'N/A' }}
                    </span>
                </td>
                <td>{{ $delivery->items->sum('quantity') }}</td>
                <td>₱{{ number_format($delivery->items->sum(function ($item) {
                    return $item->quantity * ($item->product->price ?? 0);
                }), 2) }}</td>
                <td>
                    <ul class="mb-0">
                        @foreach($delivery->items as $item)
                        <li>{{ $item->product->name ?? 'Unknown Product' }} x{{ $item->quantity }}</li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    <button
                        type="button"
                        class="btn btn-sm btn-inverse-primary pickup-btn"
                        data-delivery-id="{{ $delivery->delivery->id ?? '' }}">
                        Pick Up
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="text-center mb-3">No deliveries assigned to you.</div>
@endif