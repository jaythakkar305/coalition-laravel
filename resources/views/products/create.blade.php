@extends('products.layout')

@section('content')
    <section class="vh-100 gradient-custom">
        <div class="container py-5 h-100">
            <div class="row justify-content-center align-items-center h-80">
                <div class="col-12 col-lg-9 col-xl-7">
                    <nav class="navbar navbar-light bg-light mt-2 mb-5 mb-4 pb-2 mb-md-5">
                        <div class="container-fluid">
                            <span class="navbar-brand mb-0 h1">Add Product</span>
                        </div>
                    </nav>
                    <div class="card shadow-2-strong card-registration" style="border-radius: 15px;">
                        <div class="card-body p-4 p-md-5">
                            <form action="{{ route('products.store') }}" method="POST" id="productForm">
                                @csrf

                                <div class="row align-items-center py-3">
                                    <div class="col-md-3 ps-2">

                                        <label class="form-label" for="product_name">Product Name</label>

                                    </div>
                                    <div class="col-md-9 pe-5">
                                        <input type="text" id="product_name" name="product_name"
                                            class="form-control form-control-lg" />
                                        <div class="form-text text-danger " id="product_name_error"></div>
                                    </div>
                                </div>

                                <hr class="mx-n3">

                                <div class="row align-items-center py-3">
                                    <div class="col-md-3 ps-2">

                                        <label class="form-label" for="quantity_in_stock">Quantity in Stock</label>

                                    </div>
                                    <div class="col-md-5 pe-5">
                                        <input type="text" id="quantity_in_stock" name="quantity_in_stock"
                                            class="form-control form-control-lg" />
                                        <div class="form-text text-danger " id="quantity_in_stock_error"></div>
                                    </div>
                                </div>

                                <hr class="mx-n3">

                                <div class="row align-items-center py-3">
                                    <div class="col-md-3 ps-2">

                                        <label class="form-label" for="price_per_item">Price per Item</label>

                                    </div>
                                    <div class="col-md-5 pe-5">
                                        <input type="text" id="price_per_item" name="price_per_item"
                                            class="form-control form-control-lg" />
                                        <div class="form-text text-danger " id="price_per_item_error"></div>
                                    </div>
                                </div>

                                <input type="hidden" name="state" value="add" id="state">
                                <input type="hidden" name="record_id" value="add" id="record_id">

                                <div class="mt-4 pt-2" >
                                    <input data-mdb-ripple-init class="btn btn-primary btn-lg" type="submit"
                                        value="Submit" id="addbutton" />
                                    
                                        <input data-mdb-ripple-init class="btn btn-primary btn-lg" type="button"
                                        value="Clear" id="clearbutton"  style="display: none;" />
                                </div>

                            </form>
                        </div>
                    </div>

                    <!-- As a heading -->
                    <nav class="navbar navbar-light bg-light mt-5 mb-5">
                        <div class="container-fluid">
                            <span class="navbar-brand mb-0 h1">Products</span>
                        </div>
                    </nav>

                    <div id="product-lists">
                        @include('products.list')
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript">
    
        $('#productForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            const statevalue = $('#state').val();            
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: statevalue === 'update' ? "{{ route('products.update') }}" : "{{ route('products.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: (data) => {
                    $("#product_name_error").html('');
                    $("#price_per_item_error").html('');
                    $("#quantity_in_stock_error").html('');
                    $("#product_name").val('');
                    $("#price_per_item").val('');
                    $("#quantity_in_stock").val('');
                    getProducts(1);

                    $('#addbutton').val('Submit');
                    $('#state').val('add');
                    $('#clearbutton').hide();
                    
                },
                error: function(data) {

                    if (data.responseJSON !== undefined) {
                        if (data.responseJSON['errors']['product_name'] !== undefined) {
                            $("#product_name_error").html(data.responseJSON['errors']['product_name']);
                        } else {
                            $("#product_name_error").html('');
                        }
                        if (data.responseJSON['errors']['price_per_item'] !== undefined) {
                            $("#price_per_item_error").html(data.responseJSON['errors'][
                                'price_per_item']);
                        } else {
                            $("#price_per_item_error").html('');
                        }
                        if (data.responseJSON['errors']['quantity_in_stock'] !== undefined) {
                            $("#quantity_in_stock_error").html(data.responseJSON['errors'][
                                'quantity_in_stock'
                            ]);
                        } else {
                            $("#quantity_in_stock_error").html('');
                        }
                    } else {
                        $("#product_name_error").html('');
                        $("#price_per_item_error").html('');
                        $("#quantity_in_stock_error").html('');
                    }
                }
            });
        });

        function getProducts(page) {
            $.ajax({
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                })
                .done(function(data) {
                    $("#product-lists").empty().html(data);
                    location.hash = page;
                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
        }

        $(document).ready(function(){

            $('#state').val('add');
            $('#clearbutton').hide();

            $(window).on('hashchange', function() {
                if (window.location.hash) {
                    var page = window.location.hash.replace('#', '');
                    if (page == Number.NaN || page <= 0) {
                        return false;
                    }else{
                        getProducts(page);
                    }
                }
            });

            $(document).on('click', '.pagination a',function(event)
            {
                $('li').removeClass('active');
                $(this).parent('li').addClass('active');
                event.preventDefault();

                var myurl = $(this).attr('href');
                var page=$(this).attr('href').split('page=')[1];

                getProducts(page);
            });

            $(document).on('click', '.editbutton',function(event)
            {
                let id = $(this).data("id");
                let product_name = $(this).data("product_name");
                let quantity_in_stock = $(this).data("quantity_in_stock");
                let price_per_item = $(this).data("price_per_item");
                $('#addbutton').val('Update');
                $('#state').val('update');
                $('#clearbutton').show();                

                $('#record_id').val(id);
                $('#product_name').val(product_name);
                $('#quantity_in_stock').val(quantity_in_stock);
                $('#price_per_item').val(price_per_item);                
            });

            $(document).on('click', '#clearbutton',function(event)
            {
                $('#addbutton').val('Submit');
                $('#state').val('add');
                $('#clearbutton').hide();
                $('#productForm').trigger("reset");                
            });
            
            
        });
    </script>
@endsection
