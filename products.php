<?php
include 'includes/header.php';

if (isset($_GET['delete']) || isset($_GET['sd'])) {
    function deleteProduct($id)
    {
        return deleteRecord('product_variants', $id);
    }

    $st = deleteProduct($_GET['delete']);
    $sd = deleteRecord('products', $_GET['sd']);
    if ($st) {
        echo '<script>window.location.href = "products.php"</script>';
    }
    if ($sd) {
        echo '<script>window.location.href = "products.php"</script>';
    }
}

?>

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Product List</h4>
                    </div>
                    <a href="add-products.php" class="btn btn-primary add-list">
                        <i class="las la-plus mr-3"></i>Add Product
                    </a>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-tables table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S.No.</th>
                                <th>Product</th>
                                <th>Variant</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            <?php
                            $productDetails = getProductDetails();

                            $i = 1;
                            foreach ($productDetails as $detail) {
                            ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= htmlspecialchars_decode($detail['product_name']); ?></td>
                                    <td><?= $variantName = htmlspecialchars_decode($detail['variant_name']) ?: '-'; ?></td>
                                    <td><?= htmlspecialchars_decode($detail['price']); ?></td>
                                    <td><?= htmlspecialchars_decode($detail['stock']); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="Edit"
                                                href="edit-product.php?id=<?= $detail['product_id']; ?>">
                                                <i class="ri-pencil-line mr-0"></i>
                                            </a>
                                            <a class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title="Delete"
                                                href="products.php?delete=<?= $detail['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">
                                                <i class="ri-delete-bin-line mr-0"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
