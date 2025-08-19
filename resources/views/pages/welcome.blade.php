@extends('layouts.shop')

@section('content')

<!-- SECTION -->
<div class="section" style="display:none;">
    <!-- container -->
    <div class="container">
        <!-- Dynamic Categories Row -->
        <div class="row">
            @foreach($categories as $category)
            <div class="col-md-4 col-xs-6">
                <div class="shop">
                    <div class="shop-img">
                        <img src="{{ asset($category->image ?? 'assets/shop/img/default-category.png') }}" alt="{{ $category->name }}">
                    </div>
                    <div class="shop-body">
                        <h3>{{ $category->name }}<br>Collection</h3>
                        <a href="#" class="cta-btn category-btn" data-id="{{ $category->id }}">Shop now <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
    <!-- /container -->
</div>
<!-- /SECTION -->

<!-- SECTION -->
<div class="section section-scrollable">
    <div class="container">
    
        <!-- Product List -->
        <div class="row" id="product-list">
            @include('components.product-list', ['data' => $data])
        </div>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document"> <!-- Enlarged modal -->
        <div class="modal-content">

            <div class="modal-header" style="border:0px;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal-name">Product Name</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <!-- Image Gallery -->
                    <div class="col-md-6">
                        <div id="product-images" class="text-center" style="margin-bottom: 15px;">
                            <!-- Main Image -->
                            <img id="modal-image" src="{{ asset('assets/dashboard/images/noimage.png') }}" 
                                 class="img-responsive center-block main-product-image" style="max-height: 300px;" alt="Product Image">
                        </div>
                        <div id="image-thumbnails" class="text-center clearfix" style="margin-bottom: 15px;">
                            <!-- Thumbnails will be appended here by JS -->
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="col-md-6">
                        <p><strong>Category:</strong> <span id="modal-category" class="text-muted"></span></p>
                        <p class="h4 text-danger" style="margin-top: 15px;">₱<span id="modal-price">0.00</span></p>

                        <p><strong>Description:</strong></p>
                        <p id="modal-description" class="text-justify"></p>

                        <!-- Inventory -->
                        <div id="modal-inventory" style="margin-top: 20px;margin-bottom: 15px;">
                            <ul id="inventory-list" class="list-unstyled"></ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let selectedCategory = '';
        let searchQuery = '';

        function fetchProducts(url = "{{ route('welcome') }}") {
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    search: searchQuery,
                    category_id: selectedCategory
                },
                success: function(response) {
                    $('#product-list').html(response.html);
                },
                error: function(xhr) {
                    console.error('Error fetching products:', xhr);
                }
            });
        }

        $(document).on('click', '#search-btn', function(e) {
            e.preventDefault();
            searchQuery = $('#search_value').val();
            fetchProducts();
        });

        $(document).on('click', '.category-btn', function() {
            selectedCategory = $(this).data('id');

            $('.category-btn').removeClass('active');
            $(this).addClass('active');

            fetchProducts();
        });

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            fetchProducts(url);
        });

       $(document).on('click', '.quick-view', function () {
            var productId = $(this).data('id');

            $.ajax({
                url: '/product/details/' + productId,
                type: 'GET',
                success: function (response) {
                    var product = response.product;

                    // Basic Info
                    $('#modal-title').text(product.name);
                    $('#modal-name').text(product.name);
                    $('#modal-price').text(parseFloat(product.price).toFixed(2));
                    $('#modal-description').text(product.description);
                    $('#modal-category').text(product.category ? product.category.name : 'Uncategorized');

                    // Show main image if available
                    const mainImage = product.product_images.find(img => img.is_main == 1);
                    if (mainImage) {
                        const imagePath = '/' + mainImage.image_path;
                        $('#modal-image').attr('src', imagePath);
                    } else {
                        $('#modal-image').attr('src', '/assets/dashboard/images/noimage.png');
                    }

                    // Render thumbnails
                    const thumbnailsContainer = $('#image-thumbnails');
                    thumbnailsContainer.empty();

                    product.product_images.forEach(img => {
                        const thumbPath = '/' + img.image_path;
                        const thumbnail = $(`
                            <img src="${thumbPath}" class="img-thumbnail m-1" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;">
                        `);
                        
                        // When thumbnail clicked, update the main image
                        thumbnail.on('click', function () {
                            $('#modal-image').attr('src', thumbPath);
                        });

                        thumbnailsContainer.append(thumbnail);
                    });


                    // Inventory - Compute net quantity (in - out)
                    let totalIn = 0;
                    let totalOut = 0;

                    if (product.inventories && product.inventories.length > 0) {
                        product.inventories.forEach(function (inv) {
                            if (inv.type === 'in') {
                                totalIn += parseInt(inv.quantity);
                            } else if (inv.type === 'out') {
                                totalOut += parseInt(inv.quantity);
                            }
                        });

                        const netStock = totalIn - totalOut;
                        $('#inventory-list').html(`<li><strong>Available Stock:</strong> ${netStock}</li>`);
                    } else {
                        $('#inventory-list').html('<li>No inventory info</li>');
                    }


                    // Show modal
                    $('#productModal').modal('show');
                },
                error: function () {
                    toast('error', 'Error loading product info');
                }
            });
        });

        $(document).on('click', '.guest-purchase-request-btn', function(e) {
            e.preventDefault();
            const productId = $(this).data('id');

            sessionStorage.setItem('pending_cart_product', productId);

            setTimeout(function() {
                window.location.href = "{{ route('login') }}";
            }, 100);
        });
    });
</script>
@endpush