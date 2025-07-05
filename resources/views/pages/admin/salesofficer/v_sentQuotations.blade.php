@extends('layouts.dashboard')

@section('content')
<div class="page-content container-xxl">

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            @component('components.card', [
            'title' => 'Sent Quotation List',
            'cardtopAddButton' => false,
            'cardtopAddButtonTitle' => '',
            'cardtopAddButtonId' => '',
            'cardtopButtonMode' => ''
            ])

            @component('components.table', [
            'id' => 'sentQuotationTable',
            'thead' => '
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Total Items</th>
                <th>Grand Total</th>
                <th>Date Created</th>
                <th>Status</th>
            </tr>
            '
            ])
            @endcomponent

            @endcomponent
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ route('secure.js', ['filename' => 'salesofficer_sentquotations']) }}"></script>
@endpush