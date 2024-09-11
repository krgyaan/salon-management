<?php
include 'includes/header.php';

global $pdo;
// Get today's date
$today = date('Y-m-d');

// Get today's total income
$incomeQuery = "SELECT SUM(paid_amount) as total_income
                FROM income
                WHERE DATE(created_at) = :today";
$incomeStmt = $pdo->prepare($incomeQuery);
$incomeStmt->execute(['today' => $today]);
$incomeResult = $incomeStmt->fetch(PDO::FETCH_ASSOC);
$totalIncomeToday = $incomeResult['total_income'] ?? 0;

// Get today's total expenses
$expenseQuery = "SELECT SUM(amount) as total_expense
                 FROM expenses
                 WHERE DATE(created_at) = :today";
$expenseStmt = $pdo->prepare($expenseQuery);
$expenseStmt->execute(['today' => $today]);
$expenseResult = $expenseStmt->fetch(PDO::FETCH_ASSOC);
$totalExpensesToday = $expenseResult['total_expense'] ?? 0;

?>

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center card-total-sale">
                            <div class="icon iq-icon-box-2 bg-info-light">
                                <img src="assets/images/product/1.png" class="img-fluid" alt="image">
                            </div>
                            <div>
                                <p class="mb-2">Total Customer</p>
                                <h4>
                                    <?= getRowCount('customers'); ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center card-total-sale">
                            <div class="icon iq-icon-box-2 bg-danger-light">
                                <img src="assets/images/product/05.png" class="img-fluid" alt="image">
                            </div>
                            <div>
                                <p class="mb-2">Today's Income</p>
                                <h4>
                                    <?=
                                    "₹ " . number_format($totalIncomeToday, 2)
                                    ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center card-total-sale">
                            <div class="icon iq-icon-box-2 bg-danger-light">
                                <img src="assets/images/product/04.png" class="img-fluid" alt="image">
                            </div>
                            <div>
                                <p class="mb-2">Today's Expense</p>
                                <h4>
                                    <?=
                                    "₹ " . number_format($totalExpensesToday, 2)
                                    ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center card-total-sale">
                            <div class="icon iq-icon-box-2 bg-success-light">
                                <img src="assets/images/product/3.png" class="img-fluid" alt="image">
                            </div>
                            <div>
                                <p class="mb-2">Total Product</p>
                                <h4>
                                    <?= getRowCount('product_variants'); ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Branches by Income/Expense</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Branch</th>
                                        <th>Total Income</th>
                                        <th>Total Expense</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $allBranchesData = getIncomeAndExpenseByBranch($branch);
                                    foreach ($allBranchesData as $branchData) :
                                    ?>
                                        <tr>
                                            <td><?= $branchData['branch_name'] ?></td>
                                            <td><?= number_format($branchData['total_income'], 2) ?></td>
                                            <td><?= number_format($branchData['total_expense'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Monthly Income &amp; Expense Report</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="" method="get" class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="branch_id">Branch</label>
                                    <select name="branch_id" id="branch_id" class="form-control" <?= $branch ? 'disabled' : '' ?>>
                                        <option value="">Select Branch</option>
                                        <?php
                                        $branches = getRecords('branches', [], 'id DESC');
                                        foreach ($branches as $br) {
                                        ?>
                                            <option value="<?= $br['id'] ?>" <?= $branch == $br['id'] ? 'selected' : ''; ?>>
                                                <?= $br['name'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
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

                    <div class="card-body pt-0">
                        <div id="layout1-chart-5"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$branch_id = $_GET['branch_id'] ?? null;
$from_date = $_GET['from'] ?? null;
$to_date = $_GET['to'] ?? null;

// Initialize SQL conditions
$conditions = [];
$params = [];

// Add branch condition
if ($branch_id) {
    $conditions[] = "branch_id = :branch_id";
    $params[':branch_id'] = $branch_id;
}

// Add date range conditions
if ($from_date) {
    $conditions[] = "created_at >= :from_date";
    $params[':from_date'] = $from_date;
}
if ($to_date) {
    $conditions[] = "created_at <= :to_date";
    $params[':to_date'] = $to_date;
}

// Convert conditions to a string
$whereClause = '';
if (count($conditions) > 0) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}

// Get total income per month with filters
$incomeQuery = "SELECT MONTH(created_at) as month, SUM(paid_amount) as total_income
                FROM income
                $whereClause
                GROUP BY MONTH(created_at)";
$incomeStmt = $pdo->prepare($incomeQuery);
$incomeStmt->execute($params);
$incomeResults = $incomeStmt->fetchAll(PDO::FETCH_ASSOC);

$totalIncome = array_fill(0, 12, 0); // Initialize all months to 0
foreach ($incomeResults as $row) {
    $totalIncome[$row['month'] - 1] = $row['total_income'];
}

// Get total expenses per month with filters
$expenseQuery = "SELECT MONTH(created_at) as month, SUM(amount) as total_expense
                 FROM expenses
                 $whereClause
                 GROUP BY MONTH(created_at)";
$expenseStmt = $pdo->prepare($expenseQuery);
$expenseStmt->execute($params);
$expenseResults = $expenseStmt->fetchAll(PDO::FETCH_ASSOC);

$totalExpenses = array_fill(0, 12, 0); // Initialize all months to 0
foreach ($expenseResults as $row) {
    $totalExpenses[$row['month'] - 1] = $row['total_expense'];
}

// Pass data to JavaScript
echo "<script>
var incomeData = " . json_encode($totalIncome) . ";
var expenseData = " . json_encode($totalExpenses) . ";
</script>";


include 'includes/footer.php';
?>

<script>
    if (jQuery("#layout1-chart-5").length) {
        options = {
            series: [{
                    name: "Total Income",
                    data: incomeData,
                },
                {
                    name: "Total Expenses",
                    data: expenseData,
                },
            ],
            chart: {
                type: "bar",
                height: 300,
            },
            colors: ["#32BDEA", "#FF7E41"],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: "35%",
                    endingShape: "rounded",
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                show: false,
                width: 3,
                colors: ["transparent"],
            },
            xaxis: {
                categories: [
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "May",
                    "Jun",
                    "Jul",
                    "Aug",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dec",
                ],
                labels: {
                    minWidth: 0,
                    maxWidth: 0,
                },
            },
            yaxis: {
                show: true,
                labels: {
                    minWidth: 20,
                    maxWidth: 50,
                },
            },
            fill: {
                opacity: 1,
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "₹ " + val;
                    },
                },
            },
        };
        const chart = new ApexCharts(
            document.querySelector("#layout1-chart-5"),
            options
        );
        chart.render();
        const body = document.querySelector("body");
        if (body.classList.contains("dark")) {
            apexChartUpdate(chart, {
                dark: true,
            });
        }

        document.addEventListener("ChangeColorMode", function(e) {
            apexChartUpdate(chart, e.detail);
        });
    }
</script>
