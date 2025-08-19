<?php
session_start();

// Include database connection
$usersdb = new SQLite3("../assets/db/users.db");
$user_id = $_SESSION['user_id'];

// Check if user is logged in
if (!isset($user_id)) {
    header("Location: login.php?redirect=profil.php");
    exit;
}
// Redirect to meineRezepte if no query is set or query is invalid
$allowed_queries = ['meineRezepte', 'bookmarks', 'settings'];
$query = isset($_GET['query']) ? $_GET['query'] : '';
if (!in_array($query, $allowed_queries)) {
    header("Location: profil.php?query=meineRezepte");
    exit;
}

// profile Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    $profilbild_name = $profilbild; // default to current

    // Handle profile picture upload
    if (isset($_FILES['profilbild']) && $_FILES['profilbild']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['profilbild']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['profilbild']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $profilbild_name = uniqid('profile_') . '.' . $ext;
            move_uploaded_file($tmp_name, '../assets/img/uploads/users/' . $profilbild_name);
        }
    }

    // Password update logic
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $password_sql = '';
    if (!empty($password) || !empty($password2)) {
        if ($password === $password2) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $password_sql = ", password = :password";
        } else {
            // Passwords do not match, show error and stop
            echo '<div class="popup negative center" id="pwerror">
                    <span class="material-symbols-outlined">error</span>
                    Die Passwörter stimmen nicht überein!
                  </div>';
            return;
        }
    }

    // Update user in DB
    $stmt = $usersdb->prepare("UPDATE users SET name = :name, email_address = :email, profilbild = :profilbild $password_sql WHERE id = :id");
    $stmt->bindValue(':name', $new_name, SQLITE3_TEXT);
    $stmt->bindValue(':email', $new_email, SQLITE3_TEXT);
    $stmt->bindValue(':profilbild', $profilbild_name, SQLITE3_TEXT);
    if ($password_sql) {
        $stmt->bindValue(':password', $hashed, SQLITE3_TEXT);
    }
    $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
    $stmt->execute();

    // Update session name if changed
    $_SESSION['name'] = $new_name;

    // Optional: reload to show changes
    header("Location: profil.php?query=settings&success=1");
    exit;
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
    <title>"Mein Profil" - Kochbuch</title>
    <link rel="icon" href="../assets/icons/Topficon.png">
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/profil.css">
    <link rel="stylesheet" href="../assets/css/heading.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script src="../assets/js/horizontal_scroll.js" defer></script>
    <script>
        var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    </script>
    <script src="../assets/js/heading.js" defer></script>
    <script src="../assets/js/footer.js" defer></script>
    <script src="../assets/js/links2.js" defer></script>
