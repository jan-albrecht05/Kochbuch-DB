<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        session_start(); 
        date_default_timezone_set('Europe/Berlin');

        // CSRF token generation
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $csrf_token = $_SESSION['csrf_token'];
    ?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Kochbuch</title>
    <script>
        var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        var isAdmin = <?php echo (isset($_SESSION['rolle']) && $_SESSION['rolle'] == 'admin') ? 'true' : 'false'; ?>;
        var isEditor = <?php echo (isset($_SESSION['rolle']) && $_SESSION['rolle'] == 'editor') ? 'true' : 'false'; ?>;
    </script>
    <link rel="icon" href="../assets/icons/Topficon.png">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/heading.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
    <script src="../assets/js/heading.js" defer></script>
    <script src="../assets/js/footer.js" defer></script>
</head>
<body>
<?php
// Only allow admins
if (!isset($_SESSION['rolle']) || $_SESSION['rolle'] !== 'admin') {
    header("Location: login.php?redirect=admin-panel.php");
    exit;
}
// CSRF check helper
function check_csrf() {
    return isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}
// sets status to 0 (= active)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_status'])) {
    $rezepte = new SQLite3("../assets/db/gerichte.db");
    $id = intval($_POST['set_status_id']);
    $stmt = $rezepte->prepare("UPDATE gerichte SET status = 0 WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    if ($stmt->execute()) {
        header("Location: admin-panel.php");
        exit;
    } else {
        echo "<div style='color:red;'>Fehler beim Status-Update!</div>";
    }
}
// deletes a recepie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_rezept'])) {
    $rezepte = new SQLite3("../assets/db/gerichte.db");
    $id = intval($_POST['delete_rezept_id']);

    // 1. Delete images
    $stmt = $rezepte->prepare("SELECT bild1, bild2, bild3 FROM gerichte WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    foreach (['bild1', 'bild2', 'bild3'] as $imgField) {
        if (!empty($result[$imgField])) {
            $imgPath = "../assets/img/uploads/gerichte/" . $result[$imgField];
            if (file_exists($imgPath)) {
                @unlink($imgPath);
            }
        }
    }

    // 2. Delete zutaten
    $stmt = $rezepte->prepare("DELETE FROM zutaten WHERE gerichte_id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();

    // 3. Delete schritte
    $stmt = $rezepte->prepare("DELETE FROM schritte WHERE gerichte_id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();

    // 4. Delete gerichte entry
    $stmt = $rezepte->prepare("DELETE FROM gerichte WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    if ($stmt->execute()) {
        header("Location: admin-panel.php");
        exit;
    } else {
        echo "<div style='color:red;'>Fehler beim Löschen des Rezepts!</div>";
    }
}
//clears error message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_error'])) {
    $rezepte = new SQLite3("../assets/db/gerichte.db");
    $id = intval($_POST['clear_error_id']);
    $stmt = $rezepte->prepare("UPDATE gerichte SET error_msg = '' WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    if ($stmt->execute()) {
        header("Location: admin-panel.php");
        exit;
    } else {
        echo "<div style='color:red;'>Fehler beim Entfernen der Fehlermeldung!</div>";
    }
}
$usercount = 0;
$rezeptcount = 0;
$offene_rezepte = 0;
$fehler = 0;

// --- Handle Add User ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $db = new SQLite3("../assets/db/users.db");
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rolle = $_POST['rolle'];
    $mail = $_POST['mail'];
    $profileImgName = '';

    // Handle profile picture upload
    if (isset($_FILES['profilbild']) && $_FILES['profilbild']['error'] === UPLOAD_ERR_OK) {
        $allowedimgtypes = array("png", "jpeg", "jpg", "JPG", "ico", "PNG", "JPG", "JPEG");
        $img_extention = pathinfo($_FILES['profilbild']['name'], PATHINFO_EXTENSION);
        if (in_array(strtolower($img_extention), $allowedimgtypes)) {
            $profileImgName = $username . '.' . $img_extention;
            $targetpathProfileImage = "../assets/img/uploads/users/" . $profileImgName;
            move_uploaded_file($_FILES['profilbild']['tmp_name'], $targetpathProfileImage);
        }
    }

    // Check if username exists
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE name = :name");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if ($res['count'] > 0) {
        echo "<div style='color:red;'>Benutzername existiert bereits!</div>";
    } else {
        $stmt = $db->prepare("INSERT INTO users (name, password, rolle, profilbild, email_address) VALUES (:username, :password, :rolle, :profilbild, :mail)");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':password', $password, SQLITE3_TEXT);
        $stmt->bindValue(':rolle', $rolle, SQLITE3_TEXT);
        $stmt->bindValue(':mail', $mail, SQLITE3_TEXT);
        $stmt->bindValue(':profilbild', $profileImgName, SQLITE3_TEXT);
        if ($stmt->execute()) {
            header("Location: admin-panel.php");
            exit;
        } else {
            echo "<div style='color:red;'>Fehler beim Hinzufügen!</div>";
        }
    }
}

