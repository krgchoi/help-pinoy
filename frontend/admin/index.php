<?php
session_start();

$jwt_token = $_SESSION['access_token'];

// API CALL FIRST
$url = "http://localhost:5000/admin/dashboard_data";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "Authorization: Bearer $jwt_token"
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Check for curl error or 401 Unauthorized
if ($response === false || $http_code === 401) {
    session_destroy();
    header('Location: admin_logout.php');
    exit();
}

$data = json_decode($response, true);

// Check if response is valid JSON array
if (!is_array($data)) {
    session_destroy();
    header('Location: admin_logout.php');
    exit();
}

// Check for error status (redundant but kept for safety)
if (isset($data['status']) && $data['status'] === 'error') {
    session_destroy();
    header('Location: admin_logout.php');
    exit();
}

// ✅ NOW include navbar AFTER logic
include('./template/navbar.php');

$sd = $data['sd'] ?? 0;
$sd_month = $data['sd_month'] ?? 0;
$td = $data['td'] ?? 0;
$tu = $data['tu'] ?? 0;
$dm = $data['dm'] ?? [];
$dr = $data['dr'] ?? [];
$dt = $data['dt'] ?? [];
$rd = $data['rd'] ?? [];
$tp = $data['tp'] ?? [];
$dtr = $data['dtr'] ?? [];

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
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    :root {
        --primary-blue: #003366;
        --secondary-blue: #0057b7;
        --accent-yellow: #FFCC00;
        --success-green: #28a745;
        --warning-orange: #fd7e14;
        --info-teal: #17a2b8;
        --light-blue: #e6f2ff;
        --dark-gray: #2c3e50;
        --light-gray: #f8f9fa;
    }

    .dashboard-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
        padding: 20px;
    }

    .dashboard-header {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        color: white;
        padding: 30px 0;
        margin: -20px -20px 30px -20px;
        border-radius: 0 0 25px 25px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .dashboard-header h1 {
        font-weight: 800;
        margin-bottom: 10px;
        font-size: 2.5rem;
    }

    .dashboard-header p {
        opacity: 0.9;
        font-size: 1.1rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 30px 25px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-blue), var(--secondary-blue));
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .stat-card.primary {
        border-top: 4px solid var(--primary-blue);
    }

    .stat-card.success {
        border-top: 4px solid var(--success-green);
    }

    .stat-card.warning {
        border-top: 4px solid var(--warning-orange);
    }

    .stat-card.info {
        border-top: 4px solid var(--info-teal);
    }

    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        font-size: 1.8rem;
        color: white;
    }

    .stat-card.primary .stat-icon {
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
    }

    .stat-card.success .stat-icon {
        background: linear-gradient(135deg, var(--success-green), #20c997);
    }

    .stat-card.warning .stat-icon {
        background: linear-gradient(135deg, var(--warning-orange), #fd9843);
    }

    .stat-card.info .stat-icon {
        background: linear-gradient(135deg, var(--info-teal), #39c0ed);
    }

    .stat-content h3 {
        font-size: 0.9rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--dark-gray);
        line-height: 1;
        margin-bottom: 5px;
    }

    .stat-change {
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .stat-change.positive {
        color: var(--success-green);
    }

    .stat-change.negative {
        color: #dc3545;
    }

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 30px;
        margin-bottom: 40px;
    }

    .chart-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .chart-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 25px;
    }

    .chart-header h3 {
        font-weight: 700;
        color: var(--dark-gray);
        margin: 0;
        font-size: 1.3rem;
    }

    .chart-container {
        width: 100%;
        height: 350px;
        border-radius: 12px;
        overflow: hidden;
    }

    .tables-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-bottom: 40px;
    }

    .table-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .table-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 25px;
    }

    .table-header h3 {
        font-weight: 700;
        color: var(--dark-gray);
        margin: 0;
        font-size: 1.3rem;
    }

    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }

    .table {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead th {
        background: var(--light-blue);
        color: var(--primary-blue);
        font-weight: 700;
        padding: 15px 20px;
        border: none;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table tbody td {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
        color: var(--dark-gray);
    }

    .table tbody tr:hover {
        background: var(--light-gray);
        transform: scale(1.01);
        transition: all 0.2s ease;
    }

    .badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .bg-success {
        background: linear-gradient(135deg, var(--success-green), #20c997) !important;
    }

    .bg-warning {
        background: linear-gradient(135deg, var(--warning-orange), #fd9843) !important;
    }

    .bg-secondary {
        background: linear-gradient(135deg, #6c757d, #868e96) !important;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .charts-grid {
            grid-template-columns: 1fr;
        }

        .tables-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 15px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .charts-grid {
            grid-template-columns: 1fr;
        }

        .chart-card {
            padding: 20px;
        }

        .table-card {
            padding: 20px;
        }

        .stat-value {
            font-size: 1.8rem;
        }

        .dashboard-header h1 {
            font-size: 2rem;
        }
    }

    @media (max-width: 576px) {
        .chart-container {
            height: 300px;
        }
    }

    /* Loading animation */
    .loading {
        opacity: 0.7;
        pointer-events: none;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }
</style>

<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <h1>Admin Dashboard</h1>
                    <p>Welcome back! Here's your overview of Help Pinoy activities.</p>
                </div>
                <div class="col-auto">
                    <div class="text-end">
                        <small class="opacity-75">Last updated: <?php echo date('M j, Y g:i A'); ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Donations</h3>
                    <div class="stat-value">₱<?php echo number_format($sd, 2, '.', ','); ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        All-time contributions
                    </div>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>This Month</h3>
                    <div class="stat-value">₱<?php echo $sd_month ? number_format($sd_month, 2, '.', ',') : '0.00'; ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-chart-line"></i>
                        Current month performance
                    </div>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Donors</h3>
                    <div class="stat-value"><?php echo number_format($td); ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-user-plus"></i>
                        Active contributors
                    </div>
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Users</h3>
                    <div class="stat-value"><?php echo number_format($tu); ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-network-wired"></i>
                        Platform users
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-bar me-2"></i>Donation Trends</h3>
                </div>
                <div class="chart-container">
                    <div id="dtr_charts" style="width: 100%; height: 100%;"></div>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-pie me-2"></i>Payment Methods</h3>
                </div>
                <div class="chart-container">
                    <div id="payment_chart" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>

        <!-- Tables Section -->
        <div class="tables-grid">
            <!-- Recent Donations -->
            <div class="table-card">
                <div class="table-header">
                    <h3><i class="fas fa-history me-2"></i>Recent Donations</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
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
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($row['full_name']); ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-primary">₱<?php echo number_format($row['amount'], 2, '.', ','); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $row['donation_status'] === 'APPROVED' ? 'success' : ($row['donation_status'] === 'PENDING' ? 'warning' : 'secondary'); ?>">
                                            <?php echo htmlspecialchars($row['donation_status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Donors -->
            <div class="table-card">
                <div class="table-header">
                    <h3><i class="fas fa-trophy me-2"></i>Top Donors</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tp as $row): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-warning rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <i class="fas fa-crown text-white"></i>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($row['full_name']); ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-success">₱<?php echo number_format($row['total'], 2, '.', ','); ?></strong>
                                    </td>
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
            title: '',
            legend: {
                position: 'none'
            },
            hAxis: {
                title: 'Month',
                textStyle: {
                    color: '#6c757d'
                }
            },
            vAxis: {
                title: 'Amount (₱)',
                textStyle: {
                    color: '#6c757d'
                },
                format: '₱#,##0'
            },
            backgroundColor: 'transparent',
            colors: ['#0057b7'],
            chartArea: {
                width: '85%',
                height: '75%'
            },
            bar: {
                groupWidth: '70%'
            },
            animation: {
                startup: true,
                duration: 1000,
                easing: 'out'
            }
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
            legend: {
                position: 'labeled',
                textStyle: {
                    color: '#6c757d'
                }
            },
            pieHole: 0.4,
            backgroundColor: 'transparent',
            colors: ['#0057b7', '#FFCC00', '#28a745', '#fd7e14'],
            chartArea: {
                width: '90%',
                height: '80%'
            },
            animation: {
                startup: true,
                duration: 1000,
                easing: 'out'
            },
            pieSliceText: 'value',
            tooltip: {
                text: 'percentage'
            }
        };

        var chart = new google.visualization.PieChart(document.getElementById('payment_chart'));
        chart.draw(data, options);
    }

    window.addEventListener('resize', drawAllCharts);

    // Auto-refresh charts every 30 seconds
    setInterval(drawAllCharts, 30000);
</script>