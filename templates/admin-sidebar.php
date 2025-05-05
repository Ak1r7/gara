<nav>
    <ul>
        <li <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'class="active"' : ''; ?>>
            <a href="../admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        </li>
        <li <?php echo basename($_SERVER['PHP_SELF']) === 'manage-trains.php' ? 'class="active"' : ''; ?>>
            <a href="../admin/manage-trains.php"><i class="fas fa-train"></i> Gestionează trenuri</a>
        </li>
        <li <?php echo basename($_SERVER['PHP_SELF']) === 'manage-routes.php' ? 'class="active"' : ''; ?>>
            <a href="../admin/manage-routes.php"><i class="fas fa-route"></i> Gestionează rute</a>
        </li>
        <li <?php echo basename($_SERVER['PHP_SELF']) === 'manage-tickets.php' ? 'class="active"' : ''; ?>>
            <a href="../admin/manage-tickets.php"><i class="fas fa-ticket-alt"></i> Gestionează bilete</a>
        </li>
        <li <?php echo basename($_SERVER['PHP_SELF']) === 'manage-users.php' ? 'class="active"' : ''; ?>>
            <a href="../admin/manage-users.php"><i class="fas fa-users"></i> Gestionează utilizatori</a>
        </li>
        <li <?php echo basename($_SERVER['PHP_SELF']) === 'manage-services.php' ? 'class="active"' : ''; ?>>
            <a href="../admin/manage-services.php"><i class="fas fa-concierge-bell"></i> Gestionează servicii</a>
        </li>
        <li <?php echo basename($_SERVER['PHP_SELF']) === 'manage-notifications.php' ? 'class="active"' : ''; ?>>
            <a href="../admin/manage-notifications.php"><i class="fas fa-bell"></i> Gestionează notificări</a>
        </li>
        <li <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'class="active"' : ''; ?>>
            <a href="../admin/settings.php"><i class="fas fa-cog"></i> Setări</a>
        </li>
    </ul>
</nav>