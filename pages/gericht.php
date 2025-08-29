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

                // Increase viewcount by 1 only if not already counted in this session
                if ($row && !isset($_SESSION['viewed_' . $id])) {
                    $updateStmt = $db->prepare("UPDATE gerichte SET viewcount = viewcount + 1 WHERE id = :id");
                    $updateStmt->bindValue(':id', $id, SQLITE3_INTEGER);
                    $updateStmt->execute();
                    $_SESSION['viewed_' . $id] = true;
                    $row['viewcount']++;
                }
                // check if id exists
                if (!$row) {
                    header("Location: 404.php");
                }

            } else {
            // Invalid or missing id, show error or redirect
                die("Ungültige Rezept-ID.");
            }
        }
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="<?php echo htmlspecialchars($row["titel"])?> - Kochbuch" />
    <meta property="og:description" content="<?php echo htmlspecialchars($row["beschreibung"])?>" />
    <meta property="og:image" content="../assets/icons/Topficon.png" />
    <meta property="og:url" content="https://mein-kochbuch-free.nf" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="<?php echo htmlspecialchars($row["titel"])?> - Kochbuch" />
    <title><?php echo htmlspecialchars($row["titel"])?> - Kochbuch</title>
    <link rel="icon" href="../assets/icons/Topficon.png">
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/heading.css">
    <link rel="stylesheet" href="../assets/css/gericht.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script src="../assets/js/copytoclipboard.js"></script>
    <script src="../assets/js/gericht_images.js"></script>
    <script>
        var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        var isAdmin = <?php if($_SESSION['rolle'] == 'admin') {echo 'true';}else{echo'false';}; ?>;
        var isEditor = <?php if($_SESSION['rolle'] == 'editor') {echo 'true';}else{echo'false';}; ?>;
    </script>
    <?php
        $isloggedin = isset($_SESSION['user_id']) ? 'true' : 'false';
    ?>
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
    <!-- Popups -->
    <div class="popup positive center" id="link_copied">
        <span class="material-symbols-outlined">check</span>
        Link wurde Kopiert!
    </div>
    <div class="popup positive center" id="saved">
        <span class="material-symbols-outlined">check</span>
        Rezept wurde gespeichert!
    </div>
    <div class="popup negative center" id="login_required">
        <span class="material-symbols-outlined">lock</span>
        Bitte logge dich ein, um Rezepte zu speichern!
    </div>
    <?php
    //Banner if recepie is not validated
        if ($row['status'] != 0) { 
            echo '
                <div class="banner">
                    <p>Dieses Rezept wurde noch nicht überprüft!</p>
                </div>
            ';}
    ?>
    <div id="main">
        <h1 id="ID"><?php echo ($row["titel"])?></h1>
        <div id="image-container">
            <div id="big-image">
                <img src="../assets/img/uploads/gerichte/<?php echo ($row["bild1"])?>" id="img1" alt="">
                <img src="../assets/img/uploads/gerichte/<?php echo ($row["bild2"])?>" id="img2" alt="">
                <img src="../assets/img/uploads/gerichte/<?php echo ($row["bild3"])?>" id="img3" alt="">
            </div>
            <div id="sidebar">
                <img src="../assets/img/uploads/gerichte/<?php echo ($row["bild1"])?>"onclick = "showImg1()" alt="">
                <?php if(!empty($row["bild2"])){
                    echo '<img src="../assets/img/uploads/gerichte/'. ($row["bild2"]).'"onclick = "showImg2()" alt="">';
                }?>
                <?php if(!empty($row["bild3"])){
                    echo '<img src="../assets/img/uploads/gerichte/'. ($row["bild3"]).'"onclick = "showImg3()" alt="">';
                }?>
            </div>
        </div>
        <div id="tagsandbtns">
            <div id="info">
                <?php
                if (!empty($row['tags'])) {
                    $tags = explode(',', $row['tags']);
                    foreach ($tags as $tag) {
                        echo '<span class="hashtag center">' . htmlspecialchars(trim($tag)) . '</span> ';
                    }
                };
                ?>
            </div>
            <!-- Bookmark, Edit and Share Buttons -->
            <div id="buttons">
                <button id="printbtn" onclick="window.print();"><span class="material-symbols-outlined">print</span></button>
                <form method="POST">
                    <?php
                    $isBookmarked = false;
                    if (isset($_SESSION['user_id'])) {
                        $user_id = $_SESSION['user_id'];
                        $usersDb = new SQLite3("../assets/db/users.db");
                        $stmt = $usersDb->prepare("SELECT saved_recepies FROM users WHERE id = :id");
                        $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
                        $result = $stmt->execute();
                        $userRow = $result->fetchArray(SQLITE3_ASSOC);
                        $saved = $userRow['saved_recepies'] ?? '';
                        $saved_array = array_filter(array_map('trim', explode(',', $saved)));
                        $isBookmarked = in_array($id, $saved_array);
                    }
                    ?>
                    <button id="savebtn" name="savebtn" type="submit" <?php if ($isBookmarked) echo 'class="bookmarked"'; ?>>
                        <span class="material-symbols-outlined"><?php echo $isBookmarked ? 'bookmark_added' : 'bookmark'; ?></span>
                    </button>
                </form>
                <?php
                    if (isset($_SESSION['rolle']) && ($_SESSION['rolle'] == 'admin' || $_SESSION['rolle'] == 'editor')) {
                        echo '<button id="editbtn" onclick="window.location.href = \'rezept-bearbeiten.php?id='.$id.'\'"><span class="material-symbols-outlined">edit</span></button>';
                    }
                ?>
                <button id="sharebtn" onclick="copyTextToClipBoard()"><span class="material-symbols-outlined">share</span></button>
            </div>
        </div>
        <div id="countanddate">
            <div id="viewcount">
                <span class="material-symbols-outlined">visibility</span>
                <span><?php echo htmlspecialchars($row['viewcount'])?></span>
            </div>
            <div id="date_created">
                <span class="material-symbols-outlined">calendar_month</span>
                <span>
                    <?php
                        $dt = new DateTime('@' . $row['timecode_erstellt']);
                        $dt->setTimezone(new DateTimeZone('Europe/Berlin'));
                        echo $dt->format('d.m.Y'); 
                    ?>
                </span>
            </div>
        </div>
        <?php
        // Fetch all ingredients into an array for reuse
        $zutatenArr = [];
        $zutatenStmt = $db->prepare("SELECT * FROM zutaten WHERE gerichte_id = :id");
        $zutatenStmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $zutatenResult = $zutatenStmt->execute();
        while ($zutat = $zutatenResult->fetchArray(SQLITE3_ASSOC)) {
            $zutatenArr[] = $zutat;
        }
        ?>
        <div id="zutaten">
            <h2>Zutaten</h2>
            <div id="personen-anpassen">
                <button id="minusBtn" type="button">
                    <span class="material-symbols-outlined">remove</span>
                </button>
                <span id="personenanzahl" class="center">
                    <?php echo (int)$row["personen"]; ?>
                </span>
                <button id="plusBtn" type="button">
                    <span class="material-symbols-outlined">add</span>
                </button>
            </div>
            <div id="zutatenliste">
                <?php foreach ($zutatenArr as $zutat): 
                    $menge = str_replace(',', '.', $zutat["menge"]);
                    ?>
                    <div class="zutat"
                        data-base-menge="<?php echo htmlspecialchars($menge); ?>"
                        data-einheit="<?php echo htmlspecialchars($zutat["einheit"]); ?>">
                        <span class="menge"><?php echo htmlspecialchars($zutat["menge"]); ?></span>
                        <span class="einheit"><?php echo htmlspecialchars($zutat["einheit"]); ?></span>
                        <span class="name"><?php echo htmlspecialchars($zutat["name"]); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            let personen = <?php echo (int)$row["personen"]; ?>;
            const minPersonen = 1;
            const personenAnzahl = document.getElementById('personenanzahl');
            const minusBtn = document.getElementById('minusBtn');
            const plusBtn = document.getElementById('plusBtn');
            const zutaten = document.querySelectorAll('#zutatenliste .zutat');
            const basePersonen = <?php echo (int)$row["personen"]; ?>;

            zutaten.forEach(zutat => {
                // Save base amount as float for calculation
                zutat.dataset.base = parseFloat(zutat.getAttribute('data-base-menge'));
            });

            function updateZutaten(newPersonen) {
                zutaten.forEach(zutat => {
                    const base = parseFloat(zutat.dataset.base);
                    const einheit = zutat.getAttribute('data-einheit');
                    let displayMenge;
                    if (einheit.trim() === "%") {
                        // Do not recalculate for percent
                        displayMenge = zutat.dataset.base.replace('.', ',');
                    } else {
                        let newMenge = base * newPersonen / basePersonen;
                        newMenge = Math.round(newMenge * 100) / 100;
                        displayMenge = (newMenge % 1 === 0) ? newMenge : newMenge.toString().replace('.', ',');
                    }
                    zutat.querySelector('.menge').textContent = displayMenge;
                });
                personenAnzahl.textContent = newPersonen;

                // Add or remove disabled class for minusBtn
                if (newPersonen <= minPersonen) {
                    minusBtn.classList.add('disabled');
                } else {
                    minusBtn.classList.remove('disabled');
                }
            }

            minusBtn.addEventListener('click', function() {
                if (personen > minPersonen) {
                    personen--;
                    updateZutaten(personen);
                }
            });
            plusBtn.addEventListener('click', function() {
                personen++;
                updateZutaten(personen);
            });

            // Initial update
            updateZutaten(personen);
        });
        </script>
        <div id="arbeitsschritte">
            <h2>Zubereitung</h2>
            <?php 
                $i = 1;
                while ($schritt = $schrittResult->fetchArray(SQLITE3_ASSOC)) {
                    echo '<div class="schritt">
                            <span class="nummer">'.htmlspecialchars($i).'.</span>
                            <span class="arbeitsschritt">'.htmlspecialchars($schritt["schritt"]).'</span>
                        </div>';
                    $i++;
                }
            ?>
        </div>
        <div id="zuletzt-geändert">
            <?php
                if ($row['timecode_geaendert'] != '0000-00-00 00:00:00') {
                    $dt = new DateTime($row['timecode_geaendert'], new DateTimeZone('Europe/Berlin'));
                    echo 'Zuletzt geändert am: ' . $dt->format('d.m.Y');
                }
            ?>
        </div>
        <div id="bewerten">
            <form id="bewertungsform" method="POST" class="center">
                <fieldset id="star_fieldset">
                    <input type="radio" value="1" class="rating-input" id="1star"  name="star_rating">
                    <label for="1star" id="star1">
                        <span class="material-symbols-outlined">star</span>
                        <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="33" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                        </svg>
                    </label>
                    <input type="radio" value="2" class="rating-input" id="2star"  name="star_rating">
                    <label for="2star" id="star2">
                        <span class="material-symbols-outlined">star</span>
                        <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="33" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                        </svg>
                    </label>
                    <input type="radio" value="3" class="rating-input" id="3star"  name="star_rating">
                    <label for="3star" id="star3">
                        <span class="material-symbols-outlined">star</span>
                        <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="33" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                        </svg>
                    </label>
                    <input type="radio" value="4" class="rating-input" id="4star"  name="star_rating">
                    <label for="4star" id="star4">
                        <span class="material-symbols-outlined">star</span>
                        <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="33" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                        </svg>
                    </label>
                    <input type="radio" value="5" class="rating-input" id="5star"  name="star_rating">
                    <label for="5star" id="star5">
                        <span class="material-symbols-outlined">star</span>
                        <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="33" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                        </svg>
                    </label>
                </fieldset>
            </form>
        </div>
        <div id="bottom_section">
            <div id="bewertungen" class="center">
                <div id="box">
                    <div id="gesamt">
                        <h1>⌀
                            <?php
                                $gesamt = ($row["star5"]+$row["star4"]+$row["star3"]+$row["star2"]+$row["star1"]);
                                if($gesamt != 0){
                                    $avg = (($row["star5"]*5)+($row["star4"]*4)+($row["star3"]*3)+($row["star2"]*2)+($row["star1"])) / $gesamt;
                                }else{
                                    $avg = '0.0';
                                }
                                echo htmlspecialchars(round($avg,1));
                            ?>
                        </h1>
                    </div>
                    <div class="row">
                        <span class="zahl"><?php echo ($row["star5"])?></span>
                        <span class="stars">
                            <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                            <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                            <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                            <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="row">
                        <span class="zahl"><?php echo ($row["star4"])?></span>
                        <span class="stars">
                            <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                            <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                            <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                            <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="row">
                        <span class="zahl"><?php echo ($row["star3"])?></span>
                        <span class="stars">
                            <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                            <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                            <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                            <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="row">
                        <span class="zahl"><?php echo ($row["star2"])?></span>
                        <span class="stars">
                            <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                            <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="row">
                        <span class="zahl"><?php echo ($row["star1"])?></span>
                        <span class="stars">
                            <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
            <div id="user" class="center">
                <?php 
                $somany = 0;
                $query = "SELECT * FROM gerichte WHERE made_by_id = $username";
                $result = $db->query($query);
                // Count results
                if($result){
                    while ($row = $result->fetchArray(SQLITE3_ASSOC)){
                        $somany++;
                    }
                }
                // User Daten bestimmen
                if ($username != 0){
                    $users = new SQlite3("../assets/db/users.db");
                    $stmt2 = $users->prepare("SELECT * FROM users WHERE id = $username");
                    $stmt2->bindValue('id', $username, SQLITE3_INTEGER);
                    $result = $stmt2->execute();
                    $userrow = $result->fetchArray(SQLITE3_ASSOC);
                    $user = $userrow['name'];
                }else{
                    $user = 'anonym';
                }
                ?>
                <div class="inneruser">
                    <img id="profilbild" src="../assets/img/uploads/users/<?php echo htmlspecialchars($userrow['profilbild'])?>" alt="">
                    <div id="userinfo">
                        <h1><?php echo htmlspecialchars($user);?></php></h1>
                        <?php
                            if($username != 0){
                                echo '
                                    <a href="../pages/suche.php?user='.htmlspecialchars($user).'" class="center">'.htmlspecialchars($somany -1).' weitere Rezepte anzeigen<span class="material-symbols-outlined">keyboard_arrow_right</span></a>
                                ';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="problem" class="center">
        <div id="melden" class="center" onclick="document.getElementById('problem-form').style.display = 'flex';">
            <span class="material-symbols-outlined">emergency_home</span>
            Problem melden
        </div>
    </div>
    <div id="problem-form">
        <form method="POST">
            <h1><span class="material-symbols-outlined" style="margin-right:1rem">emergency_home</span>Problem melden</h1>
            <p>Bitte beschreibe das Problem oder den Fehler so genau wie möglich, damit wir es schnellstmöglich beheben können.</p>
            <textarea name="problem" id="problem-textarea" placeholder="Deine Nachricht..."></textarea>
            <input type="hidden" name="recipe_id" value="<?php echo htmlspecialchars($id); ?>">
            <button type="submit">Absenden</button>
            <button type="button" onclick="document.getElementById('problem-form').style.display = 'none';">Abbrechen</button>
        </form>
    </div>
    <?php
    // insert problem report into gerichte.db
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['problem']) && isset($_POST['recipe_id'])) {
        $problem = trim($_POST['problem']);
        $timecode_error = time();
        $db->exec("UPDATE gerichte SET timecode_error = $timecode_error WHERE id = $id");
        $recipe_id = intval($_POST['recipe_id']);
        if (!empty($problem)) {
            $stmt = $db->prepare("UPDATE gerichte SET error_msg = :problem WHERE id = :id");
            $stmt->bindValue(':problem', $problem, SQLITE3_TEXT);
            $stmt->bindValue(':id', $recipe_id, SQLITE3_INTEGER);
            $stmt->execute();
            // log the event
            $logs_db = new SQLite3("../assets/db/logs.db");
            $log_stmt = $logs_db->prepare("INSERT INTO logs (user, event_type, event, timecode, 'IP-Adresse') VALUES (:name, :event_type, :event, :timecode, :ip)");
            $log_stmt->bindValue(':name', $user, SQLITE3_TEXT);
            $log_stmt->bindValue(':event_type', 'Fehlermeldung', SQLITE3_TEXT);
            $log_stmt->bindValue(':event', $problem, SQLITE3_TEXT);
            $log_stmt->bindValue(':timecode', $timecode_error, SQLITE3_INTEGER);
            $log_stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR'], SQLITE3_TEXT);
            $log_stmt->execute();
            // Optional: show a success message or reload
            echo '
            <script>
                alert("Danke für die Rückmeldung!");
                document.getElementById("problem-form").style.display = "none";
            </script>';
            
        }
    }
    ?>
    <div id="footer">
        <!-- Code gets injected by footer.js -->
    </div>
    <?php
    // save/unsave button functionality
