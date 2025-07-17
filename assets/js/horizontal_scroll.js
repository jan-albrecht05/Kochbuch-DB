document.querySelectorAll('.inner-container').forEach(container => {
  let isDragging = false;
  let startX;
  let scrollLeft;

  container.addEventListener('mousedown', (e) => {
    isDragging = true;
    startX = e.pageX - container.offsetLeft;
    scrollLeft = container.scrollLeft;
    container.style.cursor = 'grabbing';
  });

  container.addEventListener('mouseleave', () => {
    isDragging = false;
    container.style.cursor = 'ew-resize';
  });

  container.addEventListener('mouseup', () => {
    isDragging = false;
    container.style.cursor = 'ew-resize';
  });

  container.addEventListener('mousemove', (e) => {
    if (!isDragging) return;
    e.preventDefault();
    const x = e.pageX - container.offsetLeft;
    const walk = (x - startX) * 1.5;
    container.scrollLeft = scrollLeft - walk;
  });

  // Get only the arrows inside this container
  const arrowLeft = container.querySelector('.arrow-left');
  const arrowRight = container.querySelector('.arrow-right');

  // Show/hide arrows according to scroll position
  function updateArrows() {
    if (!arrowLeft || !arrowRight) return;
    arrowLeft.style.display = container.scrollLeft > 0 ? 'flex' : 'none';
    arrowRight.style.display = container.scrollLeft < (container.scrollWidth - container.clientWidth) ? 'flex' : 'none';
  }
  updateArrows();

  container.addEventListener('scroll', updateArrows);

  // Add click event to arrows
  if (arrowLeft) {
    arrowLeft.addEventListener('click', (e) => {
      e.stopPropagation();
      container.scrollLeft -= 100;
    });
  }
  if (arrowRight) {
    arrowRight.addEventListener('click', (e) => {
      e.stopPropagation();
      container.scrollLeft += 100;
    });
  }
});