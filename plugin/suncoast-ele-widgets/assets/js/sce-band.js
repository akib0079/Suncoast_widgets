/* ==========================================================================
   Suncoast — Benefits Band widget behaviour
   Reveal only. Same conventions as the other modules; idempotent.
   ========================================================================== */
(function (w, d) {
	"use strict";

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
		if (!root || root.__sceBand) return root && root.__sceBand;
		var api = { root: root };
		root.__sceBand = api;
		sweep();
		registry.push(api);

		var fire = function () {
			w.requestAnimationFrame(function () { root.classList.add("is-revealed"); });
		};

		if (REDUCED) root.classList.add("is-revealed");
		else if (root.getBoundingClientRect().top < w.innerHeight * 0.85) fontsReady().then(fire);
		else if (!("IntersectionObserver" in w)) fire();
		else {
			api.io = new IntersectionObserver(function (e) {
				for (var i = 0; i < e.length; i++) {
					if (e[i].isIntersecting) { fire(); api.io.disconnect(); break; }
				}
			}, { threshold: 0.1, rootMargin: "0px 0px -10% 0px" });
			api.io.observe(root);
		}

		api.destroy = function () {
			if (api.io && api.io.disconnect) api.io.disconnect();
			delete root.__sceBand;
		};
		return api;
	}

	function initAll(scope) {
		sweep();
		var node = scope && scope.nodeType === 1 ? scope : null;
		if (node && node.classList.contains("sce-band")) { init(node); return; }
		[].forEach.call((node || d).querySelectorAll(".sce-band"), init);
	}

	w.SCEBand = { init: init, initAll: initAll };

	if (d.readyState === "loading") d.addEventListener("DOMContentLoaded", function () { initAll(); });
	else initAll();

	w.addEventListener("elementor/frontend/init", function () {
		if (!w.elementorFrontend || !w.elementorFrontend.hooks) return;
		w.elementorFrontend.hooks.addAction("frontend/element_ready/suncoast_band.default", function ($s) {
			var el = $s && $s[0] ? $s[0] : $s;
			if (el) initAll(el);
		});
	});
})(window, document);
