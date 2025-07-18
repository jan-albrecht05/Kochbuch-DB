<!DOCTYPE html>
<html lang="de">
<head>
    <?php
        session_start();
    ?>
    <?php
        if(file_exists("../assets/db/gerichte.db")){
            $db = new SQlite3("../assets/db/gerichte.db");
            // Get the maximum id
            $maxId = $db->querySingle("SELECT MAX(id) FROM gerichte");
            $id = htmlspecialchars($_GET['id']);
            // Rezept holen
            $stmt = $db->prepare("SELECT * FROM gerichte WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_ASSOC);
            // Zutaten holen
            $zutatenStmt = $db->prepare("SELECT * FROM zutaten WHERE gerichte_id = :id");
            $zutatenStmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $zutatenResult = $zutatenStmt->execute();
        }
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($row["titel"])?> - Kochbuch</title>
    <link rel="icon" href="../assets/icons/Topficon.png">
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/heading.css">
    <link rel="stylesheet" href="../assets/css/gericht.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script src="../assets/js/heading.js" defer></script>
</head>
<body>
    <div id="heading">
        <!-- Code gets injected by heading.js -->
    </div>
    <div id="main">
        <h1 id="ID"><?php echo ($row["titel"])?></h1>
        <div id="image-container">
            <div id="big-image">
                <img src="../assets/img/uploads/gerichte/<?php echo ($row["bild1"])?>">
                <img src="../assets/img/uploads/gerichte/<?php echo ($row["bild2"])?>" id="img2">
                <img src="../assets/img/uploads/gerichte/<?php echo ($row["bild3"])?>" id="img3">
            </div>
            <div id="sidebar">
                <img src="../assets/img/uploads/gerichte/<?php echo ($row["bild1"])?>">
                <?php if(!empty($row["bild2"])){
                    //  Add Click Handling
                    echo '<img src="../assets/img/uploads/gerichte/'. ($row["bild2"]).'">';
                }?>
                <?php if(!empty($row["bild3"])){
                    //  Add Click Handling
                    echo '<img src="../assets/img/uploads/gerichte/'. ($row["bild3"]).'">';
                }?>
            </div>
        </div>
        <div id="info">
            <?php
                if (!empty($row['tags'])) {
                    $tags = explode(',', $row['tags']);
                    foreach ($tags as $tag) {
                        echo '<span class="infotag">' . htmlspecialchars(trim($tag)) . '</span> ';
                    }
                };
            ?>
        </div>
        <div id="zutaten">
            <h2>Zutaten</h2>
            <p>(für <?php echo ($row["personen"])?> Personen)</p>
            <?php 
                while ($zutat = $zutatenResult->fetchArray(SQLITE3_ASSOC)) {
                    echo '<div class="zutat">
                            <span class="menge">'.htmlspecialchars($zutat["menge"]).'</span>
                            <span class="einheit">'.htmlspecialchars($zutat["einheit"]).'</span>
                            <span class="name">'.htmlspecialchars($zutat["name"]).'</span>
                        </div>';
                }
            ?>
            
        </div>
    </div>
</body>
</html>