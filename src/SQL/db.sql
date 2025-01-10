DROP TABLE IF EXISTS LIKES;
DROP TABLE IF EXISTS RECENSIONE;
DROP TABLE IF EXISTS UTENTE;
DROP TABLE IF EXISTS SCARPA;

CREATE TABLE SCARPA (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    marca VARCHAR(100) NOT NULL,
    descrizione TEXT,
    immagine VARCHAR(255) NOT NULL,
    tipo ENUM('strada', 'trail', 'pista') NOT NULL,
    data_aggiunta DATE,
    feedback TEXT,
    dettagli TEXT
);


CREATE TABLE UTENTE (
    username VARCHAR(100) PRIMARY KEY,
    email VARCHAR(255),
    pw CHAR(255),
    km INT CHECK (km >= 0),
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


INSERT INTO scarpa (id, nome, marca, descrizione, immagine, tipo, data_aggiunta, feedback)
VALUES 
(4, 'Pegasus Trail 4', 'Nike', 'Scarpa versatile per trail running.', 'pegasus_trail_4.jpg', 'trail', '2025-01-10', 'Comoda e affidabile'),
(5, 'UltraBoost Light', 'Adidas', 'Scarpa con un’ammortizzazione avanzata.', 'ultraboost_light.jpg', 'strada', '2025-01-12', 'Ottima per lunghe distanze'),
(6, 'Gel-Kayano 30', 'Asics', 'Perfetta per chi cerca stabilità.', 'gel_kayano_30.jpg', 'strada', '2025-01-15', 'Supporto eccellente'),
(7, 'Hoka Speedgoat 5', 'Hoka', 'Ideale per terreni accidentati.', 'speedgoat_5.jpg', 'trail', '2025-01-17', 'Aderenza incredibile'),
(8, 'Zoom Fly 5', 'Nike', 'Scarpa veloce per gare su strada.', 'zoom_fly_5.jpg', 'strada', '2025-01-20', 'Leggera e reattiva'),
(9, 'Altra Lone Peak 7', 'Altra', 'Perfetta per lunghe escursioni trail.', 'lone_peak_7.jpg', 'trail', '2025-01-22', 'Ampio spazio per le dita'),
(10, 'Metaspeed Sky+', 'Asics', 'Pensata per migliorare la velocità.', 'metaspeed_sky_plus.jpg', 'pista', '2025-01-25', 'Ideale per record personali');


INSERT INTO `utente` (`username`, `email`, `pw`, `km`, `admin`) VALUES
('user', NULL, '$2y$10$/BjuFjBdobvdAOqIZFHGO.aa46YkgmIb3A/YyIIA94is2Inq7cUgm', 0, 0),
('admin', NULL, '$2y$10$b154faL4nCt/0GTE/svh4.oCiawsVaBrkQ2uu6VT6Bm/37oPROi5i', 0, 1);