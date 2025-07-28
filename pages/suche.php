<!DOCTYPE html>
<html lang="de">
<head>
    <?php
        session_start();
        $suche = isset($_GET['suche']) ? trim($_GET['suche']) : '';
        $filter = isset($_GET['filter']) ? trim($_GET['filter']) : null;
        $searchforuser = isset($_GET['user']) ? trim($_GET['user']) : null;
        $somany = 0;
        $where = ["status = 0"];
        $advanced = isset($_GET['a_search']);
        $suche_ids = [];

        if ($suche !== '') {
            $search_esc = SQLite3::escapeString($suche);
            $title_tags = "(titel LIKE '%$search_esc%' OR tags LIKE '%$search_esc%')";
            if ($advanced) {
                if(file_exists("../assets/db/gerichte.db")){
                    $db = new SQlite3("../assets/db/gerichte.db");
                    $zutaten_query = "SELECT gerichte_id FROM zutaten WHERE name LIKE '%$search_esc%'";
                    $zutaten_result = $db->query($zutaten_query);
                    while ($zutaten_row = $zutaten_result->fetchArray(SQLITE3_ASSOC)) {
                        $suche_ids[] = (int)$zutaten_row['gerichte_id'];
                    }
                    if (count($suche_ids) > 0) {
                    $ids_sql = "(id IN (" . implode(',', $suche_ids) . "))";
                    // Combine with OR, not AND
                    $where[] = "($title_tags OR $ids_sql)";
                    } else {
                        $where[] = $title_tags;
                    }
                } else {
                    $where[] = $title_tags;
                }
            } else {
                $where[] = $title_tags;
            }
        }
        if ($filter !== '') {
            $filter_esc = SQLite3::escapeString($filter);
            $where[] = "(tags LIKE '%$filter_esc%')";
        }
        $where_sql = 'WHERE ' . implode(' AND ', $where);
        $sort = isset($_GET['sorting']) ? $_GET['sorting'] : 'ASC';
        $order = ($sort === 'DESC') ? 'titel DESC' : 'titel ASC';

        if($filter != 'random' || 'latest'){
            $db = new SQlite3("../assets/db/gerichte.db");
            $query = "SELECT * FROM gerichte $where_sql ORDER BY $order";
            $result = $db->query($query);
            // Count results
            if($result){
                while ($row = $result->fetchArray(SQLITE3_ASSOC)){
                    $somany++;
                }
            }
        }
        if($filter == 'random'){
            $db = new SQlite3("../assets/db/gerichte.db");
            $query = "SELECT * FROM gerichte ORDER BY RANDOM() LIMIT 10";
            $result = $db->query($query);
            // Count results
            if($result){
                while ($row = $result->fetchArray(SQLITE3_ASSOC)){
                    $somany++;
                }
            }
        }
        if($filter == 'latest'){
            $db = new SQlite3("../assets/db/gerichte.db");
            $query = "SELECT * FROM gerichte ORDER BY id DESC LIMIT 10";
            $result = $db->query($query);
            // Count results
            if($result){
                while ($row = $result->fetchArray(SQLITE3_ASSOC)){
                    $somany++;
                }
            }
        }
        if(isset($searchforuser)){
            $users = new SQlite3("../assets/db/users.db");
            $stmt2 = $users->prepare("SELECT * FROM users WHERE name LIKE '%$searchforuser%'");
            $stmt2->bindValue('id', $searchforuser, SQLITE3_INTEGER);
            $result = $stmt2->execute();
            $userrow = $result->fetchArray(SQLITE3_ASSOC);
            $userid = $userrow['id'];
            // count results
            $query = "SELECT * FROM gerichte WHERE made_by_id = $userid";
            $result = $db->query($query);
            // Count results
            if ($userid !== null) {
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM gerichte WHERE made_by_id = $userid");
                $stmt->bindValue(':uid', $userid, SQLITE3_INTEGER);
                $res = $stmt->execute();
                $row = $res->fetchArray(SQLITE3_ASSOC);
                $somany = $row ? $row['count'] : 0;
            }
        }
        
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="Suche nach <?php echo $filter.''.$suche.''.$searchforuser?> - Kochbuch" />
    <meta property="og:description" content="Ein digitales Kochbuch, in das jeder seine Lieblingsrezepte hinzufügen kann." />
    <meta property="og:image" content="../assets/icons/Topficon.png" />
    <meta property="og:url" content="https://mein-kochbuch-free.nf" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Suche nach <?php echo $filter.''.$suche.''.$searchforuser?> - Kochbuch" />
    <title>Suche nach <?php echo $filter .''. $suche.''.$searchforuser?> - Kochbuch</title>
    <link rel="icon" href="../assets/icons/Topficon.png">
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/suche.css">
    <link rel="stylesheet" href="../assets/css/heading.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script src="../assets/js/heading.js" defer></script>
    <script src="../assets/js/links2.js" defer></script>
</head>
<body>
    <div id="heading">
        <!-- Code gets injected by heading.js -->
    </div>
    <div id="main">
        <form id="controls" method="get">
            <input type="hidden" name="suche" value="<?php echo htmlspecialchars($suche);?>">
            <label for="a_search" id="advanced_search" class="center">
                <input type="checkbox" name="a_search" id="a_search" <?php if(isset($_GET['a_search'])){ echo 'checked';};if($filter == null xor $searchforuser == null){echo ' disabled';}; ?>>
                <div id="toggle">
                    <span id="toggle_btn"></span>
                </div>
                <span>erweiterte Suche</span>
            </label>
            <select id="sorting" name="sorting" <?php if($searchforuser != null){echo ' disabled';}?>>
                <option value="ASC" <?php if(isset($_GET['sorting']) && $_GET['sorting'] === 'ASC') echo 'selected'; ?>>A - Z</option>
                <option value="DESC" <?php if(isset($_GET['sorting']) && $_GET['sorting'] === 'DESC') echo 'selected'; ?>>Z - A</option>
            </select>
        </form>
        <script>
            document.getElementById('a_search').addEventListener('change', function() {
                document.getElementById('controls').submit();
            });
            document.getElementById('sorting').addEventListener('change', function() {
                document.getElementById('controls').submit();
            });
        </script>
        <div id="output">
            <?php
                if($filter != "random" && $filter != "latest"){
                    echo 'Deine Suche nach <b>'.$filter .''. $suche.''. $searchforuser.'</b> ergab '.htmlspecialchars($somany).' Treffer.';
                }
            ?>
        </div>
        <div id="results">
            <?php
                $db = new SQlite3("../assets/db/gerichte.db");
                $maxId = $db->querySingle("SELECT MAX(id) FROM gerichte");
                $result = $db->query($query);
                    if($result){
                        while ($row = $result->fetchArray(SQLITE3_ASSOC)){
                            
                            echo '
                                <div class="rezeptlink" id="'.htmlspecialchars($row['id']).'">
                                    <div class="tag-bereich">
                                        <div class="tag time">'.htmlspecialchars($row['zubereitungszeit']).'min</div>';
                                        if ($row['id'] >= $maxId - 5) {
                                            echo '<div class="tag neu">NEU!</div>';
                                        };
                            echo '  </div>
                                    <div class="img-container">';
                                    if(!empty($row['bild1'])){
                                        echo '<img src="../assets/img/uploads/gerichte/'.htmlspecialchars($row['bild1']).'" alt="">';
                                    }
                                    else{
                                        echo '<img src="" alt="Bild konnte nicht geladen werden">';
                                    };
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
                                        };
                            echo '      </div>
                                </div>
                            ';
                        }
                    }
                ?>
        </div>
    </div>
</body>
</html>