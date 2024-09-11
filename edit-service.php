<?php
include 'includes/header.php';

// Fetch product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details
$products = getOneService($product_id);
$product = $products;

if (!$product) {
    echo "Product not found.";
    exit;
}

// Handle form submission
if (isset($_POST['submit'])) {
    $sid = $_POST['product_id'];
    $sname = $_POST['product_name'];
    $sprice = $_POST['price'];
    $sdesc = $_POST['product_desc'];

    $inputArr = [
        'sid' => $sid,
        'sname' => $sname,
        'sprice' => $sprice,
        'sdesc' => $sdesc,
    ];

    $rowCount = updateService($inputArr);
    if ($rowCount) { ?>
        <script>
            Swal.fire({
                title: "Service Updated Successfully",
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
                            <h4 class="card-title">Edit Service</h4>
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
                                            <label for="price">Price</label>
                                            <input type="number" name="price" id="price" class="form-control" value="<?= htmlspecialchars($product['service_price']) ?>" />
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="product_desc" class="control-label">Product Description</label>
                                            <textarea name="product_desc" id="product_desc" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
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
