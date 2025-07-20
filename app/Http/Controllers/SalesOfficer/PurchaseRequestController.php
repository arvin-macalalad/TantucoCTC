<?php

namespace App\Http\Controllers\SalesOfficer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

use App\Models\Notification;
use App\Models\PurchaseRequest;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PurchaseRequest::with(['customer', 'items.product'])
                ->where('status', 'pending')
                ->latest();

            return DataTables::of($query)
                ->addColumn('customer_name', function ($pr) {
                    return optional($pr->customer)->name;
                })
                ->addColumn('total_items', function ($pr) {
                    return $pr->items->sum('quantity');
                })
                ->addColumn('grand_total', function ($pr) {
                    $subtotal = $pr->items->sum(fn($item) => $item->quantity * ($item->product->price ?? 0));
                    $vatRate = $pr->vat ?? 0; // VAT percentage
                    $vatAmount = $subtotal * ($vatRate / 100);
                    $deliveryFee = $pr->delivery_fee ?? 0;
                    $total = $subtotal + $vatAmount + $deliveryFee;

                    return '₱' . number_format($total, 2);
                })
                ->editColumn('created_at', function ($pr) {
                    return Carbon::parse($pr->created_at)->format('Y-m-d H:i:s');
                })
                ->addColumn('action', function ($pr) {
                    return '<button type="button" class="btn btn-sm btn-inverse-primary  review-pr p-2" data-id="' . $pr->id . '">
                            <i class="link-icon" data-lucide="eye"></i> Review PR
                        </button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.admin.salesofficer.v_purchaseList', [
            'page' => 'Pending Purchase Requests'
        ]);
    }

    public function show($id)
    {
        $pr = PurchaseRequest::with(['items.product.productImages'])->findOrFail($id);
        $html = view('components.pr-items', compact('pr'))->render();

        return response()->json(['html' => $html]);
    }

    public function updateSendQuotation(Request $request, $id)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($id);

        if ($purchaseRequest->status !== 'pending') {
            return response()->json([
                'type' => 'warning',
                'message' => 'Only pending requests can be converted to quotations.'
            ]);
        }

        // Optional: Validate vat and delivery_fee
        $validated = $request->validate([
            // 'vat' => 'nullable|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
        ]);

        // Update with additional fees + status
        $purchaseRequest->update([
            'status' => 'quotation_sent',
            'vat' => 12,
            'delivery_fee' => $validated['delivery_fee'] ?? null,
        ]);

        // Notify customer
        if ($purchaseRequest->customer) {
            Notification::create([
                'user_id' => $purchaseRequest->customer->id,
                'type' => 'quotation_sent',
                'message' => 'A quotation has been sent for your purchase request #' . $purchaseRequest->id,
            ]);
        }

        return response()->json([
            'type' => 'success',
            'message' => 'Quotation sent successfully!'
        ]);
    }
}
