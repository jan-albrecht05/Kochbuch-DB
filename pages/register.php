<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konto erstellen | Kochbuch</title>
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
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $email = trim($_POST['email']);
    $status = 1; // Default status for unverified users

    if ($password !== $password2) {
        $login_error = "Passwörter stimmen nicht überein!";
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE name = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        $timestamp_register = time();

        if ($result) {
            $login_error = "Benutzername bereits vergeben!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            //random hash for verification
            $verification_hash = bin2hex(random_bytes(16));
            $stmt = $db->prepare("INSERT INTO users (name, password, email_address, status, random_hash, timestamp_register) VALUES (:username, :password, :email, :status, :verification_hash, :timestamp_register)");
            $stmt->bindValue(':username', $username, SQLITE3_TEXT);
            $stmt->bindValue(':password', $hashed_password, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':status', $status, SQLITE3_TEXT);
            $stmt->bindValue(':verification_hash', $verification_hash, SQLITE3_TEXT);
            $stmt->bindValue(':timestamp_register', $timestamp_register, SQLITE3_TEXT);
            if ($stmt->execute()) {
                // Verification mail sending logic
                $to = $email;
                $subject = "Konto verifizieren | Kochbuch";
                $message = "
                    <html>
                    <head>
                        <meta charset='UTF-8'>
                        <title>Verifizierungs-E-Mail</title>
                        <style>
                            :root{
                                --bg-color: #f0f0f0;
                                --text-color: #111111;
                                --yellow: #fffe00;
                                --orange: #ff7400;
                            }
                            #mail-body{
                                background-color: var(--bg-color);
                                color: var(--text-color);
                                font-family: Arial, sans-serif;
                                padding: 20px;
                                font-size: x-large;
                            }
                            a{
                                height: 4rem;
                                width: 30%;
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                background-color: var(--orange);
                                text-decoration: none;
                                color: var(--text-color);
                                font-weight: bold;
                            }
                        </style>
                    </head>
                    <body id='mail-body'>
                        <h1>Willkommen beim Kochbuch!</h1>
                        <p>Hallo ".$username.",</p>
                        <p>Vielen Dank für dein Interesse an diesem Kochbuch! Um dein Konto zu aktivieren, klicke bitte auf den folgenden Link:</p>
                        <br>
                        <p><a href='https://mein-kochbuch.nf.free/pages/verifizieren.php?login=".$verification_hash."'>Konto verifizieren</a></p>
                        <br>
                        <p>Falls diese E-Mail nicht angefordert wurde, ignorieren Sie bitte diese Nachricht.</p>
                        <p>Mit freundlichen Grüßen,<br>Jan Albrecht</p>
                    </body>
                    </html>

                ";

                
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: Kochbuch <noreply.kochbuch@jan-alb.de>\r\n";
                mail($to,$subject,$message,$headers);                
                
                // Log the event
                $logs_db = new SQLite3("../assets/db/logs.db");
                $ip = $_SERVER['REMOTE_ADDR'];
                $id = 'Konto '.$db->querySingle("SELECT MAX(id) FROM users").' "'.$username.'" erstellt';
                $log_stmt = $logs_db->prepare("INSERT INTO logs (user, event_type, event, timecode, 'IP-Adresse') VALUES (:name, :event_type, :event, :timecode, :ip)");
                $log_stmt->bindValue(':name', $username, SQLITE3_TEXT);
                $log_stmt->bindValue(':event_type', 'Registrierung', SQLITE3_TEXT);
                $log_stmt->bindValue(':event', $id, SQLITE3_TEXT);
                $log_stmt->bindValue(':timecode', time(), SQLITE3_INTEGER);
                $log_stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
                $log_stmt->execute();
                //positive feedback
                $login_error = "Konto erfolgreich erstellt! Bitte überprüfe deine E-Mails, um dein Konto zu verifizieren.";
            } else {
                $login_error = "Fehler bei der Registrierung!";
            }
        }
    }
}
?>
    <div id="heading"><div id="inner-heading"><a id="logo" href="../index.php" class="center"><img id="logoimg" src="../assets/icons/Topficon.png" alt="Kochbuch" title="Home"></a></div></div>
    <div id="main">
        <form id="login-form" method="post" action="">
            <h1>Konto erstellen</h1>
            <div class="part">
                <label for="username">Benutzername:</label><br>
                <input type="text" id="username" name="username" placeholder="Benutzername" required>
            </div>
            <div class="part">
                <label for="password">Passwort:</label><br>
                <input type="password" id="password" name="password" placeholder="Passwort" required>
            </div>
            <div class="part">
                <label for="password2">Passwort wiederholen:</label><br>
                <input type="password" id="password2" name="password2" placeholder="Passwort wiederholen" required><br>
            </div>
            <?php if (isset($_GET['redirect'])): ?>
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect'] ?? ''); ?>">
                <?php endif; ?>
                <div class="part">
                    <label for="email">E-Mail:</label><br>
                    <input type="email" id="email" name="email" placeholder="E-Mail-Adresse" required>
                </div>
                <div class="pass-alert">
                    <?php if (isset($login_error)) echo "<div style='color:red;'>$login_error</div>"; ?>
                </div>
                <div id="erstellen">
                    Du hast bereits ein Konto? <a href="login.php"><u>Anmelden</u></a>
                </div>
            <button type="submit" name="login" id="login-button" class="center">Registrieren <span class='material-symbols-outlined'>login</span></button>
        </form>
    </div>
    
</body>
</footer>
</html>