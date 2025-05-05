<?php
require_once __DIR__ . '/../../config/database.php';

// Verificare autentificare și rol admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'administrator') {
    header("Location: ../auth/login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Statistici pentru dashboard
$stats = [];

// Număr utilizatori
$stmt = $conn->query("SELECT COUNT(*) as total FROM users");
$stats['users'] = $stmt->fetch()['total'];

// Număr bilete vândute
$stmt = $conn->query("SELECT COUNT(*) as total FROM tickets WHERE status = 'achiziționat'");
$stats['tickets'] = $stmt->fetch()['total'];

// Număr trenuri active
$stmt = $conn->query("SELECT COUNT(*) as total FROM trains WHERE status = 'în circulație'");
$stats['trains'] = $stmt->fetch()['total'];

// Venituri totale
$stmt = $conn->query("SELECT SUM(pret) as total FROM tickets WHERE status = 'achiziționat'");
$stats['revenue'] = $stmt->fetch()['total'] ?? 0;

// Ultimele bilete vândute
$stmt = $conn->query("SELECT t.*, u.nume as user_name, tr.numar_tren 
                     FROM tickets t 
                     JOIN users u ON t.user_id = u.user_id 
                     JOIN trains tr ON t.train_id = tr.train_id 
                     ORDER BY data_achizitie DESC LIMIT 5");
$recent_tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ultimele notificări
$stmt = $conn->query("SELECT n.*, t.numar_tren 
                     FROM notifications n 
                     JOIN trains t ON n.train_id = t.train_id 
                     ORDER BY data_emitere DESC LIMIT 5");
$recent_notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Panou de administrare - Gara Feroviară";
require_once __DIR__ . '/../../templates/header.php';
?>

<div class="admin-container">
    <aside class="admin-sidebar">
        <nav>
            <ul>
                <li class="active"><a href="../admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="../admin/manage-trains.php"><i class="fas fa-train"></i> Gestionează trenuri</a></li>
                <li><a href="../admin/manage-routes.php"><i class="fas fa-route"></i> Gestionează rute</a></li>
                <li><a href="../admin/manage-tickets.php"><i class="fas fa-ticket-alt"></i> Gestionează bilete</a></li>
                <li><a href="../admin/manage-users.php"><i class="fas fa-users"></i> Gestionează utilizatori</a></li>
                <li><a href="../admin/manage-services.php"><i class="fas fa-concierge-bell"></i> Gestionează servicii</a></li>
                <li><a href="../admin/manage-notifications.php"><i class="fas fa-bell"></i> Gestionează notificări</a></li>
                <li><a href="../admin/settings.php"><i class="fas fa-cog"></i> Setări</a></li>
            </ul>
        </nav>
    </aside>
    
    <main class="admin-content">
        <h1><i class="fas fa-tachometer-alt"></i> Panou de administrare</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Utilizatori</h3>
                    <p><?php echo $stats['users']; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon tickets">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>Bilete vândute</h3>
                    <p><?php echo $stats['tickets']; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon trains">
                    <i class="fas fa-train"></i>
                </div>
                <div class="stat-info">
                    <h3>Trenuri active</h3>
                    <p><?php echo $stats['trains']; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon revenue">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <h3>Venituri totale</h3>
                    <p><?php echo number_format($stats['revenue'], 2); ?> RON</p>
                </div>
            </div>
        </div>
        
        <div class="admin-section">
            <h2><i class="fas fa-ticket-alt"></i> Ultimele bilete vândute</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID bilet</th>
                            <th>Utilizator</th>
                            <th>Nr. tren</th>
                            <th>Clasă</th>
                            <th>Preț</th>
                            <th>Data achiziției</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_tickets as $ticket): ?>
                            <tr>
                                <td><?php echo $ticket['ticket_id']; ?></td>
                                <td><?php echo htmlspecialchars($ticket['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['numar_tren']); ?></td>
                                <td><?php echo $ticket['clasa']; ?></td>
                                <td><?php echo number_format($ticket['pret'], 2); ?> RON</td>
                                <td><?php echo date('d.m.Y H:i', strtotime($ticket['data_achizitie'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="admin-section">
            <h2><i class="fas fa-bell"></i> Ultimele notificări</h2>
            <div class="notifications-list">
                <?php foreach ($recent_notifications as $notification): ?>
                    <div class="notification-item">
                        <div class="notification-header">
                            <h3>Tren <?php echo htmlspecialchars($notification['numar_tren']); ?> - <?php echo htmlspecialchars($notification['tip_notificare']); ?></h3>
                            <span class="notification-date"><?php echo date('d.m.Y H:i', strtotime($notification['data_emitere'])); ?></span>
                        </div>
                        <p><?php echo htmlspecialchars($notification['mesaj']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>