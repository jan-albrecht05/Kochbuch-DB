CREATE TABLE gerichte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titel VARCHAR(255) NOT NULL,
    beschreibung TEXT,
    timecode_erstellt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    timecode_geaendert TIMESTAMP,
    tags TEXT,
    vorbereitungszeit INT,
    zubereitungszeit INT,
    bild1 VARCHAR(50),
    bild2 VARCHAR(50),
    bild3 VARCHAR(50),
    personen INT,
    rate1star INT DEFAULT 0,
    rate2star INT DEFAULT 0,
    rate3star INT DEFAULT 0,
    rate4star INT DEFAULT 0,
    rate5star INT DEFAULT 0,
    made_by_id INT,
    viewcount INT DEFAULT 0,
    error_msg TEXT
);

CREATE TABLE zutaten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gerichte_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    menge VARCHAR(50),
    einheit VARCHAR(50),
    FOREIGN KEY (gerichte_id) REFERENCES gerichte(id) ON DELETE CASCADE
);

CREATE TABLE schritte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gerichte_id INT NOT NULL,
    schritt TEXT NOT NULL,
    FOREIGN KEY (gerichte_id) REFERENCES gerichte(id) ON DELETE CASCADE
);