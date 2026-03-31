-- =============================================
-- Vaikų Virtuali Biblioteka - Duomenų bazė
-- =============================================
-- Nukopijuokite šį kodą į phpMyAdmin > SQL langą ir paspauskite "Vykdyti"

CREATE DATABASE IF NOT EXISTS `vaiku_biblioteka` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `vaiku_biblioteka`;

-- =============================================
-- 1. Lentelės
-- =============================================

-- Kategorijos
CREATE TABLE `category` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `color` VARCHAR(7) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Knygos
CREATE TABLE `book` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `author` VARCHAR(255) NOT NULL,
    `description` LONGTEXT DEFAULT NULL,
    `min_age` INT DEFAULT NULL,
    `max_age` INT DEFAULT NULL,
    `cover_image` VARCHAR(255) DEFAULT NULL,
    `content_url` VARCHAR(500) DEFAULT NULL,
    `category_id` INT DEFAULT NULL,
    `created_at` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    PRIMARY KEY (`id`),
    INDEX `IDX_CBE5A33112469DE2` (`category_id`),
    CONSTRAINT `FK_CBE5A33112469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vartotojai
CREATE TABLE `user` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `email` VARCHAR(180) NOT NULL,
    `username` VARCHAR(100) NOT NULL,
    `roles` JSON NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `points` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Misijos
CREATE TABLE `mission` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` LONGTEXT DEFAULT NULL,
    `reward_points` INT NOT NULL,
    `type` VARCHAR(50) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ženkliukai
CREATE TABLE `badge` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `icon` VARCHAR(100) DEFAULT NULL,
    `required_points` INT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Prizai
CREATE TABLE `reward` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` LONGTEXT DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `cost_in_points` INT NOT NULL,
    `stock` INT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vartotojo misijos (tarpinė lentelė su verifikacija)
CREATE TABLE `user_mission` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `user_id` INT NOT NULL,
    `mission_id` INT NOT NULL,
    `is_completed` TINYINT(1) NOT NULL DEFAULT 0,
    `completed_at` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `proof_text` LONGTEXT DEFAULT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
    `reviewed_at` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `rejection_reason` LONGTEXT DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `IDX_USER_MISSION_USER` (`user_id`),
    INDEX `IDX_USER_MISSION_MISSION` (`mission_id`),
    INDEX `IDX_USER_MISSION_STATUS` (`status`),
    CONSTRAINT `FK_USER_MISSION_USER` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_USER_MISSION_MISSION` FOREIGN KEY (`mission_id`) REFERENCES `mission` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vartotojo ženkliukai (tarpinė lentelė)
CREATE TABLE `user_badge` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `user_id` INT NOT NULL,
    `badge_id` INT NOT NULL,
    `awarded_at` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    PRIMARY KEY (`id`),
    INDEX `IDX_USER_BADGE_USER` (`user_id`),
    INDEX `IDX_USER_BADGE_BADGE` (`badge_id`),
    CONSTRAINT `FK_USER_BADGE_USER` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_USER_BADGE_BADGE` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vartotojo prizai (tarpinė lentelė)
CREATE TABLE `user_reward` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `user_id` INT NOT NULL,
    `reward_id` INT NOT NULL,
    `claimed_at` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    PRIMARY KEY (`id`),
    INDEX `IDX_USER_REWARD_USER` (`user_id`),
    INDEX `IDX_USER_REWARD_REWARD` (`reward_id`),
    CONSTRAINT `FK_USER_REWARD_USER` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_USER_REWARD_REWARD` FOREIGN KEY (`reward_id`) REFERENCES `reward` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Doctrine migracijų lentelė (reikalinga Symfony)
