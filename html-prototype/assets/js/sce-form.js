/* ==========================================================================
   Suncoast — shared lead-form behaviour
   --------------------------------------------------------------------------
   Used by the hero banner and the FAQ section, which post to the same
   endpoint (SCE_Forms) and therefore must behave identically: placeholder
   colour on the select, light input masks, validation that only paints red
   after a submit attempt, and the AJAX path with its busy state.

   `data-ajax="no"` leaves the form to submit natively — that is how the
   Elementor Pro Form path works.
   ========================================================================== */
(function (w, d) {
	"use strict";

	function init(form) {
		if (!form || form.__sceForm) return;
		form.__sceForm = true;

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

	w.SCEForm = { init: init };
})(window, document);
