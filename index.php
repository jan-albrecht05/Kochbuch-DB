<!DOCTYPE html>
<html lang="de">
<head>
    <?php
        session_start();
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="Home - Kochbuch" />
    <meta property="og:description" content="Ein digitales Kochbuch, in das jeder seine Lieblingsrezepte hinzufügen kann." />
    <meta property="og:image" content="assets/icons/Topficon.png" />
    <meta property="og:url" content="https://mein-kochbuch-free.nf" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Home - Kochbuch" />
    <title>Home - Kochbuch</title>
    <link rel="icon" href="assets/icons/Topficon.png">
    <link rel="stylesheet" href="assets/css/root.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/heading.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script src="assets/js/horizontal_scroll.js" defer></script>
    <script>
        let homepage = true;
        //check if url contains "login=success" parameter
        const urlParams = new URLSearchParams(window.location.search);
        const loginSuccessPopup = document.getElementById("login-success");
        if (urlParams.has('login=success')) {
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById("login-success").classList.add("open");
            });
        }
    </script>
    <script src="assets/js/heading.js" defer></script>
    <script src="assets/js/links.js" defer></script>
</head>
<body>
    <div id="heading">
        <!-- Code gets injected by heading.js -->
    </div>
    <div class="popup positive center" id="login-success">
        <span class="material-symbols-outlined">tick</span>
        Du bist erfolgreich eingelogged!
    </div>
    <div id="main">
        <div id="random-Rezepte" class="container">
            <h2 onclick="window.location.href = 'pages/suche.php?filter=random'"><span>Lass dich überraschen</span> <span class="material-symbols-outlined">chevron_right</span></h2>
                <!--<div class="arrow arrow-left">
                    <span class="material-symbols-outlined">chevron_left</span>
                </div>
                <div class="arrow arrow-right">
                    <span class="material-symbols-outlined">chevron_right</span>
                </div>-->
            <div class="inner-container">
                <?php
                    if(file_exists("assets/db/gerichte.db")){
                        $db = new SQlite3("assets/db/gerichte.db");
                        $query = "SELECT * FROM gerichte WHERE status = 0 ORDER BY RANDOM() LIMIT 6";
                        $result = $db->query($query);
                        // Get the maximum id
                        $maxId = $db->querySingle("SELECT MAX(id) FROM gerichte");

                        if($result){
                            while ($row = $result->fetchArray(SQLITE3_ASSOC)){
                                echo '
                                    <div class="rezeptlink" id="'.htmlspecialchars($row['id']).'">
                                        <div class="tag-bereich">
                                            <div class="tag time">'.htmlspecialchars($row['zubereitungszeit']).'min</div>';
                                            if ($row['id'] >= $maxId - 5) {
                                                echo '<div class="tag neu">NEU!</div>';
                                            };
                                echo '  </div>
                                        <div class="img-container">';
                                        if(!empty($row['bild1'])){
                                            echo '<img src="assets/img/uploads/gerichte/'.htmlspecialchars($row['bild1']).'" alt="">';
                                        }
                                        else{
                                            echo '<img src="" alt="">';
                                        };
                                echo '
                                        </div>
                                        <div class="rezept-titel">
                                            <h2>'.htmlspecialchars($row['titel']).'</h2>
                                        </div>
                                        <div class="rezept-info">
                                            <p class="info">'.htmlspecialchars($row['beschreibung']).'</p>
                                        </div>
                                        <div class="tags">';
                                            if (!empty($row['tags'])) {
                                                $tags = explode(',', $row['tags']);
                                                foreach ($tags as $tag) {
                                                    echo '<span class="infotag">' . htmlspecialchars(trim($tag)) . '</span> ';
                                                }
                                            };
                                echo '      </div>
                                    </div>
                                ';
                            }
                        }
                    }
                ?>
            </div>
        </div>
        <div id="neue-Rezepte" class="container">
            <h2 onclick="window.location.href = 'pages/suche.php?filter=latest'"><span>Neueste Rezepte</span> <span class="material-symbols-outlined">chevron_right</span></h2>
            <div class="inner-container">
                <?php
                    if(file_exists("assets/db/gerichte.db")){
                        $db = new SQlite3("assets/db/gerichte.db");
                        $query = "SELECT * FROM gerichte WHERE status = 0 ORDER BY id DESC LIMIT 6";
                        $result = $db->query($query);
                        // Get the maximum id
                        $maxId = $db->querySingle("SELECT MAX(id) FROM gerichte");
                        if($result){
                            while ($row = $result->fetchArray(SQLITE3_ASSOC)){
                                echo '
                                    <div class="rezeptlink" id="'.htmlspecialchars($row['id']).'">
                                        <div class="tag-bereich">
                                            <div class="tag time">'.htmlspecialchars($row['zubereitungszeit']).'min</div>';
                                            if ($row['id'] >= $maxId - 5) {
                                                echo '<div class="tag neu">NEU!</div>';
                                            };
                                echo '  </div>
                                        <div class="img-container">';
                                        if(!empty($row['bild1'])){
                                            echo '<img src="assets/img/uploads/gerichte/'.htmlspecialchars($row['bild1']).'" alt="">';
                                        }
                                        else{
                                            echo '<img src="" alt="">';
                                        };
                                echo '
                                        </div>
                                        <div class="rezept-titel">
                                            <h2>'.htmlspecialchars($row['titel']).'</h2>
                                        </div>
                                        <div class="rezept-info">
                                            <p class="info">'.htmlspecialchars($row['beschreibung']).'</p>
                                        </div>
                                        <div class="tags">';
                                            if (!empty($row['tags'])) {
                                                $tags = explode(',', $row['tags']);
                                                foreach ($tags as $tag) {
                                                    echo '<span class="infotag">' . htmlspecialchars(trim($tag)) . '</span> ';
                                                }
                                            };
                                echo '      </div>
                                    </div>
                                ';
                            }
                        }
                    }
                ?>
            </div>
        </div>
        <div id="Favoriten" class="container">
            <h2 onclick="window.location.href = '#'"><span>Meine Favoriten</span> <span class="material-symbols-outlined">chevron_right</span></h2>
            <div class="inner-container">
                <p>Melde dich an, um deine Favoriten zu sehen.<br>
                <a style="color:var(--orange)!important; display:flex;align-items:center;cursor:pointer" onclick="window.location = `pages/login.php`">login<span class="material-symbols-outlined">open_in_new</span></a></p>
            </div>
        </div>
    </div>
    <div id="footer">
        <div id="inner-footer">
            <a href="pages/FAQ.html" class="center">FAQ <span class="material-symbols-outlined">open_in_new</span></a>
            <p>© 2023 Kochbuch</p>
            <p>Alle Rechte vorbehalten.</p>
            <a href="pages/login.php"class="center">login <span class="material-symbols-outlined">open_in_new</span></a>
        </div>
    </div>
</body>
</html>