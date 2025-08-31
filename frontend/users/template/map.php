<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
  #centerList,
  #centerList * {
    font-family: 'Segoe UI', 'Roboto', 'Arial', sans-serif;
  }

  #centerList h4 {
    font-size: 1.15rem;
    font-weight: 600;
    margin-bottom: 12px;
    color: #1976d2;
  }

  .list-group-item.center-list-item {
    padding: 16px 18px 12px 18px;
    margin-bottom: 8px;
    border-radius: 10px;
    border: 1px solid #e3e6f0;
    background: #f9fbfd;
    transition: background 0.2s, box-shadow 0.2s;
    font-size: 1rem;
  }

  .list-group-item.center-list-item:hover {
    background: #e3f0fc;
    box-shadow: 0 2px 8px rgba(25, 118, 210, 0.07);
    cursor: pointer;
  }

  .center-list-item strong {
    font-size: 1.08em;
    font-weight: 600;
    color: #222;
  }

  .center-distance-badge {
    display: inline-block;
    margin: 0 0 2px 8px;
    padding: 1px 8px;
    background: #1976d2;
    color: #fff;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 500;
    vertical-align: middle;
    letter-spacing: 0.5px;
  }

  .list-group-item.center-list-item a {
    color: #1976d2;
    text-decoration: underline;
    font-weight: 500;
  }

  .list-group-item.center-list-item a:hover {
    color: #0d47a1;
    text-decoration: underline;
  }

  .list-group-item.center-list-item br {
    line-height: 1.5;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Use window.mapConfig if set, otherwise fallback to defaults
    const config = window.mapConfig || {};
    const centers = config.centers || [];
    const enableSearch = config.enableSearch || false;
    const enableSort = config.enableSort || false;
    const showUserLocation = config.showUserLocation || false;

    const defaultLat = 12.8797,
      defaultLng = 121.7740,
      defaultZoom = 6;
    let map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19
    }).addTo(map);

    // Add markers for all centers
    let markers = centers.map(center => {
      const marker = L.marker([center.latitude, center.longitude]).addTo(map);
      marker.bindPopup(`<strong>${center.name}</strong><br>${center.address}`);
      return {
        marker,
        center
      };
    });

    // Helper: Haversine formula for distance in km
    function getDistanceKm(lat1, lng1, lat2, lng2) {
      const R = 6371; // km
      const dLat = (lat2 - lat1) * Math.PI / 180;
      const dLng = (lng2 - lng1) * Math.PI / 180;
      const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLng / 2) * Math.sin(dLng / 2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return R * c;
    }

    // Helper: Update center list UI
    function updateCenterList(sortedCenters, userLat = null, userLng = null) {
      const listContainer = document.getElementById('centerList');
      if (!listContainer) return;
      let html = '<h4>All Centers</h4><ul class="list-group">';
      sortedCenters.forEach((center, idx) => {
        let distanceStr = '';
        if (userLat !== null && userLng !== null && center.latitude && center.longitude) {
          const dist = getDistanceKm(userLat, userLng, center.latitude, center.longitude);
          distanceStr = `<div class="center-distance-badge">${dist.toFixed(2)} km</div>`;
        }
        html += `<li class="list-group-item center-list-item" data-marker-idx="${idx}" style="cursor:pointer;">
          <strong>${center.name}</strong>
          ${distanceStr}
          <br>
          ${center.address}<br>
          ${center.contact_number ? `<strong>Contact:</strong> ${center.contact_number}<br>` : ''}
          ${center.email ? `<strong>Email:</strong> ${center.email}<br>` : ''}
          ${center.operating_hours ? `<strong>Operating Hours:</strong> ${center.operating_hours}<br>` : ''}
          ${center.type ? `<strong>Type:</strong> ${center.type}<br>` : ''}
          ${center.website_url ? `<strong>Website:</strong> <a href="${center.website_url}" target="_blank">Visit</a><br>` : ''}
        </li>`;
      });
      html += '</ul>';
      listContainer.innerHTML = html;

      // Add click event to each list item
      document.querySelectorAll('.center-list-item').forEach((item, idx) => {
        item.addEventListener('click', function() {
          // Find the marker for this center
          const markerObj = markers.find(m => m.center.name === sortedCenters[idx].name && m.center.address === sortedCenters[idx].address);
          if (markerObj) {
            map.setView([markerObj.center.latitude, markerObj.center.longitude], 16);
            markerObj.marker.openPopup();
          }
        });
      });
    }

    // Optionally show user location and sort by nearest
    if (showUserLocation && navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        const userLat = position.coords.latitude;
        const userLng = position.coords.longitude;
        const userMarker = L.marker([userLat, userLng], {
          icon: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
            shadowSize: [41, 41]
          })
        }).addTo(map);
        userMarker.bindPopup('<strong>Your Location</strong>').openPopup();

        if (enableSort) {
          // Sort centers by distance to user
          markers.sort((a, b) => {
            const distA = getDistanceKm(userLat, userLng, a.center.latitude, a.center.longitude);
            const distB = getDistanceKm(userLat, userLng, b.center.latitude, b.center.longitude);
            return distA - distB;
          });
          // Update the list UI if present, with distance
          updateCenterList(markers.map(m => m.center), userLat, userLng);
        }
        map.setView([userLat, userLng], 13);
      }, function() {
        // If user denies location, just show unsorted list
        if (enableSort) updateCenterList(markers.map(m => m.center));
      });
    } else if (enableSort) {
      // If not sorting by user, just show unsorted list
      updateCenterList(markers.map(m => m.center));
    }

    // Optionally enable search
    if (enableSearch) {
      const searchInput = document.getElementById('searchInput');
      if (searchInput) {
        searchInput.addEventListener('input', function(event) {
          const filter = searchInput.value.toLowerCase();
          let filtered = markers.filter(({
              center
            }) =>
            center.name.toLowerCase().includes(filter) ||
            center.address.toLowerCase().includes(filter)
          );
          // If user location is available, show distance
          if (window.lastUserLat !== undefined && window.lastUserLng !== undefined) {
            updateCenterList(filtered.map(m => m.center), window.lastUserLat, window.lastUserLng);
          } else {
            updateCenterList(filtered.map(m => m.center));
          }
        });
        searchInput.addEventListener('keypress', function(event) {
          if (event.key === 'Enter') {
            const filter = searchInput.value.toLowerCase();
            let found = false;
            markers.forEach(({
              marker,
              center
            }) => {
              const matches = center.name.toLowerCase().includes(filter) || center.address.toLowerCase().includes(filter);
              if (matches && !found) {
                map.setView([center.latitude, center.longitude], 15);
                marker.openPopup();
                found = true;
              }
            });
            if (!filter) {
              map.setView([defaultLat, defaultLng], defaultZoom);
            }
          }
        });
      }
    }

    // Store user location globally for search filtering
    if (showUserLocation && navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        window.lastUserLat = position.coords.latitude;
        window.lastUserLng = position.coords.longitude;
      });
    }
  });
</script>