<?php

namespace App\Http\Controllers\B2B;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Product;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\Notification;
use App\Models\B2BAddress;
use App\Models\User;
use App\Models\PrReserveStock;

class PurchaseRequestController extends Controller
{

    private function generateTransactionUid()
    {
        $timestamp = now()->format('YmdHis');
        return 'PR_' . $timestamp . '_' . Str::uuid()->toString();
    }

    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {
    //         // $status = $request->get('status');

    //         $purchaseRequests = PurchaseRequest::with(['items.product.productImages'])
    //             // ->when($status, fn($q) => $q->where('status', $status))
    //             ->where('status', 'pending')
    //             ->latest()
    //             ->get();

    //         $data = [];

    //         foreach ($purchaseRequests as $pr) {
    //             foreach ($pr->items as $item) {
    //                 $product = $item->product;
    //                 $image = optional($product->productImages->first())->image_path ?? '/assets/shop/img/noimage.png';

    //                 $data[] = [
    //                     'sku' => $product->sku,
    //                     'name' => $product->name,
    //                     'price' => number_format($product->price, 2),
    //                     'quantity' => $item->quantity,
    //                     'subtotal' => number_format($item->quantity * $product->price, 2),
    //                     'image' => '<img src="' . asset($image) . '" width="50" height="50">',
    //                     'status' => ucfirst($pr->status),
    //                     'created_at' => $item->created_at->toDateTimeString(),
    //                 ];
    //             }
    //         }

    //         return datatables()->of($data)->rawColumns(['image'])->make(true);
    //     }

    //     return view('pages.b2b.v_purchaseList', [
    //         'page' => 'Purchase Requests'
    //     ]);
    // }
    public function index(Request $request)
    {
        // 1️⃣ If user is NOT logged in → show login page
        if (!Auth::check()) {
            $page = 'Sign In';
            $companysettings = DB::table('company_settings')->first();

            return response()
                ->view('auth.login', compact('page', 'companysettings'))
                ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
        }

        // 2️⃣ If user is logged in → check their role
        $user = Auth::user();

        // Example role logic (adjust 'role' and role names to match your database)

        if ($user->role === 'b2b') {

            $userId = auth()->id();

            $purchaseRequests = PurchaseRequest::with(['items.product.productImages'])
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhere('status', 'pending');
                })
                ->where('customer_id', $userId)
                ->latest()
                ->get();

            $hasAddress = B2BAddress::where('user_id', $userId)->exists();

            return view('pages.b2b.v_purchaseList', [
                'page' => 'Purchase Requests',
                'purchaseRequests' => $purchaseRequests,
                'hasAddress' => $hasAddress
            ]);
        }
        //for returning to the dashboard
        return redirect()->route('home')->with('info', 'Redirected to your dashboard.');
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'product_id' => 'required|exists:products,id',
    //         'quantity'   => 'required|integer|min:1'
    //     ]);

    //     $userId = auth()->id();

    //     // Check if there is already a pending purchase request
    //     $pendingRequest = PurchaseRequest::where('customer_id', $userId)
    //         ->where('status', 'pending')
    //         ->get();

    //    if ($pendingRequest->isNotEmpty()) {
    //         return response()->json([
    //             'message' => 'You already have a pending purchase request. Please wait until it is processed before creating a new one.'
    //         ], 400);
    //     }

    //     // Create a new purchase request
    //     $purchaseRequest = PurchaseRequest::create([
    //         'customer_id' => $userId,
    //         'status' => null
    //     ]);

    //     $product = Product::findOrFail($request->product_id);
    //     $price = $this->calculateProductPrice($product);

    //     // Add the product item
    //     $purchaseRequest->items()->create([
    //         'product_id' => $request->product_id,
    //         'quantity'   => $request->quantity,
    //         'subtotal'   => $request->quantity * $price
    //     ]);

