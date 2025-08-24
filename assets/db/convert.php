<?php
    // Konvertiert die alte Zeitcode-Spalte in ein UNIX-Zeitformat
    $db = new SQLite3("users.db");
    $result = $db->query("SELECT id, timestamp_register FROM users");
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $id = $row['id'];
        $old_timecode = $row['timestamp_register'];
        $dt = new DateTime($old_timecode, new DateTimeZone('UTC'));
        $unix_timecode = $dt->getTimestamp();
        $update_stmt = $db->prepare("UPDATE users SET timestamp_register = :timestamp_register WHERE id = :id");
        $update_stmt->bindValue(':timestamp_register', $unix_timecode, SQLITE3_INTEGER);
        $update_stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $update_stmt->execute();
    }
    $db->close();
    echo "Konvertierung abgeschlossen.";
?>