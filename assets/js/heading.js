window.addEventListener('load', function(event) {
    if (typeof homepage === "undefined") {
        homepage = false;
    }
    if (homepage){
        document.getElementById('heading').innerHTML = `
        <div id="inner-heading">
            <a href="#" id="logo">
                <img src="assets/icons/Topficon.png" alt="Kochbuch Icon">
            </a>
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
                <a href="pages/suche.php?filter=soße" class="filter-link">Soße</a>
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
                <a href="pages/suche.php?filter=getränk" class="filter-link">Getränke</a>
            </div>
            <button id="rezept-erstellen" onclick="window.location.href = 'pages/rezept-erstellen.php'"><span>Rezept erstellen</span></button>
        </div>
        `;
    }
    else{
        document.getElementById('heading').innerHTML = `
        <div id="inner-heading">
            <a href="../index.php" id="logo">
                <img src="../assets/icons/Topficon.png" alt="Kochbuch Icon">
            </a>
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
                <a href="../pages/suche.php?filter=soße" class="filter-link">Soße</a>
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
                <a href="../pages/suche.php?filter=getränk" class="filter-link">Getränke</a>
            </div>
            <button id="rezept-erstellen" onclick="window.location.href = '../pages/rezept-erstellen.php'"><span>Rezept erstellen</span></button>
        </div>
        `;
    }
    //insertfooter();
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