    //     // Notify sales officers
    //     $salesOfficers = User::where('role', 'salesofficer')->get();
    //     foreach ($salesOfficers as $officer) {
    //         Notification::create([
    //             'user_id' => $officer->id,
    //             'type'    => 'purchase_request',
    //             'message' => 'A new purchase request has been submitted by ' . auth()->user()->name .
    //                 '. <br><a href="' . route('salesofficer.purchase-requests.index') . '">Visit</a>',
    //         ]);
    //     }

    //     $items = $purchaseRequest->items()->with('product.productImages')->get();

    //     $mapped = $items->map(function ($item) {
    //         $product = $item->product;
    //         $price = $this->calculateProductPrice($product);
    //         return [
    //             'id'            => $item->id,
    //             'product_name'  => $product->name,
    //             'product_image' => asset(optional($product->productImages->first())->image_path ?? '/assets/shop/img/noimage.png'),
    //             'quantity'      => $item->quantity,
    //             'price'         => $price,
    //             'subtotal'      => $item->subtotal,
    //         ];
    //     });

    //     return response()->json([
    //         'message'        => 'Purchase request created successfully.',
    //         'items'          => $mapped->take(5),
    //         'total_quantity' => $items->sum('quantity'),
    //         'subtotal'       => $items->sum('subtotal'),
    //         'pending_count'  => $items->sum('quantity')
    //     ]);
    // }


    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $userId = auth()->id();
        // 🚫 Block adding new items if user has a pending PR
        $hasPendingPR = PurchaseRequest::where('customer_id', $userId)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingPR) {
            return response()->json([
                'message' => 'You already have a pending purchase request. Please wait until it is processed before creating a new one.'
            ], 400);
        } //new for blocking PR

        $purchaseRequest = PurchaseRequest::firstOrCreate([
            'customer_id' => $userId,
            'status' => null
        ]);

        $product = Product::findOrFail($request->product_id);

        // ✅ Check available stock
        $availableStock = $product->current_stock;

        $item = $purchaseRequest->items()->where('product_id', $request->product_id)->first();

        $requestedQty = $request->quantity + ($item ? $item->quantity : 0);

        if ($requestedQty > $availableStock) {
            return response()->json([
                'message' => "Not enough stock available. Requested: {$requestedQty}, Available: {$availableStock}"
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }

        $price = $this->calculateProductPrice($product); // always use discounted price if available

        if ($item) {
            $item->quantity += $request->quantity;
            $item->unit_price = $price; // ✅ always update unit price
            $item->subtotal = round($item->quantity * $price, 2);
            $item->save();
        } else {
            $purchaseRequest->items()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'unit_price' => $price, // ✅ store unit price on create
                'subtotal' => round($request->quantity * $price, 2)
            ]);
        }

        // Notify sales officers
        $salesOfficers = User::where('role', 'salesofficer')->get();
        foreach ($salesOfficers as $officer) {
            Notification::create([
                'user_id' => $officer->id,
                'type' => 'purchase_request',
                'message' => 'A new purchase request has been updated by ' . auth()->user()->name . '. <br><a href="' . route('salesofficer.purchase-requests.index') . '">Visit</a>',
            ]);
        }

        $items = $purchaseRequest->items()->with('product.productImages')->get();

        $mapped = $items->map(function ($item) {
            $product = $item->product;
            $price = $this->calculateProductPrice($product);
            return [
                'id' => $item->id,
                'product_name' => $product->name,
                'product_image' => asset(optional($product->productImages->first())->image_path ?? '/assets/shop/img/noimage.png'),
                'quantity' => $item->quantity,
                'price' => $price,
                'subtotal' => $item->subtotal,
            ];
        });

        return response()->json([
            'message' => 'Purchase request updated successfully.',
            'items' => $mapped->take(5),
            'total_quantity' => $items->sum('quantity'),
            'subtotal' => round($items->sum('subtotal'), 2),
            'pending_count' => $items->sum('quantity')
        ]);
    }

    public function updateItem(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $item = PurchaseRequestItem::with('purchaseRequest', 'product')->findOrFail($id);

        if (
            $item->purchaseRequest->customer_id !== auth()->id() ||
            $item->purchaseRequest->status !== null
        ) {
            return response()->json([
                'message' => 'You can only update items from pending purchase requests.'
            ], 403);
        }

        $price = $this->calculateProductPrice($item->product);

        $item->quantity = $request->quantity;
        $item->unit_price = $price; // ✅ keep it updated
        $item->subtotal = round($item->quantity * $price, 2);
        $item->save();

        return response()->json([
            'message' => 'Quantity updated successfully.',
            'subtotal' => $item->subtotal
        ]);
    }

    // public function submitItem(Request $request)
    // {
    //     $user = Auth::user();

    //     // Validate the request
    //     $request->validate([
    //         'prids' => 'required|array',
    //         'prids.*' => 'integer|exists:purchase_requests,id',
    //         'expected_delivery_date' => 'nullable|date'
    //     ]);

    //     // Get the purchase requests to check ownership
    //     $purchaseRequests = PurchaseRequest::with(['items.product']) // include items and products
    //         ->where('customer_id', $user->id)
    //         ->whereIn('id', $request->prids)
    //         ->whereNull('status')
    //         ->get();

    //     if ($purchaseRequests->isEmpty()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No valid purchase requests found to submit.'
    //         ], 404);
    //     }

    //     // Pre-check stock availability for all PR items
    //     foreach ($purchaseRequests as $pr) {
    //         foreach ($pr->items as $item) {
    //             $product = $item->product;
    //             if (!$product) {
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Product not found for PR item #' . $item->id
    //                 ], 400);
    //             }

    //             // ✅ Recalculate price and subtotal dynamically
    //             $price = $product->discount > 0 && $product->discounted_price
    //                 ? $product->discounted_price
    //                 : $product->price;

    //             $item->subtotal = $item->quantity * $price; // ensure latest subtotal is used

    //             // 🔹 Optional: save updated subtotal in DB for data consistency
    //             // (only if you want to store updated value permanently)
    //             $item->update(['subtotal' => $item->subtotal]);

    //             // $availableStock = $product->stockBatches()->sum('remaining_quantity');


    //             // if ($availableStock < $item->quantity) {
    //             //     return response()->json([
    //             //         'success' => false,
    //             //         'message' => 'Insufficient stock for product: ' . $product->name
    //             //     ], 400);
    //             // }

    //             // ✅ Get batches with available and non-expired stock
    //             $batches = $product->stockBatches()
    //                 ->where('remaining_quantity', '>', 0)
    //                 ->where(function ($q) {
    //                     $q->whereNull('expiry_date')
    //                         ->orWhere('expiry_date', '>=', now());
    //                 })
    //                 ->orderBy('received_date', 'asc')
    //                 ->get();

    //             $requestedQty = (int) $product['qty'];

    //             $enoughStock = false;

    //             foreach ($batches as $batch) {
    //                 $available = (int) $batch->remaining_quantity;

    //                 // ✅ Only check one batch at a time
    //                 if ($requestedQty <= $available) {
    //                     $enoughStock = true;
    //                     break; // sufficient stock in this batch
    //                 } else {
    //                     // Not enough in this batch — fail immediately
    //                     return response()->json([
    //                         'success' => false,
    //                         'message' => 'Insufficient stock for product: ' . $product->name .
    //                             ' in Batch #' . $batch->batch .
    //                             ' (Available: ' . $available . ', Requested: ' . $requestedQty . ')'
    //                     ], 400);
    //                 }
    //             }

    //             // If there are no batches with enough stock
    //             if (!$enoughStock) {
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'No available batch with sufficient stock for product: ' . $product->name,
    //                 ], 400);
    //             }
    //         }
    //     }

    //     $updatedCount = PurchaseRequest::whereIn('id', $purchaseRequests->pluck('id'))
    //         ->update([
    //             'status' => 'pending',
    //             'b2b_delivery_date' => $request->expected_delivery_date
    //         ]);

    //     foreach ($purchaseRequests as $pr) {
    //         PrReserveStock::reserveForPurchaseRequest($pr);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Your purchase requests are being processed. Please wait for approval.',
    //         'data' => [
    //             'updated_count' => $updatedCount
    //         ]
    //     ]);
    // }

    public function submitItem(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'prids' => 'required|array',
            'prids.*' => 'integer|exists:purchase_requests,id',
            'expected_delivery_date' => 'nullable|date'
        ]);

        $purchaseRequests = PurchaseRequest::with(['items.product'])
            ->where('customer_id', $user->id)
            ->whereIn('id', $request->prids)
            ->whereNull('status')
            ->get();

        if ($purchaseRequests->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No valid purchase requests found to submit.'
            ], 404);
        }

        DB::beginTransaction(); // ✅ Begin transaction

        try {
            // 🔹 Pre-check all stocks before doing any update
            foreach ($purchaseRequests as $pr) {
                foreach ($pr->items as $item) {
                    $product = $item->product;

                    if (!$product) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Product not found for PR item #' . $item->id
                        ], 400);
                    }

                    // ✅ Dynamic price calculation
                    $price = $product->discount > 0 && $product->discounted_price
                        ? $product->discounted_price
                        : $product->price;

                    $item->subtotal = $item->quantity * $price;
                    $item->update(['subtotal' => $item->subtotal]);

                    // ✅ Get only valid batches
                    $batches = $product->stockBatches()
                        ->where('remaining_quantity', '>', 0)
                        ->where(function ($q) {
                            $q->whereNull('expiry_date')
                                ->orWhere('expiry_date', '>=', now());
                        })
                        ->orderBy('received_date', 'asc')
                        ->get();

                    $requestedQty = (int) $item->quantity; // ✅ FIXED: use item qty
                    $remainingToAllocate = $requestedQty;

                    foreach ($batches as $batch) {
                        if ($remainingToAllocate <= 0) break;

                        $available = (int) $batch->remaining_quantity;

                        if ($available >= $remainingToAllocate) {
                            // ✅ Batch can fulfill remaining qty
                            $remainingToAllocate = 0;
                        } else {
                            // 🔸 Still not enough — keep checking next batch
                            $remainingToAllocate -= $available;
                        }
                    }

                    // ❌ If after all batches, still not enough stock
                    if ($remainingToAllocate > 0) {
                        DB::rollBack(); // ✅ Stop everything
                        return response()->json([
                            'success' => false,
                            'message' => 'Insufficient stock for product: ' . $product->name .
                                ' (Requested: ' . $requestedQty . ')'
                        ], 400);
                    }
                }
            }

            // ✅ If we reach here, stock is sufficient for all products
            $updatedCount = PurchaseRequest::whereIn('id', $purchaseRequests->pluck('id'))
                ->update([
                    'status' => 'pending',
                    'b2b_delivery_date' => $request->expected_delivery_date
                ]);

            foreach ($purchaseRequests as $pr) {
                PrReserveStock::reserveForPurchaseRequest($pr);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Your purchase requests are being processed. Please wait for approval.',
                'data' => ['updated_count' => $updatedCount]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error occurred: ' . $e->getMessage()
            ], 500);
        }
    }


    public function deleteItem($id)
    {
        $item = PurchaseRequestItem::with('purchaseRequest')->findOrFail($id);
        $purchaseRequest = $item->purchaseRequest;

        if (
            $purchaseRequest->customer_id !== auth()->id() ||
            $purchaseRequest->status !== null
        ) {
            return response()->json([
                'message' => 'You can only delete items from waiting purchase requests.'
            ], 403);
        }

        $purchaseRequest->loadCount('items');

        if ($purchaseRequest->items_count === 1) {
            $purchaseRequest->delete();
            $item->delete();

            return response()->json([
                'message' => 'Item removed. Purchase request also deleted.',
                'purchase_request_deleted' => true
            ]);
        }

        $item->delete();

        return response()->json([
            'message' => 'Item removed from purchase request.',
            'purchase_request_deleted' => false
        ]);
    }

    private function calculateProductPrice(Product $product)
    {
        return ($product->discount && $product->discount > 0 && $product->discounted_price)
            ? $product->discounted_price
            : $product->price;
    }
}
