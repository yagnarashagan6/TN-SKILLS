<?php
// tnskills_asgardia.php
// Single-page form with server-side validation and simple client-side checks
// Place this file in the project root and open in a browser through a PHP server (php -S localhost:8000)

// Start session if you want to persist flash messages (optional)
// session_start();

$errors = [];
$values = [
  'full_name' => '',
  'email' => '',
  'phone' => '',
  'experience' => '',
  'skills' => [],
  'portfolio' => '',
  'cover' => ''
];

// Helper to escape output
function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  // Trim inputs
  $values['full_name'] = trim($_POST['full_name'] ?? '');
  $values['email'] = trim($_POST['email'] ?? '');
  $values['phone'] = trim($_POST['phone'] ?? '');
  $values['experience'] = trim($_POST['experience'] ?? '');
  $values['skills'] = $_POST['skills'] ?? [];
  $values['portfolio'] = trim($_POST['portfolio'] ?? '');
  $values['cover'] = trim($_POST['cover'] ?? '');

  // Validation rules
  if($values['full_name'] === ''){
    $errors['full_name'] = 'Full name is required.';
  } elseif(strlen($values['full_name']) < 3){
    $errors['full_name'] = 'Name must be at least 3 characters.';
  }

  if($values['email'] === ''){
    $errors['email'] = 'Email is required.';
  } elseif(!filter_var($values['email'], FILTER_VALIDATE_EMAIL)){
    $errors['email'] = 'Please enter a valid email address.';
  }

  if($values['phone'] === ''){
    $errors['phone'] = 'Phone number is required.';
  } else {
    $digits = preg_replace('/\D+/', '', $values['phone']);
    if(strlen($digits) < 10){
      $errors['phone'] = 'Enter a valid phone number (minimum 10 digits).';
    }
  }

  if(!is_numeric($values['experience']) || (int)$values['experience'] < 0){
    $errors['experience'] = 'Experience (years) must be a non-negative number.';
  }

  if(empty($values['skills']) || !is_array($values['skills'])){
    $errors['skills'] = 'Select at least one skill.';
  }

  if($values['portfolio'] !== '' && !filter_var($values['portfolio'], FILTER_VALIDATE_URL)){
    $errors['portfolio'] = 'Portfolio URL is not valid.';
  }

  if(empty($errors)){
    // Normally you'd persist to DB or send an email. For assignment, show a success message.
    $submitted = true;
  } else {
    $submitted = false;
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>TNSkills - Asgardia Assignment (Form validation)</title>
  <style>
    body{font-family: Arial, Helvetica, sans-serif; max-width:900px;margin:20px auto;padding:0 16px;color:#222}
    .field{margin-bottom:12px}
    label{display:block;font-weight:600;margin-bottom:4px}
    input[type="text"], input[type="email"], input[type="url"], textarea, select{width:100%;padding:8px;border:1px solid #ccc;border-radius:4px}
    .error{color:#b00020;font-size:0.95rem}
    .success{background:#e6ffed;border:1px solid #8de59a;padding:10px;border-radius:4px}
    .skills-row label{display:inline-block;margin-right:12px;font-weight:normal}
    button{background:#0066cc;color:#fff;padding:10px 14px;border:0;border-radius:6px;cursor:pointer}
  </style>
</head>
<body>
  <h1>TNSkills — Assignment Form (Asgardia)</h1>

  <?php if(!empty($submitted) && $submitted): ?>
    <div class="success">
      <strong>Submission successful!</strong>
      <p>Thank you, <?= e($values['full_name']) ?>. Your application has been received.</p>
      <ul>
        <li><strong>Email:</strong> <?= e($values['email']) ?></li>
        <li><strong>Phone:</strong> <?= e($values['phone']) ?></li>
        <li><strong>Experience:</strong> <?= e($values['experience']) ?> year(s)</li>
        <li><strong>Skills:</strong> <?= e(implode(', ', $values['skills'])) ?></li>
      </ul>
    </div>
  <?php else: ?>

  <?php if(!empty($errors)): ?>
    <div class="error">
      <strong>Please fix the errors below.</strong>
    </div>
  <?php endif; ?>

  <form method="post" id="tnform" novalidate>
    <div class="field">
      <label for="full_name">Full name *</label>
      <input id="full_name" name="full_name" type="text" value="<?= e($values['full_name']) ?>" required minlength="3">
      <?php if(isset($errors['full_name'])): ?><div class="error"><?= e($errors['full_name']) ?></div><?php endif; ?>
    </div>

    <div class="field">
      <label for="email">Email *</label>
      <input id="email" name="email" type="email" value="<?= e($values['email']) ?>" required>
      <?php if(isset($errors['email'])): ?><div class="error"><?= e($errors['email']) ?></div><?php endif; ?>
    </div>

    <div class="field">
      <label for="phone">Phone *</label>
      <input id="phone" name="phone" type="text" value="<?= e($values['phone']) ?>" placeholder="e.g. +91 98765 43210" required>
      <?php if(isset($errors['phone'])): ?><div class="error"><?= e($errors['phone']) ?></div><?php endif; ?>
    </div>

    <div class="field">
      <label for="experience">Experience (years) *</label>
      <input id="experience" name="experience" type="number" min="0" value="<?= e($values['experience']) ?>" required>
      <?php if(isset($errors['experience'])): ?><div class="error"><?= e($errors['experience']) ?></div><?php endif; ?>
    </div>

    <div class="field">
      <label>Skills (select at least one) *</label>
      <div class="skills-row">
        <?php $all = ['HTML','CSS','JavaScript','PHP','MySQL']; foreach($all as $s): ?>
          <label><input type="checkbox" name="skills[]" value="<?= e($s) ?>" <?= in_array($s, $values['skills']) ? 'checked' : '' ?>> <?= e($s) ?></label>
        <?php endforeach; ?>
      </div>
      <?php if(isset($errors['skills'])): ?><div class="error"><?= e($errors['skills']) ?></div><?php endif; ?>
    </div>

    <div class="field">
      <label for="portfolio">Portfolio URL (optional)</label>
      <input id="portfolio" name="portfolio" type="url" value="<?= e($values['portfolio']) ?>">
      <?php if(isset($errors['portfolio'])): ?><div class="error"><?= e($errors['portfolio']) ?></div><?php endif; ?>
    </div>

    <div class="field">
      <label for="cover">Short cover note</label>
      <textarea id="cover" name="cover" rows="4"><?= e($values['cover']) ?></textarea>
    </div>

    <button type="submit">Submit application</button>
  </form>

  <script>
  // Minimal client-side validation to improve UX (does not replace server validation)
  (function(){
    var form = document.getElementById('tnform');
    form.addEventListener('submit', function(e){
      var name = document.getElementById('full_name');
      var email = document.getElementById('email');
      var phone = document.getElementById('phone');
      var exp = document.getElementById('experience');
      var skills = document.querySelectorAll('input[name="skills[]"]:checked');
      var errs = [];
      if(!name.value || name.value.trim().length < 3) errs.push('Please enter a valid name (3+ chars).');
      if(!email.value || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email.value)) errs.push('Please enter a valid email.');
      var digits = phone.value.replace(/\D/g,''); if(digits.length < 10) errs.push('Please enter a valid phone number (10+ digits).');
      if(!exp.value || Number(exp.value) < 0) errs.push('Experience must be 0 or more.');
      if(skills.length === 0) errs.push('Select at least one skill.');
      if(errs.length){
        e.preventDefault();
        alert('Please fix the following:\n- ' + errs.join('\n- '));
      }
    });
  })();
  </script>

  <?php endif; ?>

</body>
</html>

<?php
// end of file
?>