<?php
// Single blog post — receives $post (array with title, date, author, tags, content_html or content)
?>
  <section class="hero" style="padding: 5rem 3rem 2rem;">
    <div class="hero-content">
      <p class="overline"><?php echo date('F j, Y', strtotime($post['date'])); ?></p>
      <h1><?php echo htmlspecialchars($post['title']); ?></h1>
      <p class="subtitle">By <?php echo htmlspecialchars($post['author']); ?></p>
    </div>
  </section>

  <section class="alt">
    <div class="container">
      <article class="blog-article">
        <div class="blog-tags">
          <?php foreach (($post['tags'] ?? []) as $tag): ?>
            <a href="/blog?tag=<?php echo urlencode($tag); ?>" class="blog-tag"><?php echo htmlspecialchars($tag); ?></a>
          <?php endforeach; ?>
        </div>
        <div class="blog-body">
          <?php echo $post['content_html'] ?? ''; ?>
        </div>
        <div class="blog-nav-back">
          <a href="/blog">&larr; All posts</a>
        </div>
      </article>
    </div>
  </section>
