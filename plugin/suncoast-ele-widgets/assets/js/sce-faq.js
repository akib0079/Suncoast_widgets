/* ==========================================================================
   Suncoast — FAQ & Form widget behaviour
   --------------------------------------------------------------------------
   • accordion: real <button> triggers, aria-expanded / aria-controls, height
     animated from a measured pixel value so it can transition (auto cannot),
     then released back to auto so reflow never clips a panel. The settle step
     runs on a timer as well as on transitionend, which is not guaranteed.
   • optional single-open mode
   • the lead form is handed to the shared SCEForm module
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

	/* ---------------------------------------------------------- accordion */
	function initFaq(root) {
		var items = [].slice.call(root.querySelectorAll(".sce-faq__item"));
		if (!items.length) return null;

		var single = root.getAttribute("data-faq-single") !== "no";

		function panelOf(item) { return item.querySelector(".sce-faq__panel"); }
		function btnOf(item) { return item.querySelector(".sce-faq__q"); }

		/* Settle a panel on its final state and drop whatever is pending. */
		function finish(item, panel) {
			if (panel.__sceT) { clearTimeout(panel.__sceT); panel.__sceT = null; }
			if (panel.__sceEnd) {
				panel.removeEventListener("transitionend", panel.__sceEnd);
				panel.__sceEnd = null;
			}
			if (item.classList.contains("is-open")) {
				panel.hidden = false;
				// back to auto so a later reflow — a resize, a font swap — can
				// never clip an open answer at a stale pixel height
				panel.style.height = "auto";
			} else {
				panel.style.height = "0px";
				panel.hidden = true;
			}
		}

		function setOpen(item, open, animate) {
			var panel = panelOf(item), btn = btnOf(item);
			if (!panel || !btn) return;

			item.classList.toggle("is-open", open);
			btn.setAttribute("aria-expanded", open ? "true" : "false");

			// drop anything the previous toggle left in flight
			if (panel.__sceT) { clearTimeout(panel.__sceT); panel.__sceT = null; }
			if (panel.__sceEnd) {
				panel.removeEventListener("transitionend", panel.__sceEnd);
				panel.__sceEnd = null;
			}

			panel.hidden = false;

			// nothing to animate when asked not to, under reduced motion, or when
			// the panel is not rendered at all (inside a collapsed tab, say)
			if (!animate || REDUCED || !panel.getClientRects().length) {
				finish(item, panel);
				return;
			}

			// height:auto cannot be transitioned, so measure, animate to the
			// pixel value, then hand control back to auto once it lands.
			var start = panel.getBoundingClientRect().height;
			panel.style.height = start + "px";
			// force a reflow so the browser registers the start value
			void panel.offsetHeight;
			panel.style.height = (open ? panel.scrollHeight : 0) + "px";

			panel.__sceEnd = function (e) {
				if (!e || "height" === e.propertyName) finish(item, panel);
			};
			panel.addEventListener("transitionend", panel.__sceEnd);

			// transitionend is not guaranteed: an interrupted transition, a
			// background tab or a surface that simply is not painting will never
			// fire it, and the panel would be stranded half open. Always settle
			// on a timer too, read from the CSS so the two stay in step.
			var ms = parseFloat(getComputedStyle(panel).transitionDuration) * 1000;
			panel.__sceT = setTimeout(function () { finish(item, panel); }, (ms || 420) + 80);
		}

		items.forEach(function (item, i) {
			var btn = btnOf(item);
			if (!btn) return;
			setOpen(item, item.classList.contains("is-open"), false);

			btn.addEventListener("click", function () {
				var open = !item.classList.contains("is-open");
				if (open && single) {
					items.forEach(function (other) {
						if (other !== item && other.classList.contains("is-open")) setOpen(other, false, true);
					});
				}
				setOpen(item, open, true);
			});

			btn.addEventListener("keydown", function (e) {
				var go = null;
				if (e.key === "ArrowDown") go = items[(i + 1) % items.length];
				else if (e.key === "ArrowUp") go = items[(i - 1 + items.length) % items.length];
				else if (e.key === "Home") go = items[0];
				else if (e.key === "End") go = items[items.length - 1];
				if (go) { e.preventDefault(); btnOf(go).focus(); }
			});
		});

		return { items: items, setOpen: setOpen };
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
		if (!root || root.__sceFaq) return root && root.__sceFaq;

		var api = { root: root };
		root.__sceFaq = api;
		sweep();
		registry.push(api);

		api.faq = initFaq(root);
		if (w.SCEForm) w.SCEForm.init(root.querySelector(".sce-form"));

		var fire = function () {
			// The rAF stages the transition: it guarantees the opacity:0 start
			// state has been painted before the class flips. A hidden document
			// never runs a frame — and has nothing to animate — so in that case
			// flip it straight away rather than wait for a frame that may never
			// come and leave the section stranded at opacity 0.
			if (d.hidden) { root.classList.add("is-revealed"); return; }
			w.requestAnimationFrame(function () { root.classList.add("is-revealed"); });
		};
		if (REDUCED) root.classList.add("is-revealed");
		else if (root.getBoundingClientRect().top < w.innerHeight * 0.85) fontsReady().then(fire);
		else if (!("IntersectionObserver" in w)) fire();
		else {
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
			api.io = new IntersectionObserver(function (e) {
				for (var i = 0; i < e.length; i++) {
					if (e[i].isIntersecting) { settle(); break; }
				}
			}, { threshold: 0.08, rootMargin: "0px 0px -10% 0px" });
			api.io.observe(root);
		}

		api.destroy = function () {
			if (api.t) { clearTimeout(api.t); api.t = null; }
			if (api.io && api.io.disconnect) api.io.disconnect();
			[].forEach.call(root.querySelectorAll(".sce-faq__panel"), function (p) {
				if (p.__sceT) { clearTimeout(p.__sceT); p.__sceT = null; }
				if (p.__sceEnd) { p.removeEventListener("transitionend", p.__sceEnd); p.__sceEnd = null; }
			});
			delete root.__sceFaq;
		};
		return api;
	}

	function initAll(scope) {
		sweep();
		var node = scope && scope.nodeType === 1 ? scope : null;
		if (node && node.classList.contains("sce-faq")) { init(node); return; }
		[].forEach.call((node || d).querySelectorAll(".sce-faq"), init);
	}

	w.SCEFaq = { init: init, initAll: initAll };

	if (d.readyState === "loading") d.addEventListener("DOMContentLoaded", function () { initAll(); });
	else initAll();

	w.addEventListener("elementor/frontend/init", function () {
		if (!w.elementorFrontend || !w.elementorFrontend.hooks) return;
		w.elementorFrontend.hooks.addAction("frontend/element_ready/suncoast_faq.default", function ($s) {
			var el = $s && $s[0] ? $s[0] : $s;
			if (el) initAll(el);
		});
	});
})(window, document);
