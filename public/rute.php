<?php
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->getConnection();

$train_id = $_GET['train_id'] ?? null;

if ($train_id) {
    // Detalii tren specific
    $stmt = $conn->prepare("SELECT * FROM trains WHERE train_id = ?");
    $stmt->execute([$train_id]);
    $train = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$train) {
        header("Location: /public/rute.php");
        exit;
    }
    
    // Preluare notificări pentru acest tren
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE train_id = ? ORDER BY data_emitere DESC");
    $stmt->execute([$train_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $page_title = "Traseu tren " . htmlspecialchars($train['numar_tren']) . " - Gara Feroviară";
} else {
    // Listă toate rutele
    $stmt = $conn->query("SELECT * FROM routes ORDER BY start_station, end_station");
    $routes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $page_title = "Rute trenuri - Gara Feroviară";
}

require_once __DIR__ . '/../templates/header.php';
?>

<?php if (isset($train)): ?>
    <section class="train-details">
        <h1><i class="fas fa-route"></i> Detalii traseu - Tren <?php echo htmlspecialchars($train['numar_tren']); ?></h1>
        
        <div class="train-info">
            <div class="info-row">
                <span class="label">Tip tren:</span>
                <span class="value train-type <?php echo strtolower($train['tip_tren']); ?>">
                    <?php echo htmlspecialchars($train['tip_tren']); ?>
                </span>
            </div>
            <div class="info-row">
                <span class="label">Rută:</span>
                <span class="value"><?php echo htmlspecialchars($train['rute']); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Plecare:</span>
                <span class="value"><?php echo htmlspecialchars($train['plecare']); ?> - <?php echo explode(' - ', $train['rute'])[0]; ?></span>
            </div>
            <div class="info-row">
                <span class="label">Sosire:</span>
                <span class="value"><?php echo htmlspecialchars($train['sosire']); ?> - <?php echo explode(' - ', $train['rute'])[count(explode(' - ', $train['rute']))-1]; ?></span>
            </div>
            <div class="info-row">
                <span class="label">Durată traseu:</span>
                <span class="value"><?php echo floor($train['durata_traseu']/60); ?>h <?php echo $train['durata_traseu']%60; ?>m</span>
            </div>
            <div class="info-row">
                <span class="label">Status:</span>
                <span class="value status <?php echo str_replace(' ', '-', strtolower($train['status'])); ?>">
                    <?php echo htmlspecialchars($train['status']); ?>
                </span>
            </div>
        </div>
        
        <?php if (!empty($notifications)): ?>
            <div class="train-notifications">
                <h2><i class="fas fa-bell"></i> Anunțuri recente</h2>
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification">
                        <div class="notification-header">
                            <span class="type"><?php echo htmlspecialchars($notification['tip_notificare']); ?></span>
                            <span class="date"><?php echo date('d.m.Y H:i', strtotime($notification['data_emitere'])); ?></span>
                        </div>
                        <p><?php echo htmlspecialchars($notification['mesaj']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="actions">
            <a href="../public/bilete.php?train_id=<?php echo $train['train_id']; ?>" class="btn primary">
                <i class="fas fa-ticket-alt"></i> Cumpără bilet
            </a>
            <a href="../public/orar.php" class="btn">
                <i class="fas fa-arrow-left"></i> Înapoi la orar
            </a>
        </div>
    </section>
<?php else: ?>
    <section class="routes-list">
        <h1><i class="fas fa-route"></i> Rute disponibile</h1>
        
        <div class="routes-grid">
            <?php foreach ($routes as $route): ?>
                <div class="route-card">
                    <div class="route-header">
                        <h3><?php echo htmlspecialchars($route['start_station']); ?> - <?php echo htmlspecialchars($route['end_station']); ?></h3>
                        <span class="route-type"><?php echo htmlspecialchars($route['tip_ruta']); ?></span>
                    </div>
                    <div class="route-body">
                        <p><i class="fas fa-route"></i> Distanță: <?php echo $route['distanta']; ?> km</p>
                        
                        <?php
                        // Preluare trenuri pentru această rută
                        $stmt = $conn->prepare("SELECT * FROM trains WHERE rute LIKE ? ORDER BY plecare LIMIT 3");
                        $search_term = '%' . $route['start_station'] . '%' . $route['end_station'] . '%';
                        $stmt->execute([$search_term]);
                        $trains = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        
                        <?php if (!empty($trains)): ?>
                            <div class="route-trains">
                                <h4>Trenuri disponibile:</h4>
                                <ul>
                                    <?php foreach ($trains as $train): ?>
                                        <li>
                                            <a href="../public/rute.php?train_id=<?php echo $train['train_id']; ?>">
                                                <?php echo htmlspecialchars($train['numar_tren']); ?> - 
                                                <?php echo htmlspecialchars($train['plecare']); ?> - 
                                                <?php echo htmlspecialchars($train['tip_tren']); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="route-footer">
                        <a href="../public/orar.php?plecare=<?php echo urlencode($route['start_station']); ?>&sosire=<?php echo urlencode($route['end_station']); ?>" class="btn small">
                            <i class="fas fa-clock"></i> Vezi orar
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>