function insertfooter(){
    if(homepage){
        footerHtml = '<div id="inner-footer">'+
        '<a href="pages/FAQ.html" class="center">FAQ <span class="material-symbols-outlined">open_in_new</span></a>'+
        '<p>© 2025 Kochbuch</p>'+
        '<p>Alle Rechte vorbehalten.</p>'+
        '</div>';
    } else {
        footerHtml = '<div id="inner-footer">'+ 
        '<a href="../pages/FAQ.html" class="center">FAQ <span class="material-symbols-outlined">open_in_new</span></a>'+
        '<p>© 2025 Kochbuch</p>'+
        '<p>Alle Rechte vorbehalten.</p>';
    };
    document.getElementById("footer").innerHTML = footerHtml;
    return;
}