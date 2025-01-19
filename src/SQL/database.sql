DROP TABLE IF EXISTS LIKES;
DROP TABLE IF EXISTS RECENSIONE;
DROP TABLE IF EXISTS UTENTE;
DROP TABLE IF EXISTS SCARPA;

CREATE TABLE SCARPA (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    marca VARCHAR(100) NOT NULL,
    tipo ENUM('strada', 'trail', 'pista') NOT NULL,
    descrizione TEXT,
    dettagli TEXT,
    votoexp INT NOT NULL CHECK (votoexp BETWEEN 1 AND 5),
    feedback TEXT,
    immagine VARCHAR(255) NOT NULL,
    data_aggiunta DATE
);

CREATE TABLE UTENTE (
    username VARCHAR(100) PRIMARY KEY,
    pw CHAR(255),
    ruolo ENUM('tartaruga', 'lepre', 'lupo', 'ghepardo') NOT NULL DEFAULT 'tartaruga',
    admin BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE RECENSIONE (
    username VARCHAR(100),
    scarpa_id INT,
    voto INT NOT NULL CHECK (voto BETWEEN 1 AND 5),
    commento TEXT,
    data_aggiunta DATE,
    PRIMARY KEY (username, scarpa_id),
    FOREIGN KEY (username) REFERENCES UTENTE(username) ON DELETE CASCADE,
    FOREIGN KEY (scarpa_id) REFERENCES SCARPA(id) ON DELETE CASCADE
);

CREATE TABLE LIKES (
    username VARCHAR(100),
    scarpa_id INT,
    data_aggiunta DATE,
    PRIMARY KEY (username, scarpa_id),
    FOREIGN KEY (username) REFERENCES UTENTE(username) ON DELETE CASCADE,
    FOREIGN KEY (scarpa_id) REFERENCES SCARPA(id) ON DELETE CASCADE
);

-- Dati per SCARPA
INSERT INTO SCARPA (nome, marca, descrizione, dettagli, votoexp, feedback, immagine, data_aggiunta)
VALUES 
('Pegasus Trail 4', 'Nike', 'Scarpa versatile per trail running.', 'Ottima per trail di media difficoltà.', 'Rosso, Nero', 4, 'Comoda e affidabile', 'pegasus_trail_4.jpg', '2025-01-10'),
('UltraBoost Light', 'Adidas', 'Scarpa con un’ammortizzazione avanzata.', 'Ideale per runner su strada.', 'Bianco, Blu', 5, 'Ottima per lunghe distanze', 'ultraboost_light.jpg', '2025-01-12'),
('Gel-Kayano 30', 'Asics', 'Perfetta per chi cerca stabilità.', 'Supporta pronatori leggeri.', 'Grigio, Arancione', 4, 'Supporto eccellente', 'gel_kayano_30.jpg', '2025-01-15'),
('Hoka Speedgoat 5', 'Hoka', 'Ideale per terreni accidentati.', 'Scarpa molto robusta.', 'Verde, Giallo', 5, 'Aderenza incredibile', 'speedgoat_5.jpg', '2025-01-17'),
('Zoom Fly 5', 'Nike', 'Scarpa veloce per gare su strada.', 'Reattiva e leggera.', 'Nero, Bianco', 4, 'Leggera e reattiva', 'zoom_fly_5.jpg', '2025-01-20'),
('Altra Lone Peak 7', 'Altra', 'Perfetta per lunghe escursioni trail.', 'Ampia vestibilità.', 'Blu, Verde', 5, 'Ampio spazio per le dita', 'lone_peak_7.jpg', '2025-01-22'),
('Metaspeed Sky+', 'Asics', 'Pensata per migliorare la velocità.', 'Ottima per gare di velocità.', 'Rosso, Nero', 5, 'Ideale per record personali', 'metaspeed_sky_plus.jpg', '2025-01-25'),
('Brooks Ghost 15', 'Brooks', 'Scarpa universale per corsa su strada.', 'Morbida e reattiva.', 'Grigio, Blu', 4, 'Ottima per runner intermedi', 'ghost_15.jpg', '2025-01-28'),
('Saucony Endorphin Speed 3', 'Saucony', 'Scarpa leggera e reattiva.', 'Ideale per allenamenti veloci.', 'Rosa, Bianco', 5, 'Reattiva e performante', 'endorphin_speed_3.jpg', '2025-01-30'),
('Mizuno Wave Rider 27', 'Mizuno', 'Scarpa con tecnologia Wave.', 'Ammortizzata e stabile.', 'Bianco, Verde', 4, 'Perfetta per runner quotidiani', 'wave_rider_27.jpg', '2025-02-01'),
('New Balance Fresh Foam 1080v12', 'New Balance', 'Comfort eccezionale per lunghe distanze.', 'Vestibilità perfetta.', 'Blu, Grigio', 5, 'Super comoda', '1080v12.jpg', '2025-02-03'),
('Nike Vaporfly Next% 3', 'Nike', 'Scarpa da gara ultraleggera.', 'Velocità e comfort.', 'Arancione, Nero', 5, 'Top di gamma', 'vaporfly_next_3.jpg', '2025-02-05'),
('Adidas Adizero Adios Pro 3', 'Adidas', 'Scarpa da competizione.', 'Leggera e veloce.', 'Giallo, Verde', 5, 'Massima performance', 'adios_pro_3.jpg', '2025-02-07'),
('Asics Novablast 3', 'Asics', 'Ottima ammortizzazione.', 'Reattiva e leggera.', 'Viola, Rosa', 4, 'Super rimbalzo', 'novablast_3.jpg', '2025-02-09'),
('Hoka Clifton 9', 'Hoka', 'Scarpa morbida e ammortizzata.', 'Ottima per lunghe distanze.', 'Azzurro, Bianco', 5, 'Comfort incredibile', 'clifton_9.jpg', '2025-02-11');

-- Dati per UTENTE
INSERT INTO UTENTE (username, pw, ruolo, admin) VALUES
('user', '$2y$10$/BjuFjBdobvdAOqIZFHGO.aa46YkgmIb3A/YyIIA94is2Inq7cUgm', 'tartaruga', FALSE),
('admin', '$2y$10$b154faL4nCt/0GTE/svh4.oCiawsVaBrkQ2uu6VT6Bm/37oPROi5i', 'ghepardo', TRUE),
('giulia', '$2y$10$GiuliaPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'lepre', FALSE),
('marco', '$2y$10$MarcoPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'lupo', FALSE),
('sara', '$2y$10$SaraPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'tartaruga', FALSE),
('luca', '$2y$10$LucaPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'ghepardo', FALSE);

-- Dati per RECENSIONE
INSERT INTO RECENSIONE (username, scarpa_id, voto, commento, data_aggiunta) VALUES
('user', 1, 5, 'Scarpe fantastiche! Perfette per correre e molto comode.', '2025-01-10'),
('admin', 2, 4, "Buone scarpe, ma la vestibilità è un po' stretta.", '2025-01-11'),
('giulia', 1, 5, 'Le adoro! Design bellissimo e comodità incredibile.', '2025-01-12'),
('marco', 3, 3, 'La qualità è buona, ma non giustifica il prezzo elevato.', '2025-01-13'),
('sara', 2, 4, 'Molto belle, ma avrei preferito una suola più morbida.', '2025-01-13'),
('luca', 1, 2, 'Non mi hanno convinto, troppo rigide per i miei gusti.', '2025-01-14');