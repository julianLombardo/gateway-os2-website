<?php
// Admin blog list — receives $posts (array)
$posts = $posts ?? [];
?>
<div class="admin-section">
  <div class="admin-section-header">
    <h2>Blog Posts</h2>
    <a href="/admin/blog/new" class="btn btn-primary">New Post</a>
  </div>

  <?php if (empty($posts)): ?>
    <p class="admin-empty">No blog posts yet. <a href="/admin/blog/new">Create one.</a></p>
  <?php else: ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Title</th>
          <th>Slug</th>
          <th>Author</th>
          <th>Date</th>
          <th>Tags</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($posts as $post): ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($post['title']); ?></strong></td>
            <td><code><?php echo htmlspecialchars($post['slug'] ?? $post['id']); ?></code></td>
            <td><?php echo htmlspecialchars($post['author']); ?></td>
            <td><?php echo date('M j, Y', strtotime($post['date'])); ?></td>
            <td>
              <?php foreach (($post['tags'] ?? []) as $tag): ?>
                <span class="admin-tag"><?php echo htmlspecialchars($tag); ?></span>
              <?php endforeach; ?>
            </td>
            <td class="admin-actions">
              <a href="/admin/blog/edit/<?php echo htmlspecialchars($post['id']); ?>" class="btn btn-secondary btn-sm">Edit</a>
              <form method="POST" action="/admin/blog/delete/<?php echo htmlspecialchars($post['id']); ?>" style="display:inline;" onsubmit="return confirm('Delete this post?')">
                <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
