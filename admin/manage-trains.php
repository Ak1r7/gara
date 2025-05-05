<?php
require_once __DIR__ . '/../../config/database.php';

// Verificare autentificare și rol admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'administrator') {
    header("Location: /auth/login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Acțiuni: adăugare, editare, ștergere
$action = $_GET['action'] ?? '';
$train_id = $_GET['id'] ?? 0;

// Procesare formular adăugare/editare tren
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numar_tren = trim($_POST['numar_tren']);
    $tip_tren = $_POST['tip_tren'];
    $plecare = $_POST['plecare'];
    $sosire = $_POST['sosire'];
    $rute = trim($_POST['rute']);
    $durata_traseu = (int)$_POST['durata_traseu'];
    $status = $_POST['status'];
    
    // Validare
    $errors = [];
    
    if (empty($numar_tren)) {
        $errors['numar_tren'] = 'Numărul trenului este obligatoriu';
    }
    
    if (empty($plecare)) {
        $errors['plecare'] = 'Ora de plecare este obligatorie';
    }
    
    if (empty($sosire)) {
        $errors['sosire'] = 'Ora de sosire este obligatorie';
    }
    
    if (empty($rute)) {
        $errors['rute'] = 'Ruta este obligatorie';
    }
    
    if ($durata_traseu <= 0) {
        $errors['durata_traseu'] = 'Durata traseului trebuie să fie pozitivă';
    }
    
    if (empty($errors)) {
        if ($action === 'edit' && $train_id > 0) {
            // Editare tren existent
            $stmt = $conn->prepare("UPDATE trains SET numar_tren = ?, tip_tren = ?, plecare = ?, sosire = ?, rute = ?, durata_traseu = ?, status = ? WHERE train_id = ?");
            $stmt->execute([$numar_tren, $tip_tren, $plecare, $sosire, $rute, $durata_traseu, $status, $train_id]);
            
            $_SESSION['success_message'] = 'Trenul a fost actualizat cu succes!';
        } else {
            // Adăugare tren nou
            $stmt = $conn->prepare("INSERT INTO trains (numar_tren, tip_tren, plecare, sosire, rute, durata_traseu, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$numar_tren, $tip_tren, $plecare, $sosire, $rute, $durata_traseu, $status]);
            
            $_SESSION['success_message'] = 'Trenul a fost adăugat cu succes!';
        }
        
        header("Location: /admin/manage-trains.php");
        exit;
    }
}

// Preluare tren pentru editare
$train = null;
if ($action === 'edit' && $train_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM trains WHERE train_id = ?");
    $stmt->execute([$train_id]);
    $train = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$train) {
        header("Location: /admin/manage-trains.php");
        exit;
    }
}

// Ștergere tren
if ($action === 'delete' && $train_id > 0) {
    // Verificăm dacă există bilete pentru acest tren
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tickets WHERE train_id = ?");
    $stmt->execute([$train_id]);
    $ticket_count = $stmt->fetchColumn();
    
    if ($ticket_count == 0) {
        $stmt = $conn->prepare("DELETE FROM trains WHERE train_id = ?");
        $stmt->execute([$train_id]);
        $_SESSION['success_message'] = 'Trenul a fost șters cu succes!';
    } else {
        $_SESSION['error_message'] = 'Nu puteți șterge acest tren deoarece există bilete emise pentru el!';
    }
    
    header("Location: /admin/manage-trains.php");
    exit;
}

// Preluare toate trenurile
$stmt = $conn->query("SELECT * FROM trains ORDER BY numar_tren");
$trains = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mesaje de succes/eroare
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

$page_title = "Gestionează trenuri - Gara Feroviară";
require_once __DIR__ . '/../../templates/header.php';
?>

