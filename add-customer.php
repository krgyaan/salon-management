<?php
include 'includes/header.php';
$editMode = false;
$customer = [];

if (isset($_GET['id'])) {
    $editMode = true;
    $editId = $_GET['id'];

    // Fetch the customer details based on the id
    $customer = getOneCustomer($editId);

    if (!$customer) {
        // Handle case where customer is not found (optional)
        echo "Customer not found!";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputArr = [
        'branch_id' => $_POST['customerBranch'],
        'name' => $_POST['customerName'],
        'city' => $_POST['customerCity'],
        'mobile' => $_POST['customerMobile'],
        'dob' => $_POST['customerDOB'],
        'address' => $_POST['customerAddress'],
    ];

    if ($editMode) {
        // Update customer
        $result = updateCustomer($editId, $inputArr);
        if ($result) { ?>
            <script>
                Swal.fire({
                    title: "Customer Updated Successfully",
                    text: "Thanks!",
                    icon: "success",
                }).then((res) => {
                    window.location.href = "customers.php";
                });
            </script>
        <?php } else { ?>
            <script>
                Swal.fire({
                    title: "Somthing went wrong",
                    text: "Oops..!",
                    icon: "error",
                }).then((res) => {
                    window.location.href = "customers.php";
                });
            </script>
        <?php
        }
    } else {
        // Add new customer
        $result = addCustomer($inputArr);
        if ($result) { ?>
            <script>
                Swal.fire({
                    title: "Customer Added Successfully",
                    text: "Thanks!",
                    icon: "success",
                }).then((res) => {
                    window.location.href = "customers.php";
                });
            </script>
        <?php } else { ?>
            <script>
                Swal.fire({
                    title: "Somthing went wrong",
                    text: "Oops..!",
                    icon: "error",
                }).then((res) => {
                    window.location.href = "customers.php";
                });
            </script>
<?php
        }
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
                            <h4 class="card-title">Add New Customer</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="branch" class="control-label">Branch</label>
                                            <select name="customerBranch" id="branch" required class="form-control">
                                                <option value="">Select Branch</option>
                                                <?php
                                                $branches = getRecords('branches', [], 'id DESC');
                                                foreach ($branches as $branch) {
                                                    $selected = ($editMode && $branch['id'] == $customer['branch_id']) ? 'selected' : '';
                                                    echo "<option value=\"{$branch['id']}\" $selected>{$branch['name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="name" class="control-label">Customer Name</label>
                                            <input type="text" name="customerName" id="name" required class="form-control"
                                                value="<?= htmlspecialchars($editMode ? $customer['name'] : '') ?>"
                                                placeholder="Enter Customer Name" />
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="city" class="control-label">City: *</label>
                                            <input type="text" id="city" class="form-control" required name="customerCity"
                                                value="<?= htmlspecialchars($editMode ? $customer['city'] : '') ?>"
                                                placeholder="City">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="mobile" class="control-label">Mobile Number</label>
                                            <input type="number" class="form-control" id="mobile" required name="customerMobile"
                                                value="<?= htmlspecialchars($editMode ? $customer['contact_number'] : '') ?>"
                                                placeholder="Customer Mobile No.">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="dob" class="control-label">Date Of Birth</label>
                                            <input type="date" class="form-control" required id="dob" name="customerDOB"
                                                value="<?= htmlspecialchars($editMode ? $customer['dob'] : '') ?>">
                                        </div>
                                        <div class="col-md-12 mb-3 form-group">
                                            <label for="address" class="control-label">Address: *</label>
                                            <textarea name="customerAddress" class="form-control" id="address" rows="3" required="required"><?= htmlspecialchars($editMode ? $customer['address'] : '') ?></textarea>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary pull-right" type="submit" name="submit"><?= $editMode ? 'Update' : 'Add' ?></button>
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
