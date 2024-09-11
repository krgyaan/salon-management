<?php
include 'includes/header.php';
// if form submit

if (isset($_POST['submit'])) {
    $branch_id = $_POST['branch_id'];
    $exdate = $_POST['date'];
    $extype = $_POST['expense_type'];
    $emp = $_POST['employee'];
    $examt = $_POST['paid_amt'];
    $exdesc = $_POST['notes'];

    $inputArr = [
        'branch_id' => $branch_id,
        'date' => $exdate,
        'type' => $extype,
        'amount' => $examt,
        'notes' => $exdesc,
        'employee' => $emp
    ];

    $row = addExpense($inputArr);

    if ($row > 0) { ?>
        <script>
            Swal.fire({
                title: "Record Added.",
                text: "Expense Record Added Successfully",
                icon: "success",
            }).then((res) => {
                window.location.href = "expenses.php";
            });
        </script>
    <?php  } else { ?>
        <script>
            Swal.fire({
                title: "Added Failed.",
                text: "Somthing went wrong.",
                icon: "success",
            }).then((res) => {
                window.location.href = "expenses.php";
            });
        </script>
<?php }
}
?>

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Add New Salary Entry</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form" id="add-products" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label for="branch_id" class="control-label">Branch Name</label>
                                    <select name="branch_id" id="branch_id" class="form-control" <?= $role !== 'admin' ? 'readonly' : '' ?>>
                                        <option value="">Selet Branch</option>
                                        <?php
                                        $branches = getRecords('branches', [], '');
                                        foreach ($branches as $br):
                                        ?>
                                            <option <?= $br['id'] == $branch ? 'selected' : '' ?> value="<?= $br['id'] ?>"><?= $br['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="date" class="control-label">Expense Type</label>
                                    <input type="date" value="<?= date('Y-m-d') ?>" name="date" id="date" class="form-control" />
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="expense_type" class="control-label">Expense Type</label>
                                    <input type="text" name="expense_type" id="expense_type" class="form-control" />
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="employee" class="control-label">Employee Name</label>
                                    <select id="employee" class="form-control" name="employee">
                                        <option value="">Select Employee</option>
                                        <?php
                                        $employees = getRecords('employees', [], 'id DESC');
                                        foreach ($employees as $employee) {
                                        ?>
                                            <option value="<?= $employee['id'] ?>"><?= $employee['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="paid_amt" class="control-label">Paid Amount</label>
                                    <input type="number" name="paid_amt" id="paid_amt" required class="form-control" />
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
