<?php
include('./template/head.php');

$jwt_token = $_SESSION['jwt_token'];

$url = "http://localhost:5000/admin/dashboard_data";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "get-token: $jwt_token"
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if ($data === null || isset($data['status']) && $data['status'] === 'expire') {
    session_destroy();
    header('Location: admin_login.php');
    exit();
}

$sd = $data['sd'];
$sd_month = $data['sd_month'];
$td = $data['td'];
$tu = $data['tu'];
$dm = $data['dm'];
$dr = $data['dr'];
$dt = $data['dt'];
$rd = $data['rd'];
$tp = $data['tp'];
$dtr = $data['dtr'];

#donation trends
$donationMonths = [];
$donationAmounts = [];
foreach ($dtr as $row) {
    $donationMonths[] = htmlspecialchars($row['month'], ENT_QUOTES, 'UTF-8');
    $donationAmounts[] = (float)$row['total_donations'];
}

$donationTrendsData = array_map(function ($month, $amount) {
    return [$month, $amount];
}, $donationMonths, $donationAmounts);


$paymentMethods = [];
$paymentMethodCounts = [];
foreach ($dm as $row) {
    $paymentMethods[] = htmlspecialchars($row['payment_method'], ENT_QUOTES, 'UTF-8');
    $paymentMethodCounts[] = (int)$row['total'];
}


$paymentMethodData = [];
for ($i = 0; $i < count($paymentMethods); $i++) {
    $paymentMethodData[] = [$paymentMethods[$i], $paymentMethodCounts[$i]];
}

?>
<style>
    table td,
    table th {
        padding: 0.75rem;
    }

    .card-body {
        min-height: 120px;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card text-bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total</h5>
                    <p class="card-text">₱ <?php echo htmlspecialchars(number_format($sd, 2, '.', ','), ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">This month</h5>
                    <p class="card-text">₱ <?php if (!empty($sd_month)) {
                                                echo htmlspecialchars(number_format((float)$sd_month, 2, '.', ','), ENT_QUOTES, 'UTF-8');
                                            } else {
                                                echo "0.00";
                                            }
                                            ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Donors</h5>
                    <p class="card-text"><?php echo htmlspecialchars($td, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?php echo htmlspecialchars($tu, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-6">
                <div class="card-header">Donation Trends</div>
                <div class="card-body">
                    <div id="dtr_charts" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-6">
                <div class="card-header">Payment Method</div>
                <div class="card-body">
                    <div id="payment_chart" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>

    </div>


    <div class="row">
        <div class="col-md-9">
            <h4>Recent Donations</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Donor Name</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rd as $row) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>

                            <td>₱ <?php echo htmlspecialchars(number_format($row['amount'], 2, '.', ','), ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-3">
            <h4>Top Donors</h4>
            <table class="table">
                <thead>
                    <th>Name</th>
                    <th>Amount</th>
                </thead>
                <tbody>
                    <?php foreach ($tp as $row) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>

                            <td>₱ <?php echo htmlspecialchars(number_format($row['total'], 2, '.', ','), ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {
        packages: ['corechart']
    });

    google.charts.setOnLoadCallback(drawAllCharts);

    function drawAllCharts() {
        drawDonationTrends();
        drawPaymentMethodChart();
    }

    function drawDonationTrends() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Month');
        data.addColumn('number', 'Donation Amount');
        data.addRows(<?php echo json_encode($donationTrendsData); ?>);

        var options = {
            title: 'Donation Trends Over Time',
            legend: {
                position: 'none'
            },
            hAxis: {
                title: 'Month'
            },
            vAxis: {
                title: 'Amount (₱)'
            },
            backgroundColor: '#f4f4f4',
            colors: ['#4285F4']
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('dtr_charts'));
        chart.draw(data, options);
    }

    function drawPaymentMethodChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Payment Method');
        data.addColumn('number', 'Count');
        data.addRows(<?php echo json_encode($paymentMethodData); ?>);

        var options = {
            title: 'Payment Method Distribution',
            legend: {
                position: 'bottom'
            },
            pieHole: 0.4,
            backgroundColor: '#f4f4f4'
        };

        var chart = new google.visualization.PieChart(document.getElementById('payment_chart'));
        chart.draw(data, options);
    }

    // Place the resize event listener here:
    window.addEventListener('resize', drawAllCharts);
</script>


<?php include('./template/foot.php'); ?>