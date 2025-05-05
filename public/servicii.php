<?php
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Preluare toate serviciile
$stmt = $conn->query("SELECT * FROM services ORDER BY nume_serviciu");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preluare harta gării
$stmt = $conn->query("SELECT * FROM station_map ORDER BY tip_locatie, numar_peron");
$station_map = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Servicii - Gara Feroviară";
require_once __DIR__ . '/../templates/header.php';
?>

<section class="services">
    <h1><i class="fas fa-concierge-bell"></i> Servicii în gară</h1>
    
    <div class="services-container">
        <?php foreach ($services as $service): ?>
            <div class="service-card">
                <div class="service-icon">
                    <?php 
                    $icon = 'fa-cog';
                    if (strpos(strtolower($service['nume_serviciu']), 'bagaj') !== false) $icon = 'fa-suitcase';
                    elseif (strpos(strtolower($service['nume_serviciu']), 'wifi') !== false) $icon = 'fa-wifi';
                    elseif (strpos(strtolower($service['nume_serviciu']), 'lounge') !== false) $icon = 'fa-couch';
                    elseif (strpos(strtolower($service['nume_serviciu']), 'auto') !== false) $icon = 'fa-car';
                    elseif (strpos(strtolower($service['nume_serviciu']), 'turist') !== false) $icon = 'fa-map-marked-alt';
                    ?>
                    <i class="fas <?php echo $icon; ?>"></i>
                </div>
                <div class="service-info">
                    <h3><?php echo htmlspecialchars($service['nume_serviciu']); ?></h3>
                    <p><?php echo htmlspecialchars($service['descriere']); ?></p>
                    
                    <?php if ($service['cost'] !== null): ?>
                        <div class="service-price">
                            <strong>Preț:</strong> <?php echo number_format($service['cost'], 2); ?> RON
                        </div>
                    <?php else: ?>
                        <div class="service-price free">
                            <strong>Gratuit</strong>
                        </div>
                    <?php endif; ?>
                    
                    <div class="service-availability">
                        <strong>Disponibilitate:</strong> 
                        <?php if ($service['disponibilitate']): ?>
                            <span class="available"><i class="fas fa-check-circle"></i> Disponibil</span>
                        <?php else: ?>
                            <span class="unavailable"><i class="fas fa-times-circle"></i> Indisponibil</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="station-map">
        <h2><i class="fas fa-map-marked-alt"></i> Hartă gară</h2>
        
        <div class="map-legend">
            <div class="legend-item"><span class="color peron"></span> Peron</div>
            <div class="legend-item"><span class="color ghiseu"></span> Ghișeu bilete</div>
            <div class="legend-item"><span class="color restaurant"></span> Restaurant</div>
            <div class="legend-item"><span class="color toaleta"></span> Toalete</div>
        </div>
        
        <div class="map-locations">
            <?php foreach ($station_map as $location): ?>
                <div class="location-card <?php echo $location['tip_locatie']; ?>">
                    <div class="location-header">
                        <h4><?php echo htmlspecialchars($location['numar_peron']); ?></h4>
                        <span class="location-type"><?php echo htmlspecialchars($location['tip_locatie']); ?></span>
                    </div>
                    <?php if (!empty($location['detalii'])): ?>
                        <p><?php echo htmlspecialchars($location['detalii']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>