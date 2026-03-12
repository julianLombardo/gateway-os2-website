<?php
// Blog post card — receives $post array with: id, slug, title, excerpt, date, tags, author
$post_url = '/blog/' . htmlspecialchars($post['slug'] ?? $post['id'] ?? '');
?>
<article class="blog-card">
  <div class="blog-card-date">
    <span class="blog-day"><?php echo date('d', strtotime($post['date'])); ?></span>
    <span class="blog-month"><?php echo date('M', strtotime($post['date'])); ?></span>
  </div>
  <div class="blog-card-body">
    <h2><a href="<?php echo $post_url; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
    <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
    <div class="blog-card-meta">
      <?php foreach (($post['tags'] ?? []) as $tag): ?>
        <a href="/blog?tag=<?php echo urlencode($tag); ?>" class="blog-tag"><?php echo htmlspecialchars($tag); ?></a>
      <?php endforeach; ?>
      <span class="blog-read-more"><a href="<?php echo $post_url; ?>">Read more &rarr;</a></span>
    </div>
  </div>
</article>
