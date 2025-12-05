<?php
include '../includes/auth_admin.php';
?>
<h1>Dashboard Admin</h1>
<p>Halo, <?= htmlspecialchars($_SESSION['username']) ?></p>
<ul>
    <li><a href="quest_list.php">Kelola Quest</a></li>
    <li><a href="phoenix_list.php">Kelola Phoenix</a></li>
    <li><a href="../auth/logout.php">Logout</a></li>
</ul>
