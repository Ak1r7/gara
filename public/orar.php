<?php
$page_title = "Orar trenuri - Gara Feroviară";
require_once __DIR__ . '/../templates/header.php';

// Filtre
$filters = [
    'tip_tren' => $_GET['tip_tren'] ?? '',
    'plecare' => $_GET['plecare'] ?? '',
    'sosire' => $_GET['sosire'] ?? '',
    'ora_plecare' => $_GET['ora_plecare'] ?? ''
];

// Construire query cu filtre
$sql = "SELECT * FROM trains WHERE 1=1";
$params = [];

if (!empty($filters['tip_tren'])) {
    $sql .= " AND tip_tren = ?";
    $params[] = $filters['tip_tren'];
}

if (!empty($filters['plecare'])) {
    $sql .= " AND rute LIKE ?";
    $params[] = '%' . $filters['plecare'] . '%';
}

if (!empty($filters['sosire'])) {
    $sql .= " AND rute LIKE ?";
    $params[] = '%' . $filters['sosire'] . '%';
}

if (!empty($filters['ora_plecare'])) {
    $sql .= " AND plecare >= ?";
    $params[] = $filters['ora_plecare'];
}

$sql .= " ORDER BY plecare ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$trains = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preluare stații unice pentru dropdown
$stmt = $conn->query("SELECT DISTINCT start_station, end_station FROM routes");
$stations = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $stations[] = $row['start_station'];
    $stations[] = $row['end_station'];
}
$stations = array_unique($stations);
sort($stations);
?>

<section class="timetable">
    <h1><i class="fas fa-clock"></i> Orar trenuri</h1>
    
    <div class="search-filters">
        <form method="GET" action="">
            <div class="filter-group">
                <label for="tip_tren">Tip tren:</label>
                <select name="tip_tren" id="tip_tren">
                    <option value="">Toate</option>
                    <option value="rapid" <?php echo $filters['tip_tren'] === 'rapid' ? 'selected' : ''; ?>>Rapid</option>
                    <option value="intercity" <?php echo $filters['tip_tren'] === 'intercity' ? 'selected' : ''; ?>>InterCity</option>
                    <option value="regio" <?php echo $filters['tip_tren'] === 'regio' ? 'selected' : ''; ?>>Regio</option>
                    <option value="express" <?php echo $filters['tip_tren'] === 'express' ? 'selected' : ''; ?>>Express</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="plecare">Plecare din:</label>
                <select name="plecare" id="plecare">
                    <option value="">Toate stațiile</option>
                    <?php foreach ($stations as $station): ?>
                        <option value="<?php echo htmlspecialchars($station); ?>" <?php echo $filters['plecare'] === $station ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($station); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="sosire">Destinație:</label>
                <select name="sosire" id="sosire">
                    <option value="">Toate stațiile</option>
                    <?php foreach ($stations as $station): ?>
                        <option value="<?php echo htmlspecialchars($station); ?>" <?php echo $filters['sosire'] === $station ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($station); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="ora_plecare">După ora:</label>
                <input type="time" name="ora_plecare" id="ora_plecare" value="<?php echo htmlspecialchars($filters['ora_plecare']); ?>">
            </div>
            
            <button type="submit" class="btn"><i class="fas fa-search"></i> Caută</button>
            <a href="../public/orar.php" class="btn secondary"><i class="fas fa-sync-alt"></i> Resetează</a>
        </form>
    </div>
    
    <div class="train-list">
        <?php if (count($trains) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nr. tren</th>
                        <th>Tip tren</th>
                        <th>Rută</th>
                        <th>Plecare</th>
                        <th>Sosire</th>
                        <th>Durată</th>
                        <th>Status</th>
                        <th>Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trains as $train): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($train['numar_tren']); ?></td>
                            <td><span class="train-type <?php echo strtolower($train['tip_tren']); ?>"><?php echo htmlspecialchars($train['tip_tren']); ?></span></td>
                            <td><?php echo htmlspecialchars($train['rute']); ?></td>
                            <td><?php echo htmlspecialchars($train['plecare']); ?></td>
                            <td><?php echo htmlspecialchars($train['sosire']); ?></td>
                            <td><?php echo floor($train['durata_traseu']/60); ?>h <?php echo $train['durata_traseu']%60; ?>m</td>
                            <td><span class="status <?php echo str_replace(' ', '-', strtolower($train['status'])); ?>"><?php echo htmlspecialchars($train['status']); ?></span></td>
                            <td>
                                <a href="../public/rute.php?train_id=<?php echo $train['train_id']; ?>" class="btn small"><i class="fas fa-route"></i> Detalii</a>
                                <a href="../public/bilete.php?train_id=<?php echo $train['train_id']; ?>" class="btn small primary"><i class="fas fa-ticket-alt"></i> Bilet</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-train"></i>
                <p>Nu s-au găsit trenuri conform criteriilor selectate.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>