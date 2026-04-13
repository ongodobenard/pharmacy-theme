<?php
/**
 * Template Name: Submit Prescription
 */
get_header(); ?>

<style>
/* ── Global mobile overflow prevention ── */
@media(max-width:768px) {
  html, body { overflow-x: hidden; max-width: 100%; }
  * { box-sizing: border-box; }
}

/* ════════════════════════════════════════════
   BASE FORM STYLES
════════════════════════════════════════════ */
.rx-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.rx-form-grid .fg { margin-bottom: 0; }
.fg.full { grid-column: 1 / -1; }
.fg { display: flex; flex-direction: column; gap: 6px; }
.fg label {
  font-size: 0.8rem; font-weight: 700; opacity: .6;
  text-transform: uppercase; letter-spacing: .04em; transition: color .2s;
}
.fg label span.req { color: var(--green,#2eaf6e); margin-left: 2px; }
.fg input,.fg select,.fg textarea {
  padding: 11px 14px; border: 1.5px solid rgba(0,0,0,.1);
  border-radius: 10px; font-size: 0.9rem; outline: none;
  transition: border-color .2s, box-shadow .2s;
  width: 100%; box-sizing: border-box; font-family: inherit; background: #fff;
}
.fg input:focus,.fg select:focus,.fg textarea:focus {
  border-color: var(--green,#2eaf6e);
  box-shadow: 0 0 0 3px rgba(46,175,110,.12);
}
.fg textarea { min-height: 90px; resize: vertical; }

/* ── Validation error state ── */
.fg.has-error label,
.fg.has-error .gender-label-text { color: #e53935 !important; opacity: 1 !important; }
.fg.has-error input,
.fg.has-error select,
.fg.has-error textarea {
  border-color: #e53935 !important;
  box-shadow: 0 0 0 3px rgba(229,57,53,.12) !important;
}
.fg.has-error .gender-wrap { border: 1.5px solid #e53935 !important; border-radius: 10px; padding: 8px 12px; }
.fg.has-error .fg-file { border-color: #e53935 !important; background: rgba(229,57,53,.04) !important; }
.field-error-msg { font-size: .72rem; color: #e53935; font-weight: 700; margin-top: 2px; display: none; }
.fg.has-error .field-error-msg { display: block; }

/* Gender radios */
.gender-wrap { display: flex; gap: 16px; padding: 10px 0 4px; transition: border-color .2s; border-radius: 10px; }
.gender-opt { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem; font-weight: 600; }
.gender-opt input[type="radio"] { width: 18px; height: 18px; accent-color: var(--green,#2eaf6e); cursor: pointer; flex-shrink: 0; }

/* File upload */
.fg-file { border: 2px dashed rgba(46,175,110,.4); border-radius: 10px; padding: 20px; text-align: center; cursor: pointer; transition: border-color .2s, background .2s; background: rgba(46,175,110,.03); }
.fg-file:hover { border-color: var(--green,#2eaf6e); background: rgba(46,175,110,.06); }
.fg-file input[type="file"] { display: none; }
.fg-file-label { display: flex; flex-direction: column; align-items: center; gap: 8px; cursor: pointer; }
.fg-file-label svg { opacity: .5; }
.fg-file-label span { font-size: 0.85rem; opacity: .7; font-weight: 600; }
.fg-file-label small { font-size: 0.72rem; opacity: .45; }
#fileName { font-size: 0.8rem; color: var(--green,#2eaf6e); font-weight: 700; margin-top: 4px; }

/* ── Note / Alert box ── */
.rx-note-box { display: flex; align-items: flex-start; gap: 12px; background: #fff8e1; border: 1.5px solid #ffe082; border-radius: 10px; padding: 14px 16px; margin-top: 16px; }
.rx-note-box .rx-note-icon { width: 32px; height: 32px; border-radius: 8px; background: #ffe082; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rx-note-box p { font-size: .78rem; font-weight: 600; color: #6d4c00; line-height: 1.6; margin: 0; }
.rx-note-box strong { color: #4e3500; }

/* ── WhatsApp steps box ── */
.rx-wa-steps { background: var(--green-light,#e8f8f0); border: 1.5px solid var(--green-mid,#b8ecd4); border-radius: 10px; padding: 14px 16px; margin-top: 14px; }
.rx-wa-steps-title { display: flex; align-items: center; gap: 7px; font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; color: var(--green-dark,#1e8a54); margin-bottom: 10px; }
.rx-wa-step { display: flex; align-items: flex-start; gap: 10px; padding: 5px 0; border-bottom: 1px solid rgba(46,175,110,.15); }
.rx-wa-step:last-child { border-bottom: none; padding-bottom: 0; }
.rx-wa-step-num { width: 20px; height: 20px; border-radius: 50%; background: var(--green,#2eaf6e); color: #fff; font-size: .62rem; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
.rx-wa-step p { font-size: .78rem; font-weight: 600; color: var(--green-dark,#1e8a54); line-height: 1.5; margin: 0; }
.rx-wa-step p strong { color: var(--dark,#1a3c2e); }

/* ════════════════════════════════════════════
   DELIVERY INFO BOXES
════════════════════════════════════════════ */
.delivery-info-box { background: #fff; border-radius: 14px; border: 1.5px solid var(--green-light,#e8f8f0); padding: 20px 22px; }
.delivery-info-box .di-title { font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: var(--green,#2eaf6e); margin-bottom: 14px; padding-bottom: 10px; border-bottom: 2px solid var(--green-light,#e8f8f0); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; line-height: 1.4; }
.delivery-row { display: flex; align-items: flex-start; gap: 12px; padding: 10px 0; border-bottom: 1px solid rgba(0,0,0,.05); }
.delivery-row:last-child { border-bottom: none; padding-bottom: 0; }
.di-icon { width: 36px; height: 36px; border-radius: 10px; background: var(--green-light,#e8f8f0); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.di-label { font-size: .82rem; font-weight: 800; color: var(--dark,#1a3c2e); margin-bottom: 3px; display: flex; align-items: center; flex-wrap: wrap; gap: 4px; }
.di-desc { font-size: .75rem; color: var(--text-light,#6b8c7a); line-height: 1.55; font-weight: 500; }
.di-badge { display: inline-block; font-size: .62rem; font-weight: 800; padding: 2px 8px; border-radius: 50px; }
.di-badge--green  { background: var(--green-light,#e8f8f0); color: var(--green,#2eaf6e); }
.di-badge--orange { background: #fff3e0; color: #e65100; }
.delivery-note-banner { display: flex; align-items: flex-start; gap: 14px; background: #fff8e1; border: 1.5px solid #ffe082; border-radius: 12px; padding: 16px 20px; max-width: 900px; margin: 24px auto 0; }
.delivery-note-banner .dnb-icon { width: 36px; height: 36px; border-radius: 10px; background: #ffe082; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.delivery-note-banner p { font-size: .8rem; font-weight: 600; color: #6d4c00; line-height: 1.6; margin: 0; }
.delivery-note-banner strong { color: #4e3500; }

/* ════════════════════════════════════════════
   DELIVERY IMAGE WITH ANIMATED BUBBLES
════════════════════════════════════════════ */
.rx-img-col { position: relative; }

/* Main bubble background — large oval */
.rx-bubble-main {
  position: absolute;
  top: 0; left: 50%;
  transform: translateX(-50%);
  width: 110%; height: 78%;
  background: var(--green-light, #e8f8f0);
  border-radius: 50% 50% 50% 50% / 40% 40% 60% 60%;
  z-index: 0;
  animation: bubbleFloat1 6s ease-in-out infinite;
}
.rx-bubble-2 { position: absolute; bottom: 80px; left: -20px; width: 160px; height: 160px; background: var(--green, #2eaf6e); border-radius: 50%; z-index: 0; opacity: .18; animation: bubbleFloat2 8s ease-in-out infinite; }
.rx-bubble-3 { position: absolute; bottom: 90px; right: -10px; width: 100px; height: 100px; background: var(--green, #2eaf6e); border-radius: 50%; z-index: 0; opacity: .12; animation: bubbleFloat3 7s ease-in-out infinite; }
.rx-bubble-4 { position: absolute; top: 10px; right: -15px; width: 70px; height: 70px; background: var(--green-mid, #b8ecd4); border-radius: 50%; z-index: 0; opacity: .5; animation: bubbleFloat2 5s ease-in-out infinite reverse; }
.rx-bubble-5 { position: absolute; top: 20px; left: -10px; width: 50px; height: 50px; background: var(--green-mid, #b8ecd4); border-radius: 50%; z-index: 0; opacity: .4; animation: bubbleFloat3 9s ease-in-out infinite; }

@keyframes bubbleFloat1 {
  0%, 100% { border-radius: 50% 50% 50% 50% / 40% 40% 60% 60%; transform: translateX(-50%) scale(1); }
  33%       { border-radius: 45% 55% 55% 45% / 45% 35% 65% 55%; transform: translateX(-50%) scale(1.02); }
  66%       { border-radius: 55% 45% 45% 55% / 55% 45% 55% 45%; transform: translateX(-50%) scale(0.98); }
}
@keyframes bubbleFloat2 {
  0%, 100% { transform: translateY(0) scale(1); }
  50%       { transform: translateY(-14px) scale(1.06); }
}
@keyframes bubbleFloat3 {
  0%, 100% { transform: translateY(0) scale(1); }
  50%       { transform: translateY(-10px) scale(1.04); }
}

/* Image wrapper */
.rx-delivery-img-wrap {
  position: relative;
  z-index: 1;
  text-align: center;
  overflow: visible;
}
.rx-delivery-img-wrap img {
  width: 100%;
  max-width: 420px;
  height: auto;
  object-fit: cover;
  object-position: top center;
  display: block;
  margin: 0 auto;
  position: relative;
  z-index: 1;
  -webkit-mask-image: linear-gradient(to bottom, black 55%, transparent 92%);
  mask-image: linear-gradient(to bottom, black 55%, transparent 92%);
}

/* ── Overlay text badge on image ── */
.rx-img-overlay-badge {
  position: relative;
  z-index: 2;
  background: var(--green, #2eaf6e);
  color: white;
  border-radius: 14px;
  padding: 14px 20px;
  margin: -24px auto 0;
  max-width: 320px;
  text-align: center;
  box-shadow: 0 8px 28px rgba(46,175,110,.35);
}
.rx-img-overlay-badge h3 {
  font-family: 'Playfair Display', serif;
  font-size: 1rem; font-weight: 700;
  color: white; margin: 0 0 4px;
}
.rx-img-overlay-badge p {
  font-size: .76rem; color: rgba(255,255,255,.85);
  font-weight: 600; margin: 0; line-height: 1.5;
}
.rx-img-overlay-badge .rx-badge-dots {
  display: flex; justify-content: center; gap: 4px; margin-top: 8px;
}
.rx-img-overlay-badge .rx-dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: rgba(255,255,255,.5);
}
.rx-img-overlay-badge .rx-dot.active { background: white; width: 18px; border-radius: 3px; }

/* Delivery badges */
.rx-delivery-badges { display: flex; flex-direction: column; gap: 8px; margin-top: 14px; position: relative; z-index: 2; }
.rx-delivery-badge { display: flex; align-items: center; gap: 12px; background: white; border-radius: 12px; padding: 11px 14px; box-shadow: 0 4px 15px rgba(46,175,110,.08); border: 1.5px solid var(--green-light,#e8f8f0); transition: transform .2s, box-shadow .2s; }
.rx-delivery-badge:hover { transform: translateX(4px); box-shadow: 0 8px 24px rgba(46,175,110,.15); }
.rx-delivery-badge-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rx-delivery-badge-icon.green  { background: var(--green-light,#e8f8f0); }
.rx-delivery-badge-icon.orange { background: #fff3e0; }
.rx-delivery-badge-icon.blue   { background: #e8f0ff; }
.rx-delivery-badge-title { font-size: .8rem; font-weight: 800; color: var(--text,#1a2e25); margin-bottom: 1px; }
.rx-delivery-badge-sub   { font-size: .7rem; color: var(--text-light,#8aaa98); font-weight: 600; }

/* Prescription only notice */
.rx-only-notice { display: flex; align-items: flex-start; gap: 14px; background: #e8f0ff; border: 1.5px solid #b3c6ff; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; }
.rx-only-notice .rx-only-icon { width: 40px; height: 40px; border-radius: 10px; background: #b3c6ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rx-only-notice p { font-size: .82rem; font-weight: 600; color: #1a237e; line-height: 1.6; margin: 0; }
.rx-only-notice strong { color: #0d1b6e; }

/* ════════════════════════════════════════════
   PAGE SECTIONS
════════════════════════════════════════════ */
.rx-hero { padding: 56px 5%; text-align: center; background: linear-gradient(135deg,#e8f0ff,#d8e8ff); }
.rx-how  { background: #fff; padding: 56px 5%; }
.rx-how-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 24px; text-align: center; }
.rx-delivery { background: var(--green-pale,#f0faf5); padding: 48px 5%; }
.rx-delivery-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; max-width: 900px; margin: 0 auto; }
.rx-form-sec { background: #fff; padding: 48px 5% 56px; } /* ← reduced top padding */
.rx-two-col { display: grid; grid-template-columns: 1fr 1.5fr; gap: 48px; align-items: start; }
.rx-cta { background: var(--green,#2eaf6e); padding: 40px 5%; text-align: center; position: relative; overflow: hidden; }

/* ════════════════════════════════════════════
   RESPONSIVE
════════════════════════════════════════════ */
@media(max-width:1024px) {
  .rx-how-grid { grid-template-columns: repeat(2,1fr); gap: 16px; }
  .rx-two-col  { grid-template-columns: 1fr; gap: 32px; }
  .rx-delivery-grid { grid-template-columns: 1fr; }
}
@media(max-width:900px) {
  .rx-form-grid { grid-template-columns: 1fr 1fr; }
  .rx-delivery-grid { grid-template-columns: 1fr; gap: 16px; }
}
@media(max-width:768px) {
  .rx-hero     { padding: 36px 16px; }
  .rx-how      { padding: 36px 16px; }
  .rx-delivery { padding: 36px 16px; }
  .rx-form-sec { padding: 32px 16px 48px; }
  .rx-cta      { padding: 32px 16px; }

  .rx-hero .sec-title { font-size: 1.55rem; }
  .rx-hero .sec-desc  { font-size: .85rem; max-width: 100%; }
  .rx-how-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
  .rx-how-grid .srv-card { padding: 14px 10px; }
  .rx-delivery-grid { grid-template-columns: 1fr; gap: 14px; }
  .delivery-info-box { padding: 14px; }
  .delivery-info-box .di-title { font-size: .7rem; }
  .di-label  { font-size: .78rem; }
  .di-desc   { font-size: .72rem; }
  .di-badge  { font-size: .58rem; padding: 2px 6px; }
  .delivery-row { gap: 10px; }
  .di-icon   { width: 30px; height: 30px; min-width: 30px; }
  .di-icon svg { width: 13px; height: 13px; }
  .delivery-note-banner { padding: 12px 14px; margin: 18px auto 0; gap: 10px; }
  .delivery-note-banner p { font-size: .75rem; }
  .delivery-note-banner .dnb-icon { width: 28px; height: 28px; }

  .rx-two-col { display: block !important; width: 100%; }
  .rx-two-col > * { width: 100%; margin-bottom: 28px; }
  .rx-two-col > *:last-child { margin-bottom: 0; }

  .cta-form { padding: 18px 14px !important; }
  .cta-form-title { font-size: 1.15rem; }
  .rx-form-grid { grid-template-columns: 1fr !important; gap: 12px; }
  .fg.full { grid-column: 1 !important; }
  .fg input,.fg select,.fg textarea { font-size: .88rem; padding: 10px 12px; }
  .fg label { font-size: .72rem; }
  .gender-wrap { padding: 8px 0 2px; gap: 20px; }
  .gender-opt  { font-size: .88rem; }
  .fg-file { padding: 14px; }
  .fg-file-label span  { font-size: .78rem; }
  .fg-file-label small { font-size: .68rem; }

  .rx-submit-btn {
    font-size: .72rem !important; padding: 9px 18px !important;
    width: 100% !important; display: flex !important;
    align-items: center; justify-content: center;
    letter-spacing: .02em; margin-top: 16px;
  }

  .rx-note-box { padding: 11px 13px; gap: 10px; }
  .rx-note-box p { font-size: .73rem; }
  .rx-note-box .rx-note-icon { width: 26px; height: 26px; }
  .rx-wa-step p { font-size: .73rem; }
  .why-feats { gap: 0; }
  .why-feat  { gap: 12px; padding: 12px 0; border-bottom: 1px solid rgba(0,0,0,.06); }
  .why-feat:last-child { border-bottom: none; }
  .wf-icon  { width: 36px; height: 36px; min-width: 36px; }
  .wf-title { font-size: .85rem; }
  .wf-desc  { font-size: .75rem; line-height: 1.5; }

  .rx-form-sec .sec-title,
  .rx-delivery .sec-title,
  .rx-how .sec-title { font-size: 1.5rem; }
  .rx-cta .sec-title { font-size: 1.3rem; color: #fff; }
  .rx-cta .btn-green { font-size: .88rem; padding: 12px 24px; }

  .rx-bubble-main { width: 100%; height: 72%; }
  .rx-bubble-2 { width: 100px; height: 100px; bottom: 70px; left: -10px; }
  .rx-bubble-3 { width: 70px;  height: 70px;  bottom: 80px; right: -5px; }
  .rx-bubble-4 { width: 50px;  height: 50px; }
  .rx-bubble-5 { width: 36px;  height: 36px; }

  .rx-img-overlay-badge { max-width: 280px; padding: 12px 16px; margin-top: -18px; }
  .rx-img-overlay-badge h3 { font-size: .9rem; }
  .rx-img-overlay-badge p  { font-size: .72rem; }

  .rx-delivery-badges { gap: 7px; margin-top: 12px; }
  .rx-delivery-badge { padding: 10px 12px; gap: 10px; }
  .rx-delivery-badge-icon { width: 32px; height: 32px; }
  .rx-delivery-badge-title { font-size: .76rem; }
  .rx-delivery-badge-sub   { font-size: .66rem; }

  .rx-only-notice { padding: 12px 14px; gap: 10px; }
  .rx-only-notice p { font-size: .76rem; }
}
@media(max-width:480px) {
  .rx-how-grid { grid-template-columns: 1fr !important; }
  .rx-hero .sec-title { font-size: 1.3rem; }
  .di-label { flex-direction: column; align-items: flex-start; gap: 3px; }
  .rx-cta .sec-title { font-size: 1.2rem; }
  .rx-submit-btn { font-size: .68rem !important; padding: 8px 14px !important; }
}
</style>


<!-- HERO -->
<section class="rx-hero">
  <div class="sec-tag sec-tag-outline reveal">Quick &amp; Easy</div>
  <h1 class="sec-title reveal rd1">Submit Your <span>Prescription</span></h1>
  <p class="sec-desc reveal rd2" style="margin:0 auto;max-width:600px;">Upload your prescription via WhatsApp and we'll prepare and deliver your medication, same day within Nairobi, next day to counties outside Nairobi.</p>
</section>

<!-- PRESCRIPTION ONLY NOTICE -->
<div style="background:white;padding:24px 5% 0;">
  <div class="rx-only-notice" style="max-width:1100px;margin:0 auto;">
    <div class="rx-only-icon">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a237e" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
    </div>
    <p>
      <strong>Prescription Medicines Only:</strong> This form is strictly for
      <strong>doctor-prescribed medicines</strong>. We cannot process orders without a valid
      prescription signed by a licensed medical practitioner.
      For <strong>over-the-counter medicines</strong> and general products, please visit our
      <a href="<?php echo function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/shop'); ?>"
         style="color:#1a237e;font-weight:800;text-decoration:underline;">Shop page</a>.
    </p>
  </div>
</div>

<!-- HOW IT WORKS -->
<section class="rx-how">
  <div style="text-align:center;margin-bottom:36px;">
    <div class="sec-tag sec-tag-solid reveal">Simple Process</div>
    <h2 class="sec-title reveal rd1">How It <span>Works</span></h2>
  </div>
  <div class="rx-how-grid">
    <div class="srv-card reveal">
      <div class="srv-icon-c"><svg viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="12" cy="12" r="4"/><line x1="12" y1="3" x2="12" y2="7"/></svg></div>
      <div style="font-weight:800;color:var(--text);margin-bottom:6px;font-size:.9rem;">1. Take a Photo</div>
      <div style="font-size:.78rem;color:var(--text-light);font-weight:500;">Take a clear photo of your prescription from your doctor.</div>
    </div>
    <div class="srv-card reveal rd1">
      <div class="srv-icon-c"><svg viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.5"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></div>
      <div style="font-weight:800;color:var(--text);margin-bottom:6px;font-size:.9rem;">2. Send via WhatsApp</div>
      <div style="font-size:.78rem;color:var(--text-light);font-weight:500;">Send the photo to our WhatsApp number with your details.</div>
    </div>
    <div class="srv-card reveal rd2">
      <div class="srv-icon-c"><svg viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.5"><polyline points="20 6 9 17 4 12"/></svg></div>
      <div style="font-weight:800;color:var(--text);margin-bottom:6px;font-size:.9rem;">3. We Verify</div>
      <div style="font-size:.78rem;color:var(--text-light);font-weight:500;">Our licensed pharmacist reviews and verifies your prescription.</div>
    </div>
    <div class="srv-card reveal rd3">
      <div class="srv-icon-c"><svg viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.5"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
      <div style="font-weight:800;color:var(--text);margin-bottom:6px;font-size:.9rem;">4. Fast Delivery</div>
      <div style="font-size:.78rem;color:var(--text-light);font-weight:500;">Delivered same day in Nairobi or next day to other counties.</div>
    </div>
  </div>
</section>

<!-- DELIVERY SCHEDULE -->
<section class="rx-delivery">
  <div style="text-align:center;margin-bottom:32px;">
    <div class="sec-tag sec-tag-solid reveal">Delivery Info</div>
    <h2 class="sec-title reveal rd1">Delivery <span>Schedule</span></h2>
  </div>
  <div class="rx-delivery-grid">
    <div class="delivery-info-box reveal">
      <div class="di-title">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Nairobi: Same Day Delivery
      </div>
      <div class="delivery-row">
        <div class="di-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--green,#2eaf6e)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        <div><div class="di-label">Order by 3:00 PM <span class="di-badge di-badge--green">Same Day</span></div><div class="di-desc">Orders placed before 3:00 PM are dispatched the same day and delivered within Nairobi by evening.</div></div>
      </div>
      <div class="delivery-row">
        <div class="di-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#e65100" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
        <div><div class="di-label">3:00 PM – 6:00 PM <span class="di-badge di-badge--orange">Possible Same Day</span></div><div class="di-desc">Orders between 3–6 PM may still be delivered the same day depending on location. Our team will confirm via WhatsApp.</div></div>
      </div>
      <div class="delivery-row">
        <div class="di-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--green,#2eaf6e)" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
        <div><div class="di-label">After 6:00 PM <span class="di-badge di-badge--orange">Next Morning</span></div><div class="di-desc">Orders received after 6:00 PM are processed the following morning and delivered next day.</div></div>
      </div>
    </div>
    <div class="delivery-info-box reveal rd1">
      <div class="di-title">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
        Outside Nairobi: Countrywide
      </div>
      <div class="delivery-row">
        <div class="di-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--green,#2eaf6e)" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
        <div><div class="di-label">Nearby Counties <span class="di-badge di-badge--green">Next Day</span></div><div class="di-desc">Kiambu, Machakos, Kajiado, Murang'a and surrounding areas typically receive orders the next day.</div></div>
      </div>
      <div class="delivery-row">
        <div class="di-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#e65100" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
        <div><div class="di-label">Distant Counties <span class="di-badge di-badge--orange">1–2 Days</span></div><div class="di-desc">Mombasa, Kisumu, Nakuru, Eldoret and farther counties take 1–2 days depending on courier routes.</div></div>
      </div>
      <div class="delivery-row">
        <div class="di-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--green,#2eaf6e)" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></div>
        <div><div class="di-label">WhatsApp Confirmation</div><div class="di-desc">We always confirm your expected delivery time via WhatsApp before dispatching your order.</div></div>
      </div>
    </div>
  </div>
  <div class="delivery-note-banner reveal">
    <div class="dnb-icon">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e65100" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    </div>
    <p><strong>Important Note:</strong> Delivery will only be initiated after you have <strong>uploaded your prescription</strong> in the WhatsApp chat and our licensed pharmacist has <strong>reviewed and approved</strong> it. Orders without a valid uploaded prescription cannot be processed or dispatched.</p>
  </div>
</section>


<!-- SUBMIT FORM -->
<section class="rx-form-sec">
  <div class="rx-two-col">

    <!-- LEFT: Delivery image with animated bubbles -->
    <div class="rx-img-col">
      <div class="rx-bubble-main"></div>
      <div class="rx-bubble-2"></div>
      <div class="rx-bubble-3"></div>
      <div class="rx-bubble-4"></div>
      <div class="rx-bubble-5"></div>

      <!-- Image — bottom fades into bubbles -->
      <div class="rx-delivery-img-wrap">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/js/images/delivery.png"
             alt="Fast Delivery — CareVee Pharmacy">
      </div>

      <!-- Overlay badge — sits over the fade area -->
      <div class="rx-img-overlay-badge reveal">
        <h3>Fast &amp; Reliable Delivery</h3>
        <p>Same day Nairobi &nbsp;&middot;&nbsp; Next day countrywide</p>
        <div class="rx-badge-dots">
          <div class="rx-dot active"></div>
          <div class="rx-dot"></div>
          <div class="rx-dot"></div>
        </div>
      </div>

      <!-- Delivery badges -->
      <div class="rx-delivery-badges" style="margin-top:16px;">
        <div class="rx-delivery-badge reveal">
          <div class="rx-delivery-badge-icon green">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <div>
            <div class="rx-delivery-badge-title">Nairobi: Same Day</div>
            <div class="rx-delivery-badge-sub">Order before 3:00 PM for same day delivery</div>
          </div>
        </div>
        <div class="rx-delivery-badge reveal rd1">
          <div class="rx-delivery-badge-icon orange">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e65100" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          </div>
          <div>
            <div class="rx-delivery-badge-title">Countrywide: Next Day</div>
            <div class="rx-delivery-badge-sub">Nearby counties next day · Distant 1–2 days</div>
          </div>
        </div>
        <div class="rx-delivery-badge reveal rd2">
          <div class="rx-delivery-badge-icon green">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
          </div>
          <div>
            <div class="rx-delivery-badge-title">WhatsApp Confirmation</div>
            <div class="rx-delivery-badge-sub">We confirm delivery time before dispatch</div>
          </div>
        </div>
        <div class="rx-delivery-badge reveal rd3">
          <div class="rx-delivery-badge-icon blue">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4a80f0" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <div>
            <div class="rx-delivery-badge-title">Licensed Pharmacists</div>
            <div class="rx-delivery-badge-sub">All prescriptions reviewed by certified pharmacists</div>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT: Form -->
    <div class="cta-form reveal rd1">
      <div class="cta-form-title">Submit Prescription</div>
      <p style="font-size:.82rem;color:var(--text-light);margin-bottom:14px;font-weight:500;line-height:1.6;">
        Fill in your details below, then open WhatsApp.
        <strong style="color:var(--dark,#1a3c2e);">Once WhatsApp opens, attach your prescription photo or file directly in the chat</strong> before sending.
      </p>

      <div class="rx-wa-steps">
        <div class="rx-wa-steps-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          How to Submit via WhatsApp
        </div>
        <div class="rx-wa-step"><div class="rx-wa-step-num">1</div><p>Fill in all required fields below and click <strong>Open WhatsApp</strong></p></div>
        <div class="rx-wa-step"><div class="rx-wa-step-num">2</div><p>WhatsApp will open with your details pre-filled in the message</p></div>
        <div class="rx-wa-step"><div class="rx-wa-step-num">3</div><p><strong>Attach your prescription photo or file</strong> using the attachment icon in WhatsApp before sending</p></div>
        <div class="rx-wa-step"><div class="rx-wa-step-num">4</div><p>Send the message, our pharmacist will review and approve your prescription</p></div>
        <div class="rx-wa-step"><div class="rx-wa-step-num">5</div><p><strong>Delivery begins only after prescription is reviewed and approved</strong></p></div>
      </div>

      <div class="rx-form-grid" style="margin-top:18px;">
        <div class="fg" id="fg_fname"><label>First Name <span class="req">*</span></label><input type="text" id="rx_fname" placeholder="e.g. James"><span class="field-error-msg">First name is required.</span></div>
        <div class="fg" id="fg_lname"><label>Last Name <span class="req">*</span></label><input type="text" id="rx_lname" placeholder="e.g. Kamau"><span class="field-error-msg">Last name is required.</span></div>
        <div class="fg" id="fg_age"><label>Age <span class="req">*</span></label><input type="number" id="rx_age" placeholder="e.g. 35" min="1" max="120"><span class="field-error-msg">Please enter your age.</span></div>
        <div class="fg" id="fg_gender">
          <label class="gender-label-text">Gender <span class="req">*</span></label>
          <div class="gender-wrap">
            <label class="gender-opt"><input type="radio" name="rx_gender" value="Male"> Male</label>
            <label class="gender-opt"><input type="radio" name="rx_gender" value="Female"> Female</label>
          </div>
          <span class="field-error-msg">Please select your gender.</span>
        </div>
        <div class="fg" id="fg_phone"><label>Phone Number <span class="req">*</span></label><input type="tel" id="rx_phone" placeholder="+254 790 007616"><span class="field-error-msg">Phone number is required.</span></div>
        <div class="fg"><label>Email <span style="opacity:.4;font-size:.7rem;">(optional)</span></label><input type="email" id="rx_email" placeholder="james@email.com"></div>
        <div class="fg full" id="fg_location"><label>Location / Delivery Address <span class="req">*</span></label><input type="text" id="rx_location" placeholder="e.g. Westlands, Nairobi or Kisumu Town, Kisumu County"><span class="field-error-msg">Please enter your delivery address.</span></div>
        <div class="fg full"><label>Any food/drug allergies <span style="opacity:.4;font-size:.7rem;">(optional)</span></label><input type="text" id="rx_allergies" placeholder="e.g. Penicillin, Peanuts"></div>
        <div class="fg full"><label>Additional notes for pharmacist <span style="opacity:.4;font-size:.7rem;">(optional)</span></label><textarea id="rx_notes" placeholder="Any special instructions..."></textarea></div>
        <div class="fg full" id="fg_file">
          <label>Select Prescription File <span class="req">*</span></label>
          <div class="fg-file" id="fgFileBox" onclick="document.getElementById('rx_file').click()">
            <input type="file" id="rx_file" accept="image/*,.pdf" onchange="showFileName(this)">
            <label class="fg-file-label" for="rx_file">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              <span>Select your prescription file</span>
              <small>Have it ready to attach in WhatsApp — JPG, PNG or PDF</small>
            </label>
            <div id="fileName"></div>
          </div>
          <span class="field-error-msg">Please select your prescription file.</span>
        </div>
      </div>

      <div class="rx-note-box">
        <div class="rx-note-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#e65100" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <p><strong>Note:</strong> Delivery will only be processed after you have <strong>uploaded your prescription</strong> in the WhatsApp chat and our pharmacist has <strong>reviewed and approved</strong> it.</p>
      </div>

      <button onclick="submitRx()" class="form-submit rx-submit-btn" style="margin-top:18px;width:100%;display:flex;align-items:center;justify-content:center;gap:8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink:0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        Open WhatsApp &amp; Attach Prescription
      </button>
    </div>

  </div>
</section>

<!-- CTA BOTTOM -->
<section class="rx-cta">
  <div class="cta-b1"></div><div class="cta-b2"></div>
  <div style="position:relative;z-index:1;">
    <h2 class="sec-title reveal" style="color:white;">Need Help? <span style="color:var(--green-mid,#b8ecd4);">Call Us Now</span></h2>
    <p style="color:rgba(255,255,255,.8);font-size:.88rem;margin:10px 0 24px;" class="reveal rd1">Our pharmacists are available Mon–Sat 8am–6pm to assist you.</p>
    <a href="tel:+254790007616" class="btn-green reveal rd2" style="background:white;color:var(--green);">
      <?php echo esc_html(medicare_phone()); ?>
    </a>
  </div>
</section>

<script>
function showFileName(input) {
  var nameEl = document.getElementById('fileName');
  var fg     = document.getElementById('fg_file');
  if (input.files && input.files[0]) {
    nameEl.textContent = 'Selected: ' + input.files[0].name;
    fg.classList.remove('has-error');
  }
}
function clearError(fieldId) {
  var fg = document.getElementById(fieldId);
  if (fg) fg.classList.remove('has-error');
}
document.addEventListener('DOMContentLoaded', function() {
  [
    ['rx_fname','fg_fname'],['rx_lname','fg_lname'],
    ['rx_age','fg_age'],['rx_phone','fg_phone'],['rx_location','fg_location'],
  ].forEach(function(p) {
    var el = document.getElementById(p[0]);
    if (el) el.addEventListener('input', function() { clearError(p[1]); });
  });
  document.querySelectorAll('input[name="rx_gender"]').forEach(function(r) {
    r.addEventListener('change', function() { clearError('fg_gender'); });
  });
});
function setError(fgId) {
  var fg = document.getElementById(fgId);
  if (fg) {
    fg.classList.add('has-error');
    if (!window._firstErrorScrolled) {
      fg.scrollIntoView({ behavior: 'smooth', block: 'center' });
      window._firstErrorScrolled = true;
    }
  }
}
function submitRx() {
  window._firstErrorScrolled = false;
  var hasError  = false;
  var fname     = document.getElementById('rx_fname').value.trim();
  var lname     = document.getElementById('rx_lname').value.trim();
  var age       = document.getElementById('rx_age').value.trim();
  var gender    = document.querySelector('input[name="rx_gender"]:checked');
  var phone     = document.getElementById('rx_phone').value.trim();
  var location  = document.getElementById('rx_location').value.trim();
  var file      = document.getElementById('rx_file').files[0];
  var email     = document.getElementById('rx_email').value.trim();
  var allergies = document.getElementById('rx_allergies').value.trim();
  var notes     = document.getElementById('rx_notes').value.trim();

  if (!fname)    { setError('fg_fname');    hasError = true; }
  if (!lname)    { setError('fg_lname');    hasError = true; }
  if (!age)      { setError('fg_age');      hasError = true; }
  if (!gender)   { setError('fg_gender');   hasError = true; }
  if (!phone)    { setError('fg_phone');    hasError = true; }
  if (!location) { setError('fg_location'); hasError = true; }
  if (!file)     { setError('fg_file');     hasError = true; }
  if (hasError) return;

  var msg =
    'Hello <?php echo esc_js(get_bloginfo("name")); ?>!\n\n' +
    'I would like to submit a prescription.\n\n' +
    'Patient Details:\n' +
    '- Name: '     + fname + ' ' + lname + '\n' +
    '- Age: '      + age   + '\n' +
    '- Gender: '   + gender.value + '\n' +
    '- Phone: '    + phone + '\n' +
    (email     ? '- Email: '     + email     + '\n' : '') +
    '- Location: ' + location   + '\n' +
    (allergies ? '- Allergies: ' + allergies + '\n' : '') +
    (notes     ? '- Notes: '     + notes     + '\n' : '') +
    '\n*** ACTION REQUIRED ***\n' +
    'Please attach your prescription photo or file to this\n' +
    'message before sending. Delivery will only begin after\n' +
    'the prescription has been reviewed and approved.\n\n' +
    'Delivery Schedule:\n' +
    '- Nairobi before 3PM: same day delivery\n' +
    '- Nairobi 3-5PM: possible same day (team will confirm)\n' +
    '- Outside Nairobi: next day or 1-2 days by county\n\n' +
    'Thank you!';

  window.open('https://wa.me/254790007616?text=' + encodeURIComponent(msg), '_blank');
}
</script>

<?php get_footer(); ?>