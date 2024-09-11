<?php
include 'includes/header.php';

if (isset($_GET['delete_id'])) {
    $st = deleteRecord('employees', $_GET['delete_id']);
    if ($st) { ?>
        <script>
            Swal.fire({
                title: "Branch Deleted Successfully",
                text: "Thanks!",
                icon: "success",
            }).then((res) => {
                window.location.href = "employees.php";
            });
        </script>
    <?php } else { ?>
        <script>
            Swal.fire({
                title: "Somthing went wrong",
                text: "Oops..!",
                icon: "error",
            }).then((res) => {
                window.location.href = "employees.php";
            });
        </script>
    <?php
    }
}

if (isset($_POST['update_employee'])) {
    $id = htmlspecialchars($_POST['employee_id']);
    $branch_id = htmlspecialchars($_POST['branch_id']);
    $empName = htmlspecialchars($_POST['emp_name']);
    $empNum = htmlspecialchars($_POST['emp_num']);
    $salary = htmlspecialchars($_POST['salary']);
    $empAddr = htmlspecialchars($_POST['emp_addr']);

    $folder = 'assets/uploads/';
    $img = $_POST['current_emp_img'];
    $proof = $_POST['current_emp_idcard'];

    // Handle employee image upload
    if (!empty($_FILES['emp_img']['name'])) {
        $img = $folder . rand(1, 100) . basename($_FILES['emp_img']['name']);
        move_uploaded_file($_FILES['emp_img']['tmp_name'], $img);
    }

    // Handle ID card upload
    if (!empty($_FILES['emp_idcard']['name'])) {
        $proof = $folder . rand(1, 100) . basename($_FILES['emp_idcard']['name']);
        move_uploaded_file($_FILES['emp_idcard']['tmp_name'], $proof);
    }

    $inputArr = [
        'branch_id' => $branch_id,
        'name'     => $empName,
        'mobile'   => $empNum,
        'address'  => $empAddr,
        'img'      => $img,
        'proof'    => $proof,
        'salary'   => $salary,
        'id'       => $id
    ];

    $rowCount = updateEmployee($id, $inputArr);
    if ($rowCount > 0) { ?>
        <script>
            Swal.fire({
                title: "Employee Updated Successfully",
                text: "Thanks!",
                icon: "success",
            }).then((res) => {
                window.location.href = "employees.php";
            });
        </script>
    <?php } else { ?>
        <script>
            Swal.fire({
                title: "Somthing went wrong",
                text: "Oops..!",
                icon: "error",
            }).then((res) => {
                window.location.href = "employees.php";
            });
        </script>
<?php
    }
}
?>

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Employee List</h4>
                    </div>
                    <button class="btn btn-primary add-list" data-toggle="modal" data-target="#addEmployee">
                        <i class="las la-plus mr-3"></i>Add Employee
                    </button>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-tables table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S.No.</th>
                                <th>Employee Image</th>
                                <th>Employee Name</th>
                                <th>Branch</th>
                                <th>Mobile</th>
                                <th>Address</th>
                                <th>Salary</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            <?php
                            $employees = getRecords('employees', [], 'id DESC');
                            $i = 1;
                            foreach ($employees as $employee) {
                            ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td>
                                        <img src="<?= $employee['img']; ?>" height="100" width="100" class="img-fluid" alt="<?= $employee['name']; ?>">
                                    </td>
                                    <td><?= $employee['name']; ?></td>
                                    <td><?= getBranchNameById($employee['branch_id']); ?></td>
                                    <td><?= $employee['contact_number']; ?></td>
                                    <td><?= $employee['address']; ?></td>
                                    <td><?= $employee['salary']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <span data-toggle="tooltip" data-placement="top" title="Edit">
                                                <a class="badge bg-success mr-2" href="#" data-toggle="modal" data-target="#editEmployee<?= $employee['id']; ?>">
                                                    <i class="ri-pencil-line mr-0"></i>
                                                </a>
                                            </span>
                                            <span data-toggle="tooltip" data-placement="top" title="Delete">
                                                <a class="badge bg-warning mr-2" href="employees.php?delete_id=<?= $employee['id']; ?>" onclick="return confirm('Are you sure you want to delete this employee?');">
                                                    <i class="ri-delete-bin-line mr-0"></i>
                                                </a>
                                            </span>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Employee Modal -->
                                <div class="modal fade" id="editEmployee<?= $employee['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editEmployeeTitle<?= $employee['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editEmployeeTitle<?= $employee['id']; ?>">Edit Employee</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <?php $employeeDetails = getEmployeeById($employee['id']); ?>
                                                <form class="form" method="post" action="" enctype="multipart/form-data">
                                                    <input type="hidden" name="employee_id" value="<?= htmlspecialchars($employee['id']); ?>">
                                                    <input type="hidden" name="current_emp_img" value="<?= htmlspecialchars($employeeDetails['img']); ?>">
                                                    <input type="hidden" name="current_emp_idcard" value="<?= htmlspecialchars($employeeDetails['id_img']); ?>">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="branch_id" class="control-label pb-0 mb-0">Branch Name</label>
                                                                <select name="branch_id" id="branch_id" class="form-control">
                                                                    <?php
                                                                    $branches = getRecords('branches', [], 'id DESC');
                                                                    foreach ($branches as $branch) {
                                                                    ?>
                                                                        <option value="<?= htmlspecialchars($branch['id']) ?>" <?php if ($branch['id'] == $employeeDetails['branch_id']) echo 'selected'; ?>>
                                                                            <?= htmlspecialchars($branch['name']) ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="emp_name" class="control-label pb-0 mb-0">Employee Name</label>
                                                                <input type="text" id="emp_name" name="emp_name" class="form-control" required value="<?= htmlspecialchars($employeeDetails['name']); ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="emp_num" class="control-label pb-0 mb-0">Employee Mobile</label>
                                                                <input type="number" name="emp_num" class="form-control" id="emp_num" required value="<?= htmlspecialchars($employeeDetails['contact_number']); ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="salary" class="control-label pb-0 mb-0">Employee Salary</label>
                                                                <input type="number" name="salary" class="form-control" id="salary" required value="<?= htmlspecialchars($employeeDetails['salary']); ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="emp_img">Employee Image</label>
                                                                <input type="file" class="form-control" name="emp_img" id="emp_img" accept="image/*">
                                                                <?php if (!empty($employeeDetails['img'])): ?>
                                                                    <img src="<?= htmlspecialchars($employeeDetails['img']); ?>" height="100" width="100" class="img-fluid mt-2" alt="Current Image">
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="emp_idcard">Any ID Card</label>
                                                                <input type="file" class="form-control" name="emp_idcard" id="emp_idcard" accept="image/*">
                                                                <?php if (!empty($employeeDetails['id_img'])): ?>
                                                                    <img src="<?= htmlspecialchars($employeeDetails['id_img']); ?>" height="100" width="100" class="img-fluid mt-2" alt="Current ID Card">
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="emp_addr" class="control-label pb-0 mb-0">Employee Address</label>
                                                                <textarea class="form-control" id="emp_addr" name="emp_addr" required><?= htmlspecialchars($employeeDetails['address']); ?></textarea>
                                                            </div>
                                                            <div class="text-right">
                                                                <button class="btn btn-primary" name="update_employee" type="submit">Update</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
