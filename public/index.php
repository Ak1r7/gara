<?php
$page_title = "Acasă - Gara Feroviară";
require_once __DIR__ . '/../templates/header.php';

// Preluare trenuri recomandate
$stmt = $conn->query("SELECT * FROM trains ORDER BY RAND() LIMIT 3");
$featured_trains = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preluare notificări recente
$stmt = $conn->query("SELECT n.*, t.numar_tren FROM notifications n JOIN trains t ON n.train_id = t.train_id ORDER BY data_emitere DESC LIMIT 3");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="hero">
    <div class="hero-slider">
        <div class="slide" style="background-image: url('/assets/images/hero1.jpg');">
            <div class="slide-content">
                <h2>Călătorește în siguranță și confort</h2>
                <p>Descoperă cele mai bune rute feroviare din țară</p>
                <a href="../public/bilete.php" class="btn">Cumpără bilete</a>
            </div>
        </div>
        <div class="slide" style="background-image: url('/assets/images/hero2.jpg');">
            <div class="slide-content">
                <h2>Oferte speciale pentru vacanțe</h2>
                <p>Reduceri de până la 30% pentru călătorii în grup</p>
                <a href="../public/bilete.php" class="btn">Vezi oferte</a>
            </div>
        </div>
    </div>
</section>

<section class="notifications">
    <h2><i class="fas fa-bell"></i> Anunțuri importante</h2>
    <div class="notification-list">
        <?php foreach ($notifications as $notification): ?>
            <div class="notification-item">
                <h3>Tren <?php echo htmlspecialchars($notification['numar_tren']); ?> - <?php echo htmlspecialchars($notification['tip_notificare']); ?></h3>
                <p><?php echo htmlspecialchars($notification['mesaj']); ?></p>
                <small><?php echo date('d.m.Y H:i', strtotime($notification['data_emitere'])); ?></small>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="featured-trains">
    <h2><i class="fas fa-train"></i> Trenuri recomandate</h2>
    <div class="train-grid">
        <?php foreach ($featured_trains as $train): ?>
            <div class="train-card">
                <div class="train-header">
                    <h3>Tren <?php echo htmlspecialchars($train['numar_tren']); ?></h3>
                    <span class="train-type <?php echo strtolower($train['tip_tren']); ?>"><?php echo htmlspecialchars($train['tip_tren']); ?></span>
                </div>
                <div class="train-route">
                    <div class="route-point">
                        <span class="time"><?php echo htmlspecialchars($train['plecare']); ?></span>
                        <span class="station"><?php echo explode(' - ', $train['rute'])[0]; ?></span>
                    </div>
                    <div class="route-line">
                        <div class="duration"><?php echo floor($train['durata_traseu']/60); ?>h <?php echo $train['durata_traseu']%60; ?>m</div>
                    </div>
                    <div class="route-point">
                        <span class="time"><?php echo htmlspecialchars($train['sosire']); ?></span>
                        <span class="station"><?php echo explode(' - ', $train['rute'])[count(explode(' - ', $train['rute']))-1]; ?></span>
                    </div>
                </div>
                <div class="train-footer">
                    <a href="../public/rute.php?train_id=<?php echo $train['train_id']; ?>" class="btn">Detalii traseu</a>
                    <a href="../public/bilete.php?train_id=<?php echo $train['train_id']; ?>" class="btn primary">Cumpără bilet</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="services-overview">
    <h2><i class="fas fa-concierge-bell"></i> Servicii în gară</h2>
    <div class="services-grid">
        <div class="service-card">
            <i class="fas fa-suitcase"></i>
            <h3>Bagaje</h3>
            <p>Depozitare și transport bagaje</p>
        </div>
        <div class="service-card">
            <i class="fas fa-wifi"></i>
            <h3>WiFi gratuit</h3>
            <p>Acces internet în toată gara</p>
        </div>
        <div class="service-card">
            <i class="fas fa-utensils"></i>
            <h3>Restaurant</h3>
            <p>Mâncare și băuturi calde</p>
        </div>
        <div class="service-card">
            <i class="fas fa-car"></i>
            <h3>Închirieri auto</h3>
            <p>Mașini disponibile la cerere</p>
        </div>
    </div>
    <div class="text-center">
        <a href="../public/servicii.php" class="btn">Vezi toate serviciile</a>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>