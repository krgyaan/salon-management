<?php include 'includes/header.php'; ?>

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Income List</h4>
                    </div>
                    <a class="btn btn-primary add-list" href="income.php">
                        <i class="las la-plus mr-3"></i>Add New Entry
                    </a>
                </div>
            </div>

            <div class="col-md-12 card-body bg-white my-2">
                <form action="" method="get" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="from">Start Date</label>
                            <input type="date" value="<?= $_GET['from'] ?? date('Y-m-d') ?>" name="from" id="from" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="to">End Date</label>
                            <input type="date" value="<?= $_GET['to'] ?? date('Y-m-d') ?>" name="to" id="to" class="form-control" />
                        </div>
                    </div>

                    <div class="col-md-3 d-flex align-items-end justify-content-start">
                        <div class="form-group">
                            <label for="to"></label>
                            <button type="submit" name="s" id="search" class="btn btn-success">Search</button>
                        </div>
                    </div>
                </form>
            </div>

            <?php
            $data = [];

            if (isset($_GET['s'])) {
                $from = $_GET['from'] ?? date('Y-m-d');
                $to = $_GET['to'] ?? date('Y-m-d');

                if (!empty($from) && !empty($to)) {
                    $data = getDailyEntries($branch, $from, $to);
                } else {
                    $data = getDailyEntries($branch);
                }
            } else {
                $data = getDailyEntries($branch);
            }
            ?>

            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-tables table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S.No.</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Mobile</th>
                                <th>Net Total</th>
                                <th>Paid Amount</th>
                                <th>Due Amount</th>
                                <th>Item Used</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            <?php
                            $i = 1;
                            foreach ($data as $key => $value) :
                            ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= date('d-m-Y', strtotime($value['item_created_at'])) ?></td>
                                    <td><?= $value['customer_name'] ?></td>
                                    <td><?= $value['mobile'] ?></td>
                                    <td><?= $value['total_amount'] ?></td>
                                    <td><?= $value['paid_amount'] ?></td>
                                    <td><?= $value['due_amount'] ?></td>
                                    <td><?= getUsedItemName($value['varient_id']);  ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include 'includes/footer.php'; ?>
