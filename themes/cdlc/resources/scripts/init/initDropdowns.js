/**
 * Initialize dropdown elements.
 */
 const initDropdowns = () => {
  const dropdownElems = document.querySelectorAll('.dropdown');
  dropdownElems.forEach((dropdown) => {
    const toggleElem = dropdown.querySelector('.dropdown__toggle');
    toggleElem.addEventListener('click', (event) => {
      event.preventDefault();
      let isExpanded = toggleElem.getAttribute('aria-expanded') === 'true';
      toggleElem.setAttribute('aria-expanded', String(!isExpanded));

      if (toggleElem.classList.contains('dropdown__toggle-search')) {
        let isPressed = toggleElem.getAttribute('aria-pressed') === 'true';
        toggleElem.setAttribute('aria-pressed', String(!isPressed));
      }

      // Focus element if this is a search dropdown.
      if (toggleElem.classList.contains('dropdown__toggle-search') && !isExpanded) {
        setTimeout(() => {
          const firstFocusableElement = document.getElementById('searchTypeWebsite');
          firstFocusableElement.focus();
        }, 100);
      }
    });

    // Close menu from menu toggle on ESC keypress.
    toggleElem.addEventListener('keyup', (event) => {
      let isExpanded = toggle.getAttribute('aria-expanded') === 'true';
      if (event.keyCode === 27 && isExpanded) {
        toggleElem.setAttribute('aria-expanded', String(!isExpanded));
      }
    });

    // Close dropdown menu from inside on ESC keypress and return focus.
    const dropdownContent = toggleElem.nextElementSibling;
    if (dropdownContent) {
      dropdownContent.addEventListener('keyup', (event) => {
        if (event.keyCode === 27) {
          toggleElem.setAttribute('aria-expanded', 'false');
          toggleElem.focus();
        }
      });

      // Prevents menu from closing when clicking inside.
      dropdownContent.addEventListener('click', (event) => {
        event.stopPropagation();
      });
    }
  });

  // Close menu dropdowns if ESC is pressed outside nav.
  const toggleElems = document.querySelectorAll('.dropdown__toggle');
  document.body.addEventListener('keyup', (event) => {
    if (event.keyCode === 27) {
      toggleElems.forEach((toggle) => {
        toggle.setAttribute('aria-expanded', 'false');
      });
    }
  });

  // Close menu dropdown on body click.
  document.body.addEventListener('click', (event) => {
    if (!event.target.matches('.dropdown__toggle')) {
      toggleElems.forEach((toggle) => {
        toggle.setAttribute('aria-expanded', 'false');
      });
    }
  });
};

export default initDropdowns;
