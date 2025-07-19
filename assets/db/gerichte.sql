BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS "gerichte" (
	"id"	INTEGER,
	"titel"	TEXT NOT NULL,
	"beschreibung"	TEXT,
	"timecode_erstellt"	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	"timecode_geaendert"	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	"tags"	TEXT,
	"vorbereitungszeit"	INTEGER,
	"zubereitungszeit"	INTEGER,
	"bild1"	TEXT,
	"bild2"	TEXT,
	"bild3"	TEXT,
	"personen"	INTEGER,
	"star1"	INTEGER DEFAULT 0,
	"star2"	INTEGER DEFAULT 0,
	"star3"	INTEGER DEFAULT 0,
	"star4"	INTEGER DEFAULT 0,
	"star5"	INTEGER DEFAULT 0,
	"made_by_id"	INTEGER,
	"viewcount"	INTEGER DEFAULT 0,
	"error_msg"	TEXT,
	"status"	INTEGER DEFAULT 0,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "schritte" (
	"id"	INTEGER,
	"gerichte_id"	INTEGER NOT NULL,
	"schritt"	TEXT NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT),
	FOREIGN KEY("gerichte_id") REFERENCES "gerichte"("id") ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS "zutaten" (
	"id"	INTEGER,
	"gerichte_id"	INTEGER NOT NULL,
	"name"	TEXT NOT NULL,
	"menge"	TEXT,
	"einheit"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT),
	FOREIGN KEY("gerichte_id") REFERENCES "gerichte"("id") ON DELETE CASCADE
);
INSERT INTO "gerichte" VALUES (1,'Nudelsalat mit Mandarinen','Omas hausgemachter Nudelsalat mit einer besonderen Zutat!','2025-07-18 18:45:00','2025-07-18 18:45:00','Nudeln,Fleisch,Hühnchen',3,20,'1.1.png',NULL,NULL,6,0,0,0,0,0,NULL,0,NULL,0);
INSERT INTO "gerichte" VALUES (2,'MarGin Cocktail','Ein leckerer alkoholfreier Cocktail - perfekt für den Sommer!','2025-07-18 18:50:42','2025-07-18 18:50:42','Vegan,vegetarisch,Getränk',2,2,'2.1.jpg',NULL,NULL,1,0,0,0,0,0,NULL,0,NULL,0);
INSERT INTO "gerichte" VALUES (3,'Käsekuchen (ohne Boden)','der Beste und einfachste Käsekuchen!','2025-07-18 19:13:15','2025-07-18 19:13:15','Dessert,Kuchen,vegetarisch',3,70,'3.1.png',NULL,NULL,7,0,0,0,0,0,NULL,0,NULL,0);
INSERT INTO "gerichte" VALUES (4,'Thaicurry mit Kokosmilch','Das beste aus Thailand - Thaicurry mit Kokosmilch jetzt für Zuhause!','2025-07-18 19:21:19','2025-07-18 19:21:19','Asiatisch,Reis,Fleisch,Hühnchen,Suppe,vegetarisch',45,35,'4.1.png',NULL,NULL,6,0,0,0,0,0,NULL,0,NULL,0);
INSERT INTO "schritte" VALUES (1,1,'Nudeln nach Packungsanleitung kochen. Nüsse ohne Fett rösten. Fleisch waschen, trocknen tupfen, salzen, pfeffern, in einer Pfanne in heißem Öl braten, in Folie beiseite stellen. Frühlingszwiebeln waschen, in Ringe schneiden. Mandarinen abtropfen lassen, den Saft dabei auffangen.');
INSERT INTO "schritte" VALUES (2,1,'Crème fraîche mit Mandarinensaft, restlichen Zutaten, Salz und Pfeffer verrühren. Zur Hälfte mit den Nudeln vermengen, auf Teller verteilen. Fleisch in Scheiben schneiden, mit Frühlingszwiebeln, Mandarinen, Nüssen darauf anrichten, Rest Dressing darüberträufeln.');
INSERT INTO "schritte" VALUES (3,2,'Limette in Scheiben schneiden, im Glas quetschen.
Zucker hinzufügen.');
INSERT INTO "schritte" VALUES (4,2,'Glas zu etwa 60 Prozent mit Ginger Ale füllen, Rest mit Maracujasaft und Eis auffüllen.
Limettenscheibe einschneiden und damit das Glas dekorieren.');
INSERT INTO "schritte" VALUES (5,3,'Backofen auf 180 Grad Ober-/Unterhitze vorheizen. Den Boden einer auslaufsicheren Springform (20 cm) mit Backpapier auslegen.');
INSERT INTO "schritte" VALUES (6,3,'Eier, Vanille Paste, Salz und Zucker mit einem Schneebesen verrühren, nicht aufschlagen.');
INSERT INTO "schritte" VALUES (7,3,'Mehl und Puddingpulver dazugeben und verrühren. Dann Quark unterrühren. ');
INSERT INTO "schritte" VALUES (8,3,'Zitronensaft dazugeben und verrühren.');
INSERT INTO "schritte" VALUES (9,3,'Milch und Sahne dazugeben und zu einer glatten Masse rühren.');
INSERT INTO "schritte" VALUES (10,3,'Käsemasse in die Form füllen und im vorgeheizten Backofen etwa 55 Minuten goldbraun backen. Nach 30 Minuten mit einem scharfen Messer am Rand des Käsekuchens entlang fahren, damit der Käsekuchen nicht einreißt.');
INSERT INTO "schritte" VALUES (11,3,'Sobald der Käsekuchen goldbraun ist, den Backofen ausschalten und bei leicht geöffneter Backofentür auskühlen lassen.');
INSERT INTO "schritte" VALUES (12,4,'Die Süßkartoffel schälen und in gleichmäßige Würfel schneiden. Knoblauch, Zwiebeln und Ingwer schälen und in feine Würfel schneiden.');
INSERT INTO "schritte" VALUES (13,4,'Öl in einem Topf erhitzen und alles zusammen gleichmäßig anschwitzen. Braunen Zucker und Currypulver dazugeben und in heissem Öl etwas Farbe nehmen lassen.');
INSERT INTO "schritte" VALUES (14,4,'Mit Geflügelbrühe und Kokosmilch aufgießen und bei schwacher Hitze ca. 10 Minuten köcheln lassen. Währenddessen die Hähnchenbrust fein würfeln und in einer Pfanne mit wenig Öl bei mittlerer Hitze von allen Seiten leicht anbraten.');
INSERT INTO "schritte" VALUES (15,4,'Chilischoten und Zuckerschoten in feine Streifen schneiden und zusammen mit der Hähnchenbrust und den Limettenblättern in den Topf geben. Weitere 10 Minuten köcheln lassen. Dabei gelegendlich umrühren.');
INSERT INTO "schritte" VALUES (16,4,'Anschließend mit mit Salz, Pfeffer und Limettensaft abschmecken.');
INSERT INTO "zutaten" VALUES (1,1,'Nudeln','250','g');
INSERT INTO "zutaten" VALUES (2,1,'Cashewkerne','100','g');
INSERT INTO "zutaten" VALUES (3,1,'Hähnchenbrustfilet','400g','g');
INSERT INTO "zutaten" VALUES (4,1,'Öl','2','EL');
INSERT INTO "zutaten" VALUES (5,1,'Bund Frühlingszwiebeln','1','Stl.');
INSERT INTO "zutaten" VALUES (6,1,'Dose Mandarinen','1','Stl.');
INSERT INTO "zutaten" VALUES (7,1,'Crème fraîche','300','g');
INSERT INTO "zutaten" VALUES (8,1,'Zitrone + Abrieb','3','EL');
INSERT INTO "zutaten" VALUES (9,1,'Senf (mittelscharf)','1','EL');
INSERT INTO "zutaten" VALUES (10,1,'flüssiger Honig','1','EL');
INSERT INTO "zutaten" VALUES (11,1,'Curry (gestreift)','1','TL');
INSERT INTO "zutaten" VALUES (12,2,'brauner Zucker','1','TL');
INSERT INTO "zutaten" VALUES (13,2,'Limette','1','Stl.');
INSERT INTO "zutaten" VALUES (14,2,'Ginger Ale','60','%');
INSERT INTO "zutaten" VALUES (15,2,'Maracujasaft','40','%');
INSERT INTO "zutaten" VALUES (16,2,'Eiswürfel','2','Stl.');
INSERT INTO "zutaten" VALUES (17,3,'Eier','3','Stk.');
INSERT INTO "zutaten" VALUES (18,3,'Zucker','210','g');
INSERT INTO "zutaten" VALUES (19,3,'Speisequark','1','kg');
INSERT INTO "zutaten" VALUES (20,3,'Mehl','50','g');
INSERT INTO "zutaten" VALUES (21,3,'Packung Vanillepuddingpulver','1','Stl.');
INSERT INTO "zutaten" VALUES (22,3,'Vanille Paste','1','TL');
INSERT INTO "zutaten" VALUES (23,3,'Saft einer Zitrone','50','%');
INSERT INTO "zutaten" VALUES (24,3,'Prise Salz','1','Stl.');
INSERT INTO "zutaten" VALUES (25,3,'Milch','200','ml');
INSERT INTO "zutaten" VALUES (26,3,'Sahne','125','g');
INSERT INTO "zutaten" VALUES (27,4,'Hähnchenbrust','400','g');
INSERT INTO "zutaten" VALUES (28,4,'Süßkartoffeln','200','g');
INSERT INTO "zutaten" VALUES (29,4,'frischer Ingwer','20','g');
INSERT INTO "zutaten" VALUES (30,4,'Knoblauchzehe','1','Stl.');
INSERT INTO "zutaten" VALUES (31,4,'Zwiebeln','2','Stl.');
INSERT INTO "zutaten" VALUES (32,4,'rote Chilischoten','2','Stl.');
INSERT INTO "zutaten" VALUES (33,4,'Öl','3','EL');
INSERT INTO "zutaten" VALUES (34,4,'Currypulver','2','EL');
INSERT INTO "zutaten" VALUES (35,4,'Geflügelbrühe','400','ml');
INSERT INTO "zutaten" VALUES (36,4,'Kokosmilch','400','ml');
INSERT INTO "zutaten" VALUES (37,4,'Zuckerschoten','150','g');
INSERT INTO "zutaten" VALUES (38,4,'Limettenblätter','3','Stl.');
INSERT INTO "zutaten" VALUES (39,4,'brauner Zucker','2','EL');
INSERT INTO "zutaten" VALUES (40,4,'Basmatireis','250','g');
COMMIT;
