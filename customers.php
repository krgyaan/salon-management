<?php
include 'includes/header.php';

if (isset($_GET['del'])) {
    $st = deleteRecord('customers', $_GET['del']);
    if ($st) { ?>
        <script>
            Swal.fire({
                title: "Customer Delete Successfully",
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
?>

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Customer List</h4>
                    </div>
                    <a href="add-customer.php" class="btn btn-primary add-list">
                        <i class="las la-plus mr-3"></i>Add New Customer
                    </a>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-tables table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S.No.</th>
                                <th>Customer Name</th>
                                <th>Address</th>
                                <th>Mobile Number</th>
                                <th>DOB</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            <?php
                            $i = 1;
                            $customwers = getRecords('customers', [], 'id DESC');
                            foreach ($customwers as $customer) :
                            ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= $customer['name']; ?></td>
                                    <td><?= $customer['city'] . "<br/>" . $customer['address']; ?></td>
                                    <td><?= $customer['contact_number']; ?></td>
                                    <td><?= $customer['dob']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title=""
                                                data-original-title="Edit" href="add-customer.php?id=<?= $customer['id']; ?>">
                                                <i class="ri-pencil-line mr-0"></i>
                                            </a>
                                            <a class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title=""
                                                data-original-title="Delete" href="customers.php?del=<?= $customer['id']; ?>">
                                                <i class="ri-delete-bin-line mr-0"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
