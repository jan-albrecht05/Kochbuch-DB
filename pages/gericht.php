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
    <script src="../assets/js/heading.js" defer></script>
    <script src="../assets/js/footer.js" defer></script>
</head>
<body>
    <div id="heading">
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
            <!-- Bookmark and Share Buttons -->
            <div id="buttons">
                <button id="savebtn"><span class="material-symbols-outlined">bookmark</span></button>
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
                <span><?php echo htmlspecialchars($row['timecode_erstellt'])?></span>
            </div>
        </div>
        <div id="zutaten">
            <h2>Zutaten</h2>
            <p>(für <?php echo ($row["personen"])?> Personen)</p>
            <?php 
                while ($zutat = $zutatenResult->fetchArray(SQLITE3_ASSOC)) {
                    echo '<div class="zutat">
                            <span class="menge">'.htmlspecialchars($zutat["menge"]).'</span>
                            <span class="einheit">'.htmlspecialchars($zutat["einheit"]).'</span>
                            <span class="name">'.htmlspecialchars($zutat["name"]).'</span>
                        </div>';
                }
            ?>
        </div>
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
        <div id="bewerten">
            <form id="bewertungsform" method="POST" class="center">
                <fieldset id="star_fieldset">
                    <input type="radio" value="1" class="rating-input" id="1star" name="star">
                    <label for="1star" id="star1">
                        <span class="material-symbols-outlined">star</span>
                        <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="33" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                        </svg>
                    </label>
                    <input type="radio" value="2" class="rating-input" id="2star" name="star">
                    <label for="2star" id="star2">
                        <span class="material-symbols-outlined">star</span>
                        <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="33" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                        </svg>
                    </label>
                    <input type="radio" value="3" class="rating-input" id="3star" name="star">
                    <label for="3star" id="star3">
                        <span class="material-symbols-outlined">star</span>
                        <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="33" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                        </svg>
                    </label>
                    <input type="radio" value="4" class="rating-input" id="4star" name="star">
                    <label for="4star" id="star4">
                        <span class="material-symbols-outlined">star</span>
                        <svg class="svg" xmlns="http://www.w3.org/2000/svg" width="24" height="33" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                        </svg>
                    </label>
                    <input type="radio" value="5" class="rating-input" id="5star" name="star">
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
                $users = new SQlite3("../assets/db/users.db");
                //echo ('Name:'.$username);
                $stmt2 = $users->prepare("SELECT * FROM users WHERE id = $username");
                $stmt2->bindValue('id', $username, SQLITE3_INTEGER);
                $result = $stmt2->execute();
                $row = $result->fetchArray(SQLITE3_ASSOC);
                $user = $row['name'];
                ?>
                <div class="inneruser">
                    <img id="profilbild" src="../assets/img/uploads/users/<?php echo htmlspecialchars($row['profilbild'])?>" alt="">
                    <div id="userinfo">
                        <h1><?php echo htmlspecialchars($user);?></php></h1>
                        <a href="../pages/suche.php?filter=<?php echo htmlspecialchars($user);?>" class="center"><?php echo htmlspecialchars($somany);?> weitere Rezepte anzeigen<span class="material-symbols-outlined">keyboard_arrow_right</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="problem" class="center">
        <div id="melden" class="center">
            <span class="material-symbols-outlined">emergency_home</span>
            Problem melden
        </div>
    </div>
    <div id="footer"></div>
</body>
</html>