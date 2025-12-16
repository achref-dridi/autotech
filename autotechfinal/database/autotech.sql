CREATE DATABASE IF NOT EXISTS autotech_db;
USE autotech_db;

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
    role ENUM('utilisateur', 'admin') DEFAULT 'utilisateur',
    reset_token VARCHAR(255),
    reset_expires DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE vehicule (
    id_vehicule INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT UNSIGNED NOT NULL,
    id_boutique INT UNSIGNED,
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
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_boutique) REFERENCES boutique(id_boutique) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE boutique (
    id_boutique INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom_boutique VARCHAR(150) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    logo VARCHAR(255),
    id_utilisateur INT UNSIGNED NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE,
    UNIQUE KEY unique_boutique_user (nom_boutique, id_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS technicien (
    id_technicien INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    specialite VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(255),
    disponibilite VARCHAR(50) DEFAULT 'actif',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rendez_vous (
    id_rdv INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    id_technicien INT UNSIGNED NOT NULL,
    id_utilisateur INT UNSIGNED NOT NULL,

    date_rdv DATETIME NOT NULL,
    type_intervention VARCHAR(255) NOT NULL,
    commentaire TEXT,
    statut VARCHAR(50) DEFAULT 'en attente',
    google_event_id VARCHAR(255),

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_rdv_technicien
        FOREIGN KEY (id_technicien)
        REFERENCES technicien(id_technicien)
        ON DELETE CASCADE,

    CONSTRAINT fk_rdv_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE,

    INDEX idx_technicien (id_technicien),
    INDEX idx_utilisateur (id_utilisateur),
    INDEX idx_date (date_rdv),

    UNIQUE KEY unique_google_event (google_event_id)
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS reservation (
    id_reservation INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_vehicule INT UNSIGNED NOT NULL,
    id_utilisateur INT UNSIGNED NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    statut VARCHAR(50) DEFAULT 'en attente',
    prix_total DECIMAL(10,2),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_reservation_vehicule
        FOREIGN KEY (id_vehicule)
        REFERENCES vehicule(id_vehicule)
        ON DELETE CASCADE,
    
    CONSTRAINT fk_reservation_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE,
    
    INDEX idx_vehicule (id_vehicule),
    INDEX idx_utilisateur (id_utilisateur),
    INDEX idx_dates (date_debut, date_fin),
    INDEX idx_statut (statut)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS trajet (
    id_trajet INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT UNSIGNED NOT NULL,
    lieu_depart VARCHAR(255) NOT NULL,
    lieu_arrivee VARCHAR(255) NOT NULL,
    date_depart DATETIME NOT NULL,
    duree_minutes INT UNSIGNED NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    description TEXT,
    places_disponibles INT UNSIGNED DEFAULT 1,
    statut ENUM('disponible', 'complet', 'termine', 'annule') DEFAULT 'disponible',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_trajet_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE,
    
    INDEX idx_utilisateur (id_utilisateur),
    INDEX idx_date_depart (date_depart),
    INDEX idx_statut (statut),
    INDEX idx_lieux (lieu_depart, lieu_arrivee)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reservation_trajet (
    id_reservation_trajet INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_trajet INT UNSIGNED NOT NULL,
    id_utilisateur INT UNSIGNED NOT NULL,
    nombre_places INT UNSIGNED DEFAULT 1,
    statut ENUM('en attente', 'confirmee', 'rejetee', 'annulee') DEFAULT 'en attente',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_res_trajet_trajet
        FOREIGN KEY (id_trajet)
        REFERENCES trajet(id_trajet)
        ON DELETE CASCADE,
    
    CONSTRAINT fk_res_trajet_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE,
    
    INDEX idx_trajet (id_trajet),
    INDEX idx_utilisateur (id_utilisateur),
    INDEX idx_statut (statut)
) ENGINE=InnoDB;


-- Sample technicians data
INSERT INTO technicien (nom, specialite, telephone, email, disponibilite) VALUES
('Moemen Toukebri', 'Diagnostic moteur', '98765432', 'ali.tech@autotech.tn', 'actif'),
('Khaled Ben Salah', 'Réparation freins', '98765433', 'khaled.tech@autotech.tn', 'actif'),
('Fatima Ezzahra', 'Électricité automobile', '98765434', 'fatima.tech@autotech.tn', 'actif'),
('Nabil Jebali', 'Changement pneus', '98765435', 'nabil.tech@autotech.tn', 'actif'),
('Amel Kareem', 'Révision générale', '98765436', 'amel.tech@autotech.tn', 'actif');
