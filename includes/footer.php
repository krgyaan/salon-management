</div>
<footer class="iq-footer">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item"><a href="../backend/privacy-policy.php">Privacy Policy</a>
                            </li>
                            <li class="list-inline-item"><a href="../backend/terms-of-service.php">Terms of Use</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-6 text-right">
                        <span class="mr-1">
                            <script>
                                document.write(new Date().getFullYear())
                            </script>©
                        </span> <a href="#" class="">Gyan</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="modal fade" id="my-profile" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="popup text-left">
                    <h4 class="mb-3">My Profile</h4>
                    <div class="content create-workform bg-body">
                        <div class="pb-2">
                            <label class="mb-1">Username</label>
                            <input type="text" class="form-control" disabled name="name" value="<?php echo $username; ?>">
                        </div>
                        <h3>Change Password</h3>
                        <form action="" method="POST" class="row">
                            <div class="col-md-6">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="cpassword">Confirm Password</label>
                                <input type="password" name="cpassword" id="cpassword" class="form-control">
                            </div>
                            <div class="col-md-12 pt-2 text-right">
                                <button type="submit" name="change_password" class="btn btn-success">Save</button>
                            </div>
                        </form>
                        <?php
                        if (isset($_POST['change_password'])) {
                            $userid = $_SESSION['user_id'];
                            $password = $_POST['password'];
                            $cpassword = $_POST['cpassword'];

                            if ($password == $cpassword) {
                                $update = updatePassword($userid, $password);
                                if ($update) {
                                    echo '<script>
                                    Swal.fire({
                                        title: "Password Updated Successfully",
                                        text: "Thanks!",
                                        icon: "success",
                                    }).then((res) => {
                                        window.location.href = "profile.php";
                                    });
                                    </script>';
                                } else {
                                    echo '<script>
                                    Swal.fire({
                                        title: "Somthing went wrong",
                                        text: "Oops..!",
                                        icon: "error",
                                    }).then((res) => {
                                        window.location.href = "profile.php";
                                    });
                                    </script>';
                                }
                            } else {
                                echo '<script>
                                Swal.fire({
                                    title: "Password Not Match",
                                    text: "Oops..!",
                                    icon: "error",
                                }).then((res) => {
                                    window.location.href = "profile.php";
                                });
                                </script>';
                            }
                        }
                        ?>
                        <div class="col-lg-12 mt-4">
                            <div class="d-flex flex-wrap align-items-ceter justify-content-center">
                                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Vendor Modal -->
