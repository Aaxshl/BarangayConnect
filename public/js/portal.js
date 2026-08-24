document.addEventListener('DOMContentLoaded', function () {
  // Auto-dismiss alerts
  document.querySelectorAll('.alert').forEach(function (el) {
    setTimeout(function () {
      const alert = bootstrap.Alert.getOrCreateInstance(el);
      if (alert) alert.close();
    }, 5000);
  });

  // Document type selector
  document.querySelectorAll('.doc-type-card').forEach(function (card) {
    card.addEventListener('click', function () {
      document.querySelectorAll('.doc-type-card').forEach(c => c.classList.remove('selected'));
      this.classList.add('selected');
      var type = this.dataset.type;
      var input = document.getElementById('selected_doc_type');
      if (input) input.value = type;
      var fields = document.getElementById('doc-request-fields');
      if (fields) {
        fields.classList.remove('d-none');
        var label = document.getElementById('selected-doc-label');
        if (label) label.textContent = this.querySelector('.doc-type-title').textContent;
      }
    });
  });

  // Geolocation for report form
  var locBtn = document.getElementById('get-location-btn');
  if (locBtn) {
    locBtn.addEventListener('click', function () {
      if (!navigator.geolocation) { alert('Geolocation not supported.'); return; }
      navigator.geolocation.getCurrentPosition(function (pos) {
        document.getElementById('latitude').value  = pos.coords.latitude.toFixed(7);
        document.getElementById('longitude').value = pos.coords.longitude.toFixed(7);
        locBtn.textContent = 'Location captured ✓';
        locBtn.classList.add('btn-success');
      }, function () { alert('Unable to get location. Please enter it manually.'); });
    });
  }

  // Photo preview
  var photoInput = document.getElementById('photo');
  if (photoInput) {
    photoInput.addEventListener('change', function () {
      var preview = document.getElementById('photo-preview');
      if (!preview || !this.files[0]) return;
      var reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result;
        preview.classList.remove('d-none');
      };
      reader.readAsDataURL(this.files[0]);
    });
  }
});
