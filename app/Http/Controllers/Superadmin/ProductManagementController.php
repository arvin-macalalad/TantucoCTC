<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class ProductManagementController extends Controller
{
    public function index(Request $request)
    {
        $page = 'Product Management';
        $pageCategory = 'Management';
        $user = User::getCurrentUser();
        
        $category_select = Category::select('name', 'id')->get();

        if ($request->ajax()) {
           $products = Product::with('inventories', 'category')->select(['id', 'sku', 'name', 'description', 'price', 'expiry_date', 'created_at', 'category_id']);
            
            return DataTables::of($products)
                ->addColumn('current_stock', fn($row) => $row->current_stock)
                ->addColumn('category', fn($row) => optional($row->category)->name ?? 'N/A')
                ->addColumn('action', function ($row) {
                    return '
                        <button type="button" class="btn btn-sm btn-info view-details p-2" data-id="' . $row->id . '"><i class="link-icon" data-lucide="eye"></i></button>
                        <button type="button" class="btn btn-sm btn-inverse-light mx-1 edit p-2" data-id="' . $row->id . '"><i class="link-icon" data-lucide="edit-3"></i></button>
                        <button type="button" class="btn btn-sm btn-inverse-danger delete p-2" data-id="' . $row->id . '"><i class="link-icon" data-lucide="trash-2"></i></button>
                    ';
                })
                ->make(true);
        }

        return view('pages.superadmin.v_productManagement', compact('page', 'pageCategory', 'category_select'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'expiry_date' => 'nullable|date',
            'category_id' => 'required|numeric',
            'description' => 'nullable|string',
            'images.*' => 'image|mimes:png,jpg,webp|max:2048',
        ]);

        $validated['sku'] = strtoupper(uniqid('SKU-'));

        DB::transaction(function () use ($request, $validated) {
            $product = Product::create($validated);
            $mainImageIndex = $request->input('main_image_index', 0);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $filename = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('assets/upload/products'), $filename);

                    $product->productImages()->create([
                        'image_path' => 'assets/upload/products/' . $filename,
                        'is_main' => $index == $mainImageIndex,
                    ]);
                }
            }
        });

        return response()->json([
            'type' => 'success',
            'message' => 'Product created successfully.'
        ]);
    }

    public function show($id)
    {
        $product = Product::with(['productImages', 'inventories'])->findOrFail($id);

        $totalStock = $product->inventories->sum(fn($inv) =>
            $inv->type === 'in' ? $inv->quantity : -$inv->quantity
        );

        return response()->json([
            'product' => $product,
            'images' => $product->productImages,
            'inventories' => $product->inventories,
            'stock' => $totalStock,
        ]);
    }

    public function edit($id)
    {
        $product = Product::with('productImages', 'inventories')->findOrFail($id);

        return response()->json([
            'product' => $product,
            'images' => $product->productImages,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'expiry_date' => 'nullable|date',
            'category_id' => 'required|numeric',
            'description' => 'nullable|string',
            'images.*' => 'image|mimes:png,jpg,webp|max:2048',
            'main_image_index' => 'nullable|integer',
            'main_image_id' => 'nullable|integer',
        ]);

        DB::transaction(function () use ($request, $validated, $id) {
            $product = Product::findOrFail($id);

            $product->update([
                'name' => $validated['name'],
                'price' => $validated['price'],
                'expiry_date' => $validated['expiry_date'],
                'category_id' => $validated['category_id'],
                'description' => $validated['description'] ?? null,
            ]);

            if ($request->filled('main_image_id')) {
                $product->productImages()->update(['is_main' => false]);
                $product->productImages()
                    ->where('id', $request->main_image_id)
                    ->update(['is_main' => true]);
            }

            if ($request->hasFile('images')) {
                // Delete old images
                foreach ($product->productImages as $image) {
                    $path = public_path($image->image_path);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                    $image->delete();
                }

                $mainImageIndex = $request->input('main_image_index', 0);

                foreach ($request->file('images') as $index => $image) {
                    $filename = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('assets/upload/products'), $filename);

                    $product->productImages()->create([
                        'image_path' => 'assets/upload/products/' . $filename,
                        'is_main' => $index == $mainImageIndex,
                    ]);
                }
            }
        });

        return response()->json([
            'type' => 'success',
            'message' => 'Product updated successfully.'
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'type' => 'success',
            'message' => 'Product deleted successfully.'
        ]);
    }
}
