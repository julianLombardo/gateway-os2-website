<?php
// Blog post editor — receives $post (array|null for create), $errors (array)
$post = $post ?? null;
$errors = $errors ?? [];
$is_edit = $post !== null;
?>
<div class="admin-section">
  <div class="admin-section-header">
    <h2><?php echo $is_edit ? 'Edit Post' : 'New Post'; ?></h2>
    <a href="/admin/blog" class="btn btn-secondary">Back to Posts</a>
  </div>

  <?php if (!empty($errors)): ?>
    <div class="auth-alert error">
      <?php foreach ($errors as $e): ?><p><?php echo htmlspecialchars($e); ?></p><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="<?php echo $is_edit ? '/admin/blog/edit/' . htmlspecialchars($post['id']) : '/admin/blog/new'; ?>" class="admin-form">
    <?php if (function_exists('csrf_field')) echo csrf_field(); ?>

    <div class="admin-form-row">
      <div class="form-group" style="flex: 2;">
        <label for="title">Title <span class="required">*</span></label>
        <input type="text" id="title" name="title" required
               value="<?php echo htmlspecialchars($post['title'] ?? ''); ?>"
               placeholder="Post title"
               oninput="autoSlug(this.value)">
      </div>
      <div class="form-group" style="flex: 1;">
        <label for="slug">Slug</label>
        <input type="text" id="slug" name="slug"
               value="<?php echo htmlspecialchars($post['slug'] ?? ''); ?>"
               placeholder="auto-generated-from-title"
               pattern="[a-z0-9\-]+"
               title="Lowercase letters, numbers, and hyphens only">
      </div>
    </div>

    <div class="admin-form-row">
      <div class="form-group">
        <label for="date">Date <span class="required">*</span></label>
        <input type="date" id="date" name="date" required
               value="<?php echo htmlspecialchars($post['date'] ?? date('Y-m-d')); ?>">
      </div>
      <div class="form-group">
        <label for="author">Author <span class="required">*</span></label>
        <input type="text" id="author" name="author" required
               value="<?php echo htmlspecialchars($post['author'] ?? ($_SESSION['display_name'] ?? '')); ?>"
               placeholder="Author name">
      </div>
      <div class="form-group">
        <label for="tags">Tags <span style="font-weight: normal; color: var(--smoke);">(comma-separated)</span></label>
        <input type="text" id="tags" name="tags"
               value="<?php echo htmlspecialchars(implode(', ', $post['tags'] ?? [])); ?>"
               placeholder="e.g. kernel, networking, update">
      </div>
    </div>

    <div class="form-group">
      <label for="excerpt">Excerpt <span class="required">*</span></label>
      <textarea id="excerpt" name="excerpt" rows="2" required
                placeholder="Brief summary shown in blog listing"><?php echo htmlspecialchars($post['excerpt'] ?? ''); ?></textarea>
    </div>

    <div class="form-group">
      <label for="content">Content <span class="required">*</span> <span style="font-weight: normal; color: var(--smoke);">(Markdown supported: ## headings, **bold**, *italic*, `code`, [links](url), - lists)</span></label>
      <div class="admin-editor-toolbar">
        <button type="button" class="admin-editor-btn" onclick="insertMarkdown('**', '**')" title="Bold">B</button>
        <button type="button" class="admin-editor-btn" onclick="insertMarkdown('*', '*')" title="Italic"><em>I</em></button>
        <button type="button" class="admin-editor-btn" onclick="insertMarkdown('`', '`')" title="Code">&lt;/&gt;</button>
        <button type="button" class="admin-editor-btn" onclick="insertMarkdown('## ', '')" title="Heading">H2</button>
        <button type="button" class="admin-editor-btn" onclick="insertMarkdown('- ', '')" title="List">List</button>
        <button type="button" class="admin-editor-btn" onclick="insertMarkdown('[', '](url)')" title="Link">Link</button>
        <span class="admin-editor-sep"></span>
        <button type="button" class="admin-editor-btn" onclick="togglePreview()" title="Preview">Preview</button>
      </div>
      <textarea id="content" name="content" rows="16" required
                placeholder="Write your post content here..."><?php echo htmlspecialchars($post['content'] ?? ''); ?></textarea>
      <div id="content-preview" class="admin-preview" style="display: none;"></div>
    </div>

    <div class="admin-form-actions">
      <button type="submit" class="btn btn-primary"><?php echo $is_edit ? 'Update Post' : 'Publish Post'; ?></button>
      <a href="/admin/blog" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<script>
function autoSlug(title) {
  var slug = document.getElementById('slug');
  if (!slug.dataset.manual) {
    slug.value = title.toLowerCase()
      .replace(/[^a-z0-9\s\-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '');
  }
}

document.getElementById('slug').addEventListener('input', function() {
  this.dataset.manual = '1';
});

function insertMarkdown(before, after) {
  var ta = document.getElementById('content');
  var start = ta.selectionStart;
  var end = ta.selectionEnd;
  var text = ta.value;
  var selected = text.substring(start, end) || 'text';
  ta.value = text.substring(0, start) + before + selected + after + text.substring(end);
  ta.focus();
  ta.selectionStart = start + before.length;
  ta.selectionEnd = start + before.length + selected.length;
}

function togglePreview() {
  var ta = document.getElementById('content');
  var preview = document.getElementById('content-preview');
  if (preview.style.display === 'none') {
    // Simple markdown to HTML
    var html = ta.value
      .replace(/^## (.+)$/gm, '<h3>$1</h3>')
      .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
      .replace(/\*(.+?)\*/g, '<em>$1</em>')
      .replace(/`(.+?)`/g, '<code>$1</code>')
      .replace(/\[(.+?)\]\((.+?)\)/g, '<a href="$2">$1</a>')
      .replace(/^- (.+)$/gm, '<li>$1</li>')
      .replace(/\n\n/g, '<br><br>')
      .replace(/\n/g, '<br>');
    preview.innerHTML = html;
    preview.style.display = 'block';
    ta.style.display = 'none';
  } else {
    preview.style.display = 'none';
    ta.style.display = 'block';
  }
}
</script>
