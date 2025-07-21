CREATE TABLE IF NOT EXISTS "users" (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT,
    "name" TEXT, 
    "password" TEXT,
    "profilbild" VARCHAR(25),
    "rolle" VARCHAR(25),
    "badge" TEXT,
    "saved_recepies" TEXT
);

CREATE TABLE IF NOT EXISTS "einkaufsliste" (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT,
    "user_id" INTEGER,
    "menge" INTEGER,
    "einheit" VARCHAR(5),
    "zutat" TEXT,
    "status" INTEGER,
    FOREIGN KEY ("user_id") REFERENCES "users"("id") ON DELETE CASCADE
);