# Suncoast Widgets

Custom Elementor widgets for the Suncoast Enclosures (Portland PNW) landing page.
They appear in the Elementor panel under a dedicated **Suncoast Ele Widgets** category.

| Widget | What it is |
|---|---|
| **Suncoast Header** | Sticky blurred navbar — logo, scroll-spy nav, phone, CTA, mobile panel |
| **Suncoast Hero Banner** | Hero photo + content column + lead form card + marquee strip |
| **Suncoast Benefits + Video** | Centred heading, three-up benefit grid, video teaser with a popup player |

---

## Install

1. Download **[`dist/suncoast-ele-widgets.zip`](dist/suncoast-ele-widgets.zip)**.
2. WP Admin → **Plugins → Add New → Upload Plugin** → choose the zip → Install → Activate.
3. Edit a page with Elementor → the **Suncoast Ele Widgets** category is in the panel.

Requires Elementor 3.5+, WordPress 6.0+, PHP 7.4+. Elementor **Pro is optional** —
only needed if you want the form built with Elementor's own Form widget.

Rebuild the zip after any source change:

```bash
./build.sh
```

### Putting the landing page together

1. Drop **Suncoast Header** into a section at the very top. Give that section
   `0` padding so the 81px bar is the only thing setting the height.
2. Drop **Suncoast Hero Banner** into the next, full-width section (also `0` padding).
3. Drop **Suncoast Benefits + Video** into the section after that — full width,
   `0` padding, since the widget paints its own `#FCFBF4` background and holds
   its own 1070px content column.
4. Give each section a **CSS ID** (`products`, `projects`, `process`, `pricing`)
   matching the header's menu links. Scroll-spy and smooth scrolling pick them
   up automatically.

---

## Layout

```
plugin/suncoast-ele-widgets/     the WordPress plugin (this is what ships)
  suncoast-ele-widgets.php       bootstrap + environment guards
  includes/
    class-sce-plugin.php         panel category + widget registration
    class-sce-assets.php         per-widget asset registration, Google Fonts
    class-sce-forms.php          AJAX endpoint, spam traps, lead CPT, e-mail
    widgets/class-sce-widget-header.php
    widgets/class-sce-widget-banner.php
    widgets/class-sce-widget-benefits.php
  assets/css|js/                 the hard-scoped CSS + vanilla JS
  templates/email-admin.php      admin notification e-mail

html-prototype/                  the standalone HTML build, kept byte-identical
                                 to the plugin's CSS/JS for quick iteration
dist/                            built plugin zip
build.sh                         zip builder
```

`html-prototype/` and the plugin share the **same** CSS and JS files — verify a
change in the prototype (`cd html-prototype && python3 -m http.server 8787`),
then `cp` it across and rebuild.

---

## How the CSS is protected from the theme

Every rule is namespaced under `.sce-scope`. Nothing touches a global selector
and nothing uses `!important`. The reset sits at a deliberate specificity:

| Layer     | Selector shape                          | Specificity |
|-----------|-----------------------------------------|-------------|
| reset     | `.sce-scope.sce-scope :where(h1, p, …)` | (0,2,0)     |
| component | `.sce-scope .sce-widget__thing`         | (0,2,0) + later in source |
| state     | `.sce-widget.is-x .sce-widget__thing`   | (0,3,0)     |
| Elementor | `{{WRAPPER}} .sce-widget__thing`        | (0,3,0)     |

(0,2,0) beats the `h1`, `body h1` and `.entry-content h1` rules every WP theme
ships, while `:where()` keeps element names from inflating the reset so our own
component rules still win the tie on source order. Elementor's generated CSS
lands above both, so controls always override the design defaults.

**Every component selector must start with `.sce-scope`.** The reset neutralises
inherited fonts, margins, list styles, link decoration, form-control chrome,
image sizing and Chrome's autofill wash.

### Why the header uses CSS variables and the banner doesn't

Elementor sections routinely carry a `transform` (entrance animations, Pro
sticky effects), which silently turns `position: fixed` into `position:
absolute`. The header detects that and re-homes `.sce-header__layer` onto
`<body>` — at which point `{{WRAPPER}} …` selectors stop matching. So **every
header control writes a CSS custom property**, and the JS copies the resolved
values onto the layer when it moves (refreshing them on resize, since they are
breakpoint-dependent). The banner never moves, so it uses plain declarations and
Elementor's native typography groups.

### Style controls have no defaults — on purpose

Untouched means the hard-scoped CSS supplies the Figma value, *including its
per-breakpoint ones*. If a control shipped a default, Elementor would emit it as
a desktop rule that outranks our tablet/mobile media queries. So: **out of the
box the widgets are pin-perfect; touch a control and you take ownership of it.**

---

