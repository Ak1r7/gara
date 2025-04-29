CREATE DATABASE IF NOT EXISTS gara_feroviara;
USE gara_feroviara;

-- 1. Tabel utilizatori
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    nume VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    parola VARCHAR(255) NOT NULL,
    telefon VARCHAR(20),
    data_inregistrare DATETIME DEFAULT CURRENT_TIMESTAMP,
    rol ENUM('utilizator', 'administrator') DEFAULT 'utilizator'
);

-- 2. Tabel trenuri
CREATE TABLE trains (
    train_id INT AUTO_INCREMENT PRIMARY KEY,
    numar_tren VARCHAR(50) NOT NULL,
    tip_tren ENUM('rapid', 'intercity', 'regio', 'express') NOT NULL,
    plecare VARCHAR(100) NOT NULL,
    sosire VARCHAR(100) NOT NULL,
    rute TEXT,
    durata_traseu INT,
    status ENUM('în circulație', 'întârziat', 'anulat') DEFAULT 'în circulație'
);

-- 3. Tabel rute
CREATE TABLE routes (
    route_id INT AUTO_INCREMENT PRIMARY KEY,
    start_station VARCHAR(255) NOT NULL,
    end_station VARCHAR(255) NOT NULL,
    distanta INT NOT NULL,
    tip_ruta ENUM('directă', 'cu opriri') DEFAULT 'directă'
);

-- 4. Tabel bilete
CREATE TABLE tickets (
    ticket_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    train_id INT,
    clasa ENUM('1', '2', '3') NOT NULL,
    pret DECIMAL(10,2) NOT NULL,
    data_achizitie DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('achiziționat', 'anulat') DEFAULT 'achiziționat',
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (train_id) REFERENCES trains(train_id)
);

-- 5. Tabel notificări
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    train_id INT,
    mesaj TEXT NOT NULL,
    data_emitere DATETIME DEFAULT CURRENT_TIMESTAMP,
    tip_notificare ENUM('întârziere', 'modificare traseu', 'anulare') DEFAULT 'întârziere',
    FOREIGN KEY (train_id) REFERENCES trains(train_id)
);

-- 6. Tabel servicii
CREATE TABLE services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    nume_serviciu VARCHAR(255) NOT NULL,
    descriere TEXT,
    cost DECIMAL(10,2),
    disponibilitate BOOLEAN DEFAULT 1
);

-- 7. Tabel hartă gară
CREATE TABLE station_map (
    map_id INT AUTO_INCREMENT PRIMARY KEY,
    numar_peron VARCHAR(50) NOT NULL,
    detalii TEXT,
    latitudine DECIMAL(10,6),
    longitudine DECIMAL(10,6),
    tip_locatie ENUM('peron', 'ghiseu', 'toaleta', 'restaurant') NOT NULL
);

-- 8. Tabel loguri autentificare
CREATE TABLE authentication_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    actiune VARCHAR(255),
    ip_address VARCHAR(50),
    data_actiune DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 9. Tabel setări site
CREATE TABLE site_settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    nume_setare VARCHAR(255) NOT NULL,
    valoare TEXT
);