// --- Handle Delete User ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user']) && check_csrf()) {
    $db = new SQLite3("../assets/db/users.db");
    $user_id = intval($_POST['delete_user_id']);
    $stmt = $db->prepare("SELECT name FROM users WHERE id = :id");
    $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
    $username = $stmt->execute()->fetchArray(SQLITE3_ASSOC)['name'];
    $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
    if ($stmt->execute()) {
        // Log the event
        $logs_db = new SQLite3("../assets/db/logs.db");
        $ip = $_SERVER['REMOTE_ADDR'];
        $event = 'Benutzer '.$username.' gelöscht';
        $log_stmt = $logs_db->prepare("INSERT INTO logs (user, event_type, event, timecode, 'IP-Adresse') VALUES (:name, :event_type, :event, :timecode, :ip)");
        $log_stmt->bindValue(':name', $_SESSION['name'], SQLITE3_TEXT);
        $log_stmt->bindValue(':event_type', 'Konto-Löschung', SQLITE3_TEXT);
        $log_stmt->bindValue(':event', $event, SQLITE3_TEXT);
        $log_stmt->bindValue(':timecode', time(), SQLITE3_INTEGER);
        $log_stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
        $log_stmt->execute();
        header("Location: admin-panel.php");
        exit;
    } else {
        echo "<div style='color:red;'>Fehler beim Löschen!</div>";
    }
}

// --- Handle Edit User ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user']) && check_csrf()) {
    $db = new SQLite3("../assets/db/users.db");
    $user_id = intval($_POST['edit_user_id']);
    $username = trim($_POST['username']);
    $rolle = $_POST['rolle'];
    $mail = $_POST['edit-mail'];
    $fields = ['name = :username', 'rolle = :rolle', 'email_address = :mail'];
    $params = [
        ':username' => $username,
        ':rolle' => $rolle,
        ':id' => $user_id,
        ':mail' => $mail
    ];

    // If password is set, update it
    if (!empty($_POST['password'])) {
        $fields[] = 'password = :password';
        $params[':password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    // Handle profile picture upload
    if (isset($_FILES['profilbild']) && $_FILES['profilbild']['error'] === UPLOAD_ERR_OK) {
        $allowedimgtypes = array("png", "jpeg", "jpg", "JPG", "ico");
        $img_extention = pathinfo($_FILES['profilbild']['name'], PATHINFO_EXTENSION);
        if (in_array(strtolower($img_extention), $allowedimgtypes)) {
            $profileImgName = strtolower($username) . '.' . $img_extention;
            $targetpathProfileImage = "../assets/img/uploads/users/" . $profileImgName;
            move_uploaded_file($_FILES['profilbild']['tmp_name'], $targetpathProfileImage);
            $fields[] = 'profilbild = :profilbild';
            $params[':profilbild'] = $profileImgName;
        }
    }
    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        if ($key === ':id') {
            $stmt->bindValue($key, $value, SQLITE3_INTEGER);
        } else {
            $stmt->bindValue($key, $value, SQLITE3_TEXT);
        }
    }
    if ($stmt->execute()) {
        header("Location: admin-panel.php");
        exit;
    } else {
        echo "<div style='color:red;'>Fehler beim Bearbeiten!</div>";
    }
}
// get number of users
        $users = new SQLite3("../assets/db/users.db");
        $result = $users->query("SELECT * FROM users");
        while ($user = $result->fetchArray(SQLITE3_ASSOC)) {
            $usercount ++;
        }
