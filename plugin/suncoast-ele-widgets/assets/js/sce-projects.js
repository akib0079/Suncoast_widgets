/* ==========================================================================
   Suncoast — Featured Projects widget behaviour
   --------------------------------------------------------------------------
   Reveal only: the cards are plain links, so there is nothing to wire up
   beyond the staged entrance. Same conventions as sce-banner.js /
   sce-benefits.js. Idempotent — safe to re-run in the Elementor editor.
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
		if (!root || root.__sceProjects) return root && root.__sceProjects;

		var api = { root: root };
		root.__sceProjects = api;
		sweep();
		registry.push(api);

		var fire = function () {
			w.requestAnimationFrame(function () { root.classList.add("is-revealed"); });
		};

		if (REDUCED) {
			root.classList.add("is-revealed");
		} else if (root.getBoundingClientRect().top < w.innerHeight * 0.85) {
			fontsReady().then(fire);
		} else if (!("IntersectionObserver" in w)) {
			fire();
		} else {
			api.io = new IntersectionObserver(function (entries) {
				for (var i = 0; i < entries.length; i++) {
					if (entries[i].isIntersecting) { fire(); api.io.disconnect(); break; }
				}
			}, { threshold: 0.08, rootMargin: "0px 0px -10% 0px" });
			api.io.observe(root);
		}

		api.destroy = function () {
			if (api.io && api.io.disconnect) api.io.disconnect();
			delete root.__sceProjects;
		};
		return api;
	}

	function initAll(scope) {
		sweep();
		var node = scope && scope.nodeType === 1 ? scope : null;
		if (node && node.classList.contains("sce-fp")) { init(node); return; }
		[].forEach.call((node || d).querySelectorAll(".sce-fp"), init);
	}

	w.SCEProjects = { init: init, initAll: initAll };

	if (d.readyState === "loading") d.addEventListener("DOMContentLoaded", function () { initAll(); });
	else initAll();

	/* ---- Elementor editor: re-init whenever the widget is (re)rendered ---- */
	w.addEventListener("elementor/frontend/init", function () {
		if (!w.elementorFrontend || !w.elementorFrontend.hooks) return;
		w.elementorFrontend.hooks.addAction(
			"frontend/element_ready/suncoast_projects.default",
			function ($scope) {
				var el = $scope && $scope[0] ? $scope[0] : $scope;
				if (el) initAll(el);
			}
		);
	});
})(window, document);
