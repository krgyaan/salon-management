<?php
include 'includes/header.php';

// Fetch product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details
$product = getProductDetailsById($product_id);

if (!$product) {
    echo "Product not found.";
    exit;
}

// Handle form submission
if (isset($_POST['submit'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_desc = $_POST['product_desc'];
    $img = $product['img']; // Keep the existing image if not updated

    if (isset($_FILES['productImg']) && $_FILES['productImg']['error'] === UPLOAD_ERR_OK) {
        $folder = 'assets/uploads/';
        $img = $folder . rand(1, 100) . basename($_FILES['productImg']['name']);
        move_uploaded_file($_FILES['productImg']['tmp_name'], $img);
    }

    // Collect variants data
    $variants = [];
    if (isset($_POST['variant'])) {
        foreach ($_POST['variant'] as $key => $variant_name) {
            $variants[] = [
                'name' => $variant_name,
                'price' => $_POST['price'][$key],
                'stock' => $_POST['stock'][$key]
            ];
        }
    }

    $inputArr = [
        'product_id' => $product_id,
        'product_name' => $product_name,
        'product_desc' => $product_desc,
        'product_img' => $img,
        'variants' => $variants
    ];

    $rowCount = updateProduct($inputArr);
    if ($rowCount) { ?>
        <script>
            Swal.fire({
                title: "Product Updated Successfully",
                text: "Thanks!",
                icon: "success",
            }).then((res) => {
                window.location.href = "products.php";
            });
        </script>
    <?php } else { ?>
        <script>
            Swal.fire({
                title: "Somthing went wrong",
                text: "Oops..!",
                icon: "error",
            }).then((res) => {
                window.location.href = "products.php";
            });
        </script>
<?php
    }
}

?>

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Edit Product</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form" id="edit-products" action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id'])  ?>">
                                            <label for="product_name" class="control-label">Product Name</label>
                                            <input type="text" id="product_name" class="form-control" required name="product_name"
                                                value="<?= htmlspecialchars($product['name']) ?>">
                                        </div>
                                        <div class="col-md-6 form-group p-2">
                                            <label for=""></label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="productImg" id="customFile" accept="image/*">
                                                <label class="custom-file-label" for="customFile">Product Image</label>
                                                <?php if ($product['img']): ?>
                                                    <img src="<?= htmlspecialchars($product['img']) ?>" alt="Product Image" width="100">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="product_desc" class="control-label">Product Description</label>
                                            <textarea name="product_desc" id="product_desc" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Product Variant</th>
                                                                    <th>Price</th>
                                                                    <th>Stock</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="add-products">
                                                                <?php
                                                                $variantCount = 1;
                                                                foreach ($product['variants'] as $variant) {
                                                                ?>
                                                                    <tr>
                                                                        <td><?= $variantCount++ ?></td>
                                                                        <td>
                                                                            <input type="text" name="variant[]" class="form-control"
                                                                                value="<?= htmlspecialchars($variant['name']) ?>" />
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="price[]" class="form-control"
                                                                                value="<?= htmlspecialchars($variant['price']) ?>" />
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="stock[]" class="form-control"
                                                                                value="<?= htmlspecialchars($variant['stock']) ?>" />
                                                                        </td>
                                                                    </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" id="add_row" class="btn btn-primary">&plus;</button>
                                                    <button type="button" id="remove_row" class="btn btn-danger">&minus;</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button class="btn btn-primary btn-lg" type="submit" name="submit">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    $(document).ready(function() {
        var counter = <?= $variantCount - 1 ?>; // Initialize counter based on existing variant count
        $('#add_row').click(function() {
            var html = '';
            html += '<tr>';
            html += '<td>' + (counter + 1) + '</td>';
            html += '<td><input type="text" name="variant[]" class="form-control" placeholder="7 * 5" /></td>';
            html += '<td><input type="number" name="price[]" class="form-control" placeholder="2000" /></td>';
            html += '<td><input type="number" name="stock[]" class="form-control" placeholder="200" /></td>';
            html += '</tr>';
            $('#add-products').append(html);
            counter++;
        });

        $('#remove_row').click(function() {
            if (counter > 1) {
                $('#add-products tr:last').remove();
                counter--;
            }
        });
    });
</script>
