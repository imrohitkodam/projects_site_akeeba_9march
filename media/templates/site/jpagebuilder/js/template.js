Joomla = window.Joomla || {}, function(e, t) {
	"use strict";
	function l(e) {
		for (var l = (e && e.target ? e.target : t).querySelectorAll("fieldset.btn-group"), o = 0; o < l.length; o++) {
			var n = l[o];
			if (!0 === n.getAttribute("disabled")) {
				n.style.pointerEvents = "none";
				for (var i = n.querySelectorAll(".btn"), r = 0; r < i.length; r++)
					i[r].classList.add("disabled")
			}
		}
	}
	t.addEventListener("DOMContentLoaded", (function(e) {
		l(e);
		var o = t.getElementById("back-top");
		if (o) {
			function n() {
				t.body.scrollTop > 20 || t.documentElement.scrollTop > 20 ? o.classList.add("visible") : o.classList.remove("visible")
			}
			n(), window.onscroll = function() {
				n()
			}, o.addEventListener("click", (function(e) {
				e.preventDefault(), window.scrollTo(0, 0)
			}))
		}
		[].slice.call(t.head.querySelectorAll('link[rel="lazy-stylesheet"]')).forEach((function(e) {
			e.rel = "stylesheet"
		}))
	})), t.addEventListener("joomla:updated", l)
}(Joomla, document);

document.addEventListener('DOMContentLoaded', function () {
  /**
   * Detect if the device is touch-capable.
   * Note: this detects capability, not the current input method (mouse vs touch).
   */
  const hasTouch =
    ('ontouchstart' in window) ||
    (navigator.maxTouchPoints > 0) ||
    (navigator.msMaxTouchPoints > 0);

  /**
   * Returns true if the menu is actually visible on screen via CSS (e.g. hover rules),
   * even if Bootstrap has not added the `.show` class.
   */
  function isActuallyVisible(menu) {
    if (!menu) return false;

    const cs = getComputedStyle(menu);

    return (
      cs.visibility !== 'hidden' &&
      cs.display !== 'none' &&
      Number(cs.opacity) > 0
    );
  }

  /**
   * Handle nested dropdown toggles.
   * - On touch devices: 1st tap opens submenu, 2nd tap navigates.
   * - On desktop (non-touch): if submenu is already visible via hover, click navigates.
   */
  document.querySelectorAll('.nav-link.dropdown-toggle').forEach(function (el) {
    el.addEventListener('pointerdown', function (e) {
      const nextMenu = el.nextElementSibling;

      // Only act if this toggle has a nested submenu right after it
      if (!nextMenu || !nextMenu.classList.contains('dropdown-menu')) return;

      const liDropdown = el.closest('li.dropdown'); // parent <li class="dropdown ...">

      // Consider the submenu "open" if:
      // - Bootstrap added `.show`, OR
      // - On non-touch devices it is visible via CSS hover (no `.show` needed)
      const openNow =
        nextMenu.classList.contains('show') ||
        (!hasTouch && isActuallyVisible(nextMenu));

      // If it's already open/visible -> navigate manually
      // (Bootstrap typically prevents default navigation for dropdown toggles)
      if (openNow) {
        // Allow ctrl/cmd click or middle click to open in a new tab
        if (e.ctrlKey || e.metaKey || e.button === 1) return;

        e.preventDefault();
        e.stopPropagation();
		if(el.href) {
	        window.location.href = el.href;
		}
        return;
      }

      // If it's not open -> first tap should open the submenu and NOT navigate
      e.preventDefault();
      e.stopPropagation();

      // Close other open submenus at the same level
      const parentMenu = el.closest('.dropdown-menu');
      if (parentMenu) {
        parentMenu.querySelectorAll('.dropdown-menu.show').forEach(function (submenu) {
          if (submenu !== nextMenu) {
            submenu.classList.remove('show');

            const li = submenu.closest('li.dropdown');
            if (li) li.classList.remove('show');
          }
        });
      }

      // Open this submenu
      nextMenu.classList.add('show');
      if (liDropdown) liDropdown.classList.add('show');
      el.setAttribute('aria-expanded', 'true');
    });
  });

  /**
   * When Bootstrap closes a dropdown, also close any nested submenus.
   */
  document.addEventListener('hide.bs.dropdown', function (ev) {
    ev.target.querySelectorAll('li.dropdown.show').forEach(li => li.classList.remove('show'));
    ev.target.querySelectorAll('.dropdown-menu.show').forEach(m => m.classList.remove('show'));
    ev.target
      .querySelectorAll('.dropdown-toggle[aria-expanded="true"]')
      .forEach(a => a.setAttribute('aria-expanded', 'false'));
  });

  /**
   * Logo click navigation (if your logo has data-href).
   */
  document.querySelectorAll('.navbar-logo').forEach(img => {
    img.addEventListener('click', () => {
      const url = img.getAttribute('data-href');
      if (url) window.location.href = url;
    });
  });
});