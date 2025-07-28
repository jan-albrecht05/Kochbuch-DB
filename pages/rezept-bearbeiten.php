<!DOCTYPE html>
<html lang="de">
<head>
    <?php
        session_start();
        if(file_exists("../assets/db/gerichte.db")){
            $db = new SQlite3("../assets/db/gerichte.db");
            
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if ($id > 0) {
                // Rezept holen
                $stmt = $db->prepare("SELECT * FROM gerichte WHERE id = :id");
                $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
                $result = $stmt->execute();
                $row = $result->fetchArray(SQLITE3_ASSOC);
                $username = $row["made_by_id"];

                // Zutaten holen
                $zutatenStmt = $db->prepare("SELECT * FROM zutaten WHERE gerichte_id = :id");
                $zutatenStmt->bindValue(':id', $id, SQLITE3_INTEGER);
                $zutatenResult = $zutatenStmt->execute();

                // schritte holen
                $schrittStmt = $db->prepare("SELECT * FROM schritte WHERE gerichte_id = :id");
                $schrittStmt->bindValue(':id', $id, SQLITE3_INTEGER);
                $schrittResult = $schrittStmt->execute();

            } else {
            // Invalid or missing id, show error or redirect
                die("Ungültige Rezept-ID.");
            }
        }
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="Rezept bearbeiten - Kochbuch" />
    <meta property="og:description" content="Ein digitales Kochbuch, in das jeder seine Lieblingsrezepte hinzufügen kann." />
    <meta property="og:image" content="../assets/icons/Topficon.png" />
    <meta property="og:url" content="https://mein-kochbuch-free.nf" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Rezept bearbeiten - Kochbuch" />
    <title>Rezept bearbeiten - Kochbuch</title>
    <link rel="icon" href="../assets/icons/Topficon.png">
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/heading.css">
    <link rel="stylesheet" href="../assets/css/rezept-erstellen.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script src="../assets/js/ingredients.js"></script>
    <script src="../assets/js/steps.js"></script>
    <script src="../assets/js/tags.js"></script>
    <script src="../assets/js/popups.js"></script>
    <script src="../assets/js/heading.js" defer></script>
</head>
<body>
    <div id="heading">
        <!-- Code gets injected by heading.js -->
    </div>
    <div class="popup negative center" id="link_copied">
        <span class="material-symbols-outlined">report</span>
        Datei muss .PNG oder .JPG sein!
    </div>
    <?php
    //if User is not logged in
        if (!isset($_SESSION['rolle'])) {
            //header: 'index.php';
        }
    ?>
    <div id="main">
        <h1>Rezept bearbeiten</h1>
        <form method="post" enctype="multipart/form-data">
        <!--Titel-->
            <label class="heading" for="titel">Titel:</label><br>
            <input type="text" id="titel" name="titel" required placeholder="Wie heißt dein Gericht?" value="<?php echo htmlspecialchars($row['titel'])?>"><br>
            <!--Bilder-->
            <h2>Bilder hinzufügen:</h2><br>
            <div id="images">
                <label for="img1" class="img-drop center" id="label-img1" style="background-image:url('../assets/img/uploads/gerichte/<?php echo htmlspecialchars($row['bild1'])?>')"><span class="material-symbols-outlined">image_arrow_up</span></label>
                <input type="file" accept="image/png, image/jpeg" id="img1" name="img1" required>
                <label for="img2" class="img-drop center" id="label-img2" style="background-image:url('../assets/img/uploads/gerichte/<?php echo htmlspecialchars($row['bild2'])?>')"><span class="material-symbols-outlined">image_arrow_up</span></label>
                <input type="file" accept="image/png, image/jpeg" id="img2" name="img2">
                <label for="img3" class="img-drop center" id="label-img3" style="background-image:url('../assets/img/uploads/gerichte/<?php echo htmlspecialchars($row['bild3'])?>')"><span class="material-symbols-outlined">image_arrow_up</span></label>
                <input type="file" accept="image/png, image/jpeg" id="img3" name="img3">
            </div>
            <span class="info">Du kannst bis zu 3 Bilder hinzufügen. Das erste Bild wird als Hauptbild verwendet.</span><br>
            <span class="info">Die Bilder sollten im JPG oder PNG Format vorliegen.</span><br>
            <!--Kurzbeschreibung-->
            <label class="heading" for="kbeschreibung">Kurzbeschreibung:</label><br>
            <textarea id="kbeschreibung" name="kbeschreibung" rows="3" required placeholder="Beschreibe es kurz, damit andere wissen, worum es geht."><?php echo htmlspecialchars($row['beschreibung'])?></textarea><br>
            <!--Tags-->
            <h2>Kategorien:</h2>
            <h3>Bitte wähle <u>alle</u> passenden Themenbereiche aus.</h4>
            <div id="tag-container">
                <?php
            // Tags need to be filled in
                if (!empty($row['tags'])) {
                    $tags = explode(',', $row['tags']);
                    foreach ($tags as $tag) {
                        //echo '<span class="hashtag center">' . htmlspecialchars(trim($tag)) . '</span> ';
                    }
                };
                ?>
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
                <?php 
                while ($zutat = $zutatenResult->fetchArray(SQLITE3_ASSOC)) {
                    // richtige Einheit auswählen
                    echo '
                    <div class="ingredient">
                        <input class="menge" name="menge[]" type="number" required placeholder="Menge" value="'.htmlspecialchars($zutat["menge"]).'">
                        <select class="einheit" name="einheit[]" required value="'.htmlspecialchars($zutat["einheit"]).'">
                            <option value="g">g</option>
                            <option value="kg">kg</option>
                            <option value="ml">ml</option>
                            <option value="l">l</option>
                            <option value="EL">EL</option>
                            <option value="TL">TL</option>
                            <option value="%">%</option>
                            <option value="Stk.">Stk.</option>
                        </select>
                        <input class="zutat" name="zutat[]" type="text" required placeholder="Zutat" value="'.htmlspecialchars($zutat["name"]).'">
                        <button type="button" class="remove-ingredient center" onclick="removeIngredient(this)" title="Löschen"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    ';
                }
                ?>
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
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select><br><br>
            <h3>Wie lange dauert die Vorbereitung?</h3>
            <span>Gib die Zeit in Minuten an.</span><br>
            <input type="number" id="vorbereitungszeit" name="vorbereitungszeit" required placeholder="25" value="<?php echo htmlspecialchars($row['vorbereitungszeit'])?>"><br>
            

            <!--Zubereitung-->
            <h2>Zubereitung:</h2>
            <div id="steps">
                <?php 
                $i = 1;
                while ($schritt = $schrittResult->fetchArray(SQLITE3_ASSOC)) {
                    echo '
                        <div class="step" id="step1">
                            <label class="schritt" for="schritt1">'.htmlspecialchars($i).'</label>
                            <textarea id="schritt1" name="schritt[]" rows="2" required placeholder="Teile die Zubereitung in Sinnvolle Schritte ein.">'.htmlspecialchars($schritt["schritt"]).'</textarea>
                            <button type="button" class="remove-step center" onclick="removeStep(this)" title="Löschen"><span class="material-symbols-outlined">close</span></button>
                        </div>';
                        $i++;
                    }
                    ?>
            </div>
            <button type="button" id="add-step" class="center" onclick="addStep()">
                <span class="material-symbols-outlined center">add</span>
                <span>Weitere Schritte hinzufügen</span>
            </button><br><br>
            <h3>Wie lange dauert die reine Zubereitung?</h3>
            <span>Gib die Zeit in Minuten an.</span><br>
            <input type="number" id="zubereitungszeit" name="zubereitungszeit" required placeholder="25" value="<?php echo htmlspecialchars($row['zubereitungszeit'])?>"><br>
            <!--Hidden inputs-->
            <input type="hidden" id="timecode" name="timecode" value="<?php echo time(); ?>">
            <div id="buttons">
                <button type="reset" id="abbrechen">Abbrechen</button>
                <button type="submit" id="speichern">Rezept speichern</button>
            </div>
        </form>
        <script>
            function setImagePreview(inputId, labelId) {
                const input = document.getElementById(inputId);
                const label = document.getElementById(labelId);
                input.addEventListener('change', function(e) {
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            label.style.backgroundImage = 'url(' + ev.target.result + ')';
                        }
                        reader.readAsDataURL(input.files[0]);
                    } else {
                        label.style.backgroundImage = '';
                    }
                });
            }
            setImagePreview('img1', 'label-img1');
            setImagePreview('img2', 'label-img2');
            setImagePreview('img3', 'label-img3');
        </script>
        <?php
            // Verbindung zur SQLite-Datenbank
            $db = new SQLite3('../assets/db/gerichte.db');
            $result = $db->querySingle("SELECT MAX(id) FROM gerichte");
            $gericht_id = $result ? $result + 1 : 1;
            $allowedimgtypes = array("png", "jpeg", "jpg", "JPG", "JPEG", "PNG", "ICO", "ico");

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Rezeptdaten erfassen
                $titel = $_POST['titel'] ?? '';
                $beschreibung = $_POST['kbeschreibung'] ?? '';
                $tags = isset($_POST['tags']) ? implode(",", $_POST['tags']) : '';
                $personen = $_POST['portionen'] ?? 1;
                $vorbereitungszeit = $_POST['vorbereitungszeit'] ?? 0;
                $zubereitungszeit = $_POST['zubereitungszeit'] ?? 0;

            // Handle file uploads
                if (!empty($_FILES['img1']['name'])) {
                    $Img1 = $_FILES['img1']['name'];
                    $img_extention = pathinfo($Img1, PATHINFO_EXTENSION);
                    if(in_array($img_extention, $allowedimgtypes)){
                        $tmpNameImg1 = $_FILES['img1']['tmp_name'];
                        $Img1Name = $gericht_id . '.1.' . $img_extention;
                        $targetpathImg1 = "../assets/img/uploads/gerichte/" . $Img1Name;
                        move_uploaded_file($tmpNameImg1, $targetpathImg1);
                    }
                }else{
                    $Img1Name = null;
                }
                if (!empty($_FILES['img2']['name'])) {
                    $Img2 = $_FILES['img2']['name'];
                    $img_extention = pathinfo($Img2, PATHINFO_EXTENSION);
                    if(in_array($img_extention, $allowedimgtypes)){
                        $tmpNameImg2 = $_FILES['img2']['tmp_name'];
                        $Img2Name = $gericht_id . '.2.' . $img_extention;
                        $targetpathImg2 = "../assets/img/uploads/gerichte/" . $Img2Name;
                        move_uploaded_file($tmpNameImg2, $targetpathImg2);
                    }
                }
                else{
                    $Img2Name = null;
                }
                if (!empty($_FILES['img3']['name'])) {
                    $Img3 = $_FILES['img3']['name'];
                    $img_extention = pathinfo($Img3, PATHINFO_EXTENSION);
                    if(in_array($img_extention, $allowedimgtypes)){
                        $tmpNameImg3 = $_FILES['img3']['tmp_name'];
                        $Img3Name = $gericht_id . '.3.' . $img_extention;
                        $targetpathImg3 = "../assets/img/uploads/gerichte/" . $Img3Name;
                        move_uploaded_file($tmpNameImg3, $targetpathImg3);
                    }
                }
                else{
                    $Img3Name = null;
                }
                
            // set right user
                $made_by_user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
                $status = ($made_by_user === 0) ? 1 : 0;
            // Rezept einfügen
                $stmt = $db->prepare("INSERT INTO gerichte (titel, beschreibung, tags, vorbereitungszeit, zubereitungszeit, bild1, bild2, bild3, personen, made_by_id, status) 
                    VALUES (:titel, :beschreibung, :tags, :vorbereitungszeit, :zubereitungszeit, :bild1, :bild2, :bild3, :personen, :made_by_id, :status)");
                    $stmt->bindValue(':titel', $titel, SQLITE3_TEXT);
                    $stmt->bindValue(':beschreibung', $beschreibung, SQLITE3_TEXT);
                    $stmt->bindValue(':tags', $tags, SQLITE3_TEXT);
                    $stmt->bindValue(':vorbereitungszeit', $vorbereitungszeit, SQLITE3_INTEGER);
                    $stmt->bindValue(':zubereitungszeit', $zubereitungszeit, SQLITE3_INTEGER);
                    $stmt->bindValue(':bild1', $Img1Name, SQLITE3_TEXT);
                    $stmt->bindValue(':bild2', $Img2Name, SQLITE3_TEXT);
                    $stmt->bindValue(':bild3', $Img3Name, SQLITE3_TEXT);
                    $stmt->bindValue(':personen', $personen, SQLITE3_TEXT);
                    $stmt->bindValue(':made_by_id', $made_by_user, SQLITE3_TEXT);
                    $stmt->bindValue(':status', $status, SQLITE3_TEXT);
                $stmt->execute();

            // Zutaten auslesen (mehrere Einträge)
            if (!empty($_POST['zutat']) && is_array($_POST['zutat'])) {
                for ($i = 0; $i < count($_POST['zutat']); $i++) {
                    $name = $_POST['zutat'][$i] ?? '';
                    $menge = $_POST['menge'][$i] ?? '';
                    $einheit = $_POST['einheit'][$i] ?? '';
                    if ($name !== '') {
                        $stmt = $db->prepare("INSERT INTO zutaten (gerichte_id, name, menge, einheit) 
                        VALUES (:gerichte_id, :name, :menge, :einheit)");
                        $stmt->bindValue(':gerichte_id', $gericht_id, SQLITE3_INTEGER);
                        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
                        $stmt->bindValue(':menge', $menge, SQLITE3_TEXT);
                        $stmt->bindValue(':einheit', $einheit, SQLITE3_TEXT);
                        $stmt->execute();
                    }
                }
            }

            // Schritte auslesen (mehrere Einträge)
if (!empty($_POST['schritt']) && is_array($_POST['schritt'])) {
    for ($j = 0; $j < count($_POST['schritt']); $j++) {
        $schritt = $_POST['schritt'][$j] ?? '';
        if ($schritt !== '') {
            $stmt = $db->prepare("INSERT INTO schritte (gerichte_id, schritt) VALUES (:gerichte_id, :schritt)");
            $stmt->bindValue(':gerichte_id', $gericht_id, SQLITE3_INTEGER);
            $stmt->bindValue(':schritt', $schritt, SQLITE3_TEXT);
            $stmt->execute();
        }
    }
}

                //Positiv Popup
                echo '
                    <div id="erfolg" class="popup-positiv center" onclick="closeErfolg()">
                        <div class="inner-popup">
                            <h1>Danke!</h1>
                            <p>Dein Rezept wurde erfolgreich gespeichert!</p>
                            <p>Du kannst es <a href="../gericht.php?id='.$gericht_id.'"><u>hier</u></a> ansehen und teilen.</p>
                            <div class="buttons">
                                <button onclick="window.location.href = `../pages/rezept-erstellen.php`" id="btn-mehr">mehr erstellen</button>
                                <button onclick="closeErfolg()" id="btn-close">schließen</button>
                            </div>
                        </div>
                    </div>
                ';
            }
        ?>
    </div>
</body>
</html>