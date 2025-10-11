<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Quotation</title>
    <style>
        /* GENERAL STYLES */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 30px;
        }

        /* COMPANY HEADER */
        .company-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-header img {
            max-width: 120px;
            height: auto;
            margin-bottom: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .company-header h1 {
            font-size: 18px;
            margin: 5px 0;
        }

        .company-contact {
            font-size: 12px;
        }

        /* SECTION STYLES */
        .section {
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 20px;
        }

        .section h3 {
            font-size: 14px;
            margin: 0 0 10px 0;
            text-decoration: underline;
        }

        /* TABLE STYLES */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .table-bordered th, .table-bordered td {
            border: 1px solid #000;
            padding: 6px;
        }

        .table-bordered th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .totals td {
            font-weight: bold;
            padding: 6px;
            font-size: 12px;
        }
        
        .grand-total-row td {
            font-size: 13px;
        }

        .disclaimer-note {
            font-size: 10px;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="company-header">
    <img src="{{ public_path($companySettings->company_logo ?? 'assets/dashboard/images/noimage.png') }}" alt="Company Logo">
    <h1>TANTUCO CONSTRUCTION & TRADING CORPORATION</h1>
    <div class="company-contact">
        Balubal, Sariaya, Quezon<br>
        VAT Reg TIN: {{ $companySettings->company_vat_reg ?? 'N/A' }}<br>
        Tel: {{ $companySettings->company_tel ?? 'N/A' }} / Telefax: {{ $companySettings->company_telefax ?? 'N/A' }}
    </div>
</div>

<div class="section">
    <h3>Purchase Quotation</h3>
    <p>
        <strong>No:</strong> {{ $quotation->id ?? 'N/A' }}-{{ date('Ymd', strtotime($quotation->created_at)) }}<br>
        <strong>Date Issued:</strong> {{ $quotation->date_issued ?? now()->toDateString() }}<br>
        <strong>Disclaimer:</strong>
        <span class="disclaimer-note">
            This document is system-generated and provided for internal/business reference only. 
            It is not BIR-accredited and shall not be considered as an official receipt or invoice 
            for tax or accounting purposes.
        </span>
    </p>
</div>

<div class="section">
    <h3>Billed To</h3>
    <p>
        <strong>Name:</strong> {{ $quotation->customer->name ?? 'N/A' }}<br>
        <strong>Address:</strong> {{ $b2bAddress->full_address ?? 'N/A' }}<br>
        @if(!empty(trim($b2bAddress?->address_notes ?? '')))
            <strong>Address Note:</strong> {{ $b2bAddress->address_notes }}<br>
        @endif
        <strong>TIN:</strong> {{ $b2bReqDetails->tin_number ?? 'N/A' }}<br>
        <strong>Business Style:</strong> {{ $b2bReqDetails->business_name ?? 'N/A' }}
    </p>
    <p style="margin-top: 10px;">
        <strong>Prepared By:</strong> {{ $superadmin->name ?? 'N/A' }}<br>
        <strong>Authorized Representative:</strong> {{ $salesOfficer->name ?? 'N/A' }}
    </p>
</div>

<h3>Quotation Items</h3>
<table class="table-bordered">
    <thead>
        <tr>
            <th>SKU</th>
            <th>Product Name</th>
            <th class="text-center">Qty</th>
            <th class="text-right">Unit Price</th>
            <th class="text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @php $subtotal = 0; @endphp
        @foreach ($quotation->items as $item)
            @php
                $price = $item->product->price ?? 0;
                $discount = $item->product->discount ?? 0;

                // Apply discount if applicable
                $discountedPrice = $discount > 0
                    ? $price - ($price * ($discount / 100))
                    : $price;

                $itemTotal = $discountedPrice * $item->quantity;
                $subtotal += $itemTotal;
            @endphp

            <tr>
                <td>{{ $item->product->sku }}</td>
                <td>{{ $item->product->name }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">
                    ₱{{ number_format($discountedPrice, 2) }}
                    @if($discount > 0)
                        <br>
                        <small>({{ $discount }}% off)</small>
                    @endif
                </td>
                <td class="text-right">₱{{ number_format($itemTotal, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@php
    $vatRate = $quotation->vat ?? 0;
    $vat = $subtotal * ($vatRate / 100);
    $delivery_fee = $quotation->delivery_fee ?? 0;
    $total = $subtotal + $vat + $delivery_fee;
    $amountPaid = 0;
@endphp

<table style="margin-top: 15px;">
    <tr class="totals">
        <td style="width: 80%;" class="text-right">Subtotal:</td>
        <td class="text-right">₱{{ number_format($subtotal, 2) }}</td>
    </tr>
    <tr class="totals">
        <td class="text-right">VAT ({{ $vatRate }}%):</td>
        <td class="text-right">₱{{ number_format($vat, 2) }}</td>
    </tr>
    <tr class="totals">
        <td class="text-right">Delivery Fee:</td>
        <td class="text-right">₱{{ number_format($delivery_fee, 2) }}</td>
    </tr>
    <tr class="totals">
        <td class="text-right">Amount Paid:</td>
        <td class="text-right">₱{{ number_format($amountPaid, 2) }}</td>
    </tr>
    <tr class="totals grand-total-row">
        <td class="text-right"><strong>Grand Total:</strong></td>
        <td class="text-right"><strong>₱{{ number_format($total, 2) }}</strong></td>
    </tr>
</table>

<p style="margin-top: 20px;">
    @php
        $b2bDate = $quotation->b2b_delivery_date;
        $delivery_date = null;
        $show_note = false;
        $note_message = '';

        if (!is_null($b2bDate)) {
            $delivery_date = \Carbon\Carbon::parse($b2bDate)->format('F j, Y');
            $diffDays = \Carbon\Carbon::parse($b2bDate)->diffInDays(now());
            if ($diffDays < 2) {
                $show_note = true;
                $note_message = "Selected date is preferred only, not guaranteed (due to volume).";
            }
        } elseif ($quotation->status !== 'pending') {
            $start = now()->addDays(1)->format('F j, Y');
            $end = now()->addDays(3)->format('F j, Y');
            $delivery_date = $start . ' to ' . $end;
            $show_note = true;
            $note_message = "Expect delay if too many orders since we are preparing it.";
        }
    @endphp

    <strong>Delivery Date:</strong> {{ $delivery_date ?? 'No date provided' }}<br>
    @if($show_note && !empty($note_message))
        <span class="disclaimer-note">Note: {{ $note_message }}</span><br>
    @endif
</p>

</body>
</html>
