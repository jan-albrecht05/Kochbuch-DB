# Kochbuch-DB

[![MIT License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)<br>
![Made with PHP](https://img.shields.io/badge/PHP-8.x-blue)
![SQLite](https://img.shields.io/badge/Database-SQLite-lightgrey)
![CSS](https://img.shields.io/badge/Style-CSS-blueviolet)
![JavaScript](https://img.shields.io/badge/Frontend-JavaScript-yellow)
![CSS](https://img.shields.io/badge/Frontend-HTML-orange)

> Ein webbasiertes Tool zur Organisation und Verwaltung von Kochrezepten mit Benutzerkonten und Rechtesystem.

---
<br><br>

# SEITEN
## 🎯 index.php
 - ✅ Header w/ Links to all tags
 - ✅ 3 different sections: random, latest, favorites
 - ❌ ?login status + popup
 - ❌ arrows for horizontal scrolling
 - ❌ fill favorites section
 ### random section
 - ✅ shows 6 random entries
 ```php
 SELECT * FROM gerichte ORDER BY RANDOM() LIMIT 6
 ```
 ### latest section
 - ✅ shows the 6 latest entries
 ```php
 SELECT * FROM gerichte ORDER BY id DESC LIMIT 6
 ```

## 🌐 suche.php
 - ✅ showing search results
 - ✅ showing filter results (+ Filter: random, latest, saved)
 - ❌ PHP paging (25, 50, 100 Gerichte)
 - ✅ sort by A-Z, Z-A
 - ✅ advanced search (search in ingredients too)
 - ✅ filter nach user

## 💥 gericht.php
 - ✅ connect to gerichte.db
 - ✅ container w/ different images
 - ✅ logic for switching images 
 - ✅ steps
 - ❌ error if recepies does not exist
 - ❌ show/hide edit-button
 - ❌ save-button-logik
 - ✅ share-link
 - ✅ Banner for action "saved" and "link copied" 
 - ✅ metadaten mit PHP anpassen
 - ❌ star input-logik + cooldown
 - ✅ star output
 - ✅ link to user
 - ❌ "Fehler melden" Popup mit textarea → cell in gerichte.db
 - ❌ calculation for ingredients
 - ❌ add ingredients to einkaufsliste.php

## ➕ rezept-erstellen.php
 - ✅ input-form
 - ✅ connect so SQL
 - ✅ Image-Handling+Upload
 - ❌ fetch error messages
 - ✅ set user id if user is logged in
 - ✅ show banner if user is not logged in

## ▶ login.php
 - ✅ login-form
 - ✅ users.db
 - ❌ Account beantragen

## ◀ logout.php
 - ✅ logout logic

## 🚹 benutzer.php
 - ❌ "meine Rezepte"
 - ❌ "meine Einkaufsliste"
 - ❌ gespeicherte Rezepte
 - ✅ users.db

## admin-panel.php
 - ✅ Statistics (user, recepies, errors)
 - ✅ Benutzer anzeigen
 - ✅ Funktion zum hinzufügen/bearbeiten/löschen von usern
 - ✅ offene Rezepte anzeigen
 - ✅ Rezepte mit Fehlern anzeigen
 - ✅ Links zu rezept-bearbeiten.php
 - ❌ Logik status zu 0 setzen
 - ❌ Rezept löschen (maybe auch Bilder)

## rezept-bearbeiten.php
 - ✅ basic functionality
 - ✅ TAG buttons bearbeiten
 - ✅ Mengen setzen
 - ✅ tabellen entsprechend bearbieten (alte Einträge komplett löschen?)
 - ✅ Datum setzen
 - ✅ logic for saving changes in the additional tables

## einkaufsliste.php
 - ❌ needs everything
 - ❌ shops hinzufügen (nicht mandatory)

## feedback-form.php
 - ❌ needs everything
 - ❌ extra db