if($isloggedin == 'true' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['savebtn'])) {
    $user_id = $_SESSION['user_id'];
    $recipe_id = $id;
    $users = new SQLite3("../assets/db/users.db");
    $stmt = $users->prepare("SELECT saved_recepies FROM users WHERE id = :id");
    $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    $saved = $row['saved_recepies'] ?? '';
    $saved_array = array_filter(array_map('trim', explode(',', $saved)));

    if (in_array($recipe_id, $saved_array)) {
        // Unsave: remove recipe_id
        $saved_array = array_diff($saved_array, [$recipe_id]);
    } else {
        // Save: add recipe_id
        $saved_array[] = $recipe_id;
    }
    $new_saved = implode(',', $saved_array);
    $update = $users->prepare("UPDATE users SET saved_recepies = :saved WHERE id = :id");
    $update->bindValue(':saved', $new_saved, SQLITE3_TEXT);
    $update->bindValue(':id', $user_id, SQLITE3_INTEGER);
    $update->execute();
    // Optional: reload to update button state
        echo "<script>window.location.href=window.location.href;</script>";
        exit;
    }
    else{
        exit;
    }
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('.rating-input');
    const recipeId = '<?php echo $id; ?>';
    const cooldownKey = 'ratingCooldown_' + recipeId;
    const selectedStarKey = 'selectedStar_' + recipeId;

    // Restore selected star from localStorage
    const lastSelected = localStorage.getItem(selectedStarKey);
    if (lastSelected) {
        const radio = document.getElementById(lastSelected + 'star');
        if (radio) {
            radio.checked = true;
            updateStars(lastSelected);
        }
    }

    // Disable rating if cooldown is active
    function isCooldownActive() {
        const cooldown = localStorage.getItem(cooldownKey);
        if (!cooldown) return false;
        return (Date.now() - parseInt(cooldown, 10)) < 5 * 60 * 1000; // 5 minutes
    }

    function updateStars(selected) {
        for (let i = 1; i <= 5; i++) {
            const label = document.getElementById('star' + i);
            if (i <= selected) {
                label.querySelector('.material-symbols-outlined').style.display = 'none';
                label.querySelector('.svg').style.display = 'inline';
            } else {
                label.querySelector('.material-symbols-outlined').style.display = 'inline';
                label.querySelector('.svg').style.display = 'none';
            }
        }
    }

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (isCooldownActive()) {
                alert('Du kannst dieses Rezept erst in 5 Minuten erneut bewerten.');
                // Optionally reset radio selection
                radios.forEach(r => r.checked = false);
                return;
            }
            const selected = parseInt(this.value);
            updateStars(selected);
            localStorage.setItem(selectedStarKey, selected);
            localStorage.setItem(cooldownKey, Date.now().toString());
            // AJAX submit
            fetch(window.location.pathname + '?id=<?php echo $id; ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'star_rating=' + selected
            })
            .then(response => response.ok ? response.text() : Promise.reject())
            .then(data => {
                // Optionally show a message
            });
        });
        // Disable radio if cooldown is active
        radio.disabled = isCooldownActive();
    });

    // Optionally, re-enable after cooldown expires (not strictly necessary for 5min)
    setInterval(() => {
        const active = isCooldownActive();
        radios.forEach(radio => radio.disabled = active);
    }, 10000); // check every 10s
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    const saveBtn = document.getElementById('savebtn');
    const saveForm = saveBtn ? saveBtn.closest('form') : null;
    const loginPopup = document.getElementById('login_required');

    if (saveForm && saveBtn) {
        saveForm.addEventListener('submit', function(e) {
            if (!isLoggedIn) {
                e.preventDefault();
                loginPopup.classList.add('open');
            }
        });
    }
});
</script>
</body>
</html>