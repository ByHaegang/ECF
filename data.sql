CREATE TABLE role (
    role_id INT PRIMARY KEY NOT NULL,
    libelle VARCHAR(50)
);

CREATE TABLE theme (
    theme_id SERIAL PRIMARY KEY NOT NULL,
    libelle VARCHAR(50)
);

CREATE TABLE allergene (
    allergene_id SERIAL PRIMARY KEY NOT NULL,
    libelle VARCHAR(50)
);

CREATE TABLE horaire (
    horaire_id SERIAL PRIMARY KEY NOT NULL,
    jour VARCHAR(50),
    heure_ouverture VARCHAR(50),
    heure_fermeture VARCHAR(50)
);

CREATE TABLE plat (
    plat_id SERIAL PRIMARY KEY NOT NULL,
    titre_plat VARCHAR(50),
    photo BYTEA
);

CREATE TABLE regime (
    regime_id SERIAL PRIMARY KEY NOT NULL,
    libelle VARCHAR(50)
);

-- ==============================================================
-- 2. CRÉATION DES TABLES DÉPENDANTES (Avec clés étrangères)
-- ==============================================================

CREATE TABLE utilisateur (
    utilisateur_id SERIAL PRIMARY KEY NOT NULL,
    email VARCHAR(50),
    password VARCHAR(255),
    nom VARCHAR(50),
    prenom VARCHAR(50),
    telephone VARCHAR(50),
    ville VARCHAR(50),
    pays VARCHAR(50),
    adresse_postale VARCHAR(50),
    role_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES role(role_id)
);

CREATE TABLE menu (
    menu_id SERIAL PRIMARY KEY NOT NULL,
    titre VARCHAR(50),
    nombre_personne_minimum INT,
    prix_par_personne DOUBLE PRECISION,
    regime VARCHAR(50),
    description VARCHAR(50),
    quantite_restante INT,
    theme_id INT NOT NULL,
    regime_id INT NOT NULL,
    FOREIGN KEY (theme_id) REFERENCES theme(theme_id),
    FOREIGN KEY (regime_id) REFERENCES regime(regime_id)
);

CREATE TABLE commande (
    numero_commande SERIAL PRIMARY KEY NOT NULL,
    date_commande TIMESTAMP, 
    date_prestation DATE,
    heure_livraison VARCHAR(50),
    prix_menu INT, 
    nombre_personne INT,
    prix_livraison INT, 
    statut VARCHAR(50),
    pret_materiel BOOLEAN,
    restitution_materiel BOOLEAN,
    utilisateur_id INT NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id)
);

CREATE TABLE avis (
    avis_id SERIAL PRIMARY KEY NOT NULL,
    note VARCHAR(50),
    description VARCHAR(50),
    statut VARCHAR(50),
    utilisateur_id INT NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id)
);

-- ==============================================================
-- 3. CRÉATION DES TABLES DE LIAISON
-- ==============================================================

CREATE TABLE commande_menu (
    numero_commande INT NOT NULL, 
    menu_id INT NOT NULL,
    PRIMARY KEY (numero_commande, menu_id),
    FOREIGN KEY (numero_commande) REFERENCES commande(numero_commande),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id)
);

CREATE TABLE propose (
    menu_id INT NOT NULL,
    plat_id INT NOT NULL,
    PRIMARY KEY (menu_id, plat_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id),
    FOREIGN KEY (plat_id) REFERENCES plat(plat_id)
);

CREATE TABLE contient (
    plat_id INT NOT NULL,
    allergene_id INT NOT NULL,
    PRIMARY KEY (plat_id, allergene_id),
    FOREIGN KEY (plat_id) REFERENCES plat(plat_id),
    FOREIGN KEY (allergene_id) REFERENCES allergene(allergene_id)
);

-- ==============================================================
-- 4. INSERTIONS (Exemple avec les régimes et thèmes)
-- ==============================================================


SELECT setval('regime_regime_id_seq', (SELECT MAX(regime_id) FROM regime));
SELECT setval('theme_theme_id_seq', (SELECT MAX(theme_id) FROM theme));
INSERT INTO regime (regime_id, libelle) VALUES
(1, 'Omnivore'),
(2, 'Végétarien'),
(3, 'Végan'),
(4, 'Sans Gluten');

INSERT INTO theme (theme_id, libelle) VALUES
(1, 'Traditionnel Français'),
(2, 'Asiatique Fusion'),
(3, 'Méditerranéen'),
(4, 'Gastronomique'),
(5, 'Street Food');

INSERT INTO menu (menu_id, titre, nombre_personne_minimum, prix_par_personne, regime, description, quantite_restante, theme_id, regime_id) VALUES
(1, 'Menu Terroir', 2, 2550, 'Omnivore', 'Entrée, plat et dessert traditionnels', 50, 1, 1),
(2, 'Délices d''Asie', 1, 1800, 'Omnivore', 'Assortiment de nems et canard laqué', 30, 2, 1),
(3, 'Fraîcheur Italienne', 2, 2200, 'Végétarien', 'Pâtes fraîches au pesto et burrata', 40, 3, 2),
(4, 'Le Prestige', 4, 6500, 'Omnivore', 'Menu gastronomique d''exception', 15, 4, 1),
(5, 'Burger Gourmet', 1, 1550, 'Omnivore', 'Burger maison avec frites fraîches', 60, 5, 1),
(6, 'Sushis & Sashimis', 2, 2800, 'Omnivore', 'Plateau de sushis variés frais', 25, 2, 1),
(7, 'Le Végé-Gourmand', 1, 1950, 'Végétarien', 'Curry de légumes de saison doux', 35, 2, 2),
(8, 'Soleil d''Orient', 4, 2400, 'Omnivore', 'Couscous royal aux trois viandes', 20, 3, 1),
(9, 'Vegan Burger Party', 1, 1600, 'Végan', 'Burger 100% végétal et frites', 45, 5, 3),
(10, 'Douceur Sans Gluten', 2, 2150, 'Sans Gluten', 'Plat adapté aux intolérances', 15, 1, 4);

-- Mise à jour des séquences pour que les futurs auto-incréments fonctionnent bien
SELECT setval('regime_regime_id_seq', (SELECT MAX(regime_id) FROM regime));
SELECT setval('theme_theme_id_seq', (SELECT MAX(theme_id) FROM theme));
SELECT setval('menu_menu_id_seq', (SELECT MAX(menu_id) FROM menu));