document.addEventListener('DOMContentLoaded', function() {
  // Sidebar Toggle
  document.querySelector('.navbar-toggler').addEventListener('click', function() {
    document.querySelector('.sidebar').classList.toggle('active');
    document.querySelector('.main-content').classList.toggle('active');
  });

  // Form Validation
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', function(event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });

  // Live Search
  const searchInput = document.getElementById('searchInput');
  if(searchInput) {
    searchInput.addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      filterTable(searchTerm);
    });
  }

  // Initialize Chart
  initializeStockChart();
});

function filterTable(searchTerm) {
  const rows = document.querySelectorAll('#itemsTable tbody tr');
  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(searchTerm) ? '' : 'none';
  });
}

function initializeStockChart() {
  const ctx = document.getElementById('stockChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Baju', 'Celana', 'Aksesoris'],
      datasets: [{
        label: 'Stok Tersedia',
        data: [120, 80, 45],
        backgroundColor: 'rgba(54, 162, 235, 0.5)'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  });
}

// AJAX Function
async function fetchData(url, params = {}) {
  try {
    const response = await fetch(url + new URLSearchParams(params), {
      headers: {
        'Content-Type': 'application/json'
      }
    });
    return await response.json();
  } catch (error) {
    console.error('Error:', error);
  }
}