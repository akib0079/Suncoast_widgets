/* ==========================================================================
   Suncoast — Hero Banner widget behaviour
   --------------------------------------------------------------------------
   • staged reveal (above-the-fold fires on fonts-ready, below-fold on IO)
   • seamless marquee: clones until it overflows, duration derived from a
     constant px/second speed so short and long copy scroll at the same pace
   • form: select placeholder colour, input masks, validation + AJAX submit
   Idempotent — safe to re-run in the Elementor editor.
   ========================================================================== */
(function (w, d) {
	"use strict";

	var REDUCED = w.matchMedia && w.matchMedia("(prefers-reduced-motion: reduce)").matches;

	function raf(fn) {
		var ticking = false;
		return function () {
			if (ticking) return;
			ticking = true;
			w.requestAnimationFrame(function () { ticking = false; fn(); });
		};
	}

	function fontsReady() {
		if (d.fonts && d.fonts.ready) {
			// never let a slow font block the reveal for more than 600ms
			return Promise.race([d.fonts.ready, new Promise(function (r) { setTimeout(r, 600); })]);
		}
		return Promise.resolve();
	}

	/* ------------------------------------------------------------- reveal */
	function initReveal(root) {
		if (REDUCED) { root.classList.add("is-revealed"); return null; }

		var fire = function () {
			// The rAF stages the transition: it guarantees the opacity:0 start
			// state has been painted before the class flips. A hidden document
			// never runs a frame — and has nothing to animate — so in that case
			// flip it straight away rather than wait for a frame that may never
			// come and leave the section stranded at opacity 0.
			if (d.hidden) { root.classList.add("is-revealed"); return; }
			w.requestAnimationFrame(function () { root.classList.add("is-revealed"); });
		};

		var r = root.getBoundingClientRect();
		var aboveFold = r.top < w.innerHeight * 0.85;

		if (aboveFold) { fontsReady().then(fire); return null; }

		if (!("IntersectionObserver" in w)) { fire(); return null; }
		// Failsafe: an IntersectionObserver delivers nothing while the
		// document is hidden, so a section could stay unrevealed — that is
		// invisible content, not merely a missing animation. Reveal anyway
		// after a few seconds if the observer has not.
		var timer = null;
		var settle0 = function () {
			if (timer) { clearTimeout(timer); timer = null; }
			if (io) io.disconnect();
		};
		var settle = function () { settle0(); fire(); };
		var io = new IntersectionObserver(function (entries) {
			for (var i = 0; i < entries.length; i++) {
				if (entries[i].isIntersecting) { settle(); break; }
			}
		}, { threshold: 0.12, rootMargin: "0px 0px -8% 0px" });
		io.observe(root);
		timer = setTimeout(settle, 3000);
		return { disconnect: settle0 };
	}

	/* ------------------------------------------------------------ marquee */
	function initMarquee(mq) {
		var track = mq.querySelector(".sce-mq__track");
		if (!track) return null;
		var sets = [].slice.call(track.querySelectorAll(".sce-mq__set"));
		if (sets.length < 2) return null;

		var base = sets[0].innerHTML;
		var speed = parseFloat(mq.getAttribute("data-speed")) || 60; // px per second

		function layout() {
			sets.forEach(function (s) { s.innerHTML = base; });

			// repeat the run until one set alone can cover the viewport,
			// otherwise a short list leaves a visible empty gap mid-cycle
			var need = mq.clientWidth;
			var guard = 0;
			while (sets[0].scrollWidth < need && guard++ < 16) {
				sets.forEach(function (s) { s.insertAdjacentHTML("beforeend", base); });
			}

			var setW = sets[0].getBoundingClientRect().width;
			if (!setW) return;
			track.style.setProperty("--sce-mq-dur", (setW / speed).toFixed(2) + "s");
		}

		layout();

		var onResize = raf(layout);
		w.addEventListener("resize", onResize, { passive: true });

		// re-measure once webfonts swap in — metrics change, speed must not
		fontsReady().then(layout);

		return { destroy: function () { w.removeEventListener("resize", onResize); } };
	}

	/* Live instances, so the Elementor editor's re-renders don't leak listeners. */
	var registry = [];
	function sweep() {
		for (var i = registry.length - 1; i >= 0; i--) {
			if (registry[i].root && registry[i].root.isConnected) continue;
			registry.splice(i, 1)[0].destroy();
		}
	}

	/* ----------------------------------------------------------- instance */
	function init(root) {
		if (!root || root.__sceBanner) return root && root.__sceBanner;

		var api = { root: root };
		root.__sceBanner = api;
		sweep();
		registry.push(api);

		api.reveal = initReveal(root);
		var mq = root.querySelector(".sce-mq");
		if (mq) api.marquee = initMarquee(mq);
		if (w.SCEForm) w.SCEForm.init(root.querySelector(".sce-form"));

		api.destroy = function () {
			if (api.reveal && api.reveal.disconnect) api.reveal.disconnect();
			if (api.marquee && api.marquee.destroy) api.marquee.destroy();
			delete root.__sceBanner;
		};
		return api;
	}

	function initAll(scope) {
		sweep();
		var node = scope && scope.nodeType === 1 ? scope : null;
		if (node && node.classList.contains("sce-banner")) { init(node); return; }
		[].forEach.call((node || d).querySelectorAll(".sce-banner"), init);
	}

	w.SCEBanner = { init: init, initAll: initAll };

	if (d.readyState === "loading") d.addEventListener("DOMContentLoaded", function () { initAll(); });
	else initAll();

	/* ---- Elementor editor: re-init whenever the widget is (re)rendered ---- */
	w.addEventListener("elementor/frontend/init", function () {
		if (!w.elementorFrontend || !w.elementorFrontend.hooks) return;
		w.elementorFrontend.hooks.addAction(
			"frontend/element_ready/suncoast_banner.default",
			function ($scope) {
				var el = $scope && $scope[0] ? $scope[0] : $scope;
				if (el) initAll(el);
			}
		);
	});
})(window, document);
