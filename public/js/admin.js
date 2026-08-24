// Sidebar toggle (mobile)
function openSidebar() {
  document.getElementById('sidebar').classList.add('open');
  document.getElementById('sidebar-overlay').classList.add('show');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebar-overlay').classList.remove('show');
}

// Auto-dismiss alerts
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.alert').forEach(function (el) {
    setTimeout(function () {
      const alert = bootstrap.Alert.getOrCreateInstance(el);
      if (alert) alert.close();
    }, 5000);
  });

  // Confirm delete dialogs
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(this.dataset.confirm || 'Are you sure?')) e.preventDefault();
    });
  });

  // Initialize tooltips
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
    new bootstrap.Tooltip(el);
  });
});

// Map init helper (used by mapping page)
function initMap(issues) {
  if (typeof L === 'undefined') return;
  var map = L.map('map').setView([14.3506, 121.0453], 15);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  var colors = {
    broken_streetlight: '#E24B4A', garbage_collection: '#BA7517',
    illegal_dumping: '#8B4513', road_damage: '#555', clogged_drainage: '#185FA5',
    flooding: '#0066CC', noise_complaint: '#9333EA', stray_animal: '#D97706',
    public_safety: '#DC2626'
  };

  issues.forEach(function (issue) {
    var color = colors[issue.request_type] || '#1a3a6b';
    L.circleMarker([issue.latitude, issue.longitude], {
      radius: 10, fillColor: color, color: '#fff',
      weight: 2, opacity: 1, fillOpacity: 0.9
    }).addTo(map).bindPopup(
      '<strong>' + issue.request_type.replace(/_/g, ' ') + '</strong><br>' +
      issue.location + '<br><span class="badge">' + issue.status + '</span>'
    );
  });
}

// QR scanner placeholder
function startQrScan() {
  alert('QR scanner requires camera access. This will activate the device camera in the full implementation using jsQR or QuaggaJS library.');
}
