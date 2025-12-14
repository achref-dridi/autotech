-- Base de données AutoTech intégrée
CREATE DATABASE IF NOT EXISTS autotech_db;
USE autotech_db;

-- Table des utilisateurs
CREATE TABLE utilisateur (
    id_utilisateur INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telephone VARCHAR(20),
    mot_de_passe VARCHAR(255) NOT NULL,
    adresse TEXT,
    ville VARCHAR(100),
    code_postal VARCHAR(10),
    photo_profil VARCHAR(255),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    statut ENUM('actif', 'inactif', 'suspendu') DEFAULT 'actif',
    role ENUM('utilisateur', 'admin') DEFAULT 'utilisateur'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des véhicules (modifiée pour inclure le propriétaire)
CREATE TABLE vehicule (
    id_vehicule INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT UNSIGNED NOT NULL,
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(100) NOT NULL,
    annee YEAR NOT NULL,
    carburant VARCHAR(50) NOT NULL,
    kilometrage INT UNSIGNED NOT NULL,
    couleur VARCHAR(50),
    transmission VARCHAR(50),
    prix_journalier DECIMAL(10,2),
    description TEXT,
    image_principale VARCHAR(255),
    statut_disponibilite ENUM('disponible', 'loué', 'indisponible') DEFAULT 'disponible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des images supplémentaires pour les véhicules
CREATE TABLE image_vehicule (
    id_image INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_vehicule INT UNSIGNED NOT NULL,
    chemin_image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_vehicule) REFERENCES vehicule(id_vehicule) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des réservations (pour futures fonctionnalités)
CREATE TABLE reservation (
    id_reservation INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_vehicule INT UNSIGNED NOT NULL,
    id_utilisateur INT UNSIGNED NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    prix_total DECIMAL(10,2),
    statut ENUM('en_attente', 'confirmée', 'annulée', 'terminée') DEFAULT 'en_attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_vehicule) REFERENCES vehicule(id_vehicule) ON DELETE CASCADE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion d'un utilisateur admin par défaut
-- Mot de passe: admin123 (hashé avec PASSWORD_DEFAULT de PHP)
INSERT INTO utilisateur (nom, prenom, email, telephone, mot_de_passe, role) VALUES
('Admin', 'AutoTech', 'admin@autotech.tn', '+216 33 856 909', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insertion d'un utilisateur test
-- Mot de passe: test123
INSERT INTO utilisateur (nom, prenom, email, telephone, mot_de_passe, ville) VALUES
('Dupont', 'Jean', 'jean.dupont@email.tn', '+216 20 123 456', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tunis');
