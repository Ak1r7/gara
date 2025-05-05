<?php
class Database {
    private $host = "localhost";
    private $db_name = "gara_feroviara";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
    
    public function createTables() {
        try {
            $sql = "
            -- Tabelul utilizatorilor
            CREATE TABLE IF NOT EXISTS users (
                user_id INT AUTO_INCREMENT PRIMARY KEY,
                nume VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                parola VARCHAR(255) NOT NULL,
                telefon VARCHAR(15),
                data_inregistrare DATETIME DEFAULT CURRENT_TIMESTAMP,
                rol ENUM('utilizator', 'administrator') DEFAULT 'utilizator'
            );
            
            -- Tabelul trenurilor
            CREATE TABLE IF NOT EXISTS trains (
                train_id INT AUTO_INCREMENT PRIMARY KEY,
                numar_tren VARCHAR(10) NOT NULL,
                tip_tren ENUM('rapid', 'intercity', 'regio', 'express') NOT NULL,
                plecare VARCHAR(50) NOT NULL,
                sosire VARCHAR(50) NOT NULL,
                rute TEXT NOT NULL,
                durata_traseu INT NOT NULL,
                status ENUM('în circulație', 'întârziat', 'anulat') DEFAULT 'în circulație'
            );
            
            -- Tabelul rutelor
            CREATE TABLE IF NOT EXISTS routes (
                route_id INT AUTO_INCREMENT PRIMARY KEY,
                start_station VARCHAR(50) NOT NULL,
                end_station VARCHAR(50) NOT NULL,
                distanta INT NOT NULL,
                tip_ruta ENUM('directă', 'cu opriri') NOT NULL
            );
            
            -- Tabelul biletelor
            CREATE TABLE IF NOT EXISTS tickets (
                ticket_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                train_id INT NOT NULL,
                clasa ENUM('1', '2', '3') NOT NULL,
                pret DECIMAL(10,2) NOT NULL,
                data_achizitie DATETIME DEFAULT CURRENT_TIMESTAMP,
                status ENUM('achiziționat', 'anulat') DEFAULT 'achiziționat',
                FOREIGN KEY (user_id) REFERENCES users(user_id),
                FOREIGN KEY (train_id) REFERENCES trains(train_id)
            );
            
            -- Tabelul notificărilor
            CREATE TABLE IF NOT EXISTS notifications (
                notification_id INT AUTO_INCREMENT PRIMARY KEY,
                train_id INT NOT NULL,
                mesaj TEXT NOT NULL,
                data_emitere DATETIME DEFAULT CURRENT_TIMESTAMP,
                tip_notificare ENUM('întârziere', 'modificare traseu', 'anulare') NOT NULL,
                FOREIGN KEY (train_id) REFERENCES trains(train_id)
            );
            
            -- Tabelul serviciilor
            CREATE TABLE IF NOT EXISTS services (
                service_id INT AUTO_INCREMENT PRIMARY KEY,
                nume_serviciu VARCHAR(100) NOT NULL,
                descriere TEXT NOT NULL,
                cost DECIMAL(10,2),
                disponibilitate BOOLEAN DEFAULT TRUE
            );
            
            -- Tabelul hărților gării
            CREATE TABLE IF NOT EXISTS station_map (
                map_id INT AUTO_INCREMENT PRIMARY KEY,
                numar_peron VARCHAR(10) NOT NULL,
                detalii TEXT,
                latitudine DECIMAL(10,8),
                longitudine DECIMAL(11,8),
                tip_locatie ENUM('peron', 'ghiseu', 'toaleta', 'restaurant') NOT NULL
            );
            
            -- Tabelul logurilor de autentificare
            CREATE TABLE IF NOT EXISTS authentication_logs (
                log_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                actiune VARCHAR(50) NOT NULL,
                ip_address VARCHAR(50) NOT NULL,
                data_actiune DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(user_id)
            );
            
            -- Tabelul setărilor site-ului
            CREATE TABLE IF NOT EXISTS site_settings (
                setting_id INT AUTO_INCREMENT PRIMARY KEY,
                nume_setare VARCHAR(100) NOT NULL,
                valoare TEXT NOT NULL
            );
            
            -- Inserare date inițiale
            INSERT IGNORE INTO site_settings (nume_setare, valoare) VALUES 
                ('nume_gara', 'Gara Centrală București'),
                ('adresa_gara', 'Piața Gării de Nord, București'),
                ('telefon_gara', '+40212345678'),
                ('email_gara', 'contact@gara-bucuresti.ro');
            
            -- Adăugare administrator implicit
            INSERT IGNORE INTO users (nume, email, parola, rol) VALUES 
                ('Admin', 'admin@gara.ro', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrator');
            ";
            
            $this->conn->exec($sql);
            echo "Tabelele au fost create cu succes!";
        } catch(PDOException $exception) {
            echo "Eroare la crearea tabelelor: " . $exception->getMessage();
        }
    }
}
?>