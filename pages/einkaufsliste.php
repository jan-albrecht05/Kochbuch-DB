<?php
session_start();

// Include database connection
$usersdb = new SQLite3("../assets/db/users.db");
$user_id = $_SESSION['user_id'];

// Check if user is logged in
if (!isset($user_id)) {
    header("Location: login.php?redirect=einkaufsliste.php");
    exit;

}?>
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
    <title>Einkaufsliste - Kochbuch</title>
    <link rel="icon" href="../assets/icons/Topficon.png">
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/listen.css">
    <link rel="stylesheet" href="../assets/css/heading.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script src="../assets/js/horizontal_scroll.js" defer></script>
    <script>
        var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
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
    <div id="main">
        <div id="top-bar">
            <div id="left">
                <h2>Wähle eine Liste aus:</h2>
                <select id="list-select" onchange="location = this.value;">
                    <?php
                    $lists = $usersdb->query("SELECT * FROM shopping_lists WHERE user_id = $user_id");
                    $hasLists = false;
                    $listsArray = [];
                    while ($list = $lists->fetchArray(SQLITE3_ASSOC)) {
                        $hasLists = true;
                        $listsArray[] = $list;
                    }
                    if (!$hasLists) {
                        echo "<option disabled>Keine Listen gefunden</option>";
                    } else {
                        echo "<option value='einkaufsliste.php'>Alle Listen</option>";
                        foreach ($listsArray as $list) {
                            $selected = (isset($_GET['list']) && $_GET['list'] == $list['id']) ? 'selected' : '';
                            echo "<option value='einkaufsliste.php?list={$list['id']}' $selected>{$list['name']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div id="right">
                <a href="einkaufsliste.php?list=new" id="new-list-btn" class="center">
                    <span class="material-symbols-outlined">add</span>
                    <span>Neue Liste erstellen</span>
                </a>
            </div>
        </div>
        <div id="list-container">
            <?php
            if (isset($_GET['list']) && $_GET['list'] == 'new') {
                echo '<h2>Neue Einkaufsliste erstellen</h2>';
                echo '<form method="POST" id="new-list-form">';
                echo '  <input type="text" name="list_name" placeholder="Name der Liste" required>';
                echo '  <input type="hidden" name="timecode" value"<?php echo time(); ?>';
                echo '  <input type="hidden" name="user_id" value="' . $user_id . '">';
                echo '  <button type="submit">Erstellen</button>';
                echo '  <button type="button" class="center" onclick="location.href=\'einkaufsliste.php\'"><span class="material-symbols-outlined">close</span></button>';
                echo '</form>';
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['list_name'])) {
                    $list_name = SQLite3::escapeString($_POST['list_name']);
                    $stmt = $usersdb->prepare("INSERT INTO shopping_lists (name, user_id, created_at) VALUES (:name, :user_id, :timecode)");
                    $stmt->bindValue(':name', $list_name, SQLITE3_TEXT);
                    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
                    $stmt->bindValue(':timecode', time(), SQLITE3_INTEGER);

                    if ($stmt->execute()) {
                        header("Location: einkaufsliste.php?list=" . $usersdb->lastInsertRowID());
                        exit;
                    } else {
                        echo "<p>Fehler beim Erstellen der Liste.</p>";
                    }
                }
            } elseif (isset($_GET['list'])) {
                $list_id = intval($_GET['list']);
                $list = $usersdb->querySingle("SELECT * FROM shopping_lists WHERE id = $list_id AND user_id = $user_id", true);
                if ($list) {
                    echo "<h2>{$list['name']}</h2>";
                    // Display items in the list
                    $items = $usersdb->query("SELECT * FROM Einkaufsliste WHERE list_id = $list_id");
                    while ($item = $items->fetchArray(SQLITE3_ASSOC)) {
                        echo "<div class='item'>{$item['name']} <span class='quantity'>({$item['quantity']})</span></div>";
                    }
                } else {
                    echo "<h2>Liste nicht gefunden</h2>";
                }
            } else {
                echo '<h2>Meine Einkaufsliste</h2>';
                // Display all lists
                foreach ($listsArray as $list) {
                    echo "<div class='list-item'><a href='einkaufsliste.php?list={$list['id']}'>{$list['name']}</a></div>";
                }
            }
            ?>
        </div>
    </div>
    <button id="add-item-btn" class="center">
        <span class="material-symbols-outlined">add</span>
    </button>
    <div id="add-item-popup">
        <form>

        </form>
    </div>
    <div id="footer">
        <!-- Code gets injected by footer.js -->
    </div>
</body>
</html>