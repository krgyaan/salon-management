<?php
include 'includes/header.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $st =  deletePurchase($id);

    if ($st) { ?>
        <script>
            Swal.fire({
                title: "Purchase Deleted Successfully",
                text: "Thanks!",
                icon: "success",
            }).then((res) => {
                window.location.href = "purchase_entry.php";
            });
        </script>
    <?php    } else { ?>
        <script>
            Swal.fire({
                title: "Somthing went wrong",
                text: "Oops..!",
                icon: "error",
            }).then((res) => {
                window.location.href = "purchase_entry.php";
            });
        </script>
<?php }
}

?>

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Purchase Entries</h4>
                    </div>
                    <a href="add_purchase_entry.php" class="btn btn-primary add-list">
                        <i class="las la-plus mr-3"></i>New Purchase Entry
                    </a>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-tables table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S.No.</th>
                                <th>Product Variant</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            <?php
                            $purchaseDetails = getRecords('purchases', [], 'id DESC');
                            foreach ($purchaseDetails as $purchase) {

                                $details = getPurchaseItems($purchase['purchase_id']);
                                foreach ($details as $key => $value) {
                                    $price = $value['price'];
                                    $quantity = $value['quantity'];
                                }

                            ?>
                                <tr>
                                    <td><?= htmlspecialchars_decode($purchase['purchase_id']) ?></td>
                                    <td>
                                        <?php
                                        $prodictVarients = getProductIdByPurchaseId($purchase['purchase_id']);
                                        foreach ($prodictVarients as $key => $value) {
                                            $p = getProductAndVariantById($value['product_id']);
                                            echo $p['product_name'] . ' - ' . $p['variant_name'] . '<br>';
                                        }
                                        ?>
                                    </td>
                                    <td><?= $price ?></td>
                                    <td><?= $quantity ?></td>
                                    <td><?= htmlspecialchars_decode($purchase['grand_total']) ?></td>
                                    <td><?= htmlspecialchars_decode($purchase['paid_amt']) ?></td>
                                    <td><?= htmlspecialchars_decode($purchase['due_amt']) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="Edit"
                                                href="edit-product.php?id=<?= $purchase['purchase_id'] ?>">
                                                <i class="ri-pencil-line mr-0"></i>
                                            </a>
                                            <a class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title="Delete"
                                                href="purchase_entry.php?delete=<?= $purchase['purchase_id'] ?>" onclick="return confirm('Are you sure you want to delete this product?');">
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
