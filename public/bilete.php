<?php
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->getConnection();

$train_id = $_GET['train_id'] ?? null;
$errors = [];
$success = false;

// Preluare tren dacă există ID
if ($train_id) {
    $stmt = $conn->prepare("SELECT * FROM trains WHERE train_id = ?");
    $stmt->execute([$train_id]);
    $train = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$train) {
        header("Location: /public/bilete.php");
        exit;
    }
}

// Preluare toate trenurile pentru dropdown
$stmt = $conn->query("SELECT * FROM trains ORDER BY numar_tren");
$all_trains = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesare formular cumpărare bilet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $selected_train_id = $_POST['train_id'];
    $clasa = $_POST['clasa'];
    $numar_bilete = $_POST['numar_bilete'];
    
    // Validare
    if (empty($selected_train_id)) {
        $errors['train_id'] = 'Selectați un tren';
    }
    
    if (empty($clasa)) {
        $errors['clasa'] = 'Selectați clasa';
    }
    
    if (empty($numar_bilete) || $numar_bilete < 1 || $numar_bilete > 10) {
        $errors['numar_bilete'] = 'Numărul de bilete trebuie să fie între 1 și 10';
    }
    
    if (empty($errors)) {
        // Calcul preț pe baza clasei și tipului de tren
        $pret_baza = 50; // Preț de bază
        $multiplicator_clasa = ($clasa == '1') ? 1.5 : ($clasa == '2' ? 1.2 : 1);
        $multiplicator_tip = ($train['tip_tren'] == 'intercity') ? 1.8 : ($train['tip_tren'] == 'rapid' ? 1.5 : ($train['tip_tren'] == 'express' ? 1.3 : 1));
        
        $pret_total = $pret_baza * $multiplicator_clasa * $multiplicator_tip * $numar_bilete;
        
        // Creare bilete în baza de date
        try {
            $conn->beginTransaction();
            
            for ($i = 0; $i < $numar_bilete; $i++) {
                $stmt = $conn->prepare("INSERT INTO tickets (user_id, train_id, clasa, pret) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $_SESSION['user_id'],
                    $selected_train_id,
                    $clasa,
                    $pret_total / $numar_bilete
                ]);
            }
            
            $conn->commit();
            $success = true;
            
            // Redirecționează către pagina de confirmare după 3 secunde
            header("Refresh:3; url=/public/confirmare-bilet.php");
        } catch (PDOException $e) {
            $conn->rollBack();
            $errors['general'] = 'A apărut o eroare la achiziționarea biletelor. Vă rugăm să încercați din nou.';
        }
    }
}

$page_title = "Bilete - Gara Feroviară";
require_once __DIR__ . '/../templates/header.php';
?>

