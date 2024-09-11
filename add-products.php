<?php
include 'includes/header.php';

// if form submit
if (isset($_POST['submit'])) {
    $product_name = $_POST['product_name'];
    $product_desc = $_POST['product_desc'];

    $folder = 'assets/uploads/';
    $img = '';

    if (isset($_FILES['productImg']) && $_FILES['productImg']['error'] === UPLOAD_ERR_OK) {
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
        'product_name' => $product_name,
        'product_desc' => $product_desc,
        'product_img' => $img,
        'variants' => $variants
    ];

    $rowCount = addProduct($inputArr);
    if ($rowCount) { ?>
        <script>
            Swal.fire({
                title: "Product Added Successfully",
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

if (isset($_POST['submit2'])) {
    $name = $_POST['service_name'];
    $price = $_POST['price'];
    $description = $_POST['service_desc'];

    $inputArr = [
        'sname' => $name,
        'sprice' => $price,
        'sdescription' => $description
    ];

    $row = addServices($inputArr);
    if ($row) { ?>
        <script>
            Swal.fire({
                title: "Service Added Successfully",
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
                            <h4 class="card-title">Add New Product</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Products</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Services</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent-2">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                <form class="form" id="add-products" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="product_name" class="control-label">Product Name</label>
                                            <input type="text" id="product_name" class="form-control" required name="product_name" placeholder="EKO">
                                        </div>
                                        <div class="col-md-6 form-group p-2">
                                            <label for=""></label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="productImg" id="customFile" accept="image/*">
                                                <label class="custom-file-label" for="customFile">Product Image</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="product_desc" class="control-label">Product Description</label>
                                            <textarea name="product_desc" id="product_desc" class="form-control" placeholder="Eko is a good product."></textarea>
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
                                                                <tr>
                                                                    <td>1</td>
                                                                    <td>
                                                                        <input type="text" name="variant[]" class="form-control" placeholder="7 * 5" />
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="price[]" class="form-control" placeholder="2000" />
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="stock[]" class="form-control" placeholder="200" />
                                                                    </td>
                                                                </tr>
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
                                        <button class="btn btn-primary btn-lg" type="submit" name="submit">Add</button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                <form class="form" id="add-products" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="product_name" class="control-label">Service Name</label>
                                            <input type="text" id="service_name" class="form-control" required name="service_name" placeholder="EKO">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for=price"">Price</label>
                                            <input type="number" class="form-control" name="price" id="price">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="service_desc" class="control-label">Service Description</label>
                                            <textarea name="service_desc" id="service_desc" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button class="btn btn-primary btn-lg" type="submit" name="submit2">Add</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>


<script>
    $(document).ready(function() {
        var counter = 1;
        $('#add_row').click(function() {
            var html = '';
            html += '<tr>';
            html += '<td>' + (counter + 1) + '</td>';
            html += '<td><input type="text" name="variant[]" class="form-control" placeholder="7 * 5" /></td>';
            html += '<td><input type="number" name="price[]" class="form-control" placeholder="2000" /></td>';
            html += '<td><input type="number" name="stock[]" class="form-control" placeholder="200" /></td>';
            html += '</tr>';
            $('tbody').append(html);
            counter++;
        });

        $('#remove_row').click(function() {
            if (counter > 1) {
                $('#add-products tr:last').remove();
                counter--;
            }
        });
    })
</script>
