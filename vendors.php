<?php

include 'includes/header.php';

if (isset($_GET['id'])) {
    $st = deleteRecord('vendors', $_GET['id']);
    if ($st) { ?>
        <script>
            Swal.fire({
                title: "Vendor Delete Successfully",
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


<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Vendor List</h4>
                    </div>
                    <button class="btn btn-primary add-list" data-toggle="modal" data-target="#addVendor">
                        <i class="las la-plus mr-3"></i>Add Vendor
                    </button>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-tables table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S.No.</th>
                                <th>Branch</th>
                                <th>Vendor Name</th>
                                <th>Mobile</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            <?php
                            $vendors = getRecords('vendors', [], 'id DESC');
                            $i = 1;
                            foreach ($vendors as $vendor) {
                            ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= htmlspecialchars_decode(getBranchNameById($vendor['branch_id'])) ?></td>
                                    <td><?= htmlspecialchars_decode($vendor['name']) ?></td>
                                    <td><?= htmlspecialchars_decode($vendor['contact_info']) ?></td>
                                    <td><?= htmlspecialchars_decode($vendor['address']) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <span data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">
                                                <a class="badge bg-success mr-2" href="#" data-toggle="modal" data-target="#editVendor<?= $vendor['id'] ?>">
                                                    <i class="ri-pencil-line mr-0"></i>
                                                </a>
                                            </span>
                                            <a class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"
                                                href="vendors.php?id=<?= $vendor['id'] ?>" onclick="return confirm('Are you sure you want to delete this vendor?');">
                                                <i class="ri-delete-bin-line mr-0"></i>
                                            </a>

                                        </div>
                                    </td>
                                </tr>


                                <!-- Edit Vendor Modal -->
                                <div class="modal fade" id="editVendor<?= $vendor['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editVendorTitle<?= $vendor['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editVendorTitle<?= $vendor['id']; ?>">Edit Vendor</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                if (isset($_POST['update_vendor']) && $_POST['vendor_id'] == $vendor['id']) {
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

                                                    $rowCount = updateVendor($vendor['id'], $inputArr);
                                                    if ($rowCount > 0) { ?>
                                                        <script>
                                                            Swal.fire({
                                                                title: "Vendor Updated Successfully",
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

                                                $vendorDetails = getVendorById($vendor['id']);
                                                ?>

                                                <form class="form" id="edit_vendor" name="edit_vendor" method="post" action="">
                                                    <input type="hidden" name="vendor_id" value="<?= $vendor['id']; ?>">
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
                                                                            <option <?php if ($branch['id'] == $_SESSION['branch_id'] || $branch['id'] == $vendorDetails['branch_id']) {
                                                                                        echo 'selected';
                                                                                    } ?> value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-12 form-group">
                                                                    <label for="vendorName" class="control-label pb-0 mb-0">Vendor Name</label>
                                                                    <input type="text" id="vendorName" name="vendorName" class="form-control" required value="<?= $vendorDetails['name']; ?>">
                                                                </div>
                                                                <div class="col-md-12 form-group">
                                                                    <label for="vendorNum" class="control-label pb-0 mb-0">Vendor Mobile</label>
                                                                    <input type="number" name="vendorNum" class="form-control" id="vendorNum" required value="<?= $vendorDetails['contact_info']; ?>">
                                                                </div>
                                                                <div class="col-md-12 form-group">
                                                                    <label for="vendorAddr" class="control-label pb-0 mb-0">Vendor Address</label>
                                                                    <textarea class="form-control" id="vendorAddr" name="vendorAddr" required><?= $vendorDetails['address']; ?></textarea>
                                                                </div>
                                                                <div class="col-md-12 text-right">
                                                                    <button class="btn btn-primary" name="update_vendor" type="submit">Update</button>
                                                                </div>
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
