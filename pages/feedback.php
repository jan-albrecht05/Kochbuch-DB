<?php
session_start();

// Include database connection
$usersdb = new SQLite3("../assets/db/users.db");
$user_id = $_SESSION['user_id'];
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
        var isAdmin = <?php if($_SESSION['rolle'] == 'admin') {echo 'true';}else{echo'false';}; ?>;
        var isEditor = <?php if($_SESSION['rolle'] == 'editor') {echo 'true';}else{echo'false';}; ?>;
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

    </div>
    <div id="footer">
        <!-- Code gets injected by footer.js -->
    </div>
</body>
</html>