CREATE TABLE `doctrine_migration_versions` (
    `version` VARCHAR(191) NOT NULL,
    `executed_at` DATETIME DEFAULT NULL,
    `execution_time` INT DEFAULT NULL,
    PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messenger žinučių lentelė (reikalinga Symfony)
CREATE TABLE `messenger_messages` (
    `id` BIGINT AUTO_INCREMENT NOT NULL,
    `body` LONGTEXT NOT NULL,
    `headers` LONGTEXT NOT NULL,
    `queue_name` VARCHAR(190) NOT NULL,
    `created_at` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `available_at` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `delivered_at` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    PRIMARY KEY (`id`),
    INDEX `IDX_75EA56E0FB7336F0` (`queue_name`),
    INDEX `IDX_75EA56E016BA31DB` (`available_at`),
    INDEX `IDX_75EA56E0E3BD61CE` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 2. Pradiniai duomenys (fixtures)
-- =============================================

-- Kategorijos
INSERT INTO `category` (`name`, `color`) VALUES
('Pasakos', '#FF6B9D'),
('Nuotykiai', '#6C63FF'),
('Mokslas', '#00D2FF'),
('Gamta', '#2ED573'),
('Fantastika', '#FFA502');

-- Knygos
INSERT INTO `book` (`title`, `author`, `description`, `category_id`, `min_age`, `max_age`, `created_at`) VALUES
('Raudonkepuraitė', 'Broliai Grimm', 'Klasikinė pasaka apie drąsią mergaitę.', 1, 4, 8, NOW()),
('Peliukas ir jo draugai', 'Autorius A', 'Nuotaikingas pasakojimas apie draugystę.', 1, 3, 6, NOW()),
('Džiunglių knyga', 'Rudyard Kipling', 'Berniukas Mauglis auga džiunglėse tarp gyvūnų.', 2, 6, 12, NOW()),
('Robinzonas Kruzas vaikams', 'Daniel Defoe', 'Supaprastinta versija vaikams.', 2, 8, 14, NOW()),
('Kosmoso paslaptys', 'Autorius B', 'Sužinok apie planetas ir žvaigždes!', 3, 7, 12, NOW()),
('Kaip veikia mašinos', 'Autorius C', 'Technika ir inžinerija vaikams.', 3, 8, 14, NOW()),
('Lietuvos miškų gyvūnai', 'Autorius D', 'Pažink Lietuvos gamtą!', 4, 5, 10, NOW()),
('Augalų pasaulis', 'Autorius E', 'Viskas apie augalus ir gėles.', 4, 6, 11, NOW()),
('Haris Poteris vaikams', 'J.K. Rowling', 'Magijos pasaulio nuotykiai.', 5, 8, 14, NOW()),
('Drakonų sala', 'Autorius F', 'Fantastiniai nuotykiai saloje.', 5, 7, 13, NOW());

-- Vartotojai (admin slaptažodis: admin123, jonas slaptažodis: user123)
-- PASTABA: slaptažodžiai yra hash'uoti su bcrypt. Naudokite Symfony komandą arba registracijos formą.
-- Čia admin slaptažodis = 'admin123', user slaptažodis = 'user123'
INSERT INTO `user` (`email`, `username`, `roles`, `password`, `points`, `created_at`) VALUES
('admin@biblioteka.lt', 'Administratorius', '["ROLE_ADMIN"]', '$2y$13$placeholder_admin_hash_replace_me', 0, NOW()),
('jonas@biblioteka.lt', 'Jonas', '[]', '$2y$13$placeholder_user_hash_replace_me', 50, NOW());

-- Misijos
INSERT INTO `mission` (`title`, `description`, `reward_points`, `type`) VALUES
('Perskaityk pirmą knygą', 'Pasirink bet kurią knygą ir ją perskaityk!', 10, 'skaitymas'),
('Perskaityk 3 knygas', 'Perskaityk bet kurias 3 knygas iš katalogo.', 30, 'skaitymas'),
('Perskaityk 5 knygas', 'Perskaityk 5 knygas ir tapk tikru skaitytoju!', 50, 'skaitymas'),
('Parašyk atsiliepimą', 'Parašyk trumpą atsiliepimą apie perskaitytą knygą.', 15, 'kūryba'),
('Rekomenduok draugui', 'Rekomenduok knygą savo draugui!', 10, 'bendravimas'),
('Skaitymo maratonas', 'Skaityk kiekvieną dieną visą savaitę!', 70, 'iššūkis'),
('Atrask naują žanrą', 'Perskaityk knygą iš žanro, kurio dar neskaičiai.', 20, 'tyrimas'),
('Knygų kirminėlis', 'Perskaityk 10 knygų!', 100, 'skaitymas');

-- Ženkliukai
INSERT INTO `badge` (`name`, `description`, `icon`, `required_points`) VALUES
('Pradedantysis', 'Surinkote pirmuosius 10 taškų!', '📖', 10),
('Skaitytojas', 'Surinkote 50 taškų!', '⭐', 50),
('Knygų draugas', 'Surinkote 100 taškų!', '🏅', 100),
('Skaitymo čempionas', 'Surinkote 200 taškų!', '🏆', 200),
('Knygų meistras', 'Surinkote 500 taškų!', '👑', 500);

-- Prizai
INSERT INTO `reward` (`name`, `description`, `image`, `cost_in_points`, `stock`) VALUES
('Spalvinimo knygelė', 'Graži spalvinimo knygelė su gyvūnais!', NULL, 30, 10),
('Lipdukai', 'Rinkinys spalvingų lipdukų!', NULL, 20, 20),
('Knyga-dovana', 'Pasirink bet kurią knygą kaip dovaną!', NULL, 100, 5),
('Žaisliukas', 'Mažas žaisliukas-staigmena!', NULL, 150, 3),
('Specialus ženkliukas', 'Unikalus aukso ženkliukas!', NULL, 200, 2);