<div class="modal fade" id="addVendor" tabindex="-1" role="dialog" aria-labelledby="addVendorTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVendorTitle">Add Vendor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                if (isset($_POST['add_vendor'])) {
                    $branch_id = htmlspecialchars($_POST['branch_id']);
                    $vendorName = htmlspecialchars($_POST['vendorName']);
                    $vendorNum = htmlspecialchars($_POST['vendorNum']);
                    $vendorAddr = htmlspecialchars($_POST['vendorAddr']);
                    $inputArr = [
                        'branch_id' => $branch_id,
                        'name'    => $vendorName,
                        'mobile'  => $vendorNum,
                        'address' => $vendorAddr
                    ];

                    $rowCount = addVendor($inputArr);
                    if ($rowCount > 0) { ?>
                        <script>
                            Swal.fire({
                                title: "Vendor Create Successfully",
                                text: "Thanks!",
                                icon: "success",
                            }).then((res) => {
                                window.location.href = "vendors.php";
                            });
                        </script>
                    <?php } else { ?>
                        <script>
                            Swal.fire({
                                title: "Somthing went wrong",
                                text: "Oops..!",
                                icon: "error",
                            }).then((res) => {
                                window.location.href = "vendors.php";
                            });
                        </script>
                <?php
                    }
                }
                ?>

                <form class="form" id="add_vendor" name="add_vendor" method="post" action="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="branch_id" class="control-label pb-0 mb-0">Branch Name</label>
                                    <select name="branch_id" id="branch_id" class="form-control"
                                        <?php if (isset($_SESSION['branch_id'])) {
                                            echo 'readonly';
                                        } ?>>
                                        <option value="">Select Branch</option>
                                        <?php
                                        $branches = getRecords('branches', [], 'id DESC');
                                        foreach ($branches as $branch) :
                                        ?>
                                            <option <?php if ($branch['id'] == $_SESSION['branch_id']) {
                                                        echo 'selected';
                                                    } ?> value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="vendorName" class="control-label pb-0 mb-0">Vendor Name</label>
                                    <input type="text" id="vendorName" name="vendorName" class="form-control" required>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="vendorNum" class="control-label pb-0 mb-0">Vendor Mobile</label>
                                    <input type="number" name="vendorNum" class="form-control" id="vendorNum" required>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="vendorAddr" class="control-label pb-0 mb-0">Vendor Address</label>
                                    <textarea class="form-control" id="vendorAddr" name="vendorAddr" required></textarea>
                                </div>
                                <div class="col-md-12 text-right">
                                    <button class="btn btn-primary" name="add_vendor" type="submit">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployee" tabindex="-1" role="dialog" aria-labelledby="addEmployeeTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEmployeeTitle">Add Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                if (isset($_POST['add_employee'])) {
                    $branch_id = htmlspecialchars($_POST['branch_id']);
                    $empName = htmlspecialchars($_POST['emp_name']);
                    $empNum = htmlspecialchars($_POST['emp_num']);
                    $salary = htmlspecialchars($_POST['salary']);
                    $empAddr = htmlspecialchars($_POST['emp_addr']);

                    $folder = 'assets/uploads/';
                    $img = '';
                    $proof = '';

                    // Handle employee image upload
                    if (isset($_FILES['emp_img']) && $_FILES['emp_img']['error'] === UPLOAD_ERR_OK) {
                        $img = $folder . rand(1, 100) . basename($_FILES['emp_img']['name']);
                        move_uploaded_file($_FILES['emp_img']['tmp_name'], $img);
                    }

                    // Handle ID card upload
                    if (isset($_FILES['emp_idcard']) && $_FILES['emp_idcard']['error'] === UPLOAD_ERR_OK) {
                        $proof = $folder . rand(1, 100) . basename($_FILES['emp_idcard']['name']);
                        move_uploaded_file($_FILES['emp_idcard']['tmp_name'], $proof);
                    }

                    $inputArr = [
                        'branch_id' => $branch_id,
                        'name'    => $empName,
                        'mobile'  => $empNum,
                        'address' => $empAddr,
                        'img'     => $img,
                        'proof'   => $proof,
                        'salary' => $salary
                    ];

                    $rowCount = addEmp($inputArr);
                    if ($rowCount > 0) { ?>
                        <script>
                            Swal.fire({
                                title: "Employee Create Successfully",
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
                <form class="form" id="add_employee" name="add_employee" method="post" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="branch_id" class="control-label pb-0 mb-0">Branch Name</label>
                                    <select name="branch_id" id="branch_id" class="form-control" <?php if (isset($_SESSION['branch_id'])) {
                                                                                                        echo 'readonly';
                                                                                                    } ?>>
                                        <option value="">Select Branch</option>
                                        <?php
                                        $branches = getRecords('branches', [], 'id DESC');
                                        foreach ($branches as $branch) {
                                        ?>
                                            <option <?php if ($branch['id'] == $_SESSION['branch_id']) {
                                                        echo 'selected';
                                                    } ?> value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="emp_name" class="control-label pb-0 mb-0">Employee Name</label>
                                    <input type="text" id="emp_name" name="emp_name" class="form-control" required>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="emp_num" class="control-label pb-0 mb-0">Employee Mobile</label>
                                    <div class="input-group">
                                        <span class="border px-2 d-flex align-items-center justify-content-center rounded-left">+91</span>
                                        <input type="number" name="emp_num" class="form-control" id="emp_num" required>
                                    </div>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="salary" class="control-label pb-0 mb-0">Employee Salary</label>
                                    <div class="input-group">
                                        <span class="border px-2 d-flex align-items-center justify-content-center rounded-left">₹</span>
                                        <input type="number" name="salary" class="form-control" id="salary" required>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="emp_img">Employee Image</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="emp_img" id="customFile1" required accept="image/*">
                                        <label class="custom-file-label" for="customFile1">Choose file</label>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="emp_idcard">Any ID Card</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="emp_idcard" id="customFile2" required accept="image/*">
                                        <label class="custom-file-label" for="customFile2">Choose file</label>
                                    </div>
                                    <small>e.g. Aadhar Card, PAN Card</small>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="emp_addr" class="control-label pb-0 mb-0">Employee Address</label>
                                    <textarea class="form-control" id="emp_addr" name="emp_addr" required></textarea>
                                </div>
                                <div class="col-md-12 text-right">
                                    <button class="btn btn-primary" name="add_employee" type="submit">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Branch Modal -->
<div class="modal fade" id="addBranch" tabindex="-1" role="dialog" aria-labelledby="addBranchTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBranchTitle">Add Branch</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                if (isset($_POST['add_branch'])) {
                    $name = htmlspecialchars($_POST['branch_name']);
                    $address = htmlspecialchars($_POST['branch_addr']);
                    $contact_number = htmlspecialchars($_POST['branch_mobile']);
                    $email = htmlspecialchars($_POST['branch_email']);
                    $password = htmlspecialchars($_POST['branch_pass']);

                    $inputArr = [
                        'name' => $name,
                        'address' => $address,
                        'contact_number' => $contact_number,
                        'email' => $email,
                        'password' => $password
                    ];

                    $rowCount = addBranch($inputArr);
                    if ($rowCount > 0) { ?>
                        <script>
                            Swal.fire({
                                title: "Branch Create Successfully",
                                text: "Thanks!",
                                icon: "success",
                            }).then((res) => {
                                window.location.href = "branches.php";
                            });
                        </script>
                    <?php } else { ?>
                        <script>
                            Swal.fire({
                                title: "Somthing went wrong",
                                text: "Oops..!",
                                icon: "error",
                            }).then((res) => {
                                window.location.href = "branches.php";
                            });
                        </script>
                <?php
                    }
                }
                ?>
                <form class="form" id="add_branch" name="add_branch" method="post" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="branch_name" class="control-label pb-0 mb-0">Branch Name</label>
                                    <input type="text" id="branch_name" name="branch_name" class="form-control" required>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="branch_mobile" class="control-label pb-0 mb-0">Contact Number</label>
                                    <input type="number" name="branch_mobile" class="form-control" id="branch_mobile" required>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="branch_email" class="control-label pb-0 mb-0">Branch Email</label>
                                    <input type="email" name="branch_email" class="form-control" id="branch_email" required>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="branch_pass" class="control-label pb-0 mb-0">Branch Password</label>
                                    <input type="password" name="branch_pass" class="form-control" id="branch_pass" required>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="branch_addr" class="control-label pb-0 mb-0">Branch Address</label>
                                    <textarea class="form-control" id="branch_addr" name="branch_addr" required></textarea>
                                </div>
                                <div class="col-md-12 text-right">
                                    <button class="btn btn-primary" name="add_branch" type="submit">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="assets/js/sweetalert.js"></script>
<script src="assets/js/backend-bundle.min.js"></script>
<script src="assets/js/table-treeview.js"></script>
<script src="assets/js/customizer.js"></script>
<script async src="assets/js/chart-custom.js"></script>
<script src="assets/js/app.js"></script>

</body>

</html>
