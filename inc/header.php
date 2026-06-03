<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Sistem Informasi Manajemen Warga';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <button class="mobile-toggle-btn" id="mobileToggleBtn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>

        <?php include __DIR__ . '/sidebar.php'; ?>

        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
        <main class="main-content">
