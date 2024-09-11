<?php
include 'includes/header.php';

if (isset($_POST['submit'])) {
    $msg = "";
    try {
        // Start the transaction
        $pdo->beginTransaction();

        // Prepare input data
        $date = $_POST['date'];
        $branch = $_POST['branch'];
        $mobile = $_POST['mobile'];
        $name = $_POST['name'];
        $city = $_POST['city'];
        $address = $_POST['address'];
        $dob = $_POST['dob'];

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

        $grand_total = $_POST['grand_total'];
        $paid_amt = $_POST['paid_amt'];
        $due_amt = $_POST['due_amt'];
        $due_date = $_POST['due_date'];
        $notes = $_POST['notes'];

        // Prepare customer data
        $customerData = [
            'branch_id' => $branch,
            'name' => $name,
            'city' => $city,
            'mobile' => $mobile,
            'dob' => $dob,
            'address' => $address,
        ];

        // Add or get existing customer
        $customerId = addCustomer($customerData);

        if ($customerId) {
            $msg .= "Customer Added, ";
        }

        // Prepare income data
        $incomeData = [
            'customer_id' => $customerId,
            'branch_id' => $branch,
            'total_amount' => $grand_total,
            'paid_amount' => $paid_amt,
            'due_amount' => $due_amt,
            'due_date' => $due_date,
            'notes' => $notes
        ];

        // Add income record
        $incomeId = addIncome($incomeData);

        // Add each item used
        foreach ($products as $product) {
            $itemData = [
                'income_id' => $incomeId,
                'varient_id' => $product['varient_id'],
                'price' => $product['price'],
                'quantity' => $product['quantity'],
                'total' => $product['total']
            ];

            addItemUsed($itemData);
        }

        $msg .= "Item Entered, ";
        // Update stock based on items used
        updateStockOnItemsUsed($incomeId);

        $msg .= "And Updated the stock.";

        // Commit the transaction
        $pdo->commit();

?>
        <script>
            Swal.fire({
                title: "Record Added.",
                text: "Income recorded and stock updated successfully",
                icon: "success",
            }).then((res) => {
                window.location.href = "income.php";
            });
        </script>
<?php
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $pdo->rollBack();
        echo "Failed to process the request: " . $e->getMessage();
    }
}

?>
<div class="content-page">
    <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Add New Entry</h4>
                        </div>
                        <?php
                         $d = date('Y-m-d');
                        ?>
                        <a class="btn btn-primary add-list" href="all_incomes.php?from=<?= $d ?>&to=<?= $d ?>&s=">
                            View Entries
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-6 d-none">
                                    <div class="form-group">
                                        <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="form-control">
                                        <input type="text" name="customerId" id="customer_id" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Branch Name</label>
                                        <select name="branch" class="form-control">
                                            <option value="">Select Branch Name</option>
                                            <?php
                                            $branches = getRecords('branches', [], '');
                                            foreach ($branches as $br) : ?>
                                                <option <?= $branch == $br['id'] ? 'selected' : '' ?> value="<?= $br['id'] ?>"><?= $br['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mobile Number *</label>
                                        <input type="number" id="mobile" name="mobile" class="form-control" placeholder="Enter mobile No">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter Customer Name" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>City</label>
                                        <input type="text" name="city" id="city" class="form-control" placeholder="Enter City">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input type="text" name="address" id="address" class="form-control" placeholder="Enter Address">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>DOB</label>
                                        <input type="date" name="dob" id="dob" class="form-control">
                                    </div>
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
                                                                <input type="number" name="price[]" class="form-control price-input" required />
                                                            </td>
                                                            <td>
                                                                <input type="number" name="quantity[]" class="form-control quantity-input" required />
                                                            </td>
                                                            <td>
                                                                <input type="number" name="total[]" class="form-control total-input" readonly />
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
                            <div class="fa-pull-right">
                                <button type="submit" name="submit" class="btn btn-primary mr-2">Add</button>
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
                $services = getServices();
                foreach ($products as $p) {
                    echo '<option data-price="' . $p['price'] . '" value="' . $p['id'] . '">' . $p['product_name'] . ' - ' . $p['variant_name'] . '</option>';
                }
                foreach ($services as $s) {
                    echo '<option data-price="' . $s['service_price'] . '" value="' . $s['id'] . '">' . $s['name'] . '</option>';
                }
                ?>
            `;

        $('#add_row').click(function() {
            var html = '';
            html += '<tr>';
            html += '<td>' + (counter + 1) + '</td>';
            html += '<td><select name="product_name[]" id="product_name" class="form-control" required><option value="">Select Product</option>' + productOptions + '</select></td>';
            html += '<td><input type="number" name="price[]" class="form-control price-input" /></td>';
            html += '<td><input type="number" name="quantity[]" class="form-control quantity-input" /></td>';
            html += '<td><input type="number" name="total[]" readonly class="form-control total-input" /></td>';
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

        function calculateRowTotal(row) {
            var price = parseFloat($(row).find('.price-input').val()) || 0;
            var quantity = parseFloat($(row).find('.quantity-input').val()) || 0;
            var total = price * quantity;
            $(row).find('.total-input').val(total.toFixed(2));
        }

        function calculateGrandTotal() {
            var grandTotal = 0;
            $('#add-products .total-input').each(function() {
                var total = parseFloat($(this).val()) || 0;
                grandTotal += total;
            });
            $('#grand_total').val(grandTotal.toFixed(2));
        }

        $(document).on('input', '.price-input, .quantity-input', function() {
            var row = $(this).closest('tr');
            calculateRowTotal(row);
            calculateGrandTotal();
        });

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

        $(document).on('change', '#product_name', function() {
            var product_id = $(this).val();
            var price = $(this).find(':selected').data('price');
            $(this).closest('tr').find('.price-input').val(price);
            calculateRowTotal($(this).closest('tr'));
            calculateGrandTotal();
        });

        $(document).on('input', '#mobile', function() {
            var mobile = $(this).val();
            $.ajax({
                url: 'getExistingCustomerDetails.php',
                type: 'POST',
                data: {
                    mobile: mobile
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data) {
                        $('#name').val(data.name).attr('readonly', 'true');
                        $('#address').val(data.address).attr('readonly', 'true');
                        $('#email').val(data.email).attr('readonly', 'true');
                        $('#city').val(data.city).attr('readonly', 'true');
                        $('#dob').val(data.dob).attr('readonly', 'true');
                        $('#customer_id').val(data.id);
                    } else {
                        $('#name').val('').attr('readonly', false);
                        $('#address').val('').attr('readonly', false);
                        $('#email').val('').attr('readonly', false);
                        $('#city').val('').attr('readonly', false);
                        $('#dob').val('').attr('readonly', false);
                        $('#customer_id').val('').attr('readonly', false);
                    }
                }
            });
        });
    });
</script>
