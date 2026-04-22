CREATE DATABASE IF NOT EXISTS ememoire_lcs 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE ememoire_lcs;


CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('etudiant', 'encadreur', 'responsable', 'admin', 'jury') NOT NULL,
    filiere VARCHAR(100) DEFAULT NULL,
    matricule VARCHAR(50) UNIQUE NULL COMMENT 'Numéro matricule de l''étudiant',
    photo VARCHAR(255) DEFAULT NULL,
    secret_2fa VARCHAR(255) DEFAULT NULL COMMENT 'Secret pour authentification à deux facteurs',
    remember_token VARCHAR(255) DEFAULT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_matricule (matricule)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : filieres
-- Description : Liste des filières gérées par l'admin
-- =====================================================
CREATE TABLE filieres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : annees_universitaires
-- Description : Années académiques
-- =====================================================
CREATE TABLE annees_universitaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(20) NOT NULL COMMENT 'Ex: 2025-2026',
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    active BOOLEAN DEFAULT FALSE COMMENT 'Année courante',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_libelle (libelle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : memoires
-- Description : Informations principales des mémoires
-- =====================================================
CREATE TABLE memoires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    theme_description TEXT COMMENT 'Description initiale du thème (soumis par l''étudiant)',
    resume TEXT COMMENT 'Résumé final du mémoire',
    mots_cles VARCHAR(255),
    fichier VARCHAR(255) DEFAULT NULL COMMENT 'Chemin du fichier PDF',
    couverture VARCHAR(255) DEFAULT NULL COMMENT 'Image de couverture',
    statut ENUM('brouillon', 'soumis', 'en_cours', 'valide', 'soutenu', 'rejete', 'archive') DEFAULT 'brouillon',
    theme_feedback TEXT COMMENT 'Commentaire de rejet de l''encadreur',
    date_soumission DATE,
    id_etudiant INT NOT NULL COMMENT 'Créateur principal (chef de groupe)',
    id_encadreur INT DEFAULT NULL,
    id_annee_universitaire INT DEFAULT NULL,
    version_actuelle INT DEFAULT 1,
    verrou_par INT DEFAULT NULL COMMENT 'ID utilisateur qui verrouille le mémoire',
    verrou_le TIMESTAMP NULL COMMENT 'Date de verrouillage',
    nb_telechargements INT DEFAULT 0,
    nb_vues INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_etudiant) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (id_encadreur) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    FOREIGN KEY (id_annee_universitaire) REFERENCES annees_universitaires(id) ON DELETE SET NULL,
    INDEX idx_statut (statut),
    INDEX idx_etudiant (id_etudiant),
    INDEX idx_encadreur (id_encadreur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : memoire_etudiants
-- Description : Groupes (plusieurs étudiants par mémoire)
-- =====================================================
CREATE TABLE memoire_etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_memoire INT NOT NULL,
    id_etudiant INT NOT NULL,
    role ENUM('chef', 'co-auteur', 'membre', 'binome') DEFAULT 'membre',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_memoire) REFERENCES memoires(id) ON DELETE CASCADE,
    FOREIGN KEY (id_etudiant) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_membre (id_memoire, id_etudiant)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : versions
-- Description : Historique des versions déposées
-- =====================================================
CREATE TABLE versions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_memoire INT NOT NULL,
    fichier VARCHAR(255) NOT NULL,
    commentaire TEXT,
    date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    numero INT NOT NULL COMMENT 'Numéro de version',
    FOREIGN KEY (id_memoire) REFERENCES memoires(id) ON DELETE CASCADE,
    INDEX idx_memoire (id_memoire)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : feedbacks
