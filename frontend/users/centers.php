<?php include "./template/header.php";

$url = "http://localhost:5000/user/user_get_locations";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$centers = json_decode($response, true);

?>
<div class="container my-4 min-vh-100" style="padding-top: 120px;">
    <h2 class="text-center">Donation Centers</h2>
    <div class="row">
        <div class="col-md-5 d-flex flex-column">
            <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search Nearby Centers...">
            <div id="centerList" class="mt-2 flex-grow-1" style="height: 430px; overflow-y: auto;"></div>
        </div>
        <div class="col-md-7">
            <div id="map" class="map-index" style="height: 500px;"></div>
            <script>
                window.mapConfig = {
                    centers: <?php echo json_encode($centers); ?>,
                    enableSearch: true,
                    enableSort: true,
                    showUserLocation: true
                };
            </script>
            <?php include './template/map.php'; ?>
        </div>
    </div>
</div>
<?php include "./template/footer.php"; ?>