<?php
// Search page — receives $query (string), $results (array)
$query = $query ?? '';
$results = $results ?? [];
?>
  <section class="hero" style="padding: 4rem 3rem 2rem;">
    <div class="hero-content">
      <p class="overline">Find</p>
      <h1>Search</h1>
    </div>
  </section>

  <section class="alt">
    <div class="container">
      <div class="search-box">
        <form method="GET" action="/search" class="search-form">
          <input type="text" name="q" value="<?php echo htmlspecialchars($query); ?>"
                 placeholder="Search pages, blog posts, features..."
                 autofocus autocomplete="off"
                 aria-label="Search">
          <button type="submit" class="btn btn-primary">Search</button>
        </form>
      </div>

      <?php if ($query): ?>
        <div class="search-results">
          <p class="search-count">
            <?php echo count($results); ?> result<?php echo count($results) !== 1 ? 's' : ''; ?>
            for &ldquo;<?php echo htmlspecialchars($query); ?>&rdquo;
          </p>

          <?php if (empty($results)): ?>
            <div class="search-empty">
              <p>No results found. Try different keywords.</p>
            </div>
          <?php endif; ?>

          <?php foreach ($results as $r): ?>
            <a href="<?php echo htmlspecialchars($r['url']); ?>" class="search-result">
              <div class="search-result-type"><?php echo htmlspecialchars($r['type']); ?></div>
              <h3><?php echo htmlspecialchars($r['title']); ?></h3>
              <p><?php echo htmlspecialchars($r['desc']); ?></p>
              <?php if (isset($r['date'])): ?>
                <span class="search-result-date"><?php echo date('M j, Y', strtotime($r['date'])); ?></span>
              <?php endif; ?>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>
