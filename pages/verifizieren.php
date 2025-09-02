<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Verifizieren</title>
    <link rel="icon" href="../assets/icons/Topficon.png">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/heading.css">
    <link rel="stylesheet" href="../assets/css/verify.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body>
    <div id="heading"><div id="inner-heading"><a id="logo" href="../index.php" class="center"><img id="logoimg" src="../assets/icons/Topficon.png" alt="Kochbuch" title="Home"></a></div></div>
    <div id="message" class="center">
<?php
session_start();

// Verbindung zur SQLite-Datenbank
$usersdb = new SQLite3("../assets/db/users.db");

// Hash aus der URL holen
$hash = $_GET['login'] ?? null;
if (!$hash) {
    echo '
    <span class="material-symbols-outlined red">error</span>
    <p class="center">
        <span>
            Kein Token angegeben.<br>
            <small>Du wirst in 5 Sekunden weitergeleitet.</small>
        </span>    
    </p>';
    header("refresh:5;url=../index.php");
    exit;
}

$hash = trim($hash);

// Nutzer aus DB suchen (status = 1, richtiger hash, und innerhalb 60 Minuten registriert)
$stmt = $usersdb->prepare("SELECT id, timestamp_register FROM users WHERE status = 1 AND random_hash = :hash LIMIT 1");
$stmt->bindValue(':hash', $hash, SQLITE3_TEXT);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);
if ($user) {
    $timestamp_register = strtotime($user['timestamp_register']);
    $now = time();

    // prüfen ob innerhalb der letzten 60 Minuten
    if ($now - $timestamp_register >= 3600) {
        // Status auf 2 setzen (verifiziert)
        $update = $usersdb->prepare("UPDATE users SET status = 2 WHERE id = :id");
        $update->bindValue(':id', $user['id'], SQLITE3_INTEGER);
        $update->execute();
        $user_row = $usersdb->query("SELECT * FROM users WHERE id = {$user['id']}")->fetchArray(SQLITE3_ASSOC);
        $username = $user_row['name'];
        echo '
        <span class="material-symbols-outlined green center">check</span>
        <p>
            Dein Konto wurde erfolgreich verifiziert.<br>
            <small>Du wirst automatisch zum Login weitergeleitet.</small>
        </p>';
            $logs_db = new SQLite3("../assets/db/logs.db");
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_stmt = $logs_db->prepare("INSERT INTO logs (user,event_type, event, timecode, 'IP-Adresse') VALUES (:name, :event_type, :event, :timecode, :ip)");
            $log_stmt->bindValue(':name', $username, SQLITE3_TEXT);
            $log_stmt->bindValue(':event_type', 'Konto-Verifizierung', SQLITE3_TEXT);
            $log_stmt->bindValue(':event', 'Konto verifiziert', SQLITE3_TEXT);
            $log_stmt->bindValue(':timecode', time(), SQLITE3_INTEGER);
            $log_stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
            $log_stmt->execute();
        header("refresh:5;url=login.php");

    } else {
        echo '
            <p class="center">
                <span class="material-symbols-outlined red">error</span>
                Verifizierungs-Link abgelaufen.
            </p>
        ';
        header("refresh:5;url=login.php");

    }
} else {
    echo '
    <p class="center">
        <span class="material-symbols-outlined red">error</span>
        Verifizierung gescheitert. Versuche es bitte erneut.
    </p>
    ';
    header("refresh:5;url=login.php");

}
?>
    </div>
</body>
</html>