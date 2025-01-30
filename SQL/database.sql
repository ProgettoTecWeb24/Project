-- Eliminazione delle tabelle esistenti
DROP TABLE IF EXISTS LIKES;
DROP TABLE IF EXISTS RECENSIONE;
DROP TABLE IF EXISTS UTENTE;
DROP TABLE IF EXISTS SCARPA;

-- Creazione delle tabelle
CREATE TABLE SCARPA (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    marca VARCHAR(100) NOT NULL,
    tipo ENUM('strada', 'trail', 'pista') NOT NULL,
    descrizione TEXT,
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
    FOREIGN KEY (username) REFERENCES UTENTE(username) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (scarpa_id) REFERENCES SCARPA(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE LIKES (
    username VARCHAR(100),
    scarpa_id INT,
    data_aggiunta DATE,
    PRIMARY KEY (username, scarpa_id),
    FOREIGN KEY (username) REFERENCES UTENTE(username) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (scarpa_id) REFERENCES SCARPA(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Dati per SCARPA
INSERT INTO SCARPA (nome, marca, tipo, descrizione, dettagli, votoexp, feedback, immagine, data_aggiunta)
VALUES 
('Pegasus Trail', 'Nike', 'trail', 'Scarpa versatile per trail running.', 'Ideale per terreni misti e sentieri leggeri.', 3, 'Comoda e affidabile', 'pegasus_trail.webp', '2025-01-10'),
('UltraBoost Light', 'Adidas', 'strada', 'Scarpa con un’ammortizzazione avanzata.', 'Perfetta per lunghe sessioni su asfalto.', 5, 'Ottima per lunghe distanze', 'ultraboost_light.webp', '2025-01-12'),
('Gel-Kayano', 'Asics', 'strada', 'Scarpa progettata per stabilità e supporto.', 'Indicate per runner con lieve pronazione.', 4, 'Supporto eccellente', 'gel_kayano.webp', '2025-01-15'),
('Speedgoat', 'Hoka', 'trail', 'Scarpa robusta per terreni tecnici.', 'Aderenza straordinaria su superfici scivolose.', 5, 'Aderenza incredibile', 'speedgoat.webp', '2025-01-17'),
('Zoom Fly', 'Nike', 'pista', 'Scarpa leggera e reattiva.', 'Ideale per sessioni veloci su pista.', 3, 'Ottima per principianti', 'zoom_fly.webp', '2025-01-20'),
('Lone Peak', 'Altra', 'trail', 'Perfetta per escursioni lunghe.', 'Vestibilità ampia per massima comodità.', 5, 'Ampio spazio per le dita', 'lone_peak.webp', '2025-01-22'),
('Metaspeed Sky+', 'Asics', 'strada', 'Scarpa pensata per massimizzare la velocità.', 'Perfetta per gare competitive.', 5, 'Ideale per record personali', 'metaspeed_sky_plus.webp', '2025-01-25'),
('Ghost', 'Brooks', 'strada', 'Scarpa universale per corse quotidiane.', 'Ammortizzazione bilanciata.', 4, 'Ottima per runner intermedi', 'ghost.webp', '2025-01-28'),
('Endorphin Speed', 'Saucony', 'trail', 'Scarpa versatile e performante.', 'Perfetta per sessioni di allenamento intense.', 5, 'Reattiva e performante', 'endorphin_speed.webp', '2025-01-30'),
('Wave Rider', 'Mizuno', 'strada', 'Scarpa ammortizzata per lunghe distanze.', 'Tecnologia Wave per maggiore stabilità.', 4, 'Perfetta per runner quotidiani', 'wave_rider.webp', '2025-02-01'),
('Fresh Foam 1080', 'New Balance', 'strada', 'Comfort eccezionale per ogni tipo di corsa.', 'Ammortizzazione soffice.', 3, 'Comoda e versatile', '1080.webp', '2025-02-03'),
('Vaporfly Next', 'Nike', 'pista', 'Scarpa ultra-leggera per competizioni.', 'Reattiva e performante.', 5, 'Top di gamma', 'vaporfly_next.webp', '2025-02-05'),
('Adizero Adios Pro', 'Adidas', 'strada', 'Scarpa da gara per massima velocità.', 'Design ottimizzato per l’efficienza.', 5, 'Massima performance', 'adios_pro.webp', '2025-02-07'),
('Novablast', 'Asics', 'strada', 'Ammortizzazione energica per allenamenti intensi.', 'Design moderno e leggero.', 4, 'Super rimbalzo', 'novablast.webp', '2025-02-09'),
('Clifton', 'Hoka', 'pista', 'Scarpa morbida e ammortizzata.', 'Perfetta per lunghe corse.', 5, 'Comfort incredibile', 'clifton.webp', '2025-02-11'),
('Rincon', 'Hoka', 'strada', 'Scarpa estremamente leggera.', 'Ideale per corse veloci.', 4, 'Design minimalista', 'rincon.webp', '2025-02-13'),
('Mach', 'Hoka', 'trail', 'Reattività superiore per sentieri tecnici.', 'Perfetta per trail running avanzato.', 5, 'Grande velocità', 'mach.webp', '2025-02-15'),
('Terrex Agravic', 'Adidas', 'trail', 'Scarpa ultra-durevole per montagna.', 'Ideale per percorsi lunghi.', 3, 'Molto resistenti', 'terrex_agravic.webp', '2025-02-17'),
('Rebel', 'New Balance', 'strada', 'Scarpa veloce per allenamenti.', 'Grande flessibilità e comfort.', 5, 'Perfetta per allenamenti brevi', 'rebel.webp', '2025-02-19'),
('Invincible Run', 'Nike', 'strada', 'Scarpa ammortizzata per chilometraggi elevati.', 'Ideale per runner pesanti.', 5, 'Massimo supporto', 'invincible_run.webp', '2025-02-21'),
('Solar Glide', 'Adidas', 'strada', 'Scarpa equilibrata per ogni corsa.', 'Adatta a tutte le distanze.', 4, 'Ottima versatilità', 'solar_glide.webp', '2025-02-23');

-- Dati per UTENTE
INSERT INTO UTENTE (username, pw, ruolo, admin) VALUES
('user', '$2y$10$/BjuFjBdobvdAOqIZFHGO.aa46YkgmIb3A/YyIIA94is2Inq7cUgm', 'tartaruga', FALSE),
('admin', '$2y$10$b154faL4nCt/0GTE/svh4.oCiawsVaBrkQ2uu6VT6Bm/37oPROi5i', 'ghepardo', TRUE),
('giulia', '$2y$10$GiuliaPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'lepre', FALSE),
('marco', '$2y$10$MarcoPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'lupo', FALSE),
('sara', '$2y$10$SaraPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'tartaruga', FALSE),
('luca', '$2y$10$LucaPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'ghepardo', FALSE),
('alessia', '$2y$10$AlessiaPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'lepre', FALSE),
('andrea', '$2y$10$AndreaPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'lupo', FALSE),
('chiara', '$2y$10$ChiaraPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'tartaruga', FALSE),
('federico', '$2y$10$FedericoPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'ghepardo', FALSE),
('giovanni', '$2y$10$GiovanniPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'lupo', FALSE),
('valeria', '$2y$10$ValeriaPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'lepre', FALSE),
('paolo', '$2y$10$PaoloPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'tartaruga', FALSE),
('marta', '$2y$10$MartaPass.aa46YkgmIb3A/YyIIA94is2Inq7c', 'ghepardo', FALSE);

-- Dati per RECENSIONE
INSERT INTO RECENSIONE (username, scarpa_id, voto, commento, data_aggiunta) VALUES
('user', 1, 5, 'Scarpe fantastiche per sentieri.', '2025-01-10'),
('giulia', 1, 4, 'Ottime ma un po rigide.', '2025-01-21'),
('marco', 2, 5, 'Perfette per la maratona.', '2025-01-12'),
('sara', 2, 4, 'Comode ma poco traspiranti.', '2025-01-13'),
('luca', 3, 3, 'Buona stabilità ma poco ammortizzate.', '2025-02-14'),
('admin', 3, 4, 'Supporto eccellente.', '2025-01-15'),
('chiara', 4, 5, 'Perfette per terreni accidentati.', '2025-01-26'),
('andrea', 4, 4, 'Resistenti e affidabili.', '2025-01-17'),
('valeria', 5, 5, 'Scarpe reattive e veloci.', '2025-02-18'),
('paolo', 5, 4, 'Buone ma costose.', '2025-01-19'),
('federico', 6, 5, 'Scarpe ottime per lunghe escursioni.', '2025-01-22'),
('giovanni', 7, 5, 'Perfette per gare competitive.', '2025-01-25'),
('marta', 8, 4, 'Confortevoli per ogni tipo di corsa.', '2025-01-28'),
('andrea', 9, 5, 'Grande reattività per allenamenti intensi.', '2025-01-30'),
('luca', 10, 4, 'Buon compromesso tra comfort e stabilità.', '2025-02-01'),
('chiara', 11, 2, 'Troppo rigide per lunghe distanze.', '2025-02-02'),
('valeria', 12, 3, 'La suola si consuma rapidamente.', '2025-02-16'),
('marta', 13, 1, 'Esperienza molto deludente.', '2025-02-08'),
('paolo', 14, 2, 'Scarpa poco durevole.', '2025-02-10'),
('giulia', 15, 3, 'Design bello ma non comode.', '2025-02-12'),
('marco', 16, 2, 'Scarpe troppo strette.', '2025-02-24'),
('sara', 15, 3, 'Non valgono il prezzo.', '2025-02-16'),
('luca', 18, 1, 'Qualità deludente.', '2025-02-18'),
('andrea', 19, 2, 'Non adatte per sessioni lunghe.', '2025-02-20'),
('federico', 15, 3, 'Nella media, nulla di speciale.', '2025-02-22'),
('giovanni', 2, 2, 'Poca ammortizzazione.', '2025-02-24'),
('marta', 12, 1, 'Materiali scadenti.', '2025-02-16');
