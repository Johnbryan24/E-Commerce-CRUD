<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Product Management</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
    body {
        padding: 20px;
    }
</style>
</head>
<body>
<div class="container">
    <h1>Product Management</h1>
    
    <!-- Add Product Form -->
    <form id="addProductForm">
        <h2>Add Product</h2>
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>
    
    <!-- Search Product Form -->
    <form id="searchProductForm">
        <h2>Search Product</h2>
        <div class="form-group">
            <label for="search">Search by Name:</label>
            <input type="text" class="form-control" id="search" name="search">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    
    <!-- Product List -->
    <h2>Product List</h2>
    <div id="productList"></div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Function to load product list
        function loadProductList() {
            $.get('/api/getdata', function(data) {
                var productList = $('#productList');
                productList.empty();
                data.forEach(function(product) {
                    productList.append(`<div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">${product.name}</h5>
                                                <p class="card-text">${product.description}</p>
                                                <p class="card-text">Price: ${product.price}</p>
                                                <p class="card-text">Quantity: ${product.quantity}</p>
                                                <button class="btn btn-primary edit-btn" data-id="${product.id}">Edit</button>
                                                <button class="btn btn-danger delete-btn" data-id="${product.id}">Delete</button>
                                            </div>
                                        </div><br>`);
                });
            });
        }

        // Load product list on page load
        loadProductList();

        // Add Product Form Submission
        $('#addProductForm').submit(function(e) {
            e.preventDefault();
            var name = $('#name').val();
            var description = $('#description').val();
            var price = $('#price').val();
            var quantity = $('#quantity').val();
            
            // Client-side validation
            if (name.trim() === '' || description.trim() === '' || price.trim() === '' || quantity.trim() === '') {
                alert('All fields are required.');
                return;
            }
            if (!$.isNumeric(price) || !$.isNumeric(quantity)) {
                alert('Price and Quantity must be numeric values.');
                return;
            }

            // Submit form
            $.post('/api/add-product', $(this).serialize(), function(response) {
                alert(response);
                $('#addProductForm')[0].reset(); // Clear the form after successful submission
                loadProductList(); // Reload product list
            });
        });

        // Search Product Form Submission
        $('#searchProductForm').submit(function(e) {
            e.preventDefault();
            var searchQuery = $('#search').val();
            $.get(`/api/products/search?q=${searchQuery}`, function(data) {
                var productList = $('#productList');
                productList.empty();
                data.forEach(function(product) {
                    productList.append(`<div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">${product.name}</h5>
                                                <p class="card-text">${product.description}</p>
                                                <p class="card-text">Price: ${product.price}</p>
                                                <p class="card-text">Quantity: ${product.quantity}</p>
                                                <button class="btn btn-primary edit-btn" data-id="${product.id}">Edit</button>
                                                <button class="btn btn-danger delete-btn" data-id="${product.id}">Delete</button>
                                            </div>
                                        </div><br>`);
                });
            });
        });
        
        // Edit Product Button Click Event
        $('#productList').on('click', '.edit-btn', function() {
            var cardBody = $(this).closest('.card-body');
            var productId = $(this).data('id');

            // Get the current values
            var name = cardBody.find('.card-title').text();
            var description = cardBody.find('.card-text:eq(0)').text();
            var price = cardBody.find('.card-text:eq(1)').text().replace('Price: ', '');
            var quantity = cardBody.find('.card-text:eq(2)').text().replace('Quantity: ', '');

            // Replace content with input fields for editing
            cardBody.html(`
                <input type="text" class="form-control mb-2 edit-name" value="${name}">
                <textarea class="form-control mb-2 edit-description">${description}</textarea>
                <input type="number" class="form-control mb-2 edit-price" value="${price}">
                <input type="number" class="form-control mb-2 edit-quantity" value="${quantity}">
                <button class="btn btn-success save-btn" data-id="${productId}">Save</button>
            `);
        });

        // Save Product Button Click Event
        $('#productList').on('click', '.save-btn', function() {
            var cardBody = $(this).closest('.card-body');
            var productId = $(this).data('id');

            // Get updated values
            var name = cardBody.find('.edit-name').val();
            var description = cardBody.find('.edit-description').val();
            var price = cardBody.find('.edit-price').val();
            var quantity = cardBody.find('.edit-quantity').val();

            // Send AJAX request to update product
           
            $.ajax({
                url: `/api/edit-product/${productId}`,
                type: 'PUT',
                data: {
                    name: name,
                    description: description,
                    price: price,
                    quantity: quantity
                },
                success: function(response) {
                    alert(response);
                    loadProductList(); // Reload product list
                },
                error: function(xhr, status, error) {
                    alert("An error occurred while updating the product. Please try again.");
                    console.error(error);
                }
            });
        });

        // Delete Product Button Click Event
        $('#productList').on('click', '.delete-btn', function() {
            var productId = $(this).data('id');
            if (confirm('Are you sure you want to delete this product?')) {
                $.ajax({
                    url: `/api/delete-product?id=${productId}`,
                    type: 'DELETE',
                    success: function(response) {
                        alert(response);
                        loadProductList(); // Reload product list
                    },
                    error: function(xhr, status, error) {
                        alert("An error occurred while deleting the product. Please try again.");
                        console.error(error);
                    }
                });
            }
        });
    });
</script>
</body>
</html>
