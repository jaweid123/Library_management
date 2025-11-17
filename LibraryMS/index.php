<?php
// index.php
session_start();
// اگر بخواهی دسترسی صفحه فقط به کاربران لاگین شده باشد، می‌توان اینجا ریدایرکت کرد.
// اما ما اینجا اجازه می‌دهیم صفحه باز شود و کاربر با لاگین modal وارد شود.
// اگر بخواهی اجباری باشد، فعال کن:
// if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }
$currentUser = $_SESSION['user'] ?? null;
?><!doctype html>
<html lang="fa">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Kabul University Library - Admin</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="brand">Kabul University Library - Admin
      <?php if ($currentUser): ?>
        <span style="font-weight:600;margin-left:12px;">Welcome, <?= htmlspecialchars($currentUser['username']) ?> 👋</span>
      <?php endif; ?>
    </div>

    <div style="display:flex;align-items:center;gap:10px;">
      <?php if (!$currentUser): ?>
        <button class="login-btn" id="btnShowLogin">Login</button>
      <?php else: ?>
        <button class="login-btn" id="btnLogout">Logout</button>
      <?php endif; ?>
      <button class="hamburger" id="menuToggle" aria-label="menu">☰</button>
    </div>

    <!-- topnav: دو ردیف دکمه (desktop) و نیز برای mobile hidden until hamburger clicked -->
    <nav id="topnav" class="topnav hidden">
      <div class="nav-row" id="row1"></div>
      <div class="nav-row" id="row2"></div>
    </nav>
  </header>

  <main>
    <section class="control">
      <div class="left">
        <select id="tableSelector"></select>
        <button id="btnRefresh" class="btn-muted">Refresh</button>
        <button id="btnAdd" class="btn-primary">Add New</button>
        <input type="text" id="globalSearch" placeholder="Search in table...">
      </div>
      <div class="right" id="feedback"></div>
    </section>

    <section id="tableArea">
      <table id="dataTable">
        <thead id="tableHead"></thead>
        <tbody id="tableBody"></tbody>
      </table>
    </section>

    <!-- entity modal -->
    <div id="modal" class="modal hidden">
      <div class="modal-content">
        <h3 id="modalTitle">Form</h3>
        <form id="entityForm"></form>
        <div class="modal-actions">
          <button id="saveBtn" class="btn-primary" type="button">Save</button>
          <button id="cancelBtn" class="btn-muted" type="button">Cancel</button>
        </div>
      </div>
    </div>

    <!-- login modal -->
    <div id="loginModal" class="modal hidden">
      <div class="modal-content">
        <h3>Login</h3>
        <form id="loginForm">
          <div class="form-row">
            <label>Username</label>
            <input id="loginUsername" name="username" required>
          </div>
          <div class="form-row">
            <label>Password</label>
            <input id="loginPassword" name="password" type="password" required>
          </div>
          <div class="modal-actions">
            <button id="loginSubmit" class="btn-primary" type="button">Login</button>
            <button id="loginCancel" class="btn-muted" type="button">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <!-- users management modal (admin) -->
    <div id="usersModal" class="modal hidden">
      <div class="modal-content">
        <h3>Manage Users (admin)</h3>
        <div id="usersListArea"></div>
        <hr>
        <h4>Add / Edit User</h4>
        <form id="userForm">
          <input type="hidden" id="userId">
          <div class="form-row"><label>Username</label><input id="userNameField" required></div>
          <div class="form-row"><label>Password</label><input id="userPasswordField" type="password"></div>
          <div class="form-row"><label>Role</label><input id="userRoleField" value="user"></div>
          <div class="modal-actions">
            <button id="userSaveBtn" class="btn-primary" type="button">Save User</button>
            <button id="userCancelBtn" class="btn-muted" type="button">Cancel</button>
          </div>
        </form>
      </div>
    </div>

  </main>

  <footer>
    <p>&copy; 2025 Kabul University Library</p>
  </footer>

  <script src="script.js" defer></script>
</body>
</html>
