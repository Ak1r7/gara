</main>
    <footer>
        <div class="container">
            <div class="footer-section">
                <h3>Despre noi</h3>
                <p><?php echo htmlspecialchars($settings['nume_gara']); ?> - Servicii feroviare de calitate din 1870.</p>
            </div>
            <div class="footer-section">
                <h3>Link-uri rapide</h3>
                <ul>
                    <li><a href="../public/orar.php">Orar trenuri</a></li>
                    <li><a href="../public/rute.php">Rute și destinații</a></li>
                    <li><a href="../public/bilete.php">Cumpără bilete</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($settings['adresa_gara']); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($settings['telefon_gara']); ?></p>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($settings['email_gara']); ?></p>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($settings['nume_gara']); ?>. Toate drepturile rezervate.</p>
                <div class="legal-links">
                    <a href="../termeni.php">Termeni și condiții</a> | 
                    <a href="../politica.php">Politica de confidențialitate</a> | 
                    <a href="../cookie.php">Politica de cookie-uri</a>
                </div>
            </div>
        </div>
    </footer>
    <script src="/assets/js/script.js"></script>
</body>
</html>