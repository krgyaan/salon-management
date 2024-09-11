<?php
include 'includes/header.php';

if (isset($_GET['delete'])) {
}
?>

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">All Expenses</h4>
                    </div>
                    <a href="expenses-entry.php" class="btn btn-primary add-list">
                        <i class="las la-plus mr-3"></i>New Expense Entry
                    </a>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-tables table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S.No.</th>
                                <th>Date</th>
                                <th>Expense Type</th>
                                <th>Employee</th>
                                <th>Amount</th>
                                <th>Remark</th>
                                <th style="display: none;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            <?php
                            $i = 1;
                            $expenses = getRecords('expenses', [], 'id DESC');
                            foreach ($expenses as $key => $value) {
                            ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?= $value['expense_date'] ?></td>
                                    <td><?= $value['expense_type'] ?></td>
                                    <td><?= $value['employee_id'] != 0 ? getEmployeeById($value['employee_id']) : 'NA' ?></td>
                                    <td><?= $value['amount'] ?></td>
                                    <td><?= $value['description'] ?></td>
                                    <td style="display: none;">
                                        <a href="expenses-entry.php?id=<?= $value['id'] ?>" class="btn btn-success btn-sm">Edit</a>
                                    </td>
                                </tr>
                            <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