## Verified against the Figma

Measured in-browser at a 1440px viewport:

| Element              | Figma            | Built            |
|----------------------|------------------|------------------|
| Header bar height    | 81               | 81 ✅            |
| Compressed bar       | —                | 81 → 72, logo 32 → 29, blur 16 → 26px ✅ |
| Logo                 | h32 @ x48 y24.5  | h32 @ x48 y24 ✅ |
| Nav link             | Poppins 500 13 / ls .65 / uppercase, w75, gap 30 | identical, w74.8 ✅ |
| Phone                | Poppins 500 14 / ls .56 | identical ✅ |
| CTA                  | 174×40, r3, #EBB04D, Poppins 600 13 / ls .65, pad 12/24 | identical ✅ |
| Banner stage         | y81 → y802 (721 tall) | y81 → y802 ✅ |
| Eyebrow              | Poppins 400 12/18, ls 3.12, #EBB04D | identical ✅ |
| H1                   | Playfair 400 54/61.4, ls −1.36, h123 | 54/61.398/−1.3608, h122.8 ✅ |
| “Rain.”              | italic, #EBB04D  | identical ✅     |
| Body copy            | Inter 400 14/150%, #E5E5E5 | identical ✅ |
| Checklist            | Inter 400 16/25.6, #FFF, gap 13 | identical ✅ |
| Form card            | 440 wide, pad 51/40/40, r16, #FCFAF5 @87%, backdrop blur, h587 | 440×589 ✅ |
| Card title           | Inter 700 24/28.8, #1A1C1C | identical ✅ |
| Submit               | Inter 700 12, ls .96, #0F0F0F | identical ✅ |
| Marquee              | y802, 53 tall, #F7DEB4, Poppins 500 18, ls −4%, #5A421B | identical ✅ |

### Benefits + Video

The section export is 1:1, so it was scanned pixel-by-pixel for ink bands and
the build re-measured against them. **Every element lands within 0.4px.**

| Element        | Figma | Built | Δ |
|----------------|-------|-------|---|
| Section height | 1192  | 1191.7 | −0.3 |
| Sub-heading    | 75.7  | 76.0   | +0.3 |
| Heading        | 99.8  | 100.0  | +0.2 |
| Short info     | 159.8 | 160.2  | +0.4 |
| Card image top | 227   | 226.8  | −0.2 |
| Card image h   | 284   | 284.0  | 0 |
| Icon + heading | 535.6 | 535.7  | +0.1 |
| Description    | 573.9 | 573.5  | −0.4 |
| Video top      | 687   | 686.7  | −0.3 |
| Video height   | 400   | 400.0  | 0 |

Cards measure 340.67 wide with 24px gaps (1070 total), radius 16, background
`#FCFBF4`, overlay `#1A1C1C` at 50%, play button ⌀58 — all derived from the
export rather than eyeballed. The overlay alpha was solved by least-squares
against the original poster image.

Also verified: no horizontal overflow at 1440 / 768 / 375, no console errors,
all nine webfont faces load, PHP 8.5 lint clean, JS syntax clean.

---

## Behaviour

**Header**
- Compresses past 8px of scroll (threshold is a control).
- Scroll-spy marks the active section and holds the last one at page bottom.
- Smooth scroll lands each section exactly `bar height + 12px` from the top;
  the gap is a control, and `scroll-padding-top` is kept in sync for deep links.
- Mobile panel: locks body scroll at the exact offset and restores it, traps Tab
  both directions, closes on Escape / scrim / close button, returns focus to the
  burger, and is `inert` + `aria-hidden` while closed.
- The panel stacks **above** the bar (bar 9990 → scrim 9991 → panel 9992), so its
  own top row (logo + ×) replaces the bar while open, matching the Figma mock.
  Getting this backwards hides the close button behind the bar; verified with a
  real `elementFromPoint` hit test rather than a rect check, because
  `getClientRects()` still reports occluded elements as visible.
- ≤1024px the nav swaps for the burger and the phone collapses to an icon
  button; ≤600px the CTA moves into the panel.
- Optional auto-hide on scroll-down, and an "overlay the next section" switch.

**Banner**
- Staggered reveal: masked line-by-line rise on the H1 (split on `<br>` in PHP),
  fade-up on everything else, 1.055 → 1 settle on the photo. Above-the-fold
  fires on `document.fonts.ready`, capped at 600ms, so nothing pops after a swap.
- Marquee: both sets measure identically, so the −50% translate lands on a
  pixel-exact seam. Duration derives from a constant px/second, so editing the
  copy never changes the pace. Pauses on hover/focus; clones itself until it
  overflows so a short list never leaves a gap.
- Form: placeholder-grey select that inks on selection, zip and phone masks,
  validation that only paints red after a submit attempt and focuses the first
  bad field.

