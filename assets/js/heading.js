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
                <button class="material-symbols-outlined">search</span>
            </p>
        </div>
        <div id="filter-container">
            <button id="close-filter" class="material-symbols-outlined" onclick="closefilter()">close</button>
            <a href="pages/suche.php?filter=asiatisch" class="filter-link">Asiatisch</a>
            <a href="pages/suche.php?filter=nudeln" class="filter-link">Nudeln</a>
            <a href="pages/suche.php?filter=kartoffeln" class="filter-link">Kartoffeln</a>
            <a href="pages/suche.php?filter=reis" class="filter-link">Reis</a>
            <a href="pages/suche.php?filter=fleisch" class="filter-link">Fleisch</a>
            <a href="pages/suche.php?filter=hühnchen" class="filter-link">Hühnchen</a>
            <a href="pages/suche.php?filter=schwein" class="filter-link">Schwein</a>
            <a href="pages/suche.php?filter=rind" class="filter-link">Rind</a>
            <a href="pages/suche.php?filter=fisch" class="filter-link">Fisch</a>
            <a href="pages/suche.php?filter=suppe" class="filter-link">Suppe</a>
            <a href="pages/suche.php?filter=soße" class="filter-link">Soße</a>
            <a href="pages/suche.php?filter=dessert" class="filter-link">Dessert</a>
            <a href="pages/suche.php?filter=kuchen" class="filter-link">Kuchen</a>
            <a href="pages/suche.php?filter=vegan" class="filter-link">Vegan</a>
            <a href="pages/suche.php?filter=vegetarisch" class="filter-link">Vegetarisch</a>
            <a href="pages/suche.php?filter=getränk" class="filter-link">Getränke</a>
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
                <button class="material-symbols-outlined">search</span>
            </p>
        </div>
        <div id="filter-container">
            <button id="close-filter" class="material-symbols-outlined" onclick="closefilter()">close</button>
            <a href="suche.php?filter=asiatisch" class="filter-link">Asiatisch</a>
            <a href="suche.php?filter=nudeln" class="filter-link">Nudeln</a>
            <a href="suche.php?filter=kartoffeln" class="filter-link">Kartoffeln</a>
            <a href="suche.php?filter=reis" class="filter-link">Reis</a>
            <a href="suche.php?filter=fleisch" class="filter-link">Fleisch</a>
            <a href="suche.php?filter=hühnchen" class="filter-link">Hühnchen</a>
            <a href="suche.php?filter=schwein" class="filter-link">Schwein</a>
            <a href="suche.php?filter=rind" class="filter-link">Rind</a>
            <a href="suche.php?filter=fisch" class="filter-link">Fisch</a>
            <a href="suche.php?filter=suppe" class="filter-link">Suppe</a>
            <a href="suche.php?filter=soße" class="filter-link">Soße</a>
            <a href="suche.php?filter=dessert" class="filter-link">Dessert</a>
            <a href="suche.php?filter=kuchen" class="filter-link">Kuchen</a>
            <a href="suche.php?filter=vegan" class="filter-link">Vegan</a>
            <a href="suche.php?filter=vegetarisch" class="filter-link">Vegetarisch</a>
            <a href="suche.php?filter=getränk" class="filter-link">Getränke</a>
            <button id="rezept-erstellen" onclick="window.location.href = 'rezept-erstellen.php'"><span>Rezept erstellen</span></button>
        </div>
        `;
    }
});
function openfilter() {
    document.getElementById('filter-container').classList.toggle('open');
    document.body.classList.add('no-scroll');
};
function closefilter() {
    document.getElementById('filter-container').classList.toggle('open');
    document.body.classList.remove('no-scroll');
};