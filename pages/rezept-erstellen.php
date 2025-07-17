<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezept erstellen - Kochbuch</title>
    <link rel="icon" href="../assets/icons/Topficon.png">
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/rezept-erstellen.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script src="../assets/js/ingredients.js"></script>
    <script src="../assets/js/steps.js"></script>
    <script src="../assets/js/tags.js"></script>
</head>
<body>
    <div id="main">
        <h1>Rezept erstellen</h1>
        <form method="post" enctype="multipart/form-data">
        <!--Titel-->
            <label class="heading" for="titel">Titel:</label><br>
            <input type="text" id="titel" name="titel" required placeholder="Wie heißt dein Gericht?"><br>
            <!--Bilder-->
            <h2>Bilder hinzufügen:</h2><br>
            <div id="images">
                <label for="img1" class="img-drop center"><span class="material-symbols-outlined">image_arrow_up</span></label>
                <input type="file" id="img1" name="img1" required>
                <label for="img2" class="img-drop center"><span class="material-symbols-outlined">image_arrow_up</span></label>
                <input type="file" id="img2" name="img2">
                <label for="img3" class="img-drop center"><span class="material-symbols-outlined">image_arrow_up</span></label>
                <input type="file" id="img3" name="img3">
            </div>
            <span class="info">Du kannst bis zu 3 Bilder hinzufügen. Das erste Bild wird als Hauptbild verwendet.</span><br>
            <span class="info">Die Bilder sollten im JPG oder PNG Format vorliegen.</span><br>
            <!--Kurzbeschreibung-->
            <label class="heading" for="kbeschreibung">Kurzbeschreibung:</label><br>
            <textarea id="kbeschreibung" name="kbeschreibung" rows="3" required placeholder="Beschreibe es kurz, damit andere wissen, worum es geht."></textarea><br>
            <!--Tags-->
            <h2>Kategorien:</h2>
            <h3>Bitte wähle <u>alle</u> passenden Themenbereiche aus.</h4>
            <div id="tag-container">
                <label class="tag" for="Asiatisch">🥡 Asiatisch</label>
                <input type="checkbox" id="Asiatisch" name="tags[]" value="Asiatisch">
                <label class="tag" for="Nudeln">🍝 Nudeln</label>
                <input type="checkbox" id="Nudeln" name="tags[]" value="Nudeln">
                <label class="tag" for="Kartoffeln">🥔 Kartoffeln</label>
                <input type="checkbox" id="Kartoffeln" name="tags[]" value="Kartoffeln">
                <label class="tag" for="Reis">🍚 Reis</label>
                <input type="checkbox" id="Reis" name="tags[]" value="Reis">
                <label class="tag" for="Fleisch">🥩 Fleisch</label>
                <input type="checkbox" id="Fleisch" name="tags[]" value="Fleisch">
                <label class="tag" for="Hühnchen">🍗 Hühnchen</label>
                <input type="checkbox" id="Hühnchen" name="tags[]" value="Hühnchen">
                <label class="tag" for="Schwein">🍖Schwein</label>
                <input type="checkbox" id="Schwein" name="tags[]" value="Schwein">
                <label class="tag" for="Rind">🐮 Rind</label>
                <input type="checkbox" id="Rind" name="tags[]" value="Rind">
                <label class="tag" for="Fisch">🐟 Fisch</label>
                <input type="checkbox" id="Fisch" name="tags[]" value="Fisch">
                <label class="tag" for="Suppe">🥣 Suppe</label>
                <input type="checkbox" id="Suppe" name="tags[]" value="Suppe">
                <label class="tag" for="Soße">🍲 Soße</label>
                <input type="checkbox" id="Soße" name="tags[]" value="Soße">
                <label class="tag" for="Dessert">🍮 Dessert</label>
                <input type="checkbox" id="Dessert" name="tags[]" value="Dessert">
                <label class="tag" for="Kuchen">🥧 Kuchen</label>
                <input type="checkbox" id="Kuchen" name="tags[]" value="Kuchen">
                <label class="tag" for="Vegan">🌿 Vegan</label>
                <input type="checkbox" id="Vegan" name="tags[]" value="Vegan">
                <label class="tag" for="vegetarisch">🥗 Vegetarisch</label>
                <input type="checkbox" id="vegetarisch" name="tags[]" value="vegetarisch">
                <label class="tag" for="Getränk">🍹 Getränk</label>
                <input type="checkbox" id="Getränk" name="tags[]" value="Getränk">
            </div>
            <!--Zutaten-->
            <h2>Zutaten:</h2><br>
            <div id="ingredients">
                <div class="ingredient">
                    <input class="menge" type="text" id="ingredient${ingredientCount}" name="ingredient${ingredientCount}" required placeholder="Menge">
                    <input class="zutat" type="text" id="ingredient${ingredientCount}" name="ingredient${ingredientCount}" required placeholder="Zutat">
                </div>
            </div>
            <button type="button" id="add-Ingredient" class="center" onclick="addInged()"><span class="material-symbols-outlined center">add</span><span>Weitere Zutaten hinzufügen</span></button>
            <br>
            <h3>Für wie viele Personen sind die Mengen angegeben?</h3>
            <select name="portionen" id="portionen" required>
                <option value="" disabled selected>Bitte wählen</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">mehr als 6</option>
            </select><br><br>
            <h3>Wie lange dauert die Vorbereitung?</h3>
            <span>Gib die Zeit in Minuten an.</span><br>
            <input type="number" id="vorbereitungszeit" name="vorbereitungszeit" required placeholder="25"><br>

            <!--Zubereitung-->
            <h2>Zubereitung:</h2>
            <div id="steps">
                <div class="step" id="step1">
                    <label class="schritt" for="schritt1">1.</label>
                    <textarea id="schritt1" name="schritt1" rows="2" required placeholder="Teile die Zubereitung in Sinnvolle Schritte ein."></textarea>
                </div>
            </div>
            <button type="button" id="add-step" class="center" onclick="addStep()">
                <span class="material-symbols-outlined center">add</span>
                <span>Weitere Schritte hinzufügen</span>
            </button><br><br>
            <h3>Wie lange dauert die reine Zubereitung?</h3>
            <span>Gib die Zeit in Minuten an.</span><br>
            <input type="number" id="zubereitungszeit" name="zubereitungszeit" required placeholder="25"><br>
            <!--Hidden inputs-->
            <input type="hidden" id="timecode" name="timecode" value="<?php echo time(); ?>">
            <input type="hidden" id="userid" name="userid" value="<?php echo $_SESSION['user_id']; ?>">
            <input type="hidden" id="ID" name="ID">
            <div id="buttons">
                <button type="reset" id="abbrechen">Abbrechen</button>
                <button type="submit" id="speichern">Rezept speichern</button>
            </div>
        </form>
    </div>
</body>
</html>