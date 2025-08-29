let currentPage = 1;

function setSectionPosition(section, position) {
    section.classList.remove('left', 'mid', 'right');
    section.classList.add(position);
}

function nextPage() {
    const sections = document.querySelectorAll('.section');
    if (currentPage < sections.length) {
        setSectionPosition(sections[currentPage - 1], 'left');
        setSectionPosition(sections[currentPage], 'mid');
        if (sections[currentPage + 1]) setSectionPosition(sections[currentPage + 1], 'right');
        currentPage++;
    }
}

function prevPage() {
    const sections = document.querySelectorAll('.section');
    if (currentPage > 1) {
        setSectionPosition(sections[currentPage - 1], 'right');
        setSectionPosition(sections[currentPage - 2], 'mid');
        if (sections[currentPage - 3]) setSectionPosition(sections[currentPage - 3], 'left');
        currentPage--;
    }
}