<section class="tickets">
    <h1><i class="fas fa-ticket-alt"></i> Achiziționare bilete</h1>
    
    <?php if ($success): ?>
        <div class="alert success">
            <i class="fas fa-check-circle"></i>
            <p>Biletele au fost achiziționate cu succes! Veți fi redirecționat către pagina de confirmare.</p>
        </div>
    <?php elseif (isset($errors['general'])): ?>
        <div class="alert error">
            <i class="fas fa-exclamation-circle"></i>
            <p><?php echo htmlspecialchars($errors['general']); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="alert warning">
            <i class="fas fa-exclamation-triangle"></i>
            <p>Pentru a achiziționa bilete, vă rugăm să vă <a href="../auth/login.php">autentificați</a> sau să vă <a href="../auth/register.php">înregistrați</a>.</p>
        </div>
    <?php endif; ?>
    
    <div class="ticket-form-container">
        <form method="POST" action="">
            <div class="form-group <?php echo isset($errors['train_id']) ? 'error' : ''; ?>">
                <label for="train_id">Selectați trenul:</label>
                <select name="train_id" id="train_id" required>
                    <option value="">-- Alegeți un tren --</option>
                    <?php foreach ($all_trains as $t): ?>
                        <option value="<?php echo $t['train_id']; ?>" 
                            <?php echo (isset($train) && $train['train_id'] == $t['train_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($t['numar_tren']); ?> - 
                            <?php echo htmlspecialchars($t['rute']); ?> - 
                            <?php echo htmlspecialchars($t['plecare']); ?>-<?php echo htmlspecialchars($t['sosire']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['train_id'])): ?>
                    <span class="error-message"><?php echo htmlspecialchars($errors['train_id']); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group <?php echo isset($errors['clasa']) ? 'error' : ''; ?>">
                <label for="clasa">Clasă:</label>
                <select name="clasa" id="clasa" required>
                    <option value="">-- Alegeți clasa --</option>
                    <option value="1" <?php echo ($_POST['clasa'] ?? '') == '1' ? 'selected' : ''; ?>>Clasa I</option>
                    <option value="2" <?php echo ($_POST['clasa'] ?? '') == '2' ? 'selected' : ''; ?>>Clasa a II-a</option>
                    <option value="3" <?php echo ($_POST['clasa'] ?? '') == '3' ? 'selected' : ''; ?>>Clasa a III-a</option>
                </select>
                <?php if (isset($errors['clasa'])): ?>
                    <span class="error-message"><?php echo htmlspecialchars($errors['clasa']); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group <?php echo isset($errors['numar_bilete']) ? 'error' : ''; ?>">
                <label for="numar_bilete">Număr de bilete:</label>
                <input type="number" name="numar_bilete" id="numar_bilete" min="1" max="10" 
                       value="<?php echo htmlspecialchars($_POST['numar_bilete'] ?? '1'); ?>" required>
                <?php if (isset($errors['numar_bilete'])): ?>
                    <span class="error-message"><?php echo htmlspecialchars($errors['numar_bilete']); ?></span>
                <?php endif; ?>
            </div>
            
            <?php if (isset($train) && isset($_SESSION['user_id'])): ?>
                <div class="price-preview">
                    <h3>Estimare preț:</h3>
                    <p id="price-estimation">-</p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <button type="submit" class="btn primary"><i class="fas fa-shopping-cart"></i> Cumpără bilete</button>
            <?php else: ?>
                <a href="../auth/login.php" class="btn primary"><i class="fas fa-sign-in-alt"></i> Autentifică-te pentru a cumpăra</a>
            <?php endif; ?>
        </form>
        
        <?php if (isset($train)): ?>
            <div class="train-info">
                <h3>Informații tren</h3>
                <div class="info-card">
                    <h4>Tren <?php echo htmlspecialchars($train['numar_tren']); ?></h4>
                    <p><strong>Tip:</strong> <?php echo htmlspecialchars($train['tip_tren']); ?></p>
                    <p><strong>Rută:</strong> <?php echo htmlspecialchars($train['rute']); ?></p>
                    <p><strong>Plecare:</strong> <?php echo htmlspecialchars($train['plecare']); ?></p>
                    <p><strong>Sosire:</strong> <?php echo htmlspecialchars($train['sosire']); ?></p>
                    <p><strong>Durată:</strong> <?php echo floor($train['durata_traseu']/60); ?>h <?php echo $train['durata_traseu']%60; ?>m</p>
                    <p><strong>Status:</strong> <span class="status <?php echo str_replace(' ', '-', strtolower($train['status'])); ?>">
                        <?php echo htmlspecialchars($train['status']); ?>
                    </span></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (!isset($train)): ?>
        <div class="popular-trains">
            <h2><i class="fas fa-star"></i> Trenuri populare</h2>
            <div class="train-grid">
                <?php 
                $stmt = $conn->query("SELECT t.*, COUNT(tk.ticket_id) as bilete_vandute 
                                    FROM trains t 
                                    LEFT JOIN tickets tk ON t.train_id = tk.train_id 
                                    GROUP BY t.train_id 
                                    ORDER BY bilete_vandute DESC 
                                    LIMIT 3");
                $popular_trains = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                
                <?php foreach ($popular_trains as $train): ?>
                    <div class="train-card">
                        <div class="train-header">
                            <h3>Tren <?php echo htmlspecialchars($train['numar_tren']); ?></h3>
                            <span class="train-type <?php echo strtolower($train['tip_tren']); ?>">
                                <?php echo htmlspecialchars($train['tip_tren']); ?>
                            </span>
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
                            <a href="../public/bilete.php?train_id=<?php echo $train['train_id']; ?>" class="btn primary">
                                <i class="fas fa-ticket-alt"></i> Cumpără bilet
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcul preț estimativ
    const trainSelect = document.getElementById('train_id');
    const classSelect = document.getElementById('clasa');
    const ticketCount = document.getElementById('numar_bilete');
    const priceDisplay = document.getElementById('price-estimation');
    
    if (trainSelect && classSelect && ticketCount && priceDisplay) {
        function calculatePrice() {
            const trainId = trainSelect.value;
            const classValue = classSelect.value;
            const count = ticketCount.value;
            
            if (!trainId || !classValue || !count) {
                priceDisplay.textContent = '-';
                return;
            }
            
            // Aici ar trebui să faceți un request AJAX pentru a obține prețul real din baza de date
            // Pentru exemplu, folosim un calcul simplu
            const basePrice = 50;
            let classMultiplier = 1;
            let typeMultiplier = 1;
            
            if (classValue === '1') classMultiplier = 1.5;
            else if (classValue === '2') classMultiplier = 1.2;
            
            // Presupunem că tipul trenului este în valoarea selectată
            const trainText = trainSelect.options[trainSelect.selectedIndex].text;
            if (trainText.includes('InterCity')) typeMultiplier = 1.8;
            else if (trainText.includes('Rapid')) typeMultiplier = 1.5;
            else if (trainText.includes('Express')) typeMultiplier = 1.3;
            
            const totalPrice = basePrice * classMultiplier * typeMultiplier * count;
            priceDisplay.textContent = totalPrice.toFixed(2) + ' RON';
        }
        
        trainSelect.addEventListener('change', calculatePrice);
        classSelect.addEventListener('change', calculatePrice);
        ticketCount.addEventListener('input', calculatePrice);
        
        // Calcul inițial
        calculatePrice();
    }
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>