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

// Acțiuni: editare, ștergere
$action = $_GET['action'] ?? '';
$user_id = $_GET['id'] ?? 0;

// Procesare formular editare utilizator
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nume = trim($_POST['nume']);
    $email = trim($_POST['email']);
    $telefon = trim($_POST['telefon'] ?? '');
    $rol = $_POST['rol'];
    
    // Validare
    $errors = [];
    
    if (empty($nume)) {
        $errors['nume'] = 'Numele este obligatoriu';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Emailul este obligatoriu';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Emailul nu este valid';
    } else {
        // Verificare dacă emailul există deja pentru alt utilizator
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Acest email este deja înregistrat';
        }
    }
    
    if (empty($errors)) {
        // Actualizare utilizator
        $stmt = $conn->prepare("UPDATE users SET nume = ?, email = ?, telefon = ?, rol = ? WHERE user_id = ?");
        $stmt->execute([$nume, $email, $telefon, $rol, $user_id]);
        
        $_SESSION['success_message'] = 'Utilizatorul a fost actualizat cu succes!';
        header("Location: /admin/manage-users.php");
        exit;
    }
}

// Ștergere utilizator
if ($action === 'delete' && $user_id > 0) {
    // Nu putem șterge contul propriu
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error_message'] = 'Nu puteți șterge propriul cont!';
    } else {
        // Verificăm dacă utilizatorul are bilete
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tickets WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $ticket_count = $stmt->fetchColumn();
        
        if ($ticket_count == 0) {
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $_SESSION['success_message'] = 'Utilizatorul a fost șters cu succes!';
        } else {
            $_SESSION['error_message'] = 'Nu puteți șterge acest utilizator deoarece are bilete asociate!';
        }
    }
    
    header("Location: /admin/manage-users.php");
    exit;
}

// Preluare utilizator pentru editare
$user = null;
if ($action === 'edit' && $user_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        header("Location: /admin/manage-users.php");
        exit;
    }
}

// Preluare toți utilizatorii
$stmt = $conn->query("SELECT * FROM users ORDER BY nume");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mesaje de succes/eroare
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

$page_title = "Gestionează utilizatori - Gara Feroviară";
require_once __DIR__ . '/../../templates/header.php';
?>

<div class="admin-container">
    <aside class="admin-sidebar">
        <?php include __DIR__ . '/../../templates/admin-sidebar.php'; ?>
    </aside>
    
    <main class="admin-content">
        <h1><i class="fas fa-users"></i> Gestionează utilizatori</h1>
        
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
        
        <?php if ($action === 'edit'): ?>
            <div class="admin-form">
                <h2>Editare utilizator</h2>
                
                <form method="POST" action="">
                    <div class="form-group <?php echo isset($errors['nume']) ? 'error' : ''; ?>">
                        <label for="nume">Nume complet</label>
                        <input type="text" name="nume" id="nume" 
                               value="<?php echo htmlspecialchars($user['nume'] ?? $_POST['nume'] ?? ''); ?>" required>
                        <?php if (isset($errors['nume'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($errors['nume']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group <?php echo isset($errors['email']) ? 'error' : ''; ?>">
                        <label for="email">Adresă email</label>
                        <input type="email" name="email" id="email" 
                               value="<?php echo htmlspecialchars($user['email'] ?? $_POST['email'] ?? ''); ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($errors['email']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefon">Număr de telefon</label>
                        <input type="tel" name="telefon" id="telefon" 
                               value="<?php echo htmlspecialchars($user['telefon'] ?? $_POST['telefon'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="rol">Rol</label>
                        <select name="rol" id="rol" required>
                            <option value="utilizator" <?php echo ($user['rol'] ?? $_POST['rol'] ?? '') === 'utilizator' ? 'selected' : ''; ?>>Utilizator</option>
                            <option value="administrator" <?php echo ($user['rol'] ?? $_POST['rol'] ?? '') === 'administrator' ? 'selected' : ''; ?>>Administrator</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn primary">
                            <i class="fas fa-save"></i> Salvează modificări
                        </button>
                        <a href="/admin/manage-users.php" class="btn secondary">
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
                            <th>Nume</th>
                            <th>Email</th>
                            <th>Telefon</th>
                            <th>Rol</th>
                            <th>Data înregistrării</th>
                            <th>Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u['nume']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo htmlspecialchars($u['telefon'] ?? '-'); ?></td>
                                <td>
                                    <span class="role <?php echo strtolower($u['rol']); ?>">
                                        <?php echo htmlspecialchars($u['rol']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d.m.Y H:i', strtotime($u['data_inregistrare'])); ?></td>
                                <td class="actions">
                                    <a href="/admin/manage-users.php?action=edit&id=<?php echo $u['user_id']; ?>" class="btn small">
                                        <i class="fas fa-edit"></i> Editează
                                    </a>
                                    <?php if ($u['user_id'] != $_SESSION['user_id']): ?>
                                        <a href="/admin/manage-users.php?action=delete&id=<?php echo $u['user_id']; ?>" class="btn small danger" onclick="return confirm('Sigur doriți să ștergeți acest utilizator?');">
                                            <i class="fas fa-trash"></i> Șterge
                                        </a>
                                    <?php endif; ?>
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