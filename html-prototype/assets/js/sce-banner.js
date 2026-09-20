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
			w.requestAnimationFrame(function () { root.classList.add("is-revealed"); });
		};

		var r = root.getBoundingClientRect();
		var aboveFold = r.top < w.innerHeight * 0.85;

		if (aboveFold) { fontsReady().then(fire); return null; }

		if (!("IntersectionObserver" in w)) { fire(); return null; }
		var io = new IntersectionObserver(function (entries) {
			for (var i = 0; i < entries.length; i++) {
				if (entries[i].isIntersecting) { fire(); io.disconnect(); break; }
			}
		}, { threshold: 0.12, rootMargin: "0px 0px -8% 0px" });
		io.observe(root);
		return io;
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

	/* --------------------------------------------------------------- form */
	function initForm(form) {
		if (!form) return;

		/* select: grey while on the placeholder option, ink once chosen */
		[].forEach.call(form.querySelectorAll(".sce-form__select"), function (sel) {
			var sync = function () { sel.classList.toggle("has-value", !!sel.value); };
			sel.addEventListener("change", sync);
			sync();
		});

		/* light input masks — never fight the user, just drop invalid chars */
		var zip = form.querySelector('[data-mask="zip"]');
		if (zip) {
			zip.addEventListener("input", function () {
				var v = zip.value.replace(/[^\d]/g, "").slice(0, 5);
				if (v !== zip.value) zip.value = v;
			});
		}
		var tel = form.querySelector('[data-mask="tel"]');
		if (tel) {
			tel.addEventListener("input", function () {
				var v = tel.value.replace(/[^\d\s()+\-.]/g, "").slice(0, 20);
				if (v !== tel.value) tel.value = v;
			});
		}

		var msg = form.querySelector(".sce-form__msg");
		function say(text, ok) {
			if (!msg) return;
			msg.textContent = text;
			msg.className = "sce-form__msg is-on " + (ok ? "is-ok" : "is-err");
			msg.setAttribute("role", ok ? "status" : "alert");
		}

		form.addEventListener("submit", function (e) {
			form.classList.add("is-validated");

			if (!form.checkValidity()) {
				e.preventDefault();
				var bad = form.querySelector(":invalid");
				if (bad) bad.focus();
				say(form.getAttribute("data-msg-invalid") || "Please complete the highlighted fields.", false);
				return;
			}

			// Native POST path (used when the widget renders an Elementor Pro form)
			if (form.getAttribute("data-ajax") !== "yes") return;

			e.preventDefault();
			var cfg = w.SCE_FORM || {};
			if (!cfg.ajaxUrl) { say("Form endpoint is not configured.", false); return; }

			var data = new FormData(form);
			data.append("action", cfg.action || "sce_lead");
			data.append("nonce", cfg.nonce || "");
			data.append("referer", d.referrer || "");

			form.classList.add("is-busy");
			var btn = form.querySelector(".sce-form__submit");
			var label = btn ? btn.textContent : "";
			if (btn) btn.textContent = form.getAttribute("data-msg-sending") || "Sending…";

			w.fetch(cfg.ajaxUrl, {
				method: "POST",
				body: data,
				credentials: "same-origin",
				headers: { "X-Requested-With": "XMLHttpRequest" }
			})
				.then(function (res) { return res.json().catch(function () { return { success: res.ok }; }); })
				.then(function (json) {
					if (json && json.success) {
						say((json.data && json.data.message) || form.getAttribute("data-msg-success") ||
							"Thank you — we'll be in touch within one business day.", true);
						form.reset();
						form.classList.remove("is-validated");
						[].forEach.call(form.querySelectorAll(".sce-form__select"), function (s) { s.classList.remove("has-value"); });
					} else {
						say((json && json.data && json.data.message) || form.getAttribute("data-msg-error") ||
							"Something went wrong. Please call us instead.", false);
					}
				})
				.catch(function () {
					say(form.getAttribute("data-msg-error") || "Network error. Please call us instead.", false);
				})
				.then(function () {
					form.classList.remove("is-busy");
					if (btn) btn.textContent = label;
				});
		});
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
		initForm(root.querySelector(".sce-form"));

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
