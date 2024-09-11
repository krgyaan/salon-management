<?php include 'includes/header.php'; ?>

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4">
                <div class="card card-transparent card-block card-stretch card-height border-none">
                    <div class="card-body p-0 mt-lg-2 mt-0">
                        <h3 class="mb-3 text-capitalize">Hi <?= $role;
                                                            if ($role == 'branch') {
                                                                echo ' (' . getBranchNameById($branch) . ')';
                                                            } ?></h3>
                        <p class="mb-0 mr-4">
                            Your dashboard gives you views of key performance or business process.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-info-light">
                                        <img src="assets/images/product/1.png" class="img-fluid" alt="image">
                                    </div>
                                    <div>
                                        <p class="mb-2">Customer Visit</p>
                                        <h4>
                                            <?php
                                            echo getRowCount('customers');
                                            ?>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-danger-light">
                                        <img src="assets/images/product/2.png" class="img-fluid" alt="image">
                                    </div>
                                    <div>
                                        <p class="mb-2">Total Income</p>
                                        <h4>$ 4598</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-success-light">
                                        <img src="assets/images/product/3.png" class="img-fluid" alt="image">
                                    </div>
                                    <div>
                                        <p class="mb-2">Product Used</p>
                                        <h4>
                                            <?php
                                            echo getRowCount('product_variants');
                                            ?>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Products &amp; Stocks</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled row top-product mb-0">
                            <?php
                            $products = getProductDetails();
                            foreach ($products as $key => $value) :
                            ?>
                                <li class="col-lg-3">
                                    <div class="card card-block  mb-0">
                                        <div class="card-body" style="width: 200px;">
                                            <div class="style-text text-left mt-3">
                                                <h5 class="mb-1"><?= $value['product_name'] . ' ' . $value['variant_name'] ?></h5>
                                                <p class="mb-0">Stock: <?= $value['stock'] ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card card-transparent card-block card-stretch mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between p-0">
                        <div class="header-title">
                            <h4 class="card-title mb-0">Recent Customer Visit</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div><a href="all_incomes.php" class="btn btn-primary view-btn font-size-14">View All</a></div>
                        </div>
                    </div>
                </div>
                <div class="card card-block card-stretch card-height-helf">
                    <?php $customers = getRecentIncomeEntries();
                    foreach ($customers as $key => $value) :
                    ?>
                        <div class="card-body card-item-right">
                            <div class="style-text">
                                <h5 class="m-0"><?= $value['customer_name'] ?></h5>
                                <div class="w-100 d-flex justify-content-between align-items-top">
                                    <p class="mb-0"><b>Total:</b> <?= number_format($value['total_amount'], 2) ?></p>
                                    <p class="mb-0"><b>Paid:</b> <?= number_format($value['paid_amount'], 2) ?></p>
                                    <p class="mb-0"><b>Due:</b> <?= number_format($value['due_amount'], 2) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
