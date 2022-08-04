/**
 * Initialize all of the main menu interactions.
 */
const initMainMenu = () => {
  const menuToggles = document.querySelectorAll('.menu-toggle');
  menuToggles.forEach((toggle) => {
    toggle.addEventListener('click', (event) => {
      event.preventDefault();

      // Close all toggles except the current one.
      menuToggles.forEach((el) => {
        if (toggle === el) {
          return;
        }

        el.setAttribute('aria-expanded', 'false');
      });

      let isExpanded = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(!isExpanded));
    });

    // Close menu from menu toggle on ESC keypress.
    toggle.addEventListener('keyup', (event) => {
      let isExpanded = toggle.getAttribute('aria-expanded') === 'true';
      if (event.keyCode === 27 && isExpanded) {
        toggle.setAttribute('aria-expanded', String(!isExpanded));
      }
    });

    // Close dropdown menu from inside on ESC keypress and return focus.
    const menuDropdown = toggle.nextElementSibling;
    if (menuDropdown) {
      menuDropdown.addEventListener('keyup', (event) => {
        if (event.keyCode === 27) {
          toggle.setAttribute('aria-expanded', 'false');
          toggle.focus();
        }
      });

      // Prevents menu from closing when clicking inside.
      menuDropdown.addEventListener('click', (event) => {
        event.stopPropagation();
      });
    }
  });

  // Close menu dropdowns if ESC is pressed outside nav.
  document.body.addEventListener('keyup', (event) => {
    if (event.keyCode === 27) {
      menuToggles.forEach((toggle) => {
        toggle.setAttribute('aria-expanded', 'false');
      });
    }
  });

  // Close menu dropdown on body click.
  document.body.addEventListener('click', (event) => {
    if (!event.target.matches('.menu-toggle')) {
      menuToggles.forEach((toggle) => {
        toggle.setAttribute('aria-expanded', 'false');
      });
    }
  });

  // Mouse behavior.
  const menuDropdowns = document.querySelectorAll('.menu-item-has-children');
  menuDropdowns.forEach((dropdown) => {
    dropdown.addEventListener('mouseenter', () => {
      dropdown.classList.add('open');
    });

    dropdown.addEventListener('mouseleave', () => {
      dropdown.classList.remove('open');
    });
  });

  // Mobile menu toggle.
  const mobileMenuToggle = document.querySelector('.btn-menu-toggle');
  const mobileMenu = document.querySelector('.nav-primary');
  mobileMenuToggle.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');

    let expanded = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
    mobileMenuToggle.setAttribute('aria-expanded', String(!expanded));
  });
};

export default initMainMenu;
