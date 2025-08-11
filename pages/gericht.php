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
                <form method="POST">
                    <button id="savebtn" name="savebtn" type="submit">
                        <span class="material-symbols-outlined">bookmark</span>
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
                        $dt = new DateTime($row['timecode_erstellt'], new DateTimeZone('UTC'));
                        echo $dt->format('d.m.Y');
                    ?>
                </span>
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
        <div id="zuletzt-geändert">
            <?php
                if ($row['timecode_geaendert'] != '0000-00-00 00:00:00') {
                    $dt = new DateTime($row['timecode_geaendert'], new DateTimeZone('UTC'));
                    echo 'Zuletzt geändert am: ' . $dt->format('d.m.Y');
                }
            ?>
        </div>
        <div id="bewerten">
            <form id="bewertungsform" method="POST" class="center" onchange="">
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
        <div id="melden" class="center">
            <span class="material-symbols-outlined">emergency_home</span>
            Problem melden
        </div>
    </div>
    <div id="footer"></div>
    <?php
    // save button functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['savebtn'])) {
    $user_id = $_SESSION['user_id'];
    $recipe_id = $id;
    $users = new SQLite3("../assets/db/users.db");
    $stmt = $users->prepare("SELECT saved_recepies FROM users WHERE id = :id");
    $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    $saved = $row['saved_recepies'] ?? '';
    $saved_array = array_filter(array_map('trim', explode(',', $saved)));
    if (!in_array($recipe_id, $saved_array)) {
        $saved_array[] = $recipe_id;
        $new_saved = implode(',', $saved_array);
        $update = $users->prepare("UPDATE users SET saved_recepies = :saved WHERE id = :id");
        $update->bindValue(':saved', $new_saved, SQLITE3_TEXT);
        $update->bindValue(':id', $user_id, SQLITE3_INTEGER);
        $update->execute();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['star_rating'])) {
    $rating = intval($_POST['star_rating']);
    if ($rating >= 1 && $rating <= 5) {
        $column = 'star' . $rating;
        $stmt = $db->prepare("UPDATE gerichte SET $column = $column + 1 WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
    }
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
</body>
</html>