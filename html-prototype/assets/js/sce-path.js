/* ==========================================================================
   Suncoast — Testimonials + Path widget behaviour
   --------------------------------------------------------------------------
   • testimonial slider: cross-fade, pagination dots, autoplay that pauses on
     hover/focus and whenever the section is off-screen, swipe on touch,
     arrow-key support
   • path: steps light up 01 → 02 → 03 → 04 on a timer, and a pointer or
     keyboard focus takes over so the visitor is never fighting the clock
   Both timers are driven by one IntersectionObserver, so nothing ticks while
   the section is out of view.
   Idempotent — safe to re-run in the Elementor editor.
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

	function num(el, attr, fallback) {
		var v = parseInt(el.getAttribute(attr), 10);
		return isNaN(v) ? fallback : v;
	}

	/* ------------------------------------------------------------- slider */
	function initSlider(root, visible) {
		var stage = root.querySelector(".sce-tm__stage");
		if (!stage) return null;

		var slides = [].slice.call(stage.querySelectorAll(".sce-tm__slide"));
		var dots = [].slice.call(root.querySelectorAll(".sce-tm__dot"));
		if (slides.length < 2) {
			if (slides[0]) slides[0].classList.add("is-active");
			return null;
		}

		var delay = num(root, "data-slider-delay", 6000);
		var auto = root.getAttribute("data-slider-auto") !== "no" && !REDUCED;
		var index = 0;
		var timer = null;
		var hovered = false;

		function show(i, userDriven) {
			index = (i + slides.length) % slides.length;
			slides.forEach(function (s, n) {
				var on = n === index;
				s.classList.toggle("is-active", on);
				s.setAttribute("aria-hidden", on ? "false" : "true");
				if (on) s.removeAttribute("inert");
				else s.setAttribute("inert", "");
			});
			dots.forEach(function (dt, n) {
				var on = n === index;
				dt.classList.toggle("is-active", on);
				dt.setAttribute("aria-selected", on ? "true" : "false");
				dt.tabIndex = on ? 0 : -1;
			});
			if (userDriven) restart();
		}

		function next() { show(index + 1); }
		function stop() { if (timer) { clearInterval(timer); timer = null; } }
		function start() {
			stop();
			if (auto && visible.on && !hovered && !d.hidden) timer = setInterval(next, delay);
		}
		function restart() { start(); }

		dots.forEach(function (dt, n) {
			dt.addEventListener("click", function () { show(n, true); dt.focus(); });
			dt.addEventListener("keydown", function (e) {
				if (e.key === "ArrowRight") { e.preventDefault(); show(index + 1, true); dots[index].focus(); }
				else if (e.key === "ArrowLeft") { e.preventDefault(); show(index - 1, true); dots[index].focus(); }
			});
		});

		stage.addEventListener("mouseenter", function () { hovered = true; stop(); });
		stage.addEventListener("mouseleave", function () { hovered = false; start(); });
		stage.addEventListener("focusin", function () { hovered = true; stop(); });
		stage.addEventListener("focusout", function () { hovered = false; start(); });

		/* swipe */
		var x0 = null;
		stage.addEventListener("touchstart", function (e) {
			x0 = e.touches[0].clientX;
		}, { passive: true });
		stage.addEventListener("touchend", function (e) {
			if (x0 === null) return;
			var dx = e.changedTouches[0].clientX - x0;
			if (Math.abs(dx) > 40) show(index + (dx < 0 ? 1 : -1), true);
			x0 = null;
		}, { passive: true });

		show(0);
		return { start: start, stop: stop, show: show };
	}

	/* --------------------------------------------------------------- path */
	function initSteps(root, visible) {
		var steps = [].slice.call(root.querySelectorAll(".sce-path__step"));
		if (!steps.length) return null;

		var delay = num(root, "data-step-delay", 2600);
		var auto = root.getAttribute("data-step-auto") !== "no" && !REDUCED;
		var index = 0;
		var timer = null;
		var held = false;

		function show(i) {
			index = (i + steps.length) % steps.length;
			steps.forEach(function (s, n) { s.classList.toggle("is-active", n === index); });
		}
		function stop() { if (timer) { clearInterval(timer); timer = null; } }
		function start() {
			stop();
			if (auto && visible.on && !held && !d.hidden) timer = setInterval(function () { show(index + 1); }, delay);
		}

		// Pointer or keyboard focus takes priority over the timer.
		steps.forEach(function (s, n) {
			s.addEventListener("mouseenter", function () { held = true; stop(); show(n); });
			s.addEventListener("focusin", function () { held = true; stop(); show(n); });
		});
		root.addEventListener("mouseleave", function () { held = false; start(); });
		root.addEventListener("focusout", function () { held = false; start(); });

		show(0);
		return { start: start, stop: stop, show: show };
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
		if (!root || root.__scePath) return root && root.__scePath;

		var api = { root: root };
		root.__scePath = api;
		sweep();
		registry.push(api);

		/* One observer drives the reveal and gates both timers, so nothing
		   ticks while the section is scrolled away. */
		var visible = { on: false };
		var slider, steps;

		var reveal = function () {
			// The rAF stages the transition: it guarantees the opacity:0 start
			// state has been painted before the class flips. A hidden document
			// never runs a frame — and has nothing to animate — so flip it
			// straight away rather than strand the section at opacity 0.
			if (d.hidden) { root.classList.add("is-revealed"); return; }
			w.requestAnimationFrame(function () { root.classList.add("is-revealed"); });
		};

		slider = initSlider(root, visible);
		steps = initSteps(root, visible);
		api.slider = slider;
		api.steps = steps;

		function setVisible(on) {
			visible.on = on;
			if (on) {
				reveal();
				if (slider) slider.start();
				if (steps) steps.start();
			} else {
				if (slider) slider.stop();
				if (steps) steps.stop();
			}
		}

		if (!("IntersectionObserver" in w)) {
			fontsReady().then(function () { setVisible(true); });
		} else {
			api.io = new IntersectionObserver(function (entries) {
				for (var i = 0; i < entries.length; i++) setVisible(entries[i].isIntersecting);
			}, { threshold: 0.12 });
			api.io.observe(root);
			// above the fold: don't wait for a scroll event that may never come
			if (root.getBoundingClientRect().top < w.innerHeight * 0.85) {
				fontsReady().then(function () { setVisible(true); });
			}
			// Failsafe: an IntersectionObserver delivers nothing while the document
			// is hidden, and an unrevealed section is invisible content rather than
			// just a missing animation. The step timers stay gated either way —
			// start() checks d.hidden itself.
			api.t = setTimeout(function () {
				if (!root.classList.contains("is-revealed")) setVisible(true);
			}, 3000);
		}

		d.addEventListener("visibilitychange", onVis);
		function onVis() {
			if (d.hidden) { if (slider) slider.stop(); if (steps) steps.stop(); }
			else setVisible(visible.on);
		}

		api.setVisible = setVisible;
		api.destroy = function () {
			if (api.t) { clearTimeout(api.t); api.t = null; }
			if (api.io && api.io.disconnect) api.io.disconnect();
			d.removeEventListener("visibilitychange", onVis);
			if (slider) slider.stop();
			if (steps) steps.stop();
			delete root.__scePath;
		};
		return api;
	}

	function initAll(scope) {
		sweep();
		var node = scope && scope.nodeType === 1 ? scope : null;
		if (node && node.classList.contains("sce-path")) { init(node); return; }
		[].forEach.call((node || d).querySelectorAll(".sce-path"), init);
	}

	w.SCEPath = { init: init, initAll: initAll };

	if (d.readyState === "loading") d.addEventListener("DOMContentLoaded", function () { initAll(); });
	else initAll();

	w.addEventListener("elementor/frontend/init", function () {
		if (!w.elementorFrontend || !w.elementorFrontend.hooks) return;
		w.elementorFrontend.hooks.addAction("frontend/element_ready/suncoast_path.default", function ($s) {
			var el = $s && $s[0] ? $s[0] : $s;
			if (el) initAll(el);
		});
	});
})(window, document);
