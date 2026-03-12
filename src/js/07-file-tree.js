/* GatewayOS2 — File Tree (guide.php) */

function toggleFolder(el) {
  var children = el.nextElementSibling;
  if (!children || !children.classList.contains('file-tree-children')) return;

  var isOpen = children.style.display !== 'none';
  children.style.display = isOpen ? 'none' : 'block';

  var icon = el.querySelector('.file-icon');
  if (icon) icon.textContent = isOpen ? '\u25B6' : '\u25BC';

  el.classList.toggle('open', !isOpen);
}