**Benefits + Video**
- Heading uses the same masked line reveal; cards and the video stagger in.
- Video opens an accessible modal: focus trap both directions, Escape, scrim
  click, iOS-safe scroll lock, focus returned to the play button, `inert` while
  closed.
- **The embed is built on open and destroyed on close** — nothing streams until
  the visitor asks, and closing actually stops the audio.
- YouTube (privacy mode by default), Vimeo, self-hosted, or any embed URL.
- The modal is moved to `<body>` on init for the same transformed-ancestor
  reason as the header, so its two style controls are written inline.
- Grid is 3-up above 992px and single-column below, with the image crop moving
  340/284 → 16/9 → 4/3 so it never becomes a tower on a phone.

**Performance**
- No libraries. Three small vanilla modules, deferred.
- Assets are registered, never globally enqueued — a page using neither widget
  downloads none of this. Three font families, only the weights in use.
- Scroll/resize handlers are rAF-throttled and passive; animation is
  opacity/transform only.
- Hero uses `wp_get_attachment_image()` (so you get `srcset`/`sizes`) with
  `fetchpriority="high"`; the media box paints `#14110B` first, so no white
  flash before decode.
- `prefers-reduced-motion` is honoured everywhere.

---

## The form

**Built-in** (default) — works with plain Elementor, no Pro needed:

- Submits over AJAX to `admin-ajax.php`.
- Three spam traps: honeypot, a sub-2-second submit trap, and a 5-per-10-minutes
  per-IP throttle.
- Saves every lead to a **Suncoast Leads** admin screen (name / phone / zip /
  project / source), so nothing is lost if mail fails.
- Sends a branded HTML notification (`templates/email-admin.php`) with a
  click-to-call button, `Reply-To` set to the lead when an e-mail is supplied.

> **Security note:** the notification recipient is never read from the request.
> It is resolved server-side from the Elementor document's saved widget settings,
> so a crafted POST cannot redirect mail elsewhere.

**Elementor saved template** — build a Pro Form, save it as a template, pick it
under *Form → Template*. `sce-form-elementor.css` restyles Pro's markup to match
the card exactly. Configure the admin notification in the form's own
*Actions After Submit → Email*.

### Hooks

```php
add_filter( 'sce/load_google_fonts', '__return_false' );  // self-hosting the fonts
add_filter( 'sce/store_leads', '__return_false' );        // don't save to the CPT
add_filter( 'sce/admin_email_to', fn( $to ) => 'sales@example.com' );
add_filter( 'sce/admin_email_subject', fn( $s ) => $s );
add_filter( 'sce/admin_email_body', fn( $b ) => $b );
add_action( 'sce/lead_submitted', function ( $lead_id, $fields, $meta ) { /* CRM push */ }, 10, 3 );
```

---

## Decisions worth a look

1. **Logo width.** The Figma layer measures 287×32; the uploaded PNG's intrinsic
   ratio renders 308×32 at that height. Height is pinned (the reliable anchor)
   and width follows, rather than squashing the asset. Re-export at 287×32 for
   the exact number.
2. **Marquee copy.** Fixed two typos from the Figma — `Rain rady` → **Rain
   ready**, `Outdoor leaving , Reimagined` → **Outdoor Living, Reimagined**. Both
   are repeater rows, so change them back in one click if they were deliberate.
3. **Marquee is full-bleed** while scrolling; the Figma's 30px inset is a
   static-layout artifact. It is restored under `prefers-reduced-motion`, when
   the strip stops.
4. **Mobile menu items.** The Figma mobile mock lists HOME / PRODUCTS / EXPLORE /
   LOCATIONS / CONTACT, which is the main site's menu. Default mirrors the
   desktop four, since this is a single-scroll landing page — switch *Menu →
   Mobile menu* to "Different items" for the mock's set (pre-filled).
5. **Header offset** defaults to off, matching the Figma (banner starts below the
   bar). *Behaviour → Overlay the next section* flips it.
6. **Hero image.** `1.-Hero-Section.png` is 1280×830 and upscales slightly into a
   1440-wide stage. A ~2560px export would sharpen it on retina.
7. **Benefits heading line-height.** Figma records 24px against a 36px Playfair
   face — that would overlap the moment the heading wraps on a phone. The build
   uses 1.2 and absorbs the difference in the surrounding margins, so the
   single-line desktop rendering is pixel-identical and wrapping is correct.
8. **Benefit icons are inlined** from the three supplied SVGs, so they recolour
   with the icon control and cost no extra request. Pick *Custom* on a card to
   use an uploaded image instead.
9. **Video block has no video yet.** The prototype points at a placeholder
   YouTube ID; set the real one under *Video teaser → Video*.
