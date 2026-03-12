/* GatewayOS2 — Admin Table Sorting & Filtering */

document.addEventListener('DOMContentLoaded', function() {
  // Sortable tables
  document.querySelectorAll('.admin-table th[data-sort]').forEach(function(th) {
    th.style.cursor = 'pointer';
    th.addEventListener('click', function() {
      var table = this.closest('table');
      var tbody = table.querySelector('tbody');
      if (!tbody) return;

      var colIndex = Array.prototype.indexOf.call(this.parentElement.children, this);
      var sortKey = this.getAttribute('data-sort');
      var ascending = this.getAttribute('data-dir') !== 'asc';
      this.setAttribute('data-dir', ascending ? 'asc' : 'desc');

      // Reset other column sort indicators
      table.querySelectorAll('th[data-sort]').forEach(function(otherTh) {
        if (otherTh !== th) otherTh.removeAttribute('data-dir');
      });

      var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));

      rows.sort(function(a, b) {
        var aVal = a.children[colIndex] ? a.children[colIndex].textContent.trim() : '';
        var bVal = b.children[colIndex] ? b.children[colIndex].textContent.trim() : '';

        if (sortKey === 'number') {
          aVal = parseFloat(aVal) || 0;
          bVal = parseFloat(bVal) || 0;
          return ascending ? aVal - bVal : bVal - aVal;
        }

        if (sortKey === 'date') {
          aVal = new Date(aVal).getTime() || 0;
          bVal = new Date(bVal).getTime() || 0;
          return ascending ? aVal - bVal : bVal - aVal;
        }

        // Default: string sort
        aVal = aVal.toLowerCase();
        bVal = bVal.toLowerCase();
        if (aVal < bVal) return ascending ? -1 : 1;
        if (aVal > bVal) return ascending ? 1 : -1;
        return 0;
      });

      for (var i = 0; i < rows.length; i++) {
        tbody.appendChild(rows[i]);
      }
    });
  });

  // Table filter
  var filterInput = document.getElementById('admin-table-filter');
  if (filterInput) {
    filterInput.addEventListener('input', function() {
      var query = this.value.toLowerCase();
      var table = document.querySelector(this.getAttribute('data-table') || '.admin-table');
      if (!table) return;

      var tbody = table.querySelector('tbody');
      if (!tbody) return;

      tbody.querySelectorAll('tr').forEach(function(row) {
        var text = row.textContent.toLowerCase();
        row.style.display = text.indexOf(query) !== -1 ? '' : 'none';
      });
    });
  }
});
