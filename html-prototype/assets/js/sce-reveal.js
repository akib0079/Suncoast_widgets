/* ==========================================================================
   Suncoast — shared reveal module
   --------------------------------------------------------------------------
   Several widgets have no behaviour beyond the staged entrance, so they share
   one module instead of shipping near-identical copies. Add a selector to
   ROOTS and a name to the hook list to opt a widget in.
   Idempotent — safe to re-run in the Elementor editor.
   ========================================================================== */
(function (w, d) {
	"use strict";

	var ROOTS = ".sce-my, .sce-diff, .sce-cta, .sce-footer";
	var REDUCED = w.matchMedia && w.matchMedia("(prefers-reduced-motion: reduce)").matches;

	function fontsReady() {
		if (d.fonts && d.fonts.ready) {
			return Promise.race([d.fonts.ready, new Promise(function (r) { setTimeout(r, 600); })]);
		}
		return Promise.resolve();
	}

	var registry = [];
	function sweep() {
		for (var i = registry.length - 1; i >= 0; i--) {
			if (registry[i].root && registry[i].root.isConnected) continue;
			registry.splice(i, 1)[0].destroy();
		}
	}

	function init(root) {
		if (!root || root.__sceReveal) return root && root.__sceReveal;

		var api = { root: root };
		root.__sceReveal = api;
		sweep();
		registry.push(api);

		var fire = function () {
			// The rAF stages the transition: it guarantees the opacity:0 start
			// state has been painted before the class flips. A hidden document
			// never runs a frame — and has nothing to animate — so in that case
			// flip it straight away rather than wait for a frame that may never
			// come and leave the section stranded at opacity 0.
			if (d.hidden) { root.classList.add("is-revealed"); return; }
			w.requestAnimationFrame(function () { root.classList.add("is-revealed"); });
		};

		if (REDUCED) {
			root.classList.add("is-revealed");
		} else if (root.getBoundingClientRect().top < w.innerHeight * 0.85) {
			fontsReady().then(fire);
		} else if (!("IntersectionObserver" in w)) {
			fire();
		} else {
			// Failsafe: an IntersectionObserver delivers nothing while the
			// document is hidden, so a section could stay unrevealed — that is
			// invisible content, not merely a missing animation. Reveal anyway
			// after a few seconds if the observer has not.
			var settle = function () {
				if (api.t) { clearTimeout(api.t); api.t = null; }
				if (api.io) api.io.disconnect();
				fire();
			};
			api.t = setTimeout(settle, 3000);
			api.io = new IntersectionObserver(function (entries) {
				for (var i = 0; i < entries.length; i++) {
					if (entries[i].isIntersecting) { settle(); break; }
				}
			}, { threshold: 0.1, rootMargin: "0px 0px -10% 0px" });
			api.io.observe(root);
		}

		api.destroy = function () {
			if (api.t) { clearTimeout(api.t); api.t = null; }
			if (api.io && api.io.disconnect) api.io.disconnect();
			delete root.__sceReveal;
		};
		return api;
	}

	function initAll(scope) {
		sweep();
		var node = scope && scope.nodeType === 1 ? scope : null;
		if (node && node.matches && node.matches(ROOTS)) { init(node); return; }
		[].forEach.call((node || d).querySelectorAll(ROOTS), init);
	}

	w.SCEReveal = { init: init, initAll: initAll };

	if (d.readyState === "loading") d.addEventListener("DOMContentLoaded", function () { initAll(); });
	else initAll();

	/* ---- Elementor editor: re-init whenever either widget is rendered ---- */
	w.addEventListener("elementor/frontend/init", function () {
		if (!w.elementorFrontend || !w.elementorFrontend.hooks) return;
		["suncoast_grid", "suncoast_difference", "suncoast_cta", "suncoast_footer"].forEach(function (name) {
			w.elementorFrontend.hooks.addAction(
				"frontend/element_ready/" + name + ".default",
				function ($scope) {
					var el = $scope && $scope[0] ? $scope[0] : $scope;
					if (el) initAll(el);
				}
			);
		});
	});
})(window, document);
