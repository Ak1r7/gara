<?php
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Preluare setări gara
$settings = [];
$stmt = $conn->query("SELECT nume_setare, valoare FROM site_settings");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['nume_setare']] = $row['valoare'];
}

// Procesare formular contact
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nume = trim($_POST['nume']);
    $email = trim($_POST['email']);
    $subiect = trim($_POST['subiect']);
    $mesaj = trim($_POST['mesaj']);
    
    // Validare
    if (empty($nume)) {
        $errors['nume'] = 'Numele este obligatoriu';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Emailul este obligatoriu';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Emailul nu este valid';
    }
    
    if (empty($subiect)) {
        $errors['subiect'] = 'Subiectul este obligatoriu';
    }
    
    if (empty($mesaj)) {
        $errors['mesaj'] = 'Mesajul este obligatoriu';
    } elseif (strlen($mesaj) < 10) {
        $errors['mesaj'] = 'Mesajul trebuie să conțină minim 10 caractere';
    }
    
    if (empty($errors)) {
        // Aici se poate adăuga logica de trimitere email sau salvare în baza de date
        $success = true;
    }
}

$page_title = "Contact - Gara Feroviară";
require_once __DIR__ . '/../templates/header.php';
?>

<section class="contact">
    <h1><i class="fas fa-envelope"></i> Contact</h1>
    
    <div class="contact-container">
        <div class="contact-info">
            <h2><i class="fas fa-info-circle"></i> Informații contact</h2>
            
            <div class="info-item">
                <i class="fas fa-building"></i>
                <h3>Adresă</h3>
                <p><?php echo htmlspecialchars($settings['adresa_gara']); ?></p>
            </div>
            
            <div class="info-item">
                <i class="fas fa-phone"></i>
                <h3>Telefon</h3>
                <p><?php echo htmlspecialchars($settings['telefon_gara']); ?></p>
            </div>
            
            <div class="info-item">
                <i class="fas fa-envelope"></i>
                <h3>Email</h3>
                <p><?php echo htmlspecialchars($settings['email_gara']); ?></p>
            </div>
            
            <div class="info-item">
                <i class="fas fa-clock"></i>
                <h3>Program</h3>
                <p>Luni - Duminică: 06:00 - 22:00</p>
                <p>Ghișee bilete: 05:30 - 23:30</p>
            </div>
            
            <div class="social-links">
                <h3>Urmărește-ne pe:</h3>
                <a href="#" class="social-link facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-link twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-link instagram"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        
        <div class="contact-form">
            <h2><i class="fas fa-paper-plane"></i> Trimite-ne un mesaj</h2>
            
            <?php if ($success): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i>
                    <p>Mesajul dumneavoastră a fost trimis cu succes! Vă vom răspunde în cel mai scurt timp posibil.</p>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group <?php echo isset($errors['nume']) ? 'error' : ''; ?>">
                    <label for="nume">Nume complet</label>
                    <input type="text" name="nume" id="nume" value="<?php echo htmlspecialchars($_POST['nume'] ?? ''); ?>" required>
                    <?php if (isset($errors['nume'])): ?>
                        <span class="error-message"><?php echo htmlspecialchars($errors['nume']); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group <?php echo isset($errors['email']) ? 'error' : ''; ?>">
                    <label for="email">Adresă email</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error-message"><?php echo htmlspecialchars($errors['email']); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group <?php echo isset($errors['subiect']) ? 'error' : ''; ?>">
                    <label for="subiect">Subiect</label>
                    <input type="text" name="subiect" id="subiect" value="<?php echo htmlspecialchars($_POST['subiect'] ?? ''); ?>" required>
                    <?php if (isset($errors['subiect'])): ?>
                        <span class="error-message"><?php echo htmlspecialchars($errors['subiect']); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group <?php echo isset($errors['mesaj']) ? 'error' : ''; ?>">
                    <label for="mesaj">Mesaj</label>
                    <textarea name="mesaj" id="mesaj" rows="5" required><?php echo htmlspecialchars($_POST['mesaj'] ?? ''); ?></textarea>
                    <?php if (isset($errors['mesaj'])): ?>
                        <span class="error-message"><?php echo htmlspecialchars($errors['mesaj']); ?></span>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn primary"><i class="fas fa-paper-plane"></i> Trimite mesaj</button>
            </form>
        </div>
    </div>
    
    <div class="map-container">
        <h2><i class="fas fa-map-marker-alt"></i> Locație pe hartă</h2>
        <div class="map-wrapper">
            <!-- Înlocuiește cu codul real de embed al hărții Google Maps -->
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2848.138383180895!2d26.07281231552789!3d44.44692697910199!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40b201f6df6a8b3d%3A0x4e0c6c5d15f5a9a1!2sGara%20de%20Nord%2C%20Bucure%C8%99ti!5e0!3m2!1sen!2sro!4v1620000000000!5m2!1sen!2sro" 
                    width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>