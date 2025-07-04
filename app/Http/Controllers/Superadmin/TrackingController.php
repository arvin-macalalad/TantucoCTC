<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\B2BAddress;
use App\Models\PurchaseRequest;
use App\Models\Delivery;
use App\Models\User;

class TrackingController extends Controller
{
    public function submitted_po(Request $request)
    {
        if ($request->ajax()) {
            $query = PurchaseRequest::with(['customer', 'items.product'])
                ->where('status', 'po_submitted')
                ->latest();

            return DataTables::of($query)
                ->addColumn('customer_name', function ($pr) {
                    return optional($pr->customer)->name;
                })
                ->addColumn('total_items', function ($pr) {
                    return $pr->items->sum('quantity');
                })
                ->addColumn('grand_total', function ($pr) {
                    $total = $pr->items->sum(fn($item) => $item->quantity * ($item->product->price ?? 0));
                    return '₱' . number_format($total, 2);
                })
                ->editColumn('created_at', function ($pr) {
                    return Carbon::parse($pr->created_at)->format('Y-m-d H:i:s');
                })
                ->addColumn('action', function ($pr) {
                    return '
                        <button type="button" class="btn btn-sm btn-inverse-primary view-pr p-2" data-id="' . $pr->id . '" title="View Purchase Request">
                            <i class="link-icon" data-lucide="eye"></i> View PR
                        </button>
                        <button type="button" class="btn btn-sm btn-inverse-info process-so p-2" data-id="' . $pr->id . '" title="Create Sales Order">
                            <i class="link-icon" data-lucide="plus-square"></i> Create SO
                        </button>
                    ';
                })
                ->make(true);
        }

        return view('pages.superadmin.v_submittedPO', [
            'page' => 'Submitted Order',
            'pageCategory' => 'Tracking',
        ]);
    }

    public function show($id)
    {
        $pr = PurchaseRequest::with(['items.product.productImages'])->findOrFail($id);
        $html = view('components.pr-items', compact('pr'))->render();

        return response()->json(['html' => $html]);
    }

    public function process_so($id)
    {
        DB::beginTransaction();

        try {
            $pr = PurchaseRequest::with(['items.product'])->findOrFail($id);

            if ($pr->status !== 'po_submitted') {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Purchase Request is not in a processable state.'
                ], 400);
            }

            // Get active delivery address of the user
            $activeAddress = B2BAddress::where('user_id', $pr->customer_id)
                ->where('status', 'active')
                ->first();

            if (!$activeAddress) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'No active delivery address found for this customer.'
                ], 400);
            }

            $total = 0;
            foreach ($pr->items as $item) {
                $total += $item->quantity * ($item->product->price ?? 0);
            }

            $orderNumber = $pr->id . '-' . strtoupper(uniqid());

            // Create the Order
            $order = Order::create([
                'user_id' => $pr->customer_id,
                'order_number' => $orderNumber,
                'total_amount' => $total,
                'b2b_address_id' => $activeAddress->id,
                'ordered_at' => now()
            ]);

            foreach ($pr->items as $item) {
                $price = $item->product->price ?? 0;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $price,
                    'subtotal' => $price * $item->quantity,
                ]);
            }

            $totalQuantity = $pr->items->sum('quantity');

            Delivery::create([
                'order_id' => $order->id,
                'quantity' => $totalQuantity,
            ]);

            $pr->status = 'so_created';
            $pr->save();

            DB::commit();

            return response()->json([
                'type' => 'success',
                'message' => 'Sales Order created successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'type' => 'error',
                'message' => 'Failed to process sales order. ' . $e->getMessage()
            ], 500);
        }
    }

    public function delivery_personnel(Request $request)
    {

        $deliveryman_select = User::select('name', 'id')->where('role', 'deliveryrider/admin')->get();

        if ($request->ajax()) {
            $query = Order::with([
                'user',
                'b2bAddress',
                'delivery.deliveryUser',
                'items.product'
            ])->whereHas('delivery')->latest();

            return DataTables::of($query)
                ->addColumn('customer_name', function ($order) {
                    return optional($order->user)->name;
                })
                ->addColumn('customer_address', function ($order) {
                    return optional($order->b2bAddress)->full_address ?? 'No Address';
                })
                ->addColumn('delivery_man', function ($order) {
                    return optional(optional($order->delivery)->deliveryUser)->name ?? '<span class="text-danger">Not Assigned</span>';
                })
                ->addColumn('order_number', function ($order) {
                    return $order->order_number ?? '-';
                })
                ->addColumn('total_amount', function ($order) {
                    return '₱' . number_format($order->total_amount ?? 0, 2);
                })
                ->addColumn('total_items', function ($order) {
                    return $order->items->sum('quantity');
                })
                ->addColumn('action', function ($order) {
                    $delivery = optional($order->delivery);
                    $deliveryId = $delivery->id;
                    $orderNumber = $order->order_number;
                    $status = $delivery->status;

                    if ($status === 'pending') {
                        return '
                            <button type="button"
                                    class="btn btn-sm btn-inverse-success assign-delivery p-2"
                                    data-id="' . $deliveryId . '"
                                    data-order-number="' . $orderNumber . '"
                                    title="Assign Delivery">
                                <i class="link-icon" data-lucide="user-check"></i> Assign Delivery
                            </button>
                        ';
                    }

                    return '<span class="badge bg-secondary text-capitalize">' . e($status ?? 'N/A') . '</span>';
                })

                ->rawColumns(['action', 'delivery_man', 'customer_address'])
                ->make(true);
        }

        return view('pages.superadmin.v_deliveryPersonnel', [
            'page' => 'Delivery Personnel',
            'pageCategory' => 'Tracking',
            'deliveryman_select' => $deliveryman_select
        ]);
    }

    public function assign_delivery_personnel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_id' => 'required|exists:deliveries,id',
            'pr_id' => 'required|exists:purchase_requests,id',
            'delivery_rider_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $delivery = Delivery::findOrFail($request->delivery_id);
            $delivery->delivery_rider_id = $request->delivery_rider_id;
            $delivery->status = 'assigned';
            $delivery->save();

            $pr = PurchaseRequest::findOrFail($request->pr_id);
            $pr->status = 'delivery_in_progress';
            $pr->save();

            return response()->json([
                'type' => 'success',
                'message' => 'Delivery personnel assigned successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to assign delivery personnel. ' . $e->getMessage(),
            ], 500);
        }
    }
}
