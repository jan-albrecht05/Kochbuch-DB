<!DOCTYPE html>
<html lang="de">
<head>
    <?php
        session_start();
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Kochbuch</title>
    <link rel="icon" href="assets/icons/Topficon.png">
    <link rel="stylesheet" href="assets/css/root.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/heading.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script src="assets/js/horizontal_scroll.js" defer></script>
    <script src="assets/js/heading.js" defer></script>
</head>
<body>
    <div id="heading">
        <!-- Code gets injected by heading.js -->
    </div>
    <div id="main">
        <div id="random-Rezepte" class="container">
            <h2 onclick="window.location.href = 'pages/search.php?random'"><span>Lass dich überraschen</span> <span class="material-symbols-outlined">chevron_right</span></h2>
                <!--<div class="arrow arrow-left">
                    <span class="material-symbols-outlined">chevron_left</span>
                </div>
                <div class="arrow arrow-right">
                    <span class="material-symbols-outlined">chevron_right</span>
                </div>-->
            <div class="inner-container">
                <div class="rezeptlink" id="1">
                    <div class="tag time">25min</div>
                    <div class="tag neu">NEU!</div>
                    <div class="img-container">
                        <img src="https://mein-kochbuch.netlify.app/bilder/gerichte/NudelsalatmitMandarienen.png" alt="Rezeptbild">
                    </div>
                    <div class="rezept-titel">
                        <h2>Rezept Titel</h2>
                    </div>
                    <div class="rezept-info">
                        <p class="info">Kurzbeschreibung des Rezepts.</p>
                        <p class="info">
                            <span class="infotag">Asiatisch</span>
                            <span class="infotag">Nudeln</span>
                        </p>
                    </div>
                </div>
                <div class="rezeptlink" id="1">
                    <div class="tag time">25min</div>
                    <div class="tag neu">NEU!</div>
                    <div class="img-container">
                        <img src="assets/icons/no-img.png" alt="Rezeptbild">
                    </div>
                    <div class="rezept-titel">
                        <h2>Rezept Titel</h2>
                    </div>
                    <div class="rezept-info">
                        <p class="info">Kurzbeschreibung des Rezepts.</p>
                    </div>
                </div>
                <div class="rezeptlink" id="1">
                    <div class="tag time">25min</div>
                    <div class="tag neu">NEU!</div>
                    <div class="img-container">
                        <img src="assets/icons/no-img.png" alt="Rezeptbild">
                    </div>
                    <div class="rezept-titel">
                        <h2>Rezept Titel</h2>
                    </div>
                    <div class="rezept-info">
                        <p class="info">Kurzbeschreibung des Rezepts.</p>
                        <p class="info">Tags: Asiatisch, Nudeln</p>
                    </div>
                </div>
                <div class="rezeptlink" id="1">
                    <div class="tag time">25min</div>
                    <div class="tag neu">NEU!</div>
                    <div class="img-container">
                        <img src="assets/icons/no-img.png" alt="Rezeptbild">
                    </div>
                    <div class="rezept-titel">
                        <h2>Rezept Titel</h2>
                    </div>
                    <div class="rezept-info">
                        <p class="info">Kurzbeschreibung des Rezepts.</p>
                        <p class="info">Tags: Asiatisch, Nudeln</p>
                    </div>
                </div>
                <div class="rezeptlink" id="1">
                    <div class="tag time">25min</div>
                    <div class="tag neu">NEU!</div>
                    <div class="img-container">
                        <img src="assets/icons/no-img.png" alt="Rezeptbild">
                    </div>
                    <div class="rezept-titel">
                        <h2>Rezept Titel</h2>
                    </div>
                    <div class="rezept-info">
                        <p class="info">Kurzbeschreibung des Rezepts.</p>
                        <p class="info">Tags: Asiatisch, Nudeln</p>
                    </div>
                </div>
                <div class="rezeptlink" id="1">
                    <div class="tag time">25min</div>
                    <div class="tag neu">NEU!</div>
                    <div class="img-container">
                        <img src="assets/icons/no-img.png" alt="Rezeptbild">
                    </div>
                    <div class="rezept-titel">
                        <h2>Rezept Titel</h2>
                    </div>
                    <div class="rezept-info">
                        <p class="info">Kurzbeschreibung des Rezepts.</p>
                        <p class="info">Tags: Asiatisch, Nudeln</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="neue-Rezepte" class="container">
            <h2 onclick="window.location.href = 'pages/search.php?latest'"><span>Neueste Rezepte</span> <span class="material-symbols-outlined">chevron_right</span></h2>
            <div class="inner-container">
                
                <div class="rezeptlink" id="1">
                    <div class="tag time">25min</div>
                    <div class="tag neu">NEU!</div>
                    <div class="img-container">
                        <img src="assets/icons/no-img.png" alt="Rezeptbild">
                    </div>
                    <div class="rezept-titel">
                        <h2>Rezept Titel</h2>
                    </div>
                    <div class="rezept-info">
                        <p class="info">Kurzbeschreibung des Rezepts.</p>
                        <p class="info">Tags: Asiatisch, Nudeln</p>
                    </div>
                </div>
                <div class="rezeptlink" id="1">
                    <div class="tag time">25min</div>
                    <div class="tag neu">NEU!</div>
                    <div class="img-container">
                        <img src="assets/icons/no-img.png" alt="Rezeptbild">
                    </div>
                    <div class="rezept-titel">
                        <h2>Rezept Titel</h2>
                    </div>
                    <div class="rezept-info">
                        <p class="info">Kurzbeschreibung des Rezepts.</p>
                        <p class="info">Tags: Asiatisch, Nudeln</p>
                    </div>
                </div>
                <div class="rezeptlink" id="1">
                    <div class="tag time">25min</div>
                    <div class="tag neu">NEU!</div>
                    <div class="img-container">
                        <img src="assets/icons/no-img.png" alt="Rezeptbild">
                    </div>
                    <div class="rezept-titel">
                        <h2>Rezept Titel</h2>
                    </div>
                    <div class="rezept-info">
                        <p class="info">Kurzbeschreibung des Rezepts.</p>
                        <p class="info">Tags: Asiatisch, Nudeln</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="neue-Rezepte" class="container">
            <h2 onclick="window.location.href = 'pages/search.php?saved'"><span>Meine Favoriten</span> <span class="material-symbols-outlined">chevron_right</span></h2>
            <div class="inner-container">
                
                <div class="rezeptlink" id="1">
                    <div class="tag time">25min</div>
                    <div class="tag neu">NEU!</div>
                    <div class="img-container">
                        <img src="assets/icons/no-img.png" alt="Rezeptbild">
                    </div>
                    <div class="rezept-titel">
                        <h2>Rezept Titel</h2>
                    </div>
                    <div class="rezept-info">
                        <p class="info">Kurzbeschreibung des Rezepts.</p>
                        <p class="info">Tags: Asiatisch, Nudeln</p>
                    </div>
                </div>
                <div class="rezeptlink" id="1">
                    <div class="tag time">25min</div>
                    <div class="tag neu">NEU!</div>
                    <div class="img-container">
                        <img src="assets/icons/no-img.png" alt="Rezeptbild">
                    </div>
                    <div class="rezept-titel">
                        <h2>Rezept Titel</h2>
                    </div>
                    <div class="rezept-info">
                        <p class="info">Kurzbeschreibung des Rezepts.</p>
                        <p class="info">Tags: Asiatisch, Nudeln</p>
                    </div>
                </div>
                <div class="rezeptlink" id="1">
                    <div class="tag time">25min</div>
                    <div class="tag neu">NEU!</div>
                    <div class="img-container">
                        <img src="assets/icons/no-img.png" alt="Rezeptbild">
                    </div>
                    <div class="rezept-titel">
                        <h2>Rezept Titel</h2>
                    </div>
                    <div class="rezept-info">
                        <p class="info">Kurzbeschreibung des Rezepts.</p>
                        <p class="info">Tags: Asiatisch, Nudeln</p>
                    </div>
                </div>
                <div class="rezeptlink" id="1">
                    <div class="tag time">25min</div>
                    <div class="tag neu">NEU!</div>
                    <div class="img-container">
                        <img src="assets/icons/no-img.png" alt="Rezeptbild">
                    </div>
                    <div class="rezept-titel">
                        <h2>Rezept Titel</h2>
                    </div>
                    <div class="rezept-info">
                        <p class="info">Kurzbeschreibung des Rezepts.</p>
                        <p class="info">Tags: Asiatisch, Nudeln</p>
                    </div>
                </div>
                <div class="rezeptlink" id="1">
                    <div class="tag time">25min</div>
                    <div class="tag neu">NEU!</div>
                    <div class="img-container">
                        <img src="assets/icons/no-img.png" alt="Rezeptbild">
                    </div>
                    <div class="rezept-titel">
                        <h2>Rezept Titel</h2>
                    </div>
                    <div class="rezept-info">
                        <p class="info">Kurzbeschreibung des Rezepts.</p>
                        <p class="info">Tags: Asiatisch, Nudeln</p>
                    </div>
                </div>
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