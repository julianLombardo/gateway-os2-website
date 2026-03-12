/* GatewayOS2 — Admin Markdown Editor with Live Preview */

document.addEventListener('DOMContentLoaded', function() {
  var editorTextarea = document.getElementById('editor-markdown');
  var previewPane = document.getElementById('editor-preview');
  if (!editorTextarea || !previewPane) return;

  function parseMarkdown(md) {
    var html = md;

    // Headers
    html = html.replace(/^### (.+)$/gm, '<h3>$1</h3>');
    html = html.replace(/^## (.+)$/gm, '<h2>$1</h2>');
    html = html.replace(/^# (.+)$/gm, '<h1>$1</h1>');

    // Bold and italic
    html = html.replace(/\*\*\*(.+?)\*\*\*/g, '<strong><em>$1</em></strong>');
    html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');

    // Inline code
    html = html.replace(/`([^`]+)`/g, '<code>$1</code>');

    // Code blocks
    html = html.replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>');

    // Links
    html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>');

    // Unordered lists
    html = html.replace(/^- (.+)$/gm, '<li>$1</li>');
    html = html.replace(/(<li>.*<\/li>\n?)+/g, function(match) {
      return '<ul>' + match + '</ul>';
    });

    // Ordered lists
    html = html.replace(/^\d+\. (.+)$/gm, '<li>$1</li>');

    // Blockquotes
    html = html.replace(/^> (.+)$/gm, '<blockquote>$1</blockquote>');

    // Horizontal rules
    html = html.replace(/^---$/gm, '<hr>');

    // Paragraphs: wrap lines that are not already wrapped in block tags
    var lines = html.split('\n');
    var result = [];
    var inBlock = false;
    for (var i = 0; i < lines.length; i++) {
      var line = lines[i].trim();
      if (line === '') {
        result.push('');
        continue;
      }
      if (line.match(/^<(h[1-6]|ul|ol|li|pre|blockquote|hr|div|table)/)) {
        inBlock = true;
        result.push(line);
      } else if (line.match(/^<\/(ul|ol|pre|blockquote|div|table)/)) {
        inBlock = false;
        result.push(line);
      } else if (!inBlock && !line.match(/^<(h[1-6]|li|hr)/)) {
        result.push('<p>' + line + '</p>');
      } else {
        result.push(line);
      }
    }

    return result.join('\n');
  }

  function updatePreview() {
    previewPane.innerHTML = parseMarkdown(editorTextarea.value);
  }

  editorTextarea.addEventListener('input', updatePreview);

  // Initial render
  updatePreview();

  // Tab key support in textarea
  editorTextarea.addEventListener('keydown', function(e) {
    if (e.key === 'Tab') {
      e.preventDefault();
      var start = this.selectionStart;
      var end = this.selectionEnd;
      this.value = this.value.substring(0, start) + '  ' + this.value.substring(end);
      this.selectionStart = this.selectionEnd = start + 2;
      updatePreview();
    }
  });
});