<div class="admin-container">
    <aside class="admin-sidebar">
        <?php include __DIR__ . '/../../templates/admin-sidebar.php'; ?>
    </aside>
    
    <main class="admin-content">
        <h1><i class="fas fa-train"></i> Gestionează trenuri</h1>
        
        <?php if ($success_message): ?>
            <div class="alert success">
                <i class="fas fa-check-circle"></i>
                <p><?php echo htmlspecialchars($success_message); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i>
                <p><?php echo htmlspecialchars($error_message); ?></p>
            </div>
        <?php endif; ?>
        
        <div class="admin-actions">
            <a href="../admin/manage-trains.php?action=add" class="btn primary">
                <i class="fas fa-plus"></i> Adaugă tren
            </a>
        </div>
        
        <?php if ($action === 'add' || $action === 'edit'): ?>
            <div class="admin-form">
                <h2><?php echo $action === 'add' ? 'Adăugare tren nou' : 'Editare tren'; ?></h2>
                
                <form method="POST" action="">
                    <div class="form-group <?php echo isset($errors['numar_tren']) ? 'error' : ''; ?>">
                        <label for="numar_tren">Număr tren</label>
                        <input type="text" name="numar_tren" id="numar_tren" 
                               value="<?php echo htmlspecialchars($train['numar_tren'] ?? $_POST['numar_tren'] ?? ''); ?>" required>
                        <?php if (isset($errors['numar_tren'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($errors['numar_tren']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="tip_tren">Tip tren</label>
                        <select name="tip_tren" id="tip_tren" required>
                            <option value="rapid" <?php echo ($train['tip_tren'] ?? $_POST['tip_tren'] ?? '') === 'rapid' ? 'selected' : ''; ?>>Rapid</option>
                            <option value="intercity" <?php echo ($train['tip_tren'] ?? $_POST['tip_tren'] ?? '') === 'intercity' ? 'selected' : ''; ?>>InterCity</option>
                            <option value="regio" <?php echo ($train['tip_tren'] ?? $_POST['tip_tren'] ?? '') === 'regio' ? 'selected' : ''; ?>>Regio</option>
                            <option value="express" <?php echo ($train['tip_tren'] ?? $_POST['tip_tren'] ?? '') === 'express' ? 'selected' : ''; ?>>Express</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group <?php echo isset($errors['plecare']) ? 'error' : ''; ?>">
                            <label for="plecare">Ora plecare</label>
                            <input type="time" name="plecare" id="plecare" 
                                   value="<?php echo htmlspecialchars($train['plecare'] ?? $_POST['plecare'] ?? ''); ?>" required>
                            <?php if (isset($errors['plecare'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($errors['plecare']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group <?php echo isset($errors['sosire']) ? 'error' : ''; ?>">
                            <label for="sosire">Ora sosire</label>
                            <input type="time" name="sosire" id="sosire" 
                                   value="<?php echo htmlspecialchars($train['sosire'] ?? $_POST['sosire'] ?? ''); ?>" required>
                            <?php if (isset($errors['sosire'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($errors['sosire']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group <?php echo isset($errors['rute']) ? 'error' : ''; ?>">
                        <label for="rute">Rută</label>
                        <textarea name="rute" id="rute" rows="3" required><?php echo htmlspecialchars($train['rute'] ?? $_POST['rute'] ?? ''); ?></textarea>
                        <small>Introduceți stațiile separate prin " - " (ex: București - Ploiești - Brașov)</small>
                        <?php if (isset($errors['rute'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($errors['rute']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group <?php echo isset($errors['durata_traseu']) ? 'error' : ''; ?>">
                            <label for="durata_traseu">Durată traseu (minute)</label>
                            <input type="number" name="durata_traseu" id="durata_traseu" min="1" 
                                   value="<?php echo htmlspecialchars($train['durata_traseu'] ?? $_POST['durata_traseu'] ?? ''); ?>" required>
                            <?php if (isset($errors['durata_traseu'])): ?>
                                <span class="error-message"><?php echo htmlspecialchars($errors['durata_traseu']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" required>
                                <option value="în circulație" <?php echo ($train['status'] ?? $_POST['status'] ?? '') === 'în circulație' ? 'selected' : ''; ?>>În circulație</option>
                                <option value="întârziat" <?php echo ($train['status'] ?? $_POST['status'] ?? '') === 'întârziat' ? 'selected' : ''; ?>>Întârziat</option>
                                <option value="anulat" <?php echo ($train['status'] ?? $_POST['status'] ?? '') === 'anulat' ? 'selected' : ''; ?>>Anulat</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn primary">
                            <i class="fas fa-save"></i> Salvează
                        </button>
                        <a href="../admin/manage-trains.php" class="btn secondary">
                            <i class="fas fa-times"></i> Anulează
                        </a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>Nr. tren</th>
                            <th>Tip</th>
                            <th>Rută</th>
                            <th>Plecare</th>
                            <th>Sosire</th>
                            <th>Durată</th>
                            <th>Status</th>
                            <th>Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trains as $t): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($t['numar_tren']); ?></td>
                                <td><?php echo htmlspecialchars($t['tip_tren']); ?></td>
                                <td><?php echo htmlspecialchars($t['rute']); ?></td>
                                <td><?php echo htmlspecialchars($t['plecare']); ?></td>
                                <td><?php echo htmlspecialchars($t['sosire']); ?></td>
                                <td><?php echo floor($t['durata_traseu']/60); ?>h <?php echo $t['durata_traseu']%60; ?>m</td>
                                <td>
                                    <span class="status <?php echo str_replace(' ', '-', strtolower($t['status'])); ?>">
                                        <?php echo htmlspecialchars($t['status']); ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="../admin/manage-trains.php?action=edit&id=<?php echo $t['train_id']; ?>" class="btn small">
                                        <i class="fas fa-edit"></i> Editează
                                    </a>
                                    <a href="../admin/manage-trains.php?action=delete&id=<?php echo $t['train_id']; ?>" class="btn small danger" onclick="return confirm('Sigur doriți să ștergeți acest tren?');">
                                        <i class="fas fa-trash"></i> Șterge
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>