// get number of recepies
        $rezepte = new SQLite3("../assets/db/gerichte.db");
        $result = $rezepte->query("SELECT * FROM gerichte");
        while ($rezept = $result->fetchArray(SQLITE3_ASSOC)) {
            $rezeptcount ++;
        };
        $result2 = $rezepte->query("SELECT * FROM gerichte WHERE error_msg != ''");
        while ($rezept2 = $result2->fetchArray(SQLITE3_ASSOC)) {
            $fehler ++;
        };
        $result3 = $rezepte->query("SELECT * FROM gerichte WHERE status != 0");
        while ($rezept2 = $result3->fetchArray(SQLITE3_ASSOC)) {
            $offene_rezepte ++;
        };
?>
    <div id="heading">
        <!-- Code gets injected by heading.js -->
    </div>
    <div id="sidebar">
        <!-- Code gets injected by heading.js -->
    </div>
    <div id="main">
        <div id="stats" class="section">
            <div class="inner">
                <a class="stat" href="#user-heading">
                    <h1 class="zahl"><?php echo htmlspecialchars($usercount)?></h1>
                    <h3 class="beschreibung">Benutzer</h3>
                </a>
                <div class="stat">
                    <h1 class="zahl"><?php echo htmlspecialchars($rezeptcount)?></h1>
                    <h3 class="beschreibung">Rezepte</h3>
                </div>
                <a class="stat" href="#open_recepies">
                    <h1 class="zahl"><?php echo htmlspecialchars($offene_rezepte)?></h1>
                    <h3 class="beschreibung">offene Rezepte</h3>
                </a>
                <a class="stat" href="#errors">
                    <h1 class="zahl" <?php if($fehler!=0){echo 'style="color:red"';}?>><?php echo htmlspecialchars($fehler)?></h1>
                    <h3 class="beschreibung" <?php if($fehler!=0){echo 'style="color:red"';}?>>Fehler</h3>
                </a>
            </div>
        </div>
        <details id="benutzer-hinzufügen" class="section" >
            <summary><h1>Benutzer<span class="center"><span class="material-symbols-outlined">arrow_back_ios</span></span></h1></summary>
            <button id="add-user" class="center">
                <span class="material-symbols-outlined">add</span> Benutzer hinzufügen
            </button>
            <div class="user" id="user-heading">
                <h2>Profilbild</h2>
                <h2>Benutzername</h2>
                <h2>Rolle</h2>
                <div style="width: 10rem"></div>
            </div>
            <?php
        $db = new SQLite3("../assets/db/users.db");
        $result = $db->query("SELECT * FROM users");
        while ($user = $result->fetchArray(SQLITE3_ASSOC)) {
            echo '<div class="user">';
            echo '  <div id="user-icon">';
            $default_img = 'assets/icons/user.png';
            $profile_img = !empty($user['profilbild'])
            ? 'assets/img/uploads/users/' . $user['profilbild']
            : $default_img;
            echo '<img loading="lazy" src="../' . htmlspecialchars($profile_img) . '" alt="">';
            echo '  </div>';
            echo '  <div id="username" class="center">';
            echo '    <span>' . htmlspecialchars($user['name']) . '</span>';
            if ($user['status'] == 1) {
                echo ' <span class="material-symbols-outlined status" style="color:red" title="Unverified">error</span>';
            } elseif ($user['status'] == 2) {
                echo ' <span class="material-symbols-outlined status" title="Verified">check_circle</span>';
            }
            echo '  </div>';
            echo '  <div id="role">';
            echo '    <span id="user-role">' . htmlspecialchars($user['rolle']) . '</span>';
            echo '  </div>';
            echo '    <form method="post" style="display:inline;">';
            echo '      <button type="button" class="edit-user-btn"
            data-id="' . htmlspecialchars($user['id']) . '"
            data-username="' . htmlspecialchars($user['name']) . '"
            data-rolle="' . htmlspecialchars($user['rolle']) . '"
            data-mail="' . htmlspecialchars($user['email_address']) . '"
            data-profilbild="' . htmlspecialchars($user['profilbild']) . '">
            <span class="material-symbols-outlined">edit</span>
            </button>';
            echo '      <input type="hidden" name="delete_user_id" value="' . htmlspecialchars($user['id']) . '">';
            echo '      <button id="delete" type="submit" name="delete_user" onclick="return confirm(\'Diesen Benutzer wirklich löschen?\');">';
            echo '        <span class="material-symbols-outlined">delete</span>';
            echo '      </button>';
            echo '    </form>';
            echo '  </div>';
        }
        ?>
        </details>
    <div class="popup center" id="add-user-popup" style="display:none;">
        <form id="add-user-form" method="POST" action="" enctype="multipart/form-data">
            <h2>Benutzer hinzufügen</h2>
            <label for="username">Benutzername:</label>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Passwort:</label>
            <input type="password" id="password" name="password" required><br>
            <label for="mail">E-Mail:</label>
            <input type="text" id="mail" name="mail"><br>
            <label for="role">Rolle:</label>
            <select id="role" name="rolle" required>
                <option value="user">User</option>
                <option value="editor">Editor</option>
                <option value="admin">Admin</option>
            </select><br>
            <label for="profilbild" id="pic-drop" class="center">
                <span class="material-symbols-outlined">image_arrow_up</span>
            </label>
            <input type="file" name="profilbild" id="profilbild"><br>
            <div class="buttons">
                <button type="reset" id="close-popup">Abbrechen</button>
                <button type="submit" name="add_user" id="add_user">Hinzufügen</button>
            </div>
        </form>
    </div>
    <div class="popup center" id="edit-user-popup" style="display:none;">
        <form id="edit-user-form" method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <h2>Benutzer bearbeiten</h2>
            <input type="hidden" name="edit_user_id" value="">
            <label for="edit-username">Benutzername:</label>
            <input type="text" id="edit-username" name="username" required><br>
            <label for="edit-password">Passwort:</label>
            <input type="password" id="edit-password" name="password" placeholder="Nur ausfüllen, wenn ändern" autocomplete="new-password"><br>
            <label for="edit-mail">E-Mail:</label>
            <input type="text" id="edit-mail" name="edit-mail"><br>
            <label for="edit-role">Rolle:</label>
            <select id="edit-role" name="rolle" required>
                <option value="user">User</option>
                <option value="editor">Editor</option>
                <option value="admin">Admin</option>
            </select><br>
            <label for="edit-profilbild">Profilbild:</label>
            <input type="file" name="profilbild" id="edit-profilbild"><br>
            <img id="current-profile-pic" src="" alt="Aktuelles Profilbild" style="max-width:80px;display:none"><br>
            <div class="buttons">
                <button type="button" id="close-popup-edit">Abbrechen</button>
                <button type="submit" name="edit_user" id="edit_user">Aktualisieren</button>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("add-user").onclick = function() {
                document.getElementById("add-user-popup").style.display = "flex";
            };
            document.getElementById("close-popup").onclick = function() {
                document.getElementById("add-user-popup").style.display = "none";
            };
            document.getElementById("close-popup-edit").onclick = function() {
                document.getElementById("edit-user-popup").style.display = "none";
            };

            // Edit user popup logic
            document.querySelectorAll(".edit-user-btn").forEach(function(btn) {
                btn.onclick = function() {
                    document.getElementById("edit-user-popup").style.display = "flex";
                    document.getElementById("edit-user-form").reset();
                    document.getElementById("edit-user-form").elements["edit_user_id"].value = btn.dataset.id;
                    document.getElementById("edit-username").value = btn.dataset.username;
                    document.getElementById("edit-mail").value = btn.dataset.mail || '';
                    document.getElementById("edit-role").value = btn.dataset.rolle;
                    // Show current profile picture if available
                    let img = document.getElementById("current-profile-pic");
                    if (btn.dataset.profilbild) {
                        img.src = "../assets/img/uploads/users/" + btn.dataset.profilbild;
                        img.style.display = "block";
                    } else {
                        img.style.display = "none";
                    }
                };
            });
        });
    </script>
        <details id="open_recepies" class="section">
            <summary><h1>offene Rezepte<span class="center"><span class="material-symbols-outlined">arrow_back_ios</span></span></h1></summary>
            <div class="rezept" id="rezept-heading">
                <h2 class="id">ID</h2>
                <h2 class="titel">Titel</h2>
                <h2 class="timecode">Timecode</h2>
                <div class="buttons" style="width:11rem"></div>
            </div>
            <?php
                $rezepte = new SQLite3("../assets/db/gerichte.db");
                $result = $rezepte->query("SELECT * FROM gerichte WHERE status == 1");
                while ($rezept = $result->fetchArray(SQLITE3_ASSOC)) {
                    echo '
                    <div class="rezept" id="'.htmlspecialchars($rezept['id']).'">
                        <div class="id">#'.htmlspecialchars($rezept['id']).'</div>
                        <div class="titel">'.htmlspecialchars($rezept['titel']).'</div>
                        <div class="timecode">'.htmlspecialchars($rezept['timecode_erstellt']+ 6 * 3600).'</div>
                        <div class="buttons">
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="set_status_id" value="'.htmlspecialchars($rezept['id']).'">
                                <button class="btn-tick" type="submit" name="set_status"><span class="material-symbols-outlined">check</span></button>
                            </form>
                            <button class="btn-edit" onclick="window.location.href = \'rezept-bearbeiten.php?id='.htmlspecialchars($rezept['id']).'\'"><span class="material-symbols-outlined">edit</span></button>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="delete_rezept_id" value="'.htmlspecialchars($rezept['id']).'">
                                <button class="btn-delete" type="submit" name="delete_rezept" onclick="return confirm(\'Wirklich löschen?\');">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    ';
                }
            ?>
        </details>
        <details id="errors" class="section">
            <summary><h1>Fehlermeldungen<span class="center"><span class="material-symbols-outlined">arrow_back_ios</span></span></h1></summary>
            <div class="rezept" id="rezept-heading">
                <h2 class="id">ID</h2>
                <h2 class="titel">Titel</h2>
                <h2 class="error_msg">Fehler</h2>
                <h2 class="timecode">Timecode</h2>
                <div class="buttons" style="width:8rem"></div>
            </div>
            <?php
                $rezepte = new SQLite3("../assets/db/gerichte.db");
                $result = $rezepte->query("SELECT * FROM gerichte WHERE error_msg != ''");
                while ($rezept = $result->fetchArray(SQLITE3_ASSOC)) {
                    echo '
                    <div class="rezept" id="'.htmlspecialchars($rezept['id']).'">
                        <div class="id">#'.htmlspecialchars($rezept['id']).'</div>
                        <div class="titel">'.htmlspecialchars($rezept['titel']).'</div>
                        <div class="error_msg">'.htmlspecialchars($rezept['error_msg']).'</div>
                        <div class="timecode">'.date("d.m.Y H:m",$rezept['timecode_error']+ 6 * 3600).'</div>
                        <div class="buttons">
                            <button class="btn-edit" onclick="window.location.href = \'rezept-bearbeiten.php?id='.htmlspecialchars($rezept['id']).'\'"><span class="material-symbols-outlined">edit</span></button>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="clear_error_id" value="'.htmlspecialchars($rezept['id']).'">
                                <button class="btn-tick" type="submit" name="clear_error"><span class="material-symbols-outlined">check</span></button>
                            </form>
                        </div>
                    </div>
                    ';
                }
            ?>
        </details>
        <details id="logs" class="section" <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['timeselection'])) echo 'open'; ?>>
            <summary><h1>Logs<span class="center"><span class="material-symbols-outlined">arrow_back_ios</span></span></h1></summary>
            <form id="log-selection" method="POST" action="#logs" onchange="this.submit()">
                <select id="timeselection" name="timeselection">
                    <option value="0" <?php if(($_POST['timeselection'] ?? '') === '0') echo 'selected'; ?>>letzte 24h</option>
                    <option value="letzte 7 Tage" <?php if(($_POST['timeselection'] ?? '') === 'letzte 7 Tage') echo 'selected'; ?>>letzte 7 Tage</option>
                    <option value="letzte 30 Tage" <?php if(($_POST['timeselection'] ?? '') === 'letzte 30 Tage') echo 'selected'; ?>>letzte 30 Tage</option>
                    <option value="letzte 6 Monate" <?php if(($_POST['timeselection'] ?? '') === 'letzte 6 Monate') echo 'selected'; ?>>letzte 6 Monate</option>
                    <option value="alle" <?php if(($_POST['timeselection'] ?? '') === 'alle') echo 'selected'; ?>>alle</option>
                </select>
                <div id="log-buttons">
                    <input type="checkbox" name="Logins" id="Logins" <?php if(isset($_POST['Logins']) || !$_POST) echo 'checked'; ?>>
                    <label for="Logins">Logins</label>
                    <input type="checkbox" name="Registrierungen" id="Registrierungen" <?php if(isset($_POST['Registrierungen']) || !$_POST) echo 'checked'; ?>>
                    <label for="Registrierungen">Registrierungen</label>
                    <input type="checkbox" name="Konto-Verifizierung" id="Konto-Verifizierung" <?php if(isset($_POST['Konto-Verifizierung']) || !$_POST) echo 'checked'; ?>>
                    <label for="Konto-Verifizierung">Konto-Verifizierungen</label>
                    <input type="checkbox" name="Konto-Löschung" id="Konto-Löschung" <?php if(isset($_POST['Konto-Löschung']) || !$_POST) echo 'checked'; ?>>
                    <label for="Konto-Löschung">Konto-Löschungen</label>
                    <input type="checkbox" name="Rezept-Erstellung" id="Rezept-Erstellung" <?php if(isset($_POST['Rezept-Erstellung']) || !$_POST) echo 'checked'; ?>>
                    <label for="Rezept-Erstellung">Gerichte</label>
                    <input type="checkbox" name="Rezept-Bearbeitung" id="Rezept-Bearbeitung" <?php if(isset($_POST['Rezept-Bearbeitung']) || !$_POST) echo 'checked'; ?>>
                    <label for="Rezept-Bearbeitung">Bearbeitungen</label>
                    <input type="checkbox" name="Fehlermeldung" id="Fehlermeldung" <?php if(isset($_POST['Fehlermeldung']) || !$_POST) echo 'checked'; ?>>
                    <label for="Fehlermeldung">Fehlermeldungen</label>
                    <input type="checkbox" name="Error" id="Error" <?php if(isset($_POST['Error']) || !$_POST) echo 'checked'; ?>>
                    <label for="Error">Errors</label>
                    <input type="checkbox" name="Feedback" id="Feedback" <?php if(isset($_POST['Feedback']) || !$_POST) echo 'checked'; ?>>
                    <label for="Feedback">Feedbacks</label>
                </div>
            </form>
            <div id="log-heading">
                <h2 class="timecode">Timecode</h2>
                <h2 class="loguser">User</h2>
                <h2 class="event">Event</h2>
                <h2 class="IP-Adresse">IP-Adresse</h2>
            </div>
            <?php
                // --- LOG FILTERING ---
                $timespan_selected = time() - 86400; // Default: last 24h
                $event_types_selected = [
                    'Logins' => 'login',
                    'Registrierungen' => 'Registrierung',
                    'Konto-Löschung' => 'Konto-Löschung',
                    'Konto-Verifizierung' => 'Konto-Verifizierung',
                    'Rezept-Erstellung' => 'Rezept-Erstellung',
                    'Rezept-Bearbeitung' => 'Rezept-Bearbeitung',
                    'Fehlermeldung' => 'Fehlermeldung',
                    'Error' => 'Error',
                    'Feedback' => 'Feedback'
                ];
                $selected_types = [];

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Timespan
                switch ($_POST['timeselection'] ?? '0') {
                    case 'letzte 7 Tage':
                        $timespan_selected = time() - 7 * 86400;
                        break;
                    case 'letzte 30 Tage':
                        $timespan_selected = time() - 30 * 86400;
                        break;
                    case 'letzte 6 Monate':
                        $timespan_selected = strtotime('-6 months');
                        break;
                    case 'alle':
                        $timespan_selected = 0;
                        break;
                    default:
                        $timespan_selected = time() - 86400;
                }
                // Event types
                foreach ($event_types_selected as $postKey => $eventName) {
                    if (isset($_POST[$postKey])) {
                        $selected_types[] = $eventName;
                    }
                }
            }
            // Always default to all if none selected
            if (empty($selected_types)) {
                $selected_types = array_values($event_types_selected);
            }

            // Prepare SQL
            $logs = new SQLite3("../assets/db/logs.db");
            $type_placeholders = implode(',', array_fill(0, count($selected_types), '?'));
            $sql = "SELECT * FROM logs WHERE 1";
            $params = [];
            if ($timespan_selected > 0) {
                $sql .= " AND timecode >= ?";
                $params[] = $timespan_selected;
            }
            if (!empty($selected_types)) {
                $sql .= " AND event_type IN ($type_placeholders)";
                $params = array_merge($params, $selected_types);
            }
            $sql .= " ORDER BY timecode DESC";
            $stmt = $logs->prepare($sql);
            // Bind params
            $idx = 1;
            foreach ($params as $p) {
                if (is_int($p)) {
                    $stmt->bindValue($idx, $p, SQLITE3_INTEGER);
                } else {
                    $stmt->bindValue($idx, $p, SQLITE3_TEXT);
                }
                $idx++;
            }
            $result = $stmt->execute();
                while ($log = $result->fetchArray(SQLITE3_ASSOC)) { 
                $class = '';
                $date = new DateTime('@' . $log['timecode']);
                $date->setTimezone(new DateTimeZone('Europe/Berlin'));
                $timecode = $date->format('d.m.Y H:i');
                if (str_contains($log['event'], 'error')) {
                    $class = ' error';
                }
                echo '
                <div class="log'.$class.'">
                    <div class="timecode">'.$timecode.'</div>
                    <div class="loguser">'.htmlspecialchars($log['user']).'</div>
                    <div class="event">'.htmlspecialchars($log['event']).'</div>
                    <div class="IP-Adresse">'.htmlspecialchars($log['IP-Adresse']).'</div>
                </div>
                ';
            }
            ?>
        </details>
        <details id="feedbacks" class="section" onclick="loadanimations()">
            <summary><h1>Feedbacks<span class="center"><span class="material-symbols-outlined">arrow_back_ios</span></span></h1></summary>
            <form id="feedback-selection" onchange="//updateLogDisplay()">
                <select id="timeselection">
                    <option value="0">letzte 24h</option>
                    <option value="letzte 7 Tage">letzte 7 Tage</option>
                    <option value="letzte 30 Tage">letzte 30 Tage</option>
                    <option value="letzte 6 Monate">letzte 6 Monate</option>
                    <option value="alle" selected>alle</option>
                </select>
                <div id="feedback-buttons">
                    <input type="checkbox" name="beendet" id="beendet">
                    <label for="beendet">nur beendete anzeigen</label>
                </div>
            </form>
            <div id="zufriedenheit">
                <div id="y-achse">
                    <p class="y-achse">10</p>
                    <p class="y-achse">8</p>
                    <p class="y-achse">6</p>
                    <p class="y-achse">4</p>
                    <p class="y-achse">2</p>
                    <p class="y-achse">0</p>
                </div>
                <div id="diagramm">
                    <hr class="bg-hr">
                    <hr class="bg-hr">
                    <hr class="bg-hr">
                    <hr class="bg-hr">
                    <div id="output">
                        <div class="max-box" style="height:<?php echo 43;?>%">
                            <div class="inner-box center"><?php echo 4.3;?></div>
                        </div>
                        <div class="max-box" style="height:<?php echo 79;?>%">
                            <div class="inner-box center"><?php echo 7.9;?></div>
                        </div>
                        <div class="max-box" style="height:<?php echo 60;?>%">
                            <div class="inner-box center"><?php echo 6.0;?></div>
                        </div>
                        <div class="max-box" style="height:<?php echo 12;?>%">
                            <div class="inner-box center"><?php echo 1.2;?></div>
                        </div>
                        <div class="max-box" style="height:<?php echo 45;?>%">
                            <div class="inner-box center"><?php echo 4.5;?></div>
                        </div>
                        <div class="max-box" style="height:<?php echo 95;?>%">
                            <div class="inner-box center"><?php echo 9.5;?></div>
                        </div>
                    </div>
                    <div id="x-achse">
                        <p class="x-achse">Index</p>
                        <p class="x-achse">Suche</p>
                        <p class="x-achse">Filter</p>
                        <p class="x-achse">Gericht</p>
                        <p class="x-achse">Listen</p>
                        <p class="x-achse">Intuitivität</p>
                    </div>
                </div>
            </div>
            <div id="devices">
                <div id="max-width">
                    <div id="handy" class="center" style="width:<?php echo 60;?>%">Handy</div>
                    <div id="tablet" class="center" style="width:<?php echo 10;?>%">Tablet</div>
                    <div id="pc" class="center" style="width:<?php echo 30;?>%">PC</div>
                </div>
            </div>
            <div id="messages">
                <h2>Rückmeldungen zu <b><?php echo 'Index'?></b></h2>
                <?php echo '<li class="message">Dies ist eine Nachricht</li>';?>
            </div>
        </details>
    </div>
</body>
    <div id="footer">
        <!-- Code gets injected by footer.js -->
    </div>
</html>