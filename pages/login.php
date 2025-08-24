<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Kochbuch</title>
    <link rel="icon" href="../assets/icons/Topficon.png">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/heading.css">
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body>
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $db = new SQLite3("../assets/db/users.db");
    $logs_db = new SQLite3("../assets/db/logs.db");
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE name = :username");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if ($result) {
        if ($result['status'] == 1) {
            $login_error = "Dieses Konto muss erst verifiziert werden!";
        } elseif (password_verify($password, $result['password']) && $result['status'] == 2) {
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['name'] = $result['name'];
            $_SESSION['rolle'] = $result['rolle'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_stmt = $logs_db->prepare("INSERT INTO logs (user, event, timecode, 'IP-Adresse') VALUES (:name, :event, :timecode, :ip)");
            $log_stmt->bindValue(':name', $result['name'], SQLITE3_TEXT);
            $log_stmt->bindValue(':event', 'login', SQLITE3_TEXT);
            $log_stmt->bindValue(':timecode', time(), SQLITE3_INTEGER);
            $log_stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
            $log_stmt->execute();

            // Handle redirect
            if (!empty($_POST['redirect'])) {
                $redirect = trim(str_replace(array("\r", "\n"), '', $_POST['redirect']));
                header("Location: " . $redirect);
            } else {
                header("Location: ../index.php?login=success");
            }
            exit;
        } else {
            $login_error = "Benutzername oder Passwort falsch!";
        }
    } else {
        $login_error = "Benutzername oder Passwort falsch!";
    }
}
?>
    <div id="heading"><div id="inner-heading"><a id="logo" href="../index.php" class="center"><img id="logoimg" src="../assets/icons/Topficon.png" alt="Kochbuch" title="Home"></a></div></div>
    <div id="main">
        <form id="login-form" method="post" action="">
            <h1>Login</h1>
            <div class="part">
                <label for="username">Benutzername:</label><br>
                <input type="text" id="username" name="username" placeholder="Benutzername">
            </div>
            <div class="part">
                <label for="password">Passwort:</label><br>
                <input type="password" id="password" name="password" placeholder="Passwort">
            </div>
            <div class="pass-alert">
                <?php if (isset($login_error)) echo $login_error?>
            </div>
            <div id="erstellen">
                Noch kein Konto? <a href="register.php"><u>Konto erstellen</u></a>
            </div>
            <?php if (isset($_GET['redirect'])): ?>
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect'] ?? ''); ?>">
            <?php endif; ?>
            <button type="submit" name="login" id="login-button" class="center">Login<span class='material-symbols-outlined'>login</span></button>
        </form>
    </div>
    
</body>
</footer>
</html>