-- Description : Commentaires des encadreurs/étudiants sur les versions
-- =====================================================
CREATE TABLE feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_version INT NOT NULL,
    id_utilisateur INT NOT NULL,
    message TEXT NOT NULL,
    id_critere INT NULL,
    note_critere DECIMAL(4,2) NULL,
    piece_jointe VARCHAR(255) DEFAULT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_version) REFERENCES versions(id) ON DELETE CASCADE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_version (id_version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : soutenances
-- Description : Planification des soutenances
-- =====================================================
CREATE TABLE soutenances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_memoire INT NOT NULL,
    date DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    salle VARCHAR(50) NOT NULL,
    statut ENUM('planifiee', 'terminee', 'archive') DEFAULT 'planifiee',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_memoire) REFERENCES memoires(id) ON DELETE CASCADE,
    INDEX idx_date (date),
    INDEX idx_salle (salle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : jury
-- Description : Membres du jury pour chaque soutenance
-- =====================================================
CREATE TABLE jury (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_soutenance INT NOT NULL,
    id_utilisateur INT NOT NULL,
    role ENUM('president', 'examinateur') NOT NULL,
    FOREIGN KEY (id_soutenance) REFERENCES soutenances(id) ON DELETE CASCADE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_membre_soutenance (id_soutenance, id_utilisateur),
    INDEX idx_soutenance (id_soutenance)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : evaluations
-- Description : Notes et appréciations des jurys
-- =====================================================
CREATE TABLE evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_soutenance INT NOT NULL,
    id_utilisateur INT NOT NULL COMMENT 'Membre du jury',
    note DECIMAL(4,2) NOT NULL COMMENT 'Note sur 20',
    appreciation TEXT,
    mention VARCHAR(50) DEFAULT NULL,
    date_evaluation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_soutenance) REFERENCES soutenances(id) ON DELETE CASCADE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_evaluation (id_soutenance, id_utilisateur),
    INDEX idx_soutenance (id_soutenance)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : criteres_evaluation
-- Description : Grille d'évaluation personnalisable
-- =====================================================
CREATE TABLE criteres_evaluation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    poids DECIMAL(5,2) DEFAULT 1.00,
    actif BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : notifications
-- Description : Notifications internes
-- =====================================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    message TEXT NOT NULL,
    lien VARCHAR(255) DEFAULT NULL,
    lu BOOLEAN DEFAULT FALSE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_utilisateur (id_utilisateur),
    INDEX idx_lu (lu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : logs
-- Description : Journalisation des actions administrateurs
-- =====================================================
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip VARCHAR(45),
    user_agent TEXT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_utilisateur (id_utilisateur),
    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE disponibilites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    date DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    motif VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_utilisateur (id_utilisateur),
    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : salles
-- Description : Salles disponibles pour les soutenances
-- =====================================================
CREATE TABLE salles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE,
    capacite INT NOT NULL,
    equipement TEXT,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : plagiat_checks
-- Description : Résultats des vérifications de plagiat
-- =====================================================
CREATE TABLE plagiat_checks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_memoire INT NOT NULL,
    id_version INT NOT NULL,
    date_check TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    score DECIMAL(5,2) COMMENT 'Pourcentage de similarité',
    rapport TEXT,
    status ENUM('en_attente', 'termine', 'erreur') DEFAULT 'en_attente',
    FOREIGN KEY (id_memoire) REFERENCES memoires(id) ON DELETE CASCADE,
    FOREIGN KEY (id_version) REFERENCES versions(id) ON DELETE CASCADE,
    INDEX idx_memoire (id_memoire)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : attestations
-- Description : Attestations téléversées par l'admin
-- =====================================================
CREATE TABLE attestations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_etudiant INT NOT NULL,
    id_memoire INT NOT NULL,
    type VARCHAR(50) DEFAULT 'soutenance',
    numero VARCHAR(50) UNIQUE COMMENT 'Numéro d''attestation',
    date_emission DATE NOT NULL,
    fichier VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_etudiant) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (id_memoire) REFERENCES memoires(id) ON DELETE CASCADE,
    UNIQUE KEY unique_memoire_etudiant (id_memoire, id_etudiant),
    INDEX idx_etudiant (id_etudiant)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : matricules
-- Description : Gestion des numéros matricules (pré-inscription)
-- =====================================================
CREATE TABLE matricules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricule VARCHAR(50) NOT NULL UNIQUE,
    etudiant_id INT NULL COMMENT 'ID de l''étudiant qui a utilisé ce matricule',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    INDEX idx_matricule (matricule)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Insertion des données de base minimales
-- =====================================================

-- Insertion des filières par défaut
INSERT INTO filieres (nom) VALUES 
('Systèmes Informatiques'),
('Réseaux et Télécommunications'),
('Génie Logiciel'),
('Intelligence Artificielle'),
('Cybersécurité');

-- Insertion d'une année universitaire par défaut (active)
INSERT INTO annees_universitaires (libelle, date_debut, date_fin, active) VALUES 
('2025-2026', '2025-09-01', '2026-07-31', 1);

-- Insertion de critères d'évaluation par défaut
INSERT INTO criteres_evaluation (nom, description, poids, actif) VALUES 
('Qualité du fond', 'Pertinence, originalité, rigueur scientifique', 1.0, 1),
('Qualité de la forme', 'Structure, style, orthographe', 1.0, 1),
('Présentation orale', 'Clarté, maîtrise du sujet, qualité des supports', 1.0, 1),
('Défense et discussion', 'Capacité à répondre aux questions', 1.0, 1);

-- Insertion de salles par défaut
INSERT INTO salles (nom, capacite, equipement, active) VALUES 
('Salle 101', 30, 'Vidéo projecteur, tableau blanc', 1),
('Salle 102', 40, 'Vidéo projecteur, tableau blanc, climatisation', 1),
('Amphi A', 150, 'Écran géant, sonorisation, micros', 1),
('Salle 201', 25, 'Tableau interactif', 1);

-- Aucun utilisateur n'est créé (le premier admin sera créé via l'interface d'installation).
-- =====================================================

-- Afficher un récapitulatif des tables créées
SHOW TABLES;