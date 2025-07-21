function copyTextToClipBoard(){
    let url = getCurrentURL();
    navigator.clipboard.writeText(url);
    //alert("Link kopiert!")
    document.getElementById("link_copied").classList.add("open");
}
function getCurrentURL () {
    return window.location.href
}