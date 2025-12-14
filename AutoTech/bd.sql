
CREATE DATABASE IF NOT EXISTS voitureDB;

-- Utiliser la base de données
USE voitureDB;

CREATE TABLE vehicule (
    id_vehicule INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(100) NOT NULL,
    annee YEAR NOT NULL,
    carburant VARCHAR(50) NOT NULL,
    kilometrage INT UNSIGNED NOT NULL,
    couleur VARCHAR(50),
    transmission VARCHAR(50),
    image_principale VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


