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

// Donation trends
$donationMonths = [];
$donationAmounts = [];
foreach ($dtr as $row) {
    $donationMonths[] = htmlspecialchars($row['month'], ENT_QUOTES, 'UTF-8');
    $donationAmounts[] = (float)$row['total_donations'];
}
$donationTrendsData = array_map(function ($month, $amount) {
    return [$month, $amount];
}, $donationMonths, $donationAmounts);

// Payment method
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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<style>
    .dashboard-card {
        border-radius: 15px;
        padding: 20px;
        color: #fff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
    }
    .bg-gradient-primary {
        background: linear-gradient(45deg, #1e3c72, #2a5298);
    }
    .bg-gradient-success {
        background: linear-gradient(45deg, #11998e, #38ef7d);
    }
    .bg-gradient-warning {
        background: linear-gradient(45deg, #ffb347, #ffcc33);
        color: #333;
    }
    .bg-gradient-info {
        background: linear-gradient(45deg, #2193b0, #6dd5ed);
    }
    .card-title {
        font-size: 1.2rem;
        font-weight: bold;
    }
    .card-value {
        font-size: 1.5rem;
        font-weight: bold;
    }
</style>

<div class="container-fluid py-4">
    <div class="row g-4">
        <div class="col-md-3">
            <div class="dashboard-card bg-gradient-primary">
                <i class="bi bi-cash-stack fs-1 mb-3"></i>
                <h5 class="card-title">Total Donations</h5>
                <p class="card-value">₱ <?php echo number_format($sd, 2, '.', ','); ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card bg-gradient-success">
                <i class="bi bi-calendar-event fs-1 mb-3"></i>
                <h5 class="card-title">This Month</h5>
                <p class="card-value">₱ <?php echo $sd_month ? number_format($sd_month, 2, '.', ',') : '0.00'; ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card bg-gradient-warning">
                <i class="bi bi-people fs-1 mb-3"></i>
                <h5 class="card-title">Donors</h5>
                <p class="card-value"><?php echo $td; ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card bg-gradient-info">
                <i class="bi bi-person-lines-fill fs-1 mb-3"></i>
                <h5 class="card-title">Total Users</h5>
                <p class="card-value"><?php echo $tu; ?></p>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold">Donation Trends</div>
                <div class="card-body">
                    <div id="dtr_charts" style="width: 100%; height: 350px;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold">Payment Methods</div>
                <div class="card-body">
                    <div id="payment_chart" style="width: 100%; height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold">Recent Donations</div>
                <div class="card-body">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Donor Name</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rd as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td>₱ <?php echo number_format($row['amount'], 2, '.', ','); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $row['payment_status'] === 'PAID' ? 'success' : ($row['payment_status'] === 'PENDING' ? 'warning' : 'secondary'); ?>">
                                            <?php echo htmlspecialchars($row['payment_status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold">Top Donors</div>
                <div class="card-body">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tp as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td>₱ <?php echo number_format($row['total'], 2, '.', ','); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Google Charts -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', { packages: ['corechart'] });
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
            title: '',
            legend: { position: 'none' },
            hAxis: { title: 'Month' },
            vAxis: { title: 'Amount (₱)' },
            backgroundColor: '#fff',
            colors: ['#4e73df']
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
            title: '',
            legend: { position: 'bottom' },
            pieHole: 0.4,
            backgroundColor: '#fff',
            colors: ['#36b9cc', '#f6c23e', '#1cc88a', '#e74a3b']
        };

        var chart = new google.visualization.PieChart(document.getElementById('payment_chart'));
        chart.draw(data, options);
    }

    window.addEventListener('resize', drawAllCharts);
</script>

<?php include('./template/foot.php'); ?>
