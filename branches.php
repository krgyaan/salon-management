<?php
include 'includes/header.php';

if (isset($_GET['delete_id'])) {
    $st = deleteRecord('branches', $_GET['delete_id']);
    if ($st) { ?>
        <script>
            Swal.fire({
                title: "Branch Deleted Successfully",
                text: "Thanks!",
                icon: "success",
            }).then((res) => {
                window.location.href = "branches.php";
            });
        </script>';
<?php }
}

if (isset($_POST['update_branch'])) {
    $branch_id = htmlspecialchars($_POST['branch_id']);
    $branchName = htmlspecialchars($_POST['branch_name']);
    $branchMobile = htmlspecialchars($_POST['branch_mobile']);
    $branchAddr = htmlspecialchars($_POST['branch_addr']);

    $inputArr = [
        'name' => $branchName,
        'contact_number' => $branchMobile,
        'address' => $branchAddr
    ];

    $rowCount = updateBranch($branch_id, $inputArr);
    if ($rowCount > 0) { ?>
        <script>
            Swal.fire({
                title: "Branch Updated Successfully",
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

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Branch List</h4>
                    </div>
                    <button class="btn btn-primary add-list" data-toggle="modal" data-target="#addBranch">
                        <i class="las la-plus mr-3"></i>Add New Branch
                    </button>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-tables table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S.No.</th>
                                <th>Branch Name</th>
                                <th>Mobile</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            <?php
                            $branches = getRecords('branches', [], 'id DESC');
                            $i = 1;
                            foreach ($branches as $branch) {
                            ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= htmlspecialchars_decode($branch['name']); ?></td>
                                    <td><?= htmlspecialchars_decode($branch['contact_number']); ?></td>
                                    <td><?= htmlspecialchars_decode($branch['address']); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <span data-toggle="tooltip" data-placement="top" title="Edit">
                                                <a class="badge bg-success mr-2" href="#" data-toggle="modal" data-target="#editBranch<?= $branch['id']; ?>">
                                                    <i class="ri-pencil-line mr-0"></i>
                                                </a>
                                            </span>
                                            <a class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title="Delete"
                                                href="branches.php?delete_id=<?= $branch['id']; ?>" onclick="return confirm('Are you sure you want to delete this branch?');">
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
