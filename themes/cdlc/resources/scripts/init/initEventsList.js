/**
 * Initialize dropdown elements.
 */
const initEventsList = () => {
  const data = JSON.parse(document.querySelector('[data-js="tribe-events-view-data"]').textContent);

  if (data.slug === 'list') {
    let nextBtn = document.querySelector('.btn-next');
    let prevBtn = document.querySelector('.btn-prev');

    if (!data.prev_url) {
      prevBtn.remove();
    } else {
      prevBtn.setAttribute('href', data.prev_url);
    }

    if (!data.next_url) {
      nextBtn.remove();
    } else {
      nextBtn.setAttribute('href', data.next_url);
    }
  }
};

export default initEventsList;
