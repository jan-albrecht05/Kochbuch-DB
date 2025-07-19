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
 - ❌ arrows for horizontal scrolling
 - ❕ different order/sorting 
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
 - ❌ showing search results
 - ❌ showing filter results (+ Filter: random, latest, saved)
 - ❌ PHP paging (25, 50, 100 Gerichte)
 - ❌ sort by A-Z, Z-A

## 💥 gericht.php
 - ✅ connect to gerichte.db
 - ✅ container w/ different images
 - ✅ logic for switching images 
 - ✅ steps
 - ❌ calculation for ingredients
 - ❌ save-button-logik
 - ✅ share-link
 - ❌ Banner for action "saved" and "link copied" 
 - ❌ metadaten mit PHP anpassen
 - ❌ star input-logik + cooldown
 - ✅ star output
 - ❌ link to user
 - ❌ "Fehler melden" Popup mit textarea → cell in gerichte.db
 - ❌ add ingredients to einkaufsliste.php

## ➕ rezept-erstellen.php
 - ✅ input-form
 - ✅ connect so SQL
 - ✅ Image-Handling+Upload
 - ❌fetch error messages

## ▶ login.php
 - ❌ login-form
 - ❌ users.db

## ◀ logout.php
 - ❌ logout logic

## 🚹 benutzer.php
 - ❌ "meine Rezepte"
 - ❌ "meine Einkaufsliste"
 - ❌ gespeicherte Rezepte
 - ❌ users.db

## einkaufsliste.php
 - ❌ needs everything
