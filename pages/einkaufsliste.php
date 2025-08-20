<?php
session_start();

// Include database connection
$usersdb = new SQLite3("../assets/db/users.db");
$user_id = $_SESSION['user_id'];

// Check if user is logged in
if (!isset($user_id)) {
    header("Location: login.php?redirect=einkaufsliste.php");
    exit;

}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id']) && !empty($_POST['item_id']) && !empty($_POST['list_id'])) {
    // Check if the check button was clicked (not the select)
    if (!isset($_POST['item_action'])) {
        $item_id = intval($_POST['item_id']);
        $list_id = intval($_POST['list_id']);
        $stmt = $usersdb->prepare("UPDATE Einkaufsliste SET status = 1 WHERE id = :item_id AND list_id = :list_id");
        $stmt->bindValue(':item_id', $item_id, SQLITE3_INTEGER);
        $stmt->bindValue(':list_id', $list_id, SQLITE3_INTEGER);
        $stmt->execute();
        header("Location: einkaufsliste.php?list=$list_id");
        exit;
    }
}
$lists = $usersdb->query("SELECT * FROM shopping_lists WHERE user_id = $user_id");
$list = $lists->fetchArray(SQLITE3_ASSOC);
if (!$list) {
    // If no list is selected, redirect to the first list or create a new one
    $list = 'Einkaufsliste';
    exit;
}
?>
<?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_name']) && isset($_POST['item_quantity'])) {
                $item_name = SQLite3::escapeString($_POST['item_name']);
                $item_quantity = intval($_POST['item_quantity']);
                $item_einheit = SQLite3::escapeString($_POST['item_einheit']);
                $list_id = intval($_POST['list_id']);

                if ($list_id > 0) {
                    $stmt = $usersdb->prepare("INSERT INTO Einkaufsliste (zutat, menge, einheit, list_id) VALUES (:name, :quantity, :einheit, :list_id)");
                    $stmt->bindValue(':name', $item_name, SQLITE3_TEXT);
                    $stmt->bindValue(':quantity', $item_quantity, SQLITE3_INTEGER);
                    $stmt->bindValue(':einheit', $item_einheit, SQLITE3_TEXT);
                    $stmt->bindValue(':list_id', $list_id, SQLITE3_INTEGER);

                    if ($stmt->execute()) {
                        header("Location: einkaufsliste.php?list=$list_id");
                        exit;
                    } else {
                        echo "<p>Fehler beim Hinzufügen des Items.</p>";
                    }
                } else {
                    echo "<p>Ungültige Liste ausgewählt.</p>";
                }
            }
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
    <title><?php echo $list['name']?> - Kochbuch</title>
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
            $hasItems = false;
            $hidden = '';
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
                    $items = $usersdb->query("SELECT * FROM Einkaufsliste WHERE list_id = $list_id ORDER BY laden ASC");
                
                    while ($item = $items->fetchArray(SQLITE3_ASSOC)) {
                        $hasItems = true;
                        if (empty($item['einheit'])){
                            $item['einheit'] = 'Stk'; // Default unit if none provided
                        }
                        $class = ($item['status'] == 1) ? 'checked' : '';
                        $disabled = ($item['status'] == 1) ? 'disabled' : '';
                        echo "
                            <div class='item $class'>
                                <div>{$item['menge']} {$item['einheit']} <b>{$item['zutat']}</b></div>
                                <form method='POST' action='einkaufsliste.php?list={$list_id}' class='delete-item-form'>
                                    <input type='hidden' name='item_id' value='{$item['id']}'>
                                    <select name='item_action' onchange='this.form.submit()' $disabled>
                                        <option value='' disabled selected></option>
                                        <option value='Aldi'>Aldi</option>
                                        <option value='Lidl'>Lidl</option>
                                        <option value='Netto'>Netto</option>
                                        <option value='Rossmann'>Rossmann</option>
                                    </select>
                                    <input type='hidden' name='list_id' value='{$list_id}'>
                                    <button type='submit' class='check-btn' $disabled>
                                        <span class='material-symbols-outlined'>check</span>
                                    </button>
                                </form>
                            </div>";
                    }
                    if (!$hasItems) {
                        echo "<i>Keine Einträge in dieser Liste.</i>";
                        $hidden = 'style="display:none;"';
                    }
                    echo '
                        <div id="timestamp">
                            <p>Erstellt am: ' . date('d.m.Y H:i', $list['created_at']) . '</p>
                        </div>
                    ';
                    echo '
                        <div id="controls">
                            <button id="clean-list-btn" class="center"'. $hidden.'>
                                <span class="material-symbols-outlined">cleaning_services</span>
                                <span>Liste bereinigen</span>
                            </button>
                            <button id="delete-list-btn" class="center" onclick="if(confirm(\'Bist du sicher, dass du diese Liste löschen möchtest?\')) { location.href=\'einkaufsliste.php\'; }">
                                <span class="material-symbols-outlined">delete</span>
                                <span>Liste löschen</span>
                            </button>
                        </div>
                    ';
                } else {
                    echo "<h2>Liste nicht gefunden</h2>";
                }
            } else {
                echo '<h2>Alle Einträge</h2>';
                // Get all items from all lists of this user
                $all_items = $usersdb->query("
                    SELECT Einkaufsliste.*, shopping_lists.name AS list_name
                    FROM Einkaufsliste
                    JOIN shopping_lists ON Einkaufsliste.list_id = shopping_lists.id
                    WHERE shopping_lists.user_id = $user_id
                    ORDER BY Einkaufsliste.list_id, Einkaufsliste.id
                ");
                $hasItems = false;
                while ($item = $all_items->fetchArray(SQLITE3_ASSOC)) {
                    $hasItems = true;
                    if (empty($item['einheit'])) {
                        $item['einheit'] = 'Stk';
                    }
                    $class = ($item['status'] == 1) ? 'checked' : '';
                    $disabled = ($item['status'] == 1) ? 'disabled' : '';
                    echo "
                        <div class='item $class'>
                            <div>
                                {$item['menge']} {$item['einheit']} <b>{$item['zutat']}</b>
                            </div>
                <form method='POST' action='einkaufsliste.php' class='delete-item-form'>
                    <input type='hidden' name='item_id' value='{$item['id']}'>
                    <select name='item_action' onchange='this.form.submit()' $disabled>
                        <option value='' disabled selected></option>
                        <option value='Aldi'>Aldi</option>
                        <option value='Lidl'>Lidl</option>
                        <option value='Netto'>Netto</option>
                        <option value='Rossmann'>Rossmann</option>
                    </select>
                    <input type='hidden' name='list_id' value='{$item['list_id']}'>
                    <button type='submit' class='check-btn center' $disabled>
                        <span class='material-symbols-outlined'>check</span>
                    </button>
                </form>
            </div>";
                    }
                    echo '
                            <div id="controls">
                                <button id="clean-list-btn" class="center"'. $hidden.'>
                                    <span class="material-symbols-outlined">cleaning_services</span>
                                    <span>Listen bereinigen</span>
                                </button>
                            </div>
                        ';
                    if (!$hasItems) {
                        echo "<p>Du hast noch keine Einträge in deinen Listen.</p>";
                    }
                }
            ?>
        </div>
    </div>
    <button id="add-item-btn" class="center" onclick="document.getElementById('add-item-popup').style.display='block'">
        <span class="material-symbols-outlined">add</span>
    </button>
    <div id="add-item-popup">
        <form method="POST" id="add-item-form">
            <h3>Etwas zur Einkaufsliste hinzufügen</h3>
            <input type="text" name="item_name" placeholder="Name *" required>
            <input type="number" name="item_quantity" placeholder="Menge *" required>
            <input type="text" name="item_einheit" placeholder="Einheit (optional)">
            <input type="hidden" name="list_id" value="<?php echo isset($_GET['list']) ? intval($_GET['list']) : ''; ?>">
            <select name="item_laden">
                <option value="" selected> jeder Laden</option>
                <option value="Aldi">Aldi</option>
                <option value="Lidl">Lidl</option>
                <option value="Netto">Netto</option>
                <option value="Rossmann">Rossmann</option>
            </select>
            <p>* Pflichtfeld</p>
            <div id="controls">
                <button type="button" class="center" onclick="document.getElementById('add-item-popup').style.display='none'">Abbrechen</button>
                <button type="submit">Hinzufügen</button>
            </div>
        </form>
    </div>
    <div id="footer">
        <!-- Code gets injected by footer.js -->
    </div>
</body>
</html>