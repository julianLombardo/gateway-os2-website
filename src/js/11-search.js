/* GatewayOS2 — Live Search */

document.addEventListener('DOMContentLoaded', function() {
  var searchInput = document.querySelector('.search-form input[name="q"]');
  if (!searchInput) return;

  var resultsContainer = document.querySelector('.search-results');
  if (!resultsContainer) return;

  var liveSearch = debounce(function() {
    var query = searchInput.value.trim();
    if (query.length < 2) return;

    fetch('/search.php?q=' + encodeURIComponent(query) + '&ajax=1')
      .then(function(r) { return r.text(); })
      .then(function(html) {
        resultsContainer.innerHTML = html;
      })
      .catch(function() {
        // Silently fail on network errors
      });
  }, 300);

  searchInput.addEventListener('input', liveSearch);
});
