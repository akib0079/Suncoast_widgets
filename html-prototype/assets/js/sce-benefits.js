/* ==========================================================================
   Suncoast — Benefits + Video widget behaviour
   --------------------------------------------------------------------------
   • staged reveal (shared conventions with sce-banner.js)
   • accessible video modal: focus trap, Esc, scrim, iOS-safe scroll lock
   • the embed is built on open and destroyed on close, so the video actually
     stops and nothing streams until the visitor asks for it
   • the modal node is moved to <body> on init — an Elementor ancestor with a
     transform would otherwise break its `position:fixed`
   Idempotent — safe to re-run in the Elementor editor.
   ========================================================================== */
(function (w, d) {
	"use strict";

	var REDUCED = w.matchMedia && w.matchMedia("(prefers-reduced-motion: reduce)").matches;
	var FOCUSABLE = 'a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),iframe,video,[tabindex]:not([tabindex="-1"])';

	function fontsReady() {
		if (d.fonts && d.fonts.ready) {
			return Promise.race([d.fonts.ready, new Promise(function (r) { setTimeout(r, 600); })]);
		}
		return Promise.resolve();
	}

	/* --------------------------------------------------------- scroll lock
	   Shared with sce-header.js through a single counter on `window`, so the
	   mobile menu and this modal can never unlock each other's scroll. */
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

	/* ------------------------------------------------------------- reveal */
	function initReveal(root) {
		if (REDUCED) { root.classList.add("is-revealed"); return null; }
		var fire = function () {
			w.requestAnimationFrame(function () { root.classList.add("is-revealed"); });
		};
		if (root.getBoundingClientRect().top < w.innerHeight * 0.85) {
			fontsReady().then(fire);
			return null;
		}
		if (!("IntersectionObserver" in w)) { fire(); return null; }
		var io = new IntersectionObserver(function (entries) {
			for (var i = 0; i < entries.length; i++) {
				if (entries[i].isIntersecting) { fire(); io.disconnect(); break; }
			}
		}, { threshold: 0.1, rootMargin: "0px 0px -10% 0px" });
		io.observe(root);
		return io;
	}

	/* -------------------------------------------------------------- embed */
	function buildEmbed(modal) {
		var type = modal.getAttribute("data-type");
		var src = modal.getAttribute("data-src");
		if (!src) return null;

		if ("file" === type) {
			var v = d.createElement("video");
			v.src = src;
			v.setAttribute("playsinline", "");
			v.controls = modal.getAttribute("data-controls") !== "no";
			v.loop = modal.getAttribute("data-loop") === "yes";
			v.muted = modal.getAttribute("data-muted") === "yes";
			if (modal.getAttribute("data-autoplay") !== "no") {
				v.autoplay = true;
				// A browser may still refuse an unmuted autoplay; surfacing the
				// controls beats a silently blank frame.
				var p = v.play && v.play();
				if (p && p.catch) p.catch(function () { v.controls = true; });
			}
			return v;
		}

		var f = d.createElement("iframe");
		f.src = src;
		f.title = modal.getAttribute("data-title") || "Video";
		f.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share";
		f.setAttribute("allowfullscreen", "");
		f.setAttribute("referrerpolicy", "strict-origin-when-cross-origin");
		return f;
	}

	/* -------------------------------------------------------------- modal */
	function initModal(root, api) {
		var modal = root.querySelector(".sce-modal");
		var play = root.querySelector(".sce-ben__play");
		if (!modal || !play) return;

		var frame = modal.querySelector(".sce-modal__frame");
		var closeBtn = modal.querySelector(".sce-modal__close");
		var scrim = modal.querySelector(".sce-modal__scrim");
		var opener = null;

		// Escape any transformed ancestor so `position:fixed` keeps working.
		var home = { parent: modal.parentNode, next: modal.nextSibling };
		d.body.appendChild(modal);

		function open() {
			if (modal.classList.contains("is-open")) return;
			// The play button is the only way in, so it is also the only correct
			// place to put focus back — don't chase document.activeElement.
			opener = play;

			var embed = buildEmbed(modal);
			if (embed) { frame.textContent = ""; frame.appendChild(embed); }

			modal.classList.add("is-open");
			modal.removeAttribute("aria-hidden");
			modal.removeAttribute("inert");
			play.setAttribute("aria-expanded", "true");
			lockScroll();
			setTimeout(function () { if (closeBtn) closeBtn.focus(); }, 60);
		}

		function close() {
			if (!modal.classList.contains("is-open")) return;
			modal.classList.remove("is-open");
			modal.setAttribute("aria-hidden", "true");
			modal.setAttribute("inert", "");
			play.setAttribute("aria-expanded", "false");
			unlockScroll();
			// Tear the embed down once the fade-out has finished — this is what
			// actually stops YouTube/Vimeo audio.
			setTimeout(function () {
				if (!modal.classList.contains("is-open")) frame.textContent = "";
			}, 360);
			if (opener && opener.focus) opener.focus({ preventScroll: true });
		}

		function onKeydown(e) {
			if (!modal.classList.contains("is-open")) return;
			if (e.key === "Escape") { e.preventDefault(); close(); return; }
			if (e.key !== "Tab") return;
			var items = [].slice.call(modal.querySelectorAll(FOCUSABLE)).filter(function (el) {
				return el.offsetWidth || el.offsetHeight || el.getClientRects().length;
			});
			if (!items.length) return;
			var first = items[0], last = items[items.length - 1];
			if (e.shiftKey && d.activeElement === first) { e.preventDefault(); last.focus(); }
			else if (!e.shiftKey && d.activeElement === last) { e.preventDefault(); first.focus(); }
		}

		play.addEventListener("click", open);
		if (closeBtn) closeBtn.addEventListener("click", close);
		if (scrim) scrim.addEventListener("click", close);
		d.addEventListener("keydown", onKeydown);

		api.closeModal = close;
		api.openModal = open;
		api.teardownModal = function () {
			d.removeEventListener("keydown", onKeydown);
			close();
			if (modal.parentNode) modal.parentNode.removeChild(modal);
			if (home.parent && home.parent.isConnected) home.parent.insertBefore(modal, home.next);
		};
	}

	/* ----------------------------------------------------------- instance */
	var registry = [];
	function sweep() {
		for (var i = registry.length - 1; i >= 0; i--) {
			if (registry[i].root && registry[i].root.isConnected) continue;
			registry.splice(i, 1)[0].destroy();
		}
	}

	function init(root) {
		if (!root || root.__sceBenefits) return root && root.__sceBenefits;

		var api = { root: root };
		root.__sceBenefits = api;
		sweep();
		registry.push(api);

		api.reveal = initReveal(root);
		initModal(root, api);

		api.destroy = function () {
			if (api.reveal && api.reveal.disconnect) api.reveal.disconnect();
			if (api.teardownModal) api.teardownModal();
			delete root.__sceBenefits;
		};
		return api;
	}

	function initAll(scope) {
		sweep();
		var node = scope && scope.nodeType === 1 ? scope : null;
		if (node && node.classList.contains("sce-ben")) { init(node); return; }
		[].forEach.call((node || d).querySelectorAll(".sce-ben"), init);
	}

	w.SCEBenefits = { init: init, initAll: initAll };

	if (d.readyState === "loading") d.addEventListener("DOMContentLoaded", function () { initAll(); });
	else initAll();

	/* ---- Elementor editor: re-init whenever the widget is (re)rendered ---- */
	w.addEventListener("elementor/frontend/init", function () {
		if (!w.elementorFrontend || !w.elementorFrontend.hooks) return;
		w.elementorFrontend.hooks.addAction(
			"frontend/element_ready/suncoast_benefits.default",
			function ($scope) {
				var el = $scope && $scope[0] ? $scope[0] : $scope;
				if (el) initAll(el);
			}
		);
	});
})(window, document);
