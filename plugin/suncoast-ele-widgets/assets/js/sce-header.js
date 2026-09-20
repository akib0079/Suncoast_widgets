/* ==========================================================================
   Suncoast — Header widget behaviour
   --------------------------------------------------------------------------
   • sticky / compress on scroll (rAF-throttled, passive listeners)
   • optional auto-hide on scroll-down
   • fixed-containing-block guard: Elementor sections routinely carry a
     `transform` (entrance animations, sticky effects) which silently turns
     `position:fixed` into `position:absolute`. We detect that and re-home
     `.sce-header__layer` onto <body>. The layer carries its own `sce-scope`
     class + CSS variables and holds every state class, so all styling keeps
     resolving after the move.
   • mobile panel: slide-in, stagger, focus trap, Esc, iOS-safe scroll lock
   • smooth in-page scrolling with a live header offset
   • scroll-spy that marks the active section
   Idempotent: calling SCEHeader.init(el) twice is safe (Elementor editor).
   ========================================================================== */
(function (w, d) {
	"use strict";

	var REDUCED = w.matchMedia && w.matchMedia("(prefers-reduced-motion: reduce)").matches;
	var FOCUSABLE = 'a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])';

	/* ---------------------------------------------------------------- utils */
	function raf(fn) {
		var ticking = false;
		return function () {
			if (ticking) return;
			ticking = true;
			w.requestAnimationFrame(function () { ticking = false; fn(); });
		};
	}

	/* True when a positioned ancestor would hijack `position:fixed`. */
	function hasFixedBreakingAncestor(el) {
		var p = el.parentElement;
		while (p && p !== d.body && p !== d.documentElement) {
			var cs = w.getComputedStyle(p);
			if (
				(cs.transform && cs.transform !== "none") ||
				(cs.filter && cs.filter !== "none") ||
				(cs.perspective && cs.perspective !== "none") ||
				(cs.contain && /paint|layout|strict|content/.test(cs.contain)) ||
				(cs.willChange && /transform|filter|perspective/.test(cs.willChange))
			) return true;
			p = p.parentElement;
		}
		return false;
	}

	/* Custom properties the header stylesheet consumes.

	   Elementor writes its controls as `{{WRAPPER}} .sce-header__layer { --x: … }`.
	   Once the layer is re-homed to <body> those selectors stop matching, so we
	   copy the resolved values across as inline styles and refresh them on
	   resize (the values are breakpoint-dependent). The root never moves, so it
	   stays the authoritative source. */
	var VARS = [
		"--sce-hdr-h", "--sce-hdr-h-stuck", "--sce-hdr-overlap",
		"--sce-hdr-bg", "--sce-hdr-bg-stuck", "--sce-hdr-blur", "--sce-hdr-blur-stuck",
		"--sce-hdr-border", "--sce-hdr-border-stuck", "--sce-hdr-shadow", "--sce-hdr-shadow-stuck",
		"--sce-hdr-z", "--sce-hdr-logo-h", "--sce-hdr-logo-h-panel",
		"--sce-hdr-nav-gap", "--sce-hdr-nav-color", "--sce-hdr-nav-hover", "--sce-hdr-nav-active",
		"--sce-hdr-nav-underline", "--sce-hdr-nav-size", "--sce-hdr-nav-weight",
		"--sce-hdr-nav-ls", "--sce-hdr-nav-tt",
		"--sce-hdr-phone-color", "--sce-hdr-phone-hover", "--sce-hdr-phone-icon",
		"--sce-hdr-phone-size", "--sce-hdr-phone-ls",
		"--sce-cta-bg", "--sce-cta-bg-hover", "--sce-cta-color", "--sce-cta-color-hover",
		"--sce-cta-radius", "--sce-cta-w", "--sce-cta-h", "--sce-cta-size",
		"--sce-cta-weight", "--sce-cta-ls",
		"--sce-hdr-panel-bg", "--sce-hdr-panel-w", "--sce-hdr-panel-link",
		"--sce-hdr-panel-link-hover", "--sce-hdr-panel-size",
		"--sce-hdr-burger-color", "--sce-hdr-scrim",
		"--sce-shell-max", "--sce-shell-pad"
	];

	function syncVars(from, to) {
		var cs = w.getComputedStyle(from);
		for (var i = 0; i < VARS.length; i++) {
			var v = cs.getPropertyValue(VARS[i]);
			if (v && v.trim()) to.style.setProperty(VARS[i], v.trim());
		}
	}

	/* Live instances, so the Elementor editor's re-renders don't leak listeners. */
	var registry = [];
	function sweep() {
		for (var i = registry.length - 1; i >= 0; i--) {
			var api = registry[i];
			if (api.root && api.root.isConnected) continue;
			registry.splice(i, 1);
			api.destroy(true);
		}
	}

	/* --------------------------------------------------------- scroll lock
	   Shared with sce-benefits.js through a single counter on `window`, so the
	   mobile menu and the video modal can never unlock each other's scroll. */
	function store() {
		return w.__sceScrollLock || (w.__sceScrollLock = { n: 0, y: 0 });
	}
	function lockScroll() {
		var s = store();
		if (s.n++ > 0) return;
		s.y = w.scrollY || w.pageYOffset || 0;
		var sbw = w.innerWidth - d.documentElement.clientWidth;
		d.body.style.position = "fixed";
		d.body.style.top = -s.y + "px";
		d.body.style.left = "0";
		d.body.style.right = "0";
		d.body.style.width = "100%";
		d.body.style.overflow = "hidden";
		if (sbw > 0) d.body.style.paddingRight = sbw + "px";
	}
	function unlockScroll() {
		var s = store();
		if (--s.n > 0) return;
		s.n = 0;
		var prev = d.documentElement.style.scrollBehavior;
		d.documentElement.style.scrollBehavior = "auto";
		d.body.style.position = "";
		d.body.style.top = "";
		d.body.style.left = "";
		d.body.style.right = "";
		d.body.style.width = "";
		d.body.style.overflow = "";
		d.body.style.paddingRight = "";
		w.scrollTo(0, s.y);
		d.documentElement.style.scrollBehavior = prev;
	}

	/* ============================================================== instance */
	function init(root) {
		if (!root || root.__sceHeader) return root && root.__sceHeader;

		var layer = root.querySelector(".sce-header__layer");
		if (!layer) return;
		var bar = layer.querySelector(".sce-header__bar");
		var panel = layer.querySelector(".sce-header__panel");
		var scrim = layer.querySelector(".sce-header__scrim");
		var burger = layer.querySelector(".sce-header__burger");
		var closeBtn = layer.querySelector(".sce-header__close");
		if (!bar) return;

		var stickAt = parseInt(root.getAttribute("data-stick-at"), 10);
		if (isNaN(stickAt)) stickAt = 8;
		var hideOnScroll = root.getAttribute("data-hide-on-scroll") === "yes";
		var spyOn = root.getAttribute("data-scrollspy") !== "no";
		var anchorPad = parseInt(root.getAttribute("data-anchor-offset"), 10);
		if (isNaN(anchorPad)) anchorPad = 12;

		var api = { root: root, layer: layer };
		root.__sceHeader = api;
		sweep();
		registry.push(api);

		/* ---- 1. keep `position:fixed` working wherever we're dropped ---- */
		var home = null;
		if (hasFixedBreakingAncestor(layer)) {
			home = { parent: layer.parentNode, next: layer.nextSibling };
			syncVars(root, layer);
			d.body.appendChild(layer);
		}

		/* ---- 2. sticky / compress / auto-hide --------------------------- */
		var lastY = w.scrollY || 0;
		var barH = 0;

		function publishHeight() {
			var h = bar.offsetHeight;
			if (h === barH) return;
			barH = h;
			d.documentElement.style.setProperty("--sce-header-h", h + "px");
			d.documentElement.style.scrollPaddingTop = (h + anchorPad) + "px";
		}
		publishHeight();

		var onScroll = raf(function () {
			var y = w.scrollY || w.pageYOffset || 0;
			layer.classList.toggle("is-stuck", y > stickAt);
			root.classList.toggle("is-stuck", y > stickAt);

			if (hideOnScroll && !layer.classList.contains("is-menu-open")) {
				var delta = y - lastY;
				if (y > barH * 2 && delta > 4) layer.classList.add("is-hidden");
				else if (delta < -4 || y <= barH) layer.classList.remove("is-hidden");
			}
			lastY = y;
			publishHeight();
			if (spyOn) spy();
		});

		/* ---- 3. scroll-spy ---------------------------------------------- */
		var links = [].slice.call(layer.querySelectorAll('a[href^="#"]'));
		var targets = [];
		function buildTargets() {
			var seen = {};
			targets = [];
			links.forEach(function (a) {
				var id = a.getAttribute("href").slice(1);
				if (!id || seen[id]) return;
				var el = d.getElementById(id);
				if (!el) return;
				seen[id] = 1;
				targets.push({ id: id, el: el });
			});
			targets.sort(function (a, b) {
				return a.el.getBoundingClientRect().top - b.el.getBoundingClientRect().top;
			});
		}
		buildTargets();

		var activeId = null;
		function spy() {
			if (!targets.length) return;
			var edge = bar.offsetHeight + anchorPad + 12;
			var found = null;
			for (var i = 0; i < targets.length; i++) {
				var r = targets[i].el.getBoundingClientRect();
				if (r.top - edge <= 0 && r.bottom - edge > 0) { found = targets[i].id; break; }
			}
			var atEnd = (w.innerHeight + (w.scrollY || 0)) >= (d.documentElement.scrollHeight - 2);
			if (!found && atEnd) found = targets[targets.length - 1].id;
			if (found === activeId) return;
			activeId = found;
			links.forEach(function (a) {
				var on = found !== null && a.getAttribute("href") === "#" + found;
				a.classList.toggle("is-active", on);
				if (on) a.setAttribute("aria-current", "true");
				else a.removeAttribute("aria-current");
			});
		}

		/* ---- 4. smooth in-page navigation -------------------------------- */
		function goTo(id, viaLink) {
			var el = d.getElementById(id);
			if (!el) return false;
			var doScroll = function () {
				var top = el.getBoundingClientRect().top + (w.scrollY || 0) - bar.offsetHeight - anchorPad;
				w.scrollTo({ top: top < 0 ? 0 : top, behavior: REDUCED ? "auto" : "smooth" });
				// move keyboard focus without triggering a second scroll jump
				if (!el.hasAttribute("tabindex")) el.setAttribute("tabindex", "-1");
				el.focus({ preventScroll: true });
				if (viaLink && w.history && w.history.replaceState) {
					w.history.replaceState(null, "", "#" + id);
				}
			};
			if (layer.classList.contains("is-menu-open")) { closeMenu(); setTimeout(doScroll, 200); }
			else doScroll();
			return true;
		}

		function onLinkClick(e) {
			var a = e.target.closest ? e.target.closest('a[href^="#"]') : null;
			if (!a || !layer.contains(a)) return;
			var href = a.getAttribute("href");
			if (!href || href === "#") return;
			if (goTo(href.slice(1), true)) e.preventDefault();
		}
		layer.addEventListener("click", onLinkClick);

		/* ---- 5. mobile panel --------------------------------------------- */
		var lastFocused = null;

		function openMenu() {
			if (layer.classList.contains("is-menu-open")) return;
			// The burger is the only way in, so it is also the only correct
			// place to put focus back — don't chase document.activeElement.
			lastFocused = burger || d.activeElement;
			layer.classList.add("is-menu-open");
			root.classList.add("is-menu-open");
			if (burger) burger.setAttribute("aria-expanded", "true");
			if (panel) {
				panel.removeAttribute("aria-hidden");
				panel.removeAttribute("inert");
			}
			lockScroll();
			if (closeBtn) setTimeout(function () { closeBtn.focus(); }, 60);
		}
		function closeMenu() {
			if (!layer.classList.contains("is-menu-open")) return;
			layer.classList.remove("is-menu-open");
			root.classList.remove("is-menu-open");
			if (burger) burger.setAttribute("aria-expanded", "false");
			if (panel) {
				panel.setAttribute("aria-hidden", "true");
				// `inert` matters during the 0.52s slide-out: visibility is still
				// `visible`, so without it the links stay tabbable while the panel
				// is already aria-hidden — a screen-reader/keyboard mismatch.
				panel.setAttribute("inert", "");
			}
			unlockScroll();
			if (lastFocused && lastFocused.focus) lastFocused.focus({ preventScroll: true });
		}

		if (burger) burger.addEventListener("click", openMenu);
		if (closeBtn) closeBtn.addEventListener("click", closeMenu);
		if (scrim) scrim.addEventListener("click", closeMenu);

		function onKeydown(e) {
			if (!layer.classList.contains("is-menu-open")) return;
			if (e.key === "Escape") { e.preventDefault(); closeMenu(); return; }
			if (e.key !== "Tab" || !panel) return;
			var items = [].slice.call(panel.querySelectorAll(FOCUSABLE)).filter(function (el) {
				return el.offsetWidth || el.offsetHeight || el.getClientRects().length;
			});
			if (!items.length) return;
			var first = items[0], last = items[items.length - 1];
			if (e.shiftKey && d.activeElement === first) { e.preventDefault(); last.focus(); }
			else if (!e.shiftKey && d.activeElement === last) { e.preventDefault(); first.focus(); }
		}
		d.addEventListener("keydown", onKeydown);

		var onResize = raf(function () {
			if (w.innerWidth > 1024) closeMenu();
			// breakpoint may have changed the resolved variables
			if (home) syncVars(root, layer);
			barH = -1;
			publishHeight();
			buildTargets();
			activeId = null;
			if (spyOn) spy();
		});

		/* ---- 6. boot ------------------------------------------------------ */
		w.addEventListener("scroll", onScroll, { passive: true });
		w.addEventListener("resize", onResize, { passive: true });
		onScroll();

		// honour a deep link that landed with a hash
		if (w.location.hash && w.location.hash.length > 1) {
			var hid = w.location.hash.slice(1);
			setTimeout(function () { goTo(hid, false); }, 60);
		}

		/**
		 * @param {boolean} detached true when the widget's DOM was replaced
		 *        under us (Elementor editor re-render) — the old layer then has
		 *        no home to return to and must simply be removed.
		 */
		api.destroy = function (detached) {
			w.removeEventListener("scroll", onScroll);
			w.removeEventListener("resize", onResize);
			d.removeEventListener("keydown", onKeydown);
			layer.removeEventListener("click", onLinkClick);
			closeMenu();
			if (detached) {
				if (layer.parentNode) layer.parentNode.removeChild(layer);
			} else if (home) {
				home.parent.insertBefore(layer, home.next);
			}
			delete root.__sceHeader;
		};
		api.openMenu = openMenu;
		api.closeMenu = closeMenu;
		api.goTo = goTo;
		return api;
	}

	function initAll(scope) {
		sweep();
		var node = scope && scope.nodeType === 1 ? scope : null;
		if (node && node.classList.contains("sce-header")) { init(node); return; }
		[].forEach.call((node || d).querySelectorAll(".sce-header"), init);
	}

	w.SCEHeader = { init: init, initAll: initAll };

	if (d.readyState === "loading") d.addEventListener("DOMContentLoaded", function () { initAll(); });
	else initAll();

	/* ---- Elementor editor: re-init whenever the widget is (re)rendered ---- */
	w.addEventListener("elementor/frontend/init", function () {
		if (!w.elementorFrontend || !w.elementorFrontend.hooks) return;
		w.elementorFrontend.hooks.addAction(
			"frontend/element_ready/suncoast_header.default",
			function ($scope) {
				var el = $scope && $scope[0] ? $scope[0] : $scope;
				if (el) initAll(el);
			}
		);
	});
})(window, document);
