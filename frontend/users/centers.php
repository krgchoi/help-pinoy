<?php
include "./template/header.php";

$url = "http://localhost:5000/user/user_get_locations";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$centers = json_decode($response, true);
?>

<style>
    .center-card {
        background: #ffffff;
        border-radius: 10px;
        padding: 15px;
        transition: 0.2s;
        cursor: pointer;
    }

    .center-card:hover {
        background: #f8f9fa;
        box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.1);
    }

    #centerList::-webkit-scrollbar {
        width: 6px;
    }

    #centerList::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }

    #map {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0px 3px 12px rgba(0, 0, 0, 0.2);
    }
</style>

<div class="container my-4 min-vh-100" style="padding-top: 120px;">
    <div class="text-center mb-4">
        <h2 class="fw-bold">Drop-Off Locations</h2>
        <p class="text-muted">Find the nearest Drop-off Locations in Guimbal.</p>
    </div>

    <div class="row g-4">
        <!-- LEFT SIDE: LIST + SEARCH -->
        <div class="col-md-5 d-flex flex-column">

            <div class="input-group mb-3">
                <span class="input-group-text bg-primary text-white">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control" placeholder="Search Nearby Centers...">
            </div>

            <div id="centerList" class="flex-grow-1"
                style="height: 460px; overflow-y: auto; padding-right: 6px;">
            </div>
        </div>

        <!-- RIGHT SIDE: MAP -->
        <div class="col-md-7">
            <div id="map" class="w-100" style="height: 520px;"></div>

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