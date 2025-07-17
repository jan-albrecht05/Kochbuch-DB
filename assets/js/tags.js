// Toggle 'active' class on click
document.addEventListener('DOMContentLoaded', function() {
    const tags = document.querySelectorAll('.tag');
    tags.forEach(tag => {
        tag.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    });
});