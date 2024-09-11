<?php
include 'includes/header.php';
// if form submit

if (isset($_POST['submit'])) {
    $purchase_date = $_POST['purchase_date'];
    $purchase_id = $_POST['purchase_id'];
    $vendor_id = $_POST['vendor_name'];
    $grand_total = $_POST['grand_total'];
    $paid_amt = $_POST['paid_amt'];
    $due_date = $_POST['due_date'];
    $due_amt = $_POST['due_amt'];
    $notes = $_POST['notes'];

    // Collect items data
    $products = [];
    if (isset($_POST['product_name'])) {
        foreach ($_POST['product_name'] as $key => $product_name) {
            $products[] = [
                'varient_id' => $product_name,
                'price' => $_POST['price'][$key],
                'quantity' => $_POST['quantity'][$key],
                'total' => $_POST['total'][$key]
            ];
        }
    }

    $inputArr = [
        'purchase_id' => $purchase_id,
        'purchase_date' => $purchase_date,
        'vendor_id' => $vendor_id,
        'grand_total' => $grand_total,
        'paid_amt' => $paid_amt,
        'due_date' => $due_date,
        'due_amt' => $due_amt,
        'notes' => $notes,
        'products' => $products
    ];

    $rowCount = addPurchase($inputArr);

    if ($rowCount) { ?>
        <script>
            Swal.fire({
                title: "Product Entered Successfully",
                text: "Thanks!",
                icon: "success",
            }).then((res) => {
                window.location.href = "purchase_entry.php";
            });
        </script>
    <?php } else { ?>
        <script>
            Swal.fire({
                title: "Somthing went wrong",
                text: "Oops..!",
                icon: "error",
            }).then((res) => {
                window.location.href = "purchase_entry.php";
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
                            <h4 class="card-title">Add New Purchase Entry</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form" id="add-products" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label for="vendor_name" class="control-label">Purchase Date</label>
                                    <input type="date" class="form-control" required name="purchase_date" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="vendor_name" class="control-label">Purchase ID</label>
                                    <input type="text" class="form-control" readonly name="purchase_id" value="<?= generatePurchaseId() ?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="vendor_name" class="control-label">Vendor Name</label>
                                    <select id="vendor_name" class="form-control" required name="vendor_name">
                                        <option value="">Select Vendor</option>
                                        <?php
                                        $vendors = getRecords('vendors', [], 'id DESC');
                                        foreach ($vendors as $vendor) {
                                        ?>
                                            <option value="<?= $vendor['id'] ?>"><?= $vendor['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <div class="row py-3">
                                        <div class="col-md-10">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Product Variant</th>
                                                            <th>Price</th>
                                                            <th>Quantity</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="add-products">
                                                        <tr>
                                                            <td>1</td>
                                                            <td>
                                                                <select name="product_name[]" id="product_name" class="form-control" required>
                                                                    <option value="">Select Product</option>
                                                                    <?php
                                                                    $products = getProductDetails();
                                                                    foreach ($products as $p) {
                                                                        echo '<option data-price="' . $p['price'] . '" value="' . $p['id'] . '">' . $p['product_name'] . ' - ' . $p['variant_name'] . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="price[]" class="form-control price-input" required placeholder="200" />
                                                            </td>
                                                            <td>
                                                                <input type="number" name="quantity[]" class="form-control quantity-input" required placeholder="20" />
                                                            </td>
                                                            <td>
                                                                <input type="number" name="total[]" class="form-control total-input" readonly placeholder="4000" />
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="4" align="right">Grand Total</td>
                                                            <td>
                                                                <input type="number" name="grand_total" id="grand_total" class="border-0 bg-transparent" readonly />
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" id="add_row" class="btn btn-primary">&plus;</button>
                                            <button type="button" id="remove_row" class="btn btn-danger">&minus;</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="paid_amt" class="control-label">Paid Amount</label>
                                    <input type="number" name="paid_amt" id="paid_amt" required class="form-control" />
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="due_date" class="control-label">Next Due Date</label>
                                    <input type="date" name="due_date" id="due_date" required class="form-control" />
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="due_amt" class="control-label">Due Amount</label>
                                    <input type="number" name="due_amt" id="due_amt" readonly class="form-control" />
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="notes" class="control-label">Notes</label>
                                    <textarea name="notes" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="text-right">
                                <button class="btn btn-primary btn-lg" type="submit" name="submit">Add</button>
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
        var counter = 1;

        var productOptions = `
                <?php
                $products = getProductDetails();
                foreach ($products as $p) {
                    echo '<option data-price="' . $p['price'] . '" value="' . $p['id'] . '">' . $p['product_name'] . ' - ' . $p['variant_name'] . '</option>';
                }
                ?>
            `;

        $('#add_row').click(function() {
            var html = '';
            html += '<tr>';
            html += '<td>' + (counter + 1) + '</td>';
            html += '<td><select name="product_name[]" id="product_name" class="form-control" required><option value="">Select Product</option>' + productOptions + '</select></td>';
            html += '<td><input type="number" name="price[]" class="form-control price-input" placeholder="200" /></td>';
            html += '<td><input type="number" name="quantity[]" class="form-control quantity-input" placeholder="20" /></td>';
            html += '<td><input type="number" name="total[]" readonly class="form-control total-input" placeholder="4000" /></td>';
            html += '</tr>';
            $('tbody#add-products').append(html);
            counter++;
        });

        $('#remove_row').click(function() {
            if (counter > 1) {
                $('tbody#add-products tr:last').remove();
                counter--;
                calculateGrandTotal();
            }
        });

        // Function to calculate the total for each row
        function calculateRowTotal(row) {
            var price = parseFloat($(row).find('.price-input').val()) || 0;
            var quantity = parseFloat($(row).find('.quantity-input').val()) || 0;
            var total = price * quantity;
            $(row).find('.total-input').val(total.toFixed(2));
        }

        // Function to calculate the grand total
        function calculateGrandTotal() {
            var grandTotal = 0;
            $('#add-products .total-input').each(function() {
                var total = parseFloat($(this).val()) || 0;
                grandTotal += total;
            });
            $('#grand_total').val(grandTotal.toFixed(2));
            // $('#add-products').find('tfoot tr:nth-child(1) td:last').text(`₹${grandTotal.toFixed(2)}`);
        }

        // Trigger calculation on input change
        $(document).on('input', '.price-input, .quantity-input', function() {
            var row = $(this).closest('tr');
            calculateRowTotal(row);
            calculateGrandTotal();
        });

        // calculate due_amt
        $('#paid_amt').on('change', function() {
            var total = $('#grand_total').val();
            var paid = $('#paid_amt').val();

            if (paid > total) {
                alert('Paid Amount Must Be Equal or Lower Than Total Amount');
                $('#paid_amt').val(total);
                $('#due_amt').val(0);
            } else {
                $('#due_amt').val((total - paid).toFixed(2));
            }
        });

        // get product price
        $(document).on('change', '#product_name', function() {
            var product_id = $(this).val();
            var price = $(this).find(':selected').data('price');
            $(this).closest('tr').find('.price-input').val(price);
            calculateRowTotal($(this).closest('tr'));
            calculateGrandTotal();
        });
    });
</script>
