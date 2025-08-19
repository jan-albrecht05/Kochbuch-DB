window.addEventListener('load', function(event) {
    if (typeof homepage === "undefined") {
        homepage = false;
    }
    if (homepage){
        document.getElementById('heading').innerHTML = `
        <div id="inner-heading">
            <span id="logo" onclick="showSidebar()">
                <img src="assets/icons/Topficon.png" alt="Kochbuch Icon">
            </span>
            <p id="filter" onclick="openfilter()">
                <span class="material-symbols-outlined">filter_list</span>
            </p>
            <p href="pages/suche.php" id="suche" class="center">
                <input type="text" id="search" placeholder="Suche nach Rezepten...">
                <button class="material-symbols-outlined" onclick="suchen()">search</span>
            </p>
        </div>
        <div id="filter-container">
            <button id="close-filter" class="material-symbols-outlined" onclick="closefilter()">close</button>
            <div class="row">
                <a href="pages/suche.php?filter=asiatisch" class="filter-link">Asiatisch</a>
            </div>
            <div class="row">
                <a href="pages/suche.php?filter=nudeln" class="filter-link">Nudeln</a>
                <a href="pages/suche.php?filter=kartoffeln" class="filter-link">Kartoffeln</a>
                <a href="pages/suche.php?filter=reis" class="filter-link">Reis</a>
            </div>
            <div class="row">
                <a href="pages/suche.php?filter=fleisch" class="filter-link">Fleisch</a>
                <a href="pages/suche.php?filter=fisch" class="filter-link">Fisch</a>
            </div>
            <div class="row">
                <a href="pages/suche.php?filter=hühnchen" class="filter-link">Hühnchen</a>
                <a href="pages/suche.php?filter=schwein" class="filter-link">Schwein</a>
                <a href="pages/suche.php?filter=rind" class="filter-link">Rind</a>
            </div>
            <div class="row">
                <a href="pages/suche.php?filter=suppe" class="filter-link">Suppe</a>
                <a href="pages/suche.php?filter=soße" class="filter-link">Soßen</a>
            </div>
            <div class="row">
                <a href="pages/suche.php?filter=dessert" class="filter-link">Dessert</a>
                <a href="pages/suche.php?filter=kuchen" class="filter-link">Kuchen</a>
            </div>
            <div class="row">
                <a href="pages/suche.php?filter=vegan" class="filter-link">Vegan</a>
                <a href="pages/suche.php?filter=vegetarisch" class="filter-link">Vegetarisch</a>
            </div>
            <div class="row">
                <a href="pages/suche.php?filter=cocktail" class="filter-link">Cocktails</a>
                <a href="pages/suche.php?filter=getränk" class="filter-link">Getränke</a>
                <a href="pages/suche.php?filter=mocktail" class="filter-link">Mocktails</a>
            </div>
            <button id="rezept-erstellen" onclick="window.location.href = 'pages/rezept-erstellen.php'"><span>Rezept erstellen</span></button>
        </div>
        `;
    }
    else{
        document.getElementById('heading').innerHTML = `
        <div id="inner-heading">
            <span id="logo" onclick="showSidebar()">
                <img src="../assets/icons/Topficon.png" alt="Kochbuch Icon">
            </span>
            <p id="filter" onclick="openfilter()">
                <span class="material-symbols-outlined">filter_list</span>
            </p>
            <p href="../pages/suche.php" id="suche" class="center">
                <input type="text" id="search" placeholder="Suche nach Rezepten...">
                <button class="material-symbols-outlined" onclick="suchen()">search</span>
            </p>
        </div>
        <div id="filter-container">
            <button id="close-filter" class="material-symbols-outlined" onclick="closefilter()">close</button>
            <div class="row">
                <a href="../pages/suche.php?filter=asiatisch" class="filter-link">Asiatisch</a>
            </div>
            <div class="row">
                <a href="../pages/suche.php?filter=nudeln" class="filter-link">Nudeln</a>
                <a href="../pages/suche.php?filter=kartoffeln" class="filter-link">Kartoffeln</a>
                <a href="../pages/suche.php?filter=reis" class="filter-link">Reis</a>
            </div>
            <div class="row">
                <a href="../pages/suche.php?filter=fleisch" class="filter-link">Fleisch</a>
                <a href="../pages/suche.php?filter=fisch" class="filter-link">Fisch</a>
            </div>
            <div class="row">
                <a href="../pages/suche.php?filter=hühnchen" class="filter-link">Hühnchen</a>
                <a href="../pages/suche.php?filter=schwein" class="filter-link">Schwein</a>
                <a href="../pages/suche.php?filter=rind" class="filter-link">Rind</a>
            </div>
            <div class="row">
                <a href="../pages/suche.php?filter=suppe" class="filter-link">Suppe</a>
                <a href="../pages/suche.php?filter=soße" class="filter-link">Soßen</a>
            </div>
            <div class="row">
                <a href="../pages/suche.php?filter=dessert" class="filter-link">Dessert</a>
                <a href="../pages/suche.php?filter=kuchen" class="filter-link">Kuchen</a>
            </div>
            <div class="row">
                <a href="../pages/suche.php?filter=vegan" class="filter-link">Vegan</a>
                <a href="../pages/suche.php?filter=vegetarisch" class="filter-link">Vegetarisch</a>
            </div>
            <div class="row">
                <a href="../pages/suche.php?filter=cocktail" class="filter-link">Cocktails</a>
                <a href="../pages/suche.php?filter=getränk" class="filter-link">Getränke</a>
                <a href="../pages/suche.php?filter=mocktail" class="filter-link">Mocktails</a>
            </div>
            <button id="rezept-erstellen" onclick="window.location.href = '../pages/rezept-erstellen.php'"><span>Rezept erstellen</span></button>
        </div>
        `;
    }
    insertSidebar();
    insertfooter();
    // Add event listener for Enter key on search input
    setTimeout(function() {
        var searchInput = document.getElementById("search");
        if (searchInput) {
            searchInput.addEventListener("keydown", function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    suchen();
                }
            });
        }
    }, 0);
});
function openfilter() {
    document.getElementById('filter-container').classList.toggle('open');
    document.body.classList.add('no-scroll');
};
function closefilter() {
    document.getElementById('filter-container').classList.toggle('open');
    document.body.classList.remove('no-scroll');
};
function suchen(){
    let searchterm = document.getElementById("search").value
    if(searchterm == ''){
        document.getElementById("search").style.width = "10rem";
        return;
    }
    if(homepage){
        window.location.href = "pages/suche.php?suche="+searchterm;
    }else{
        window.location.href = "../pages/suche.php?suche="+searchterm;
    }
}
function insertSidebar() {
    // Use window.isLoggedIn (set by PHP)
    var isLoggedIn = window.isLoggedIn === true || window.isLoggedIn === "true";
    var sidebarLinks = '';
    if (homepage) {
        sidebarLinks += `
            <a onclick="showSidebar()" class="sidebar-link" id="home-link">
                <span class="material-symbols-outlined">home</span>
                <span>Home</span>
            </a>
            <a href="pages/rezept-erstellen.php" class="sidebar-link" id="rezept-erstellen-link">
                    <span class="material-symbols-outlined">add</span>
                    <span>Rezept erstellen</span>
                </a>
                <a href="#" class="sidebar-link" id="feedback-link">
                    <span class="material-symbols-outlined">feedback</span>
                    <span>Feedback geben</span>
                </a>
        `;
        if (isLoggedIn) {
            sidebarLinks += `
                <a href="pages/profil.php?query=bookmarks" class="sidebar-link" id="saved-rezepte-link">
                    <span class="material-symbols-outlined">bookmark</span>
                    <span>gespeicherte Rezepte</span>
                </a>
                <a href="pages/einkaufsliste.php" class="sidebar-link" id="list-link">
                    <span class="material-symbols-outlined">list</span>
                    <span>Einkaufsliste</span>
                </a>
                <a href="pages/profil.php?query=meineRezepte" class="sidebar-link" id="meine-rezepte-link">
                    <span class="material-symbols-outlined">person</span>
                    <span>meine Rezepte</span>
                </a>
                <a href="pages/admin-panel.php" class="sidebar-link" id="admin-link">
                    <span class="material-symbols-outlined">shield_person</span>
                    <span>Admin-Panel</span>
                </a>
                <a href="pages/profil.php?query=settings" class="sidebar-link" id="einstellungen-link">
                    <span class="material-symbols-outlined">settings</span>
                    <span>Einstellungen</span>
                </a>
                <a href="pages/logout.php" class="sidebar-link" id="logout-link">
                    <span class="material-symbols-outlined">logout</span>
                    <span>Logout</span>
                </a>
            `;
        } else {
            sidebarLinks += `
                <a href="pages/login.php" class="sidebar-link" id="login-link">
                    <span class="material-symbols-outlined">login</span>
                    <span>Login</span>
                </a>
            `;
        }
        document.getElementById("sidebar").innerHTML = `<div id="sidebar-content">${sidebarLinks}</div>`;
    } else {
        sidebarLinks += `
            <a href="../index.php" class="sidebar-link" id="home-link">
                <span class="material-symbols-outlined">home</span>
                <span>Home</span>
            </a>
            <a href="../pages/rezept-erstellen.php" class="sidebar-link" id="rezept-erstellen-link">
                <span class="material-symbols-outlined">add</span>
                <span>Rezept erstellen</span>
            </a>
            <a href="#" class="sidebar-link" id="feedback-link">
                <span class="material-symbols-outlined">feedback</span>
                <span>Feedback geben</span>
            </a>
        `;
        if (isLoggedIn) {
            sidebarLinks += `
                <a href="../pages/profil.php?query=bookmarks" class="sidebar-link" id="saved-rezepte-link">
                    <span class="material-symbols-outlined">bookmark</span>
                    <span>gespeicherte Rezepte</span>
                </a>
                <a href="../pages/einkaufsliste.php" class="sidebar-link" id="list-link">
                    <span class="material-symbols-outlined">list</span>
                    <span>Einkaufsliste</span>
                </a>
                <a href="../pages/profil.php?query=meineRezepte" class="sidebar-link" id="meine-rezepte-link">
                    <span class="material-symbols-outlined">person</span>
                    <span>meine Rezepte</span>
                </a>
                <a href="../pages/admin-panel.php" class="sidebar-link" id="admin-link">
                    <span class="material-symbols-outlined">shield_person</span>
                    <span>Admin-Panel</span>
                </a>
                <a href="../pages/profil.php?query=settings" class="sidebar-link" id="einstelungen-link">
                    <span class="material-symbols-outlined">settings</span>
                    <span>Einstellungen</span>
                </a>
                <a href="../pages/logout.php" class="sidebar-link" id="logout-link">
                    <span class="material-symbols-outlined">logout</span>
                    <span>Logout</span>
                </a>
            `;
        } else {
            sidebarLinks += `
                <a href="../pages/login.php" class="sidebar-link" id="login-link">
                    <span class="material-symbols-outlined">login</span>
                    <span>Login</span>
                </a>
            `;
        }
        document.getElementById("sidebar").innerHTML = `<div id="sidebar-content">${sidebarLinks}</div>`;
    }
}
function showSidebar() {
    document.getElementById("sidebar").classList.toggle("open");
    document.body.classList.toggle("no-scroll");
}