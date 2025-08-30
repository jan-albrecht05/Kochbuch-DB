<?php
session_start();

// Include database connection
$usersdb = new SQLite3("../assets/db/users.db");
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="Home - Kochbuch" />
    <meta property="og:description" content="Ein digitales Kochbuch, in das jeder seine Lieblingsrezepte hinzufügen kann." />
    <meta property="og:image" content="../assets/icons/Topficon.png" />
    <meta property="og:url" content="https://mein-kochbuch-free.nf" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Home - Kochbuch" />
    <title>Feedback geben - Kochbuch</title>
    <link rel="icon" href="../assets/icons/Topficon.png">
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/feedback.css">
    <link rel="stylesheet" href="../assets/css/heading.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script>
        var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        var isAdmin = <?php if(isset($_SESSION['rolle']) && $_SESSION['rolle'] == 'admin') {echo 'true';}else{echo'false';}; ?>;
        var isEditor = <?php if(isset($_SESSION['rolle']) && $_SESSION['rolle'] == 'editor') {echo 'true';}else{echo'false';}; ?>;
    </script>
    <script src="../assets/js/heading.js" defer></script>
    <script src="../assets/js/footer.js" defer></script>
    <script src="../assets/js/sections.js" defer></script>
</head>
<body>
    <div id="heading">
        <!-- Code gets injected by heading.js -->
    </div>
    <div id="sidebar">
        <!-- Code gets injected by heading.js -->
    </div>
    <form method="post" id="main" onsubmit="return false;">
        <div id="progress-bar">
            <?php $progress = "0%"; ?>
            <div id="progress" style="width: <?php echo $progress?>;"></div>
            <div id="progress-text" class="center"><?php echo $progress?></div>
        </div>
        <div class="section mid" id="start">
            <h1>Feedback geben</h1>
            <h3>Dein Feedback ist mir wichtig!</h3>
            <p>Auf den nächsten Seiten kannst du jede Seite kurz bewerten und mir zusätzliche Anmerkungen hinterlassen. So sehe ich, welche Seiten und Funktionen gut ankommen und wo ich noch etwas verbessern kann.</p>
            <p>Die Eingaben gehen ganz schnell: Einfach den Schieberegler bewegen und, wenn du magst, einen kurzen Kommentar schreiben. Am Ende klickst du nur noch auf „Absenden“.</p>
            <p>Die Beantwortung des Feedbackbogens dauert etwa 3 Minuten.</p>
            <p><b>Vielen Dank</b>, dass du dir die Zeit nimmst – dein Feedback hilft dabei, das Kochbuch noch besser zu machen!</p>
            <div class="buttons">
                <span></span>
                <button id="starten" onclick="nextPage()">Starten</button>
            </div>
        </div>
    <!-- Index -->
        <div class="section right" id="page1">
            <h1>Seite 1</h1>
            <h3>Wie bewertest du die Startseite?</h3>
            <div class="slider-input">
                <input type="range" min="1" max="10" step=".5" value="5.5" name="index" id="index"><br>
                <div class="lines">
                    <span>|</span>
                    <span>|</span>
                    <span>|</span>
                </div>
                <div class="caption">
                    <span>sehr schlecht</span>
                    <span>neutral</span>
                    <span>sehr gut</span>
                </div>
            </div>
            <div class="text-input">
                <textarea name="comment_index" id="comment_index" placeholder="Dein Kommentar..." resize="none"></textarea>
            </div>
            <div class="buttons">
                <button id="zurück" type="button" onclick="prevPage()">Zurück</button>
                <div class="page center">1 / 7</div>
                <button id="next" type="button" onclick="nextPage()">Nächste</button>
            </div>
        </div>
    <!-- Filter -->
        <div class="section right" id="page2">
            <h1>Seite 2</h1>
            <h3>Wie bewertest du die Filter?</h3>
            <div class="slider-input">
                <input type="range" min="1" max="10" step=".5" value="5.5" name="filter" id="filter"><br>
                <div class="lines">
                    <span>|</span>
                    <span>|</span>
                    <span>|</span>
                </div>
                <div class="caption">
                    <span>sehr schlecht</span>
                    <span>neutral</span>
                    <span>sehr gut</span>
                </div>
            </div>
            <div class="text-input">
                <textarea name="comment_filter" id="comment_filter" placeholder="Dein Kommentar..." resize="none"></textarea>
            </div>
            <div class="buttons">
                <button id="zurück" type="button" onclick="prevPage()">Zurück</button>
                <div class="page center">2 / 7</div>
                <button id="next" type="button" onclick="nextPage()">Nächste</button>
            </div>
        </div>
    <!-- Suche -->
        <div class="section right" id="page3">
            <h1>Seite 3</h1>
            <h3>Wie bewertest du die Suche?</h3>
            <div class="slider-input">
                <input type="range" min="1" max="10" step=".5" value="5.5" name="search" id="search"><br>
                <div class="lines">
                    <span>|</span>
                    <span>|</span>
                    <span>|</span>
                </div>
                <div class="caption">
                    <span>sehr schlecht</span>
                    <span>neutral</span>
                    <span>sehr gut</span>
                </div>
            </div>
            <div class="text-input">
                <textarea name="comment_search" id="comment_search" placeholder="Dein Kommentar..." resize="none"></textarea>
            </div>
            <div class="buttons">
                <button id="zurück" type="button" onclick="prevPage()">Zurück</button>
                <div class="page center">3 / 7</div>
                <button id="next" type="button" onclick="nextPage()">Nächste</button>
            </div>
        </div>
    <!-- Gericht -->
        <div class="section right" id="page4">
            <h1>Seite 4</h1>
            <h3>Wie bewertest du die Rezept-Seite?</h3>
            <div class="slider-input">
                <input type="range" min="1" max="10" step=".5" value="5.5" name="gericht" id="gericht"><br>
                <div class="lines">
                    <span>|</span>
                    <span>|</span>
                    <span>|</span>
                </div>
                <div class="caption">
                    <span>sehr schlecht</span>
                    <span>neutral</span>
                    <span>sehr gut</span>
                </div>
            </div>
            <div class="text-input">
                <textarea name="comment_gericht" id="comment_gericht" placeholder="Dein Kommentar..." resize="none"></textarea>
            </div>
            <div class="buttons">
                <button id="zurück" type="button" onclick="prevPage()">Zurück</button>
                <div class="page center">4 / 7</div>
                <button id="next" type="button" onclick="nextPage()">Nächste</button>
            </div>
        </div>
    <!-- Einkaufsliste -->
        <div class="section right" id="page5">
            <h1>Seite 5</h1>
            <h3>Wie bewertest du die Einkaufslisten?</h3>
            <div class="slider-input">
                <input type="range" min="1" max="10" step=".5" value="5.5" name="einkaufsliste" id="einkaufsliste"><br>
                <div class="lines">
                    <span>|</span>
                    <span>|</span>
                    <span>|</span>
                </div>
                <div class="caption">
                    <span>sehr schlecht</span>
                    <span>neutral</span>
                    <span>sehr gut</span>
                </div>
            </div>
            <div class="text-input">
                <textarea name="comment_einkaufsliste" id="comment_einkaufsliste" placeholder="Dein Kommentar..." resize="none"></textarea>
            </div>
            <div class="buttons">
                <button id="zurück" type="button" onclick="prevPage()">Zurück</button>
                <div class="page center">5 / 7</div>
                <button id="next" type="button" onclick="nextPage()">Nächste</button>
            </div>
        </div>
    <!-- Gericht -->
        <div class="section right" id="page6">
            <h1>Seite 6</h1>
            <h3>Wie bewertest du die Profil-Seite?</h3>
            <div class="slider-input">
                <input type="range" min="1" max="10" step=".5" value="5.5" name="profil" id="profil"><br>
                <div class="lines">
                    <span>|</span>
                    <span>|</span>
                    <span>|</span>
                </div>
                <div class="caption">
                    <span>sehr schlecht</span>
                    <span>neutral</span>
                    <span>sehr gut</span>
                </div>
            </div>
            <div class="text-input">
                <textarea name="comment_profil" id="comment_profil" placeholder="Dein Kommentar..." resize="none"></textarea>
            </div>
            <div class="buttons">
                <button id="zurück" type="button" onclick="prevPage()">Zurück</button>
                <div class="page center">6 / 7</div>
                <button id="next" type="button" onclick="nextPage()">Nächste</button>
            </div>
        </div>
    <!-- Gericht -->
        <div class="section right" id="page7">
            <h1>Seite 7</h1>
            <h3>Wie intuitiv ist das Kochbuch für dich?</h3>
            <div class="slider-input">
                <input type="range" min="1" max="10" step=".5" value="5.5" name="intuitiv" id="intuitiv"><br>
                <div class="lines">
                    <span>|</span>
                    <span>|</span>
                    <span>|</span>
                </div>
                <div class="caption">
                    <span>sehr schlecht</span>
                    <span>neutral</span>
                    <span>sehr gut</span>
                </div>
            </div>
            <div class="text-input">
                <textarea name="comment_intuitiv" id="comment_intuitiv" placeholder="Dein Kommentar..." resize="none"></textarea>
            </div>
            <div class="buttons">
                <button id="zurück" type="button" onclick="prevPage()">Zurück</button>
                <div class="page center">7 / 7</div>
                <button id="next" type="button" onclick="nextPage()" type="submit">Absenden</button>
            </div>
        </div>
    <!-- Gericht -->
        <div class="section right" id="finish">
            <h1>Vielen Dank für dein Feedback!</h1>
            <p>Wir schätzen deine Meinung und werden sie nutzen, um das Kochbuch zu verbessern.</p>
        </div>
    </form>
    <div id="footer">
        <!-- Code gets injected by footer.js -->
    </div>
</body>
</html>