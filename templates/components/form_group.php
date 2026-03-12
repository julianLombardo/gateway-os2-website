<?php
// Form field wrapper — receives $label, $name, $type, $value, $placeholder, $required, $error
// Optional: $autocomplete, $min, $max, $pattern, $minlength, $maxlength
$type = $type ?? 'text';
$value = $value ?? '';
$placeholder = $placeholder ?? '';
$required = $required ?? false;
$error = $error ?? '';
?>
<div class="form-group<?php echo $error ? ' has-error' : ''; ?>">
  <label for="<?php echo htmlspecialchars($name); ?>">
    <?php echo htmlspecialchars($label); ?>
    <?php if ($required): ?><span class="required">*</span><?php endif; ?>
  </label>
  <?php if ($type === 'textarea'): ?>
    <textarea
      id="<?php echo htmlspecialchars($name); ?>"
      name="<?php echo htmlspecialchars($name); ?>"
      placeholder="<?php echo htmlspecialchars($placeholder); ?>"
      <?php echo $required ? 'required' : ''; ?>
      <?php echo !empty($minlength) ? 'minlength="' . (int)$minlength . '"' : ''; ?>
      rows="<?php echo $rows ?? 4; ?>"
    ><?php echo htmlspecialchars($value); ?></textarea>
  <?php elseif ($type === 'password'): ?>
    <div class="password-wrapper">
      <input
        type="password"
        id="<?php echo htmlspecialchars($name); ?>"
        name="<?php echo htmlspecialchars($name); ?>"
        placeholder="<?php echo htmlspecialchars($placeholder); ?>"
        <?php echo $required ? 'required' : ''; ?>
        <?php echo !empty($minlength) ? 'minlength="' . (int)$minlength . '"' : ''; ?>
        <?php echo !empty($autocomplete) ? 'autocomplete="' . htmlspecialchars($autocomplete) . '"' : ''; ?>
      >
      <button type="button" class="toggle-password" onclick="togglePassword('<?php echo htmlspecialchars($name); ?>', this)" aria-label="Show password">
        <span class="eye-icon">Show</span>
      </button>
    </div>
  <?php else: ?>
    <input
      type="<?php echo htmlspecialchars($type); ?>"
      id="<?php echo htmlspecialchars($name); ?>"
      name="<?php echo htmlspecialchars($name); ?>"
      value="<?php echo htmlspecialchars($value); ?>"
      placeholder="<?php echo htmlspecialchars($placeholder); ?>"
      <?php echo $required ? 'required' : ''; ?>
      <?php echo !empty($autocomplete) ? 'autocomplete="' . htmlspecialchars($autocomplete) . '"' : ''; ?>
      <?php echo !empty($pattern) ? 'pattern="' . htmlspecialchars($pattern) . '"' : ''; ?>
      <?php echo !empty($minlength) ? 'minlength="' . (int)$minlength . '"' : ''; ?>
      <?php echo !empty($maxlength) ? 'maxlength="' . (int)$maxlength . '"' : ''; ?>
    >
  <?php endif; ?>
  <?php if ($error): ?>
    <span class="form-error"><?php echo htmlspecialchars($error); ?></span>
  <?php endif; ?>
</div>
