-- Emlak Arayış Veritabanı Şeması
-- emlakarayis.com

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Veritabanı oluştur
CREATE DATABASE IF NOT EXISTS `emlakarayis` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_turkish_ci;

USE `emlakarayis`;

-- -----------------------------------------------------
-- Tablo: `admins` (Yöneticiler)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- Varsayılan admin (şifre: admin123 - Değiştirin!)
INSERT INTO `admins` (`username`, `password`) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- -----------------------------------------------------
-- Tablo: `users` (Doğrulanmış Kullanıcılar)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `phone` VARCHAR(20) NOT NULL UNIQUE,
    `agent_name` VARCHAR(100) NOT NULL,
    `agency_name` VARCHAR(150) DEFAULT NULL,
    `instagram` VARCHAR(100) DEFAULT NULL,
    `is_verified` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `remember_token` VARCHAR(64) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_phone` (`phone`),
    INDEX `idx_verified` (`is_verified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- -----------------------------------------------------
-- Tablo: `verification_requests` (Doğrulama Talepleri)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `verification_requests`;
CREATE TABLE `verification_requests` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `phone` VARCHAR(20) NOT NULL,
    `agent_name` VARCHAR(100) NOT NULL,
    `agency_name` VARCHAR(150) DEFAULT NULL,
    `instagram` VARCHAR(100) DEFAULT NULL,
    `verification_code` VARCHAR(6) DEFAULT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `admin_note` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `processed_at` DATETIME DEFAULT NULL,
    INDEX `idx_phone` (`phone`),
    INDEX `idx_status` (`status`),
    INDEX `idx_code` (`verification_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- -----------------------------------------------------
-- Tablo: `searches` (Arayışlar)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `searches`;
CREATE TABLE `searches` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `transaction_type` ENUM('satilik', 'kiralik') NOT NULL,
    `property_type` ENUM('daire', 'villa', 'arsa', 'ticari', 'rezidans', 'mustakil', 'ofis', 'dukkan', 'depo') NOT NULL,
    `city` VARCHAR(50) NOT NULL,
    `district` VARCHAR(50) DEFAULT NULL,
    `neighborhood` VARCHAR(100) DEFAULT NULL,
    `max_price` DECIMAL(15,2) NOT NULL,
    `features` TEXT DEFAULT NULL,
    `special_note` VARCHAR(100) DEFAULT NULL,
    `duration_days` INT DEFAULT 7,
    `status` ENUM('active', 'expired', 'closed') DEFAULT 'active',
    `expires_at` DATETIME NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_status` (`status`),
    INDEX `idx_transaction` (`transaction_type`),
    INDEX `idx_property` (`property_type`),
    INDEX `idx_city` (`city`),
    INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- -----------------------------------------------------
-- Örnek Veriler
-- -----------------------------------------------------

-- Örnek kullanıcılar
INSERT INTO `users` (`phone`, `agent_name`, `agency_name`, `instagram`, `is_verified`) VALUES
('905551234567', 'Ahmet Yılmaz', 'Ekol Gayrimenkul', '@ekolgayrimenkul', 1),
('905559876543', 'Ayşe Demir', 'Mavi Vizyon Emlak', '@mavivizyon', 1),
('905553334455', 'Murat Aras', 'Aras Arsa Ofisi', '@arasarsa', 1);

-- Örnek arayışlar
INSERT INTO `searches` (`user_id`, `transaction_type`, `property_type`, `city`, `district`, `neighborhood`, `max_price`, `features`, `special_note`, `duration_days`, `expires_at`) VALUES
(1, 'satilik', 'daire', 'İstanbul', 'Kadıköy', 'Moda', 15000000.00, '3+1, 120m², Kombili, Ara Kat', 'Acil, Krediye Uygun', 7, DATE_ADD(NOW(), INTERVAL 7 DAY)),
(2, 'kiralik', 'ofis', 'İstanbul', 'Şişli', 'Esentepe', 45000.00, 'Plaza Katı, 200m², Eşyalı', 'Üst Düzey Yönetici İçin', 14, DATE_ADD(NOW(), INTERVAL 14 DAY)),
(3, 'satilik', 'arsa', 'İzmir', 'Urla', 'İskele', 12000000.00, '1000m², Konut İmarlı, Deniz Manzaralı', NULL, 7, DATE_ADD(NOW(), INTERVAL 7 DAY)),
(1, 'kiralik', 'daire', 'Ankara', 'Çankaya', 'Ayrancı', 25000.00, '2+1, Eşyalı, Site İçi', 'Genç Çalışan İçin', 7, DATE_ADD(NOW(), INTERVAL 7 DAY)),
(2, 'satilik', 'villa', 'Muğla', 'Bodrum', 'Yalıkavak', 35000000.00, 'Deniz Manzaralı, Havuzlu, 5+2', 'Yatırımcı Müşteri', 14, DATE_ADD(NOW(), INTERVAL 14 DAY)),
(3, 'kiralik', 'dukkan', 'İstanbul', 'Beşiktaş', 'Çarşı', 80000.00, 'Cadde Üzeri, 50m²', 'Devren de Olur', 7, DATE_ADD(NOW(), INTERVAL 7 DAY)),
(1, 'satilik', 'daire', 'Antalya', 'Muratpaşa', 'Lara', 8500000.00, 'Site İçi, 3+1, Yüzme Havuzu', NULL, 7, DATE_ADD(NOW(), INTERVAL 7 DAY)),
(2, 'kiralik', 'depo', 'İstanbul', 'Tuzla', 'OSB', 60000.00, '500m², Yüksek Tavan, Rampalı', 'Lojistik Firma İçin', 14, DATE_ADD(NOW(), INTERVAL 14 DAY));

SET FOREIGN_KEY_CHECKS = 1;
