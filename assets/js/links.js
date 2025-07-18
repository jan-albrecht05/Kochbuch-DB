document.querySelectorAll(".rezeptlink").forEach(item => {
    item.addEventListener("click", function() {
        const clickedElementId = this.id;
        location.href = "pages/gericht.php?id=" + clickedElementId;
    });
});