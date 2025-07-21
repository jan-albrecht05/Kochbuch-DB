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
    <script src="../assets/js/footer.js"></script>
    <script>
        window.onload = () =>{
            footer();
        }
    </script>
    <style>
        @media (min-height:644px) {
            #footer{
                position: absolute;
            }
        }
    </style>
</head>
<body>
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $db = new SQLite3("../assets/db/users.db");
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if ($result && password_verify($password, $result['password'])) {
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['rolle'] = $result['rolle'];
        $_SESSION['profile_picture'] = $result['profile_picture'];

        // Handle redirect
        if (!empty($_POST['redirect'])) {
            $redirect = trim(str_replace(array("\r", "\n"), '', $_POST['redirect']));
            header("Location: " . $redirect);
        } else {
            header("Location: ../index.php");
        }
        exit;
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
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" placeholder="Username">
            </div>
            <div class="part">
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" placeholder="Password">
                <div class="pass-alert">
                    <?php if (isset($login_error)) echo "<div style='color:red;'>$login_error</div>"; ?>
                </div>
            </div>
            <?php if (isset($_GET['redirect'])): ?>
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect'] ?? ''); ?>">
            <?php endif; ?>
            <button type="submit" name="login" id="login-button" class="center">Login<span class='material-symbols-outlined'>login</span></button>
        </form>
    </div>
    
</body>
<footer id="footer" class="center">
    <div id="footer-content">
        <!--code gets injected by footer.js-->
    </div>
</footer>
</html>