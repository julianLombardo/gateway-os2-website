<?php
// Blog listing page — receives $posts (array), $tag_filter (string), $pagination (array)
?>
  <section class="hero" style="padding: 5rem 3rem 3rem;">
    <div class="hero-content">
      <p class="overline">From the Workshop</p>
      <h1>Blog<?php echo $tag_filter ? ' <em>#' . htmlspecialchars($tag_filter) . '</em>' : ''; ?></h1>
      <p class="subtitle">Development updates, technical deep dives, and the story behind GatewayOS2.</p>
      <?php if ($tag_filter): ?>
        <br><a href="/blog" class="btn btn-secondary">Clear filter</a>
      <?php endif; ?>
    </div>
  </section>

  <section class="alt">
    <div class="container">
      <div class="blog-list">
        <?php if (empty($posts)): ?>
          <p style="text-align: center; color: var(--stone);">No posts found.</p>
        <?php endif; ?>
        <?php foreach ($posts as $post): ?>
          <article class="blog-card">
            <div class="blog-card-date">
              <span class="blog-day"><?php echo date('d', strtotime($post['date'])); ?></span>
              <span class="blog-month"><?php echo date('M', strtotime($post['date'])); ?></span>
            </div>
            <div class="blog-card-body">
              <h2><a href="/blog/<?php echo htmlspecialchars($post['slug'] ?? $post['id']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
              <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
              <div class="blog-card-meta">
                <?php foreach (($post['tags'] ?? []) as $tag): ?>
                  <a href="/blog?tag=<?php echo urlencode($tag); ?>" class="blog-tag"><?php echo htmlspecialchars($tag); ?></a>
                <?php endforeach; ?>
                <span class="blog-read-more"><a href="/blog/<?php echo htmlspecialchars($post['slug'] ?? $post['id']); ?>">Read more &rarr;</a></span>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <?php if (!empty($pagination)): ?>
        <?php include BASE_DIR . '/templates/partials/pagination.php'; ?>
      <?php endif; ?>
    </div>
  </section>
