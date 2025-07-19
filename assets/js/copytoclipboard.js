function copyTextToClipBoard(){
    let url = getCurrentURL();
    navigator.clipboard.writeText(url);
    alert("Link kopiert!")
}
function getCurrentURL () {
    return window.location.href
}