</head>
<body>
    <div id="heading">
        <!-- Code gets injected by heading.js -->
    </div>
    <div id="sidebar">
        <!-- Code gets injected by heading.js -->
    </div>
    <?php if (isset($_GET['success']) && $_GET['success'] == '1') { ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('success1').classList.add("open");
            });
        </script>
    <?php } ?>
    <div class="popup positive center" id="success1">
        <span class="material-symbols-outlined">check</span>
        Profil erfolgreich aktualisiert!
    </div>
    <div id="main">
        <div id="profil">
            <?php
                $user_id = $_SESSION['user_id'];
                $row = $usersdb->query("SELECT name, email_address, profilbild FROM users WHERE id = $user_id")->fetchArray(SQLITE3_ASSOC);
                $username = $row ? $row['name'] : 'Gast';
                $email = $row ? $row['email_address'] : '';
                $profilbild = $row && !empty($row['profilbild']) ? $row['profilbild'] : 'default.png';
                echo '<img id="profilbild" src="../assets/img/uploads/users/'.htmlspecialchars($profilbild).'"><br>';
            ?>
            <h1>Hallo <?php echo htmlspecialchars($username); ?>!</h1>
            <div id="profillinks">
                <a href="einkaufsliste.php" class="link">
                    <span class="material-symbols-outlined">list</span>
                    <span>Einkaufsliste</span>
                </a>
                <?php
                //get query and add class to the link
                $links = [
                    'meineRezepte' => 'Meine Rezepte',
                    'bookmarks' => 'Gespeicherte Rezepte',
                    'settings' => 'Einstellungen'
                ];
                foreach ($links as $key => $value) {
                    $class = ($query === $key) ? 'link active' : 'link';
                    echo "<a href='profil.php?query=$key' class='$class'>
                            <span class='material-symbols-outlined'>" . ($key === 'meineRezepte' ? 'person' : ($key === 'bookmarks' ? 'bookmark' : 'settings')) . "</span>
                            <span>$value</span>
                          </a>";
                }
                ?>
                
            </div>
            <div id="result">
                <?php
                if ($user_id) {
                    switch ($query) {
                        case 'meineRezepte':
                            // User's own recipes
                            
                            $db = new SQLite3("../assets/db/gerichte.db");
                            $result = $db->query("SELECT * FROM gerichte WHERE made_by_id = $user_id");
                            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                                echo '
                                    <div class="rezeptlink" id="'.htmlspecialchars($row['id']).'">
                                        <div class="tag-bereich">
                                            <div class="tag time">'.htmlspecialchars($row['zubereitungszeit']).'min</div>
                                        </div>
                                        <div class="img-container">';
                                if(!empty($row['bild1'])){
                                    echo '<img src="../assets/img/uploads/gerichte/'.htmlspecialchars($row['bild1']).'" alt="">';
                                } else {
                                    echo '<img src="" alt="">';
                                }
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
                                }
                                echo '      </div>
                                    </div>
                                ';
                            }
                            break;

                        case 'bookmarks':
                            // Bookmarked recipes
                            $row = $usersdb->query("SELECT saved_recepies FROM users WHERE id = $user_id")->fetchArray(SQLITE3_ASSOC);
                            $saved = $row['saved_recepies'] ?? '';
                            $saved_array = array_filter(array_map('trim', explode(',', $saved)));
                            if (empty($saved_array)) {
                                echo '<p>Du hast noch keine Rezepte gespeichert.</p>';
                            } else {
                                $db = new SQLite3("../assets/db/gerichte.db");
                                $ids = implode(',', array_map('intval', $saved_array));
                                $result = $db->query("SELECT * FROM gerichte WHERE id IN ($ids) AND status = 0");
                                while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                                    echo '
                                        <div class="rezeptlink" id="'.htmlspecialchars($row['id']).'">
                                            <div class="tag-bereich">
                                                <div class="tag time">'.htmlspecialchars($row['zubereitungszeit']).'min</div>
                                            </div>
                                            <div class="img-container">';
                                    if(!empty($row['bild1'])){
                                        echo '<img src="../assets/img/uploads/gerichte/'.htmlspecialchars($row['bild1']).'" alt="">';
                                    } else {
                                        echo '<img src="" alt="">';
                                    }
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
                                    }
                                    echo '      </div>
                                        </div>
                                    ';
                                }
                            }
                            break;
                    }}
                    ?>
            </div>
            <div id="profil-einstellungen">
                    <?php
                    if ($user_id) {
                        switch ($query) {
                        case 'settings':
                            echo '
                            <h2>Profileinstellungen</h2>
                            <form method="post" id="einstellungen" enctype="multipart/form-data">
                                <div class="part">
                                    <label for="profilbild" style="cursor:pointer;">
                                        <img src="../assets/img/uploads/users/'.htmlspecialchars($profilbild).'"" alt="Profilbild" style="width:80px;height:80px;border-radius:50%;object-fit:cover;">
                                        <br>
                                        Profilbild ändern
                                    </label>
                                    <input type="file" id="profilbild" name="profilbild" accept="image/*">
                                </div>
                                <div class="part">
                                    <label for="name">Name:</label>
                                    <input type="text" id="name" name="name" value="'.htmlspecialchars($username).'" required>
                                </div>
                                <div class="part">
                                    <label for="email">E-Mail:</label>
                                    <input type="email" id="email" name="email" value="'.htmlspecialchars($email).'" required>
                                </div>
                                <div class="part">
                                    <label for="password">Passwort:</label>
                                    <input type="password" id="password" name="password" placeholder="Neues Passwort (optional)">
                                </div>
                                <div class="part">
                                    <label for="password2">Passwort wiederholen:</label>
                                    <input type="password" id="password2" name="password2" placeholder="Neues Passwort wiederholen">
                                </div>
                                <button type="submit" name="save_settings">Speichern</button>
                            </form>
                            ';
                            break;     
                    }
                }
                ?>
                
            </div>
        </div>
    </div>
    <div id="footer">
        <!-- Code gets injected by footer.js -->
    </div>
</body>
</html>