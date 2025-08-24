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
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/rezept-erstellen.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script src="../assets/js/ingredients.js"></script>
    <script src="../assets/js/steps.js"></script>
    <script src="../assets/js/tags.js"></script>
    <script src="../assets/js/popups.js"></script>
    <script>
        var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        var isAdmin = <?php if($_SESSION['rolle'] == 'admin') {echo 'true';}else{echo'false';}; ?>;
        var isEditor = <?php if($_SESSION['rolle'] == 'editor') {echo 'true';}else{echo'false';}; ?>;
    </script>
    <script src="../assets/js/heading.js" defer></script>
    <script src="../assets/js/footer.js" defer></script>
</head>
<body>
    <div id="heading">
        <!-- Code gets injected by heading.js -->
    </div>
    <div id="sidebar">
        <!-- Code gets injected by heading.js -->
    </div>
    <div class="popup negative center" id="link_copied">
        <span class="material-symbols-outlined">report</span>
        Datei muss .PNG oder .JPG sein!
    </div>
    <div id="fototipps" class="">
        <h2>Fototipps</h2>
        <h3>Bildausschnitt</h3>
        <p>Die Bilder werden im 4:3 Querformat dargestellt. Nimm sie also so auf, dass das Gericht in diesem Ausschnitt gut zu erkennen ist.</p>
        <h3>Helligkeit</h3>
        <p>Achte darauf, dass die Bilder nicht zu dunkel sind. Beleuchte das Gericht mit hellem künstlichen Licht oder im draußen im Schatten.</p>
        <h3>Schärfe</h3>
        <p>Achte auf einen guten Fokus, um dein Gericht am besten zu präsentieren. Ein schönes Foto ist einladend.</p>
        <h3>Umgebung</h3>
        <p>Vermeide ablenkende Objekte. Richte das Gericht gern schön auf einem Teller an, achte aber auf die Umgebung.</p>
        <h3>Details</h3>
        <p>Zeige schwierige oder besondere Arbeitsschritte oder Zutaten gern auf einem zweiten oder dritten Foto.</p>
        <button onclick="fototipps()">schließen</button>
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
                <input type="file" accept="image/png, image/jpeg" id="img1" name="img1">
                <label for="img2" class="img-drop center" id="label-img2" style="background-image:url('../assets/img/uploads/gerichte/<?php echo htmlspecialchars($row['bild2'])?>')"><span class="material-symbols-outlined">image_arrow_up</span></label>
                <input type="file" accept="image/png, image/jpeg" id="img2" name="img2">
                <label for="img3" class="img-drop center" id="label-img3" style="background-image:url('../assets/img/uploads/gerichte/<?php echo htmlspecialchars($row['bild3'])?>')"><span class="material-symbols-outlined">image_arrow_up</span></label>
                <input type="file" accept="image/png, image/jpeg" id="img3" name="img3">
            </div>
            <span class="info">Du kannst bis zu 3 Bilder hinzufügen. Das erste Bild wird als Hauptbild verwendet.</span><br>
            <span class="info">Die Bilder sollten im JPG oder PNG Format vorliegen.</span><br>
            <span class="info">Bitte beachte <span id="forfototipps" onclick="fototipps()">diese Fototipps</span>.</span><br>
            <!--Kurzbeschreibung-->
            <label class="heading" for="kbeschreibung">Kurzbeschreibung:</label><br>
            <textarea id="kbeschreibung" name="kbeschreibung" rows="3" required placeholder="Beschreibe es kurz, damit andere wissen, worum es geht."><?php echo htmlspecialchars($row['beschreibung'])?></textarea><br>
            <!--Tags-->
            <h2>Kategorien:</h2>
            <h3>Bitte wähle <u>alle</u> passenden Themenbereiche aus.</h4>
            <div id="tag-container">
                <?php
                    $allTags = [
                        "Asiatisch" => "🥡 Asiatisch",
                        "Nudeln" => "🍝 Nudeln",
                        "Kartoffeln" => "🥔 Kartoffeln",
                        "Reis" => "🍚 Reis",
                        "Fleisch" => "🥩 Fleisch",
                        "Hühnchen" => "🍗 Hühnchen",
                        "Schwein" => "🍖Schwein",
                        "Rind" => "🐮 Rind",
                        "Fisch" => "🐟 Fisch",
                        "Suppe" => "🥣 Suppe",
                        "Soße" => "🍲 Soße",
                        "Dessert" => "🍮 Dessert",
                        "Kuchen" => "🥧 Kuchen",
                        "Vegan" => "🌿 Vegan",
                        "vegetarisch" => "🥗 Vegetarisch",
                        "Getränk" => "🥤 Getränk",
                        "Cocktail" => "🍹 Cocktail",
                        "Mocktail" => "🍸 Mocktail"
                    ];
                    $tags = !empty($row['tags']) ? array_map('trim', explode(',', $row['tags'])) : [];
                    foreach ($allTags as $tagValue => $tagLabel) {
                        $isChecked = in_array($tagValue, $tags) ? 'checked' : '';
                        $isActive = in_array($tagValue, $tags) ? 'active' : '';
                        echo '<label class="tag '.$isActive.'" for="'.$tagValue.'">'.$tagLabel.'</label>';
                        echo '<input type="checkbox" id="'.$tagValue.'" name="tags[]" value="'.$tagValue.'" '.$isChecked.'>';
                    }
                ?>
            </div>
            <!--Zutaten-->
            <h2>Zutaten:</h2><br>
            <div id="ingredients">
                <?php 
                while ($zutat = $zutatenResult->fetchArray(SQLITE3_ASSOC)) {
                    echo '
                    <div class="ingredient">
                        <input class="menge" name="menge[]" type="number" required placeholder="Menge" value="'.htmlspecialchars($zutat["menge"]).'">
                        <select class="einheit" name="einheit[]" required value="'.htmlspecialchars($zutat["einheit"]).'">
                            <option value="g"'; if($zutat["einheit"] == 'g'){echo 'selected';}echo'>g</option>
                            <option value="kg"'; if($zutat["einheit"] == 'kg'){echo 'selected';}echo'>kg</option>
                            <option value="ml"'; if($zutat["einheit"] == 'ml'){echo 'selected';}echo'>ml</option>
                            <option value="l"'; if($zutat["einheit"] == 'l'){echo 'selected';}echo'>l</option>
                            <option value="EL"'; if($zutat["einheit"] == 'EL'){echo 'selected';}echo'>EL</option>
                            <option value="TL"'; if($zutat["einheit"] == 'TL'){echo 'selected';}echo'>TL</option>
                            <option value="%"'; if($zutat["einheit"] == '%'){echo 'selected';}echo'>%</option>
                            <option value="Stk."'; if($zutat["einheit"] == 'Stk.'){echo 'selected';}echo'>Stk.</option>
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
                <option value="" disabled>Bitte wählen</option>
                <?php
                    for ($p = 1; $p <= 10; $p++) {
                        $selected = ($row['personen'] == $p) ? 'selected' : '';
                        echo "<option value=\"$p\" $selected>$p</option>";
                    }
                ?>
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
                // Image handling
                $imgFields = ['img1', 'img2', 'img3'];
                $imgNames = [];
                foreach ($imgFields as $imgField) {
                    if (isset($_FILES[$imgField]) && $_FILES[$imgField]['error'] === UPLOAD_ERR_OK) {
                        $tmpName = $_FILES[$imgField]['tmp_name'];
                        $ext = strtolower(pathinfo($_FILES[$imgField]['name'], PATHINFO_EXTENSION));
                        if (in_array($ext, $allowedimgtypes)) {
                            $newName = uniqid($imgField . '_') . '.' . $ext;
                            $targetPath = '../assets/img/uploads/gerichte/' . $newName;
                            move_uploaded_file($tmpName, $targetPath);
                            $imgNames[] = $newName;
                        } else {
                            // keep old image if upload fails
                            $imgNames[] = $row['bild' . substr($imgField, -1)];
                        }
                    } else {
                        // keep old image if not uploaded
                        $imgNames[] = $row['bild' . substr($imgField, -1)];
                    }
                }

                // Update gerichte with new image names
                $timecode_geaendert = gmdate('Y-m-d H:i:s');
                $stmt = $db->prepare("UPDATE gerichte SET titel = :titel, beschreibung = :beschreibung, tags = :tags, vorbereitungszeit = :vorbereitungszeit, zubereitungszeit = :zubereitungszeit, bild1 = :bild1, bild2 = :bild2, bild3 = :bild3, personen = :personen, timecode_geaendert = :geaendert WHERE id = :id");
                $stmt->bindValue(':titel', $_POST['titel'], SQLITE3_TEXT);
                $stmt->bindValue(':beschreibung', $_POST['kbeschreibung'], SQLITE3_TEXT);
                $stmt->bindValue(':tags', isset($_POST['tags']) ? implode(",", $_POST['tags']) : '', SQLITE3_TEXT);
                $stmt->bindValue(':vorbereitungszeit', $_POST['vorbereitungszeit'], SQLITE3_INTEGER);
                $stmt->bindValue(':zubereitungszeit', $_POST['zubereitungszeit'], SQLITE3_INTEGER);
                $stmt->bindValue(':bild1', $imgNames[0], SQLITE3_TEXT);
                $stmt->bindValue(':bild2', $imgNames[1], SQLITE3_TEXT);
                $stmt->bindValue(':bild3', $imgNames[2], SQLITE3_TEXT);
                $stmt->bindValue(':personen', $_POST['portionen'], SQLITE3_TEXT);
                $stmt->bindValue(':geaendert', $timecode_geaendert, SQLITE3_TEXT);
                $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
                $stmt->execute();

                // 2. Update zutaten
                $existingZutaten = []; // fetch all zutaten IDs for this recipe
                $zutatenResult = $db->query("SELECT id FROM zutaten WHERE gerichte_id = $id");
                while ($row = $zutatenResult->fetchArray(SQLITE3_ASSOC)) {
                    $existingZutaten[] = $row['id'];
                }
                $submittedZutaten = $_POST['zutat'] ?? [];
                $submittedZutatenIds = $_POST['zutat_id'] ?? [];
                $submittedMengen = $_POST['menge'] ?? [];
                $submittedEinheiten = $_POST['einheit'] ?? [];
                for ($i = 0; $i < count($submittedZutaten); $i++) {
                    $zutat = $submittedZutaten[$i];
                    $zutat_id = $submittedZutatenIds[$i] ?? null;
                    $menge = $submittedMengen[$i] ?? '';
                    $einheit = $submittedEinheiten[$i] ?? '';
                    if ($zutat_id) {
                        // Update
                        $stmt = $db->prepare("UPDATE zutaten SET name = :name, menge = :menge, einheit = :einheit WHERE id = :id");
                        $stmt->bindValue(':name', $zutat, SQLITE3_TEXT);
                        $stmt->bindValue(':menge', $menge, SQLITE3_TEXT);
                        $stmt->bindValue(':einheit', $einheit, SQLITE3_TEXT);
                        $stmt->bindValue(':id', $zutat_id, SQLITE3_INTEGER);
                        $stmt->execute();
                    } else {
                        // Insert
                        $stmt = $db->prepare("INSERT INTO zutaten (gerichte_id, name, menge, einheit) VALUES (:gerichte_id, :name, :menge, :einheit)");
                        $stmt->bindValue(':gerichte_id', $id, SQLITE3_INTEGER);
                        $stmt->bindValue(':name', $zutat, SQLITE3_TEXT);
                        $stmt->bindValue(':menge', $menge, SQLITE3_TEXT);
                        $stmt->bindValue(':einheit', $einheit, SQLITE3_TEXT);
                        $stmt->execute();
                    }
                }
                // Delete removed zutaten
                foreach ($existingZutaten as $db_id) {
                    if (!in_array($db_id, $submittedZutatenIds)) {
                        $db->exec("DELETE FROM zutaten WHERE id = $db_id");
                    }
                }

                // 3. Repeat similar logic for schritte
                $existingSchritte = []; // fetch all schritte IDs for this recipe
                $schritteResult = $db->query("SELECT id FROM schritte WHERE gerichte_id = $id");
                while ($row = $schritteResult->fetchArray(SQLITE3_ASSOC)) {
                    $existingSchritte[] = $row['id'];
                }
                $submittedSchritte = $_POST['schritt'] ?? [];
                $submittedSchritteIds = $_POST['schritt_id'] ?? [];
                for ($i = 0; $i < count($submittedSchritte); $i++) {
                    $schritt = $submittedSchritte[$i];
                    $schritt_id = $submittedSchritteIds[$i] ?? null;
                    if ($schritt_id) {
                        // Update
                        $stmt = $db->prepare("UPDATE schritte SET schritt = :schritt WHERE id = :id");
                        $stmt->bindValue(':schritt', $schritt, SQLITE3_TEXT);
                        $stmt->bindValue(':id', $schritt_id, SQLITE3_INTEGER);
                        $stmt->execute();
                    } else {
                        // Insert
                        $stmt = $db->prepare("INSERT INTO schritte (gerichte_id, schritt) VALUES (:gerichte_id, :schritt)");
                        $stmt->bindValue(':gerichte_id', $id, SQLITE3_INTEGER);
                        $stmt->bindValue(':schritt', $schritt, SQLITE3_TEXT);
                        $stmt->execute();
                    }
                }
                // Delete removed schritte
                foreach ($existingSchritte as $db_id) {
                    if (!in_array($db_id, $submittedSchritteIds)) {
                        $db->exec("DELETE FROM schritte WHERE id = $db_id");
                    }
                }
            // Log the event
            $logs_db = new SQLite3("../assets/db/logs.db");
            $username = isset($_SESSION['name']) ? $_SESSION['name'] : 'Gast';
            $ip = $_SERVER['REMOTE_ADDR'];
            $id = 'Rezept '.$db->querySingle("SELECT MAX(id) FROM gerichte").' bearbeitet';
            $log_stmt = $logs_db->prepare("INSERT INTO logs (user, event, timecode, 'IP-Adresse') VALUES (:name, :event, :timecode, :ip)");
            $log_stmt->bindValue(':name', $username, SQLITE3_TEXT);
            $log_stmt->bindValue(':event', $id, SQLITE3_TEXT);
            $log_stmt->bindValue(':timecode', time(), SQLITE3_INTEGER);
            $log_stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
            $log_stmt->execute();
                //Positiv Popup
                echo '
                    <div id="erfolg" class="popup-positiv center" onclick="closeErfolg()">
                        <div class="inner-popup">
                            <h1>Danke!</h1>
                            <p>Dein Rezept wurde erfolgreich gespeichert!</p>
                            <p>Du kannst es <a href="../pages/gericht.php?id='.$id.'"><u>hier</u></a> ansehen und teilen.</p>
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
    <div id="footer">
        
    </div>
</body>
</html>