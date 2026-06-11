<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PT Megah Catur Abadi — Distribusi Alat Kesehatan</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg:       #060810;
      --bg2:      #0C1220;
      --bg3:      #111928;
      --gold:     #C89B3C;
      --gold-lt:  #E2B85A;
      --gold-dim: rgba(200,155,60,0.14);
      --cream:    #F0E8D8;
      --text:     #C8C2BC;
      --muted:    #6A6660;
      --border:   rgba(200,155,60,0.18);
      --green:    #3A7D6B;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--bg);
      color: var(--text);
      overflow-x: hidden;
      -webkit-font-smoothing: antialiased;
    }

    ::-webkit-scrollbar { width: 3px; }
    ::-webkit-scrollbar-track { background: var(--bg); }
    ::-webkit-scrollbar-thumb { background: var(--gold); }

    /* ─── NAVBAR ─── */
    #navbar {
      position: fixed; top: 0; left: 0; right: 0; z-index: 100;
      padding: 1.5rem 5%;
      display: flex; align-items: center; justify-content: space-between;
      transition: all 0.4s ease;
    }
    #navbar.scrolled {
      background: rgba(6,8,16,0.9);
      backdrop-filter: blur(18px);
      border-bottom: 1px solid var(--border);
      padding: 1rem 5%;
    }
    .logo { display: flex; flex-direction: column; gap: 2px; }
    .logo-mark {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.6rem; font-weight: 600;
      color: var(--gold); letter-spacing: 0.18em; line-height: 1;
    }
    .logo-name {
      font-size: 0.52rem; letter-spacing: 0.22em;
      color: var(--muted); text-transform: uppercase;
    }
    .nav-links { display: flex; gap: 2.5rem; list-style: none; }
    .nav-links a {
      text-decoration: none; color: var(--muted);
      font-size: 0.75rem; font-weight: 400;
      letter-spacing: 0.14em; text-transform: uppercase;
      transition: color 0.3s;
    }
    .nav-links a:hover { color: var(--gold); }
    
    .nav-actions { display: flex; gap: 1rem; align-items: center; }
    .nav-cta {
      padding: 0.55rem 1.4rem;
      border: 1px solid var(--gold); border-radius: 2px;
      color: var(--gold); text-decoration: none;
      font-size: 0.72rem; font-weight: 500;
      letter-spacing: 0.12em; text-transform: uppercase;
      transition: all 0.3s;
    }
    .nav-cta:hover { background: var(--gold); color: var(--bg); }
    
    .nav-cta-secondary {
      padding: 0.55rem 1.4rem;
      border: 1px solid rgba(200,155,60,0.22); border-radius: 2px;
      color: var(--text); text-decoration: none;
      font-size: 0.72rem; font-weight: 500;
      letter-spacing: 0.12em; text-transform: uppercase;
      transition: all 0.3s;
    }
    .nav-cta-secondary:hover { border-color: var(--gold); color: var(--gold); }
    
    .mobile-only { display: none; }
    .nav-burger { display: none; flex-direction: column; gap: 5px; cursor: pointer; padding: 4px; }
    .nav-burger span { display: block; width: 22px; height: 1px; background: var(--text); transition: all 0.3s; }

    /* ─── HERO ─── */
    .hero {
      min-height: 100vh; display: flex; align-items: center;
      padding: 0 5%; position: relative; overflow: hidden;
    }
    .hero-bg {
      position: absolute; inset: 0;
      background:
        radial-gradient(ellipse 55% 65% at 72% 48%, rgba(200,155,60,0.075) 0%, transparent 65%),
        radial-gradient(ellipse 35% 45% at 18% 78%, rgba(58,125,107,0.055) 0%, transparent 55%),
        var(--bg);
    }
    .hero-bg::before {
      content: '';
      position: absolute; inset: 0;
      background-image:
        linear-gradient(rgba(200,155,60,0.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(200,155,60,0.025) 1px, transparent 1px);
      background-size: 64px 64px;
    }
    .hero-content { position: relative; z-index: 2; max-width: 660px; }
    .eyebrow {
      display: inline-flex; align-items: center; gap: 0.85rem;
      margin-bottom: 2rem;
      opacity: 0; animation: fadeUp 0.8s ease forwards 0.3s;
    }
    .eyebrow::before { content: ''; display: block; width: 28px; height: 1px; background: var(--gold); }
    .eyebrow span {
      font-size: 0.68rem; letter-spacing: 0.28em;
      text-transform: uppercase; color: var(--gold); font-weight: 500;
    }
    .hero h1 {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(3rem, 6.5vw, 5.8rem);
      font-weight: 300; line-height: 1.04; color: var(--cream);
      margin-bottom: 1.6rem;
      opacity: 0; animation: fadeUp 0.9s ease forwards 0.5s;
    }
    .hero h1 em { font-style: italic; color: var(--gold); }
    .hero-desc {
      font-size: 0.975rem; line-height: 1.8; color: var(--muted);
      max-width: 460px; margin-bottom: 2.5rem;
      opacity: 0; animation: fadeUp 0.9s ease forwards 0.7s;
    }
    .hero-actions {
      display: flex; gap: 1rem; flex-wrap: wrap;
      opacity: 0; animation: fadeUp 0.9s ease forwards 0.9s;
    }

    .btn-gold {
      display: inline-flex; align-items: center; gap: 0.6rem;
      padding: 0.9rem 2rem; background: var(--gold); color: var(--bg);
      text-decoration: none; font-size: 0.8rem; font-weight: 500;
      letter-spacing: 0.09em; text-transform: uppercase; border-radius: 2px;
      border: none; cursor: pointer; transition: all 0.3s; font-family: 'DM Sans', sans-serif;
    }
    .btn-gold:hover { background: var(--gold-lt); transform: translateY(-1px); }
    .btn-outline {
      display: inline-flex; align-items: center; gap: 0.6rem;
      padding: 0.9rem 2rem; background: transparent;
      color: var(--text); text-decoration: none;
      font-size: 0.8rem; font-weight: 400;
      letter-spacing: 0.09em; text-transform: uppercase;
      border: 1px solid rgba(200,200,190,0.22); border-radius: 2px;
      transition: all 0.3s;
    }
    .btn-outline:hover { border-color: var(--gold); color: var(--gold); }

    /* Hero decorative sphere */
    .hero-orb {
      position: absolute; right: 5%; top: 50%; transform: translateY(-50%);
      width: min(44vw, 520px); aspect-ratio: 1;
      opacity: 0; animation: fadeIn 1.4s ease forwards 1.1s;
      pointer-events: none;
    }
    .orb-ring {
      position: absolute; border-radius: 50%;
      border: 1px solid rgba(200,155,60,0.18);
    }
    .orb-r1 { inset: 5%; animation: spinSlow 30s linear infinite; }
    .orb-r2 { inset: 20%; border-color: rgba(200,155,60,0.1); animation: spinSlow 42s linear infinite reverse; }
    .orb-r3 { inset: 35%; border: 1.5px solid rgba(200,155,60,0.28); }
    .orb-center {
      position: absolute; inset: 0;
      display: flex; align-items: center; justify-content: center;
      flex-direction: column; text-align: center;
    }
    .orb-text {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(2rem, 5vw, 3.8rem);
      color: rgba(200,155,60,0.12); font-weight: 600; letter-spacing: 0.22em; line-height: 1;
    }
    .orb-sub {
      font-size: 0.58rem; letter-spacing: 0.32em; text-transform: uppercase;
      color: rgba(200,155,60,0.14); margin-top: 0.6rem;
    }
    .orb-cross {
      position: absolute;
      display: flex; align-items: center; justify-content: center;
    }
    .oc-top    { top: 4%;   left: 50%; transform: translateX(-50%); }
    .oc-right  { right: 4%; top:  50%; transform: translateY(-50%); }
    .oc-bottom { bottom: 4%;left: 50%; transform: translateX(-50%); }
    .oc-left   { left: 4%;  top:  50%; transform: translateY(-50%); }
    .orb-cross svg { fill: none; stroke: var(--gold); stroke-width: 0.9; opacity: 0.5; }

    /* ─── MARQUEE ─── */
    .marquee-wrap {
      border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
      background: var(--bg2); overflow: hidden; padding: 1.1rem 0;
    }
    .marquee-track {
      display: flex; white-space: nowrap;
      animation: marquee 28s linear infinite;
    }
    .mq-item {
      display: inline-flex; align-items: center; gap: 0.85rem;
      padding: 0 2.5rem;
      font-size: 0.7rem; letter-spacing: 0.22em; text-transform: uppercase; color: var(--muted);
    }
    .mq-item .dot { width: 4px; height: 4px; border-radius: 50%; background: var(--gold); flex-shrink: 0; }
    .mq-item.hi { color: var(--gold); }

    /* ─── SHARED SECTION STYLES ─── */
    section { padding: 7rem 5%; }

    .sec-eyebrow {
      display: inline-flex; align-items: center; gap: 0.85rem; margin-bottom: 1.2rem;
    }
    .sec-eyebrow::before { content: ''; display: block; width: 22px; height: 1px; background: var(--gold); }
    .sec-eyebrow span {
      font-size: 0.65rem; letter-spacing: 0.28em; text-transform: uppercase;
      color: var(--gold); font-weight: 500;
    }
    .sec-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(2.2rem, 4vw, 3.6rem);
      font-weight: 400; color: var(--cream); line-height: 1.12;
    }
    .sec-title em { font-style: italic; color: var(--gold); }

    /* ─── ABOUT ─── */
    .about { background: var(--bg); }
    .about-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 6rem; align-items: center;
    }
    .about-desc {
      font-size: 0.95rem; line-height: 1.85; color: var(--muted); margin-bottom: 1.25rem;
    }
    .about-rule {
      width: 100%; height: 1px;
      background: linear-gradient(90deg, var(--gold) 0%, transparent 100%);
      margin: 2rem 0;
    }
    .about-nums { display: flex; gap: 2.5rem; }
    .about-num-val {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2.8rem; font-weight: 600; color: var(--gold); line-height: 1;
    }
    .about-num-lbl {
      font-size: 0.72rem; letter-spacing: 0.1em; color: var(--muted);
      text-transform: uppercase; margin-top: 0.35rem; line-height: 1.5;
    }
    .about-card {
      background: var(--bg2); border: 1px solid var(--border); border-radius: 3px;
      padding: 2.75rem; position: relative; overflow: hidden;
    }
    .about-card::before {
      content: ''; position: absolute; top: 0; left: 0; right: 0;
      height: 2px; background: linear-gradient(90deg, var(--gold), transparent);
    }
    .about-card-ico {
      width: 52px; height: 52px;
      border: 1px solid var(--border); border-radius: 2px;
      display: flex; align-items: center; justify-content: center;
      color: var(--gold); margin-bottom: 1.75rem;
    }
    .about-card h3 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.5rem; color: var(--cream); font-weight: 400; margin-bottom: 0.85rem;
    }
    .about-card p { font-size: 0.88rem; color: var(--muted); line-height: 1.8; }
    .about-card-deco {
      position: absolute; bottom: -30px; right: -30px;
      width: 130px; height: 130px; border-radius: 50%;
      border: 1px solid rgba(200,155,60,0.08);
    }
    .info-pair {
      display: grid; grid-template-columns: 1fr 1fr;
      gap: 1px; background: var(--border);
      margin-top: 1px; border: 1px solid var(--border);
    }
    .info-cell {
      background: var(--bg2); padding: 1.4rem 1.5rem;
    }
    .info-cell-lbl { font-size: 0.62rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--gold); margin-bottom: 0.4rem; }
    .info-cell-val { font-size: 0.84rem; color: var(--muted); line-height: 1.55; }

    /* ─── PRODUCTS ─── */
    .products { background: var(--bg2); }
    .prod-header {
      display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3.5rem;
    }
    .prod-header p { max-width: 340px; font-size: 0.88rem; color: var(--muted); line-height: 1.8; }
    .prod-grid {
      display: grid; grid-template-columns: repeat(3, 1fr);
      gap: 1.5px; background: var(--border); border: 1px solid var(--border);
    }
    .prod-card {
      background: var(--bg2); padding: 2.4rem 2rem;
      position: relative; cursor: default; overflow: hidden;
      transition: background 0.35s;
    }
    .prod-card:hover { background: var(--bg3); }
    .prod-card::after {
      content: ''; position: absolute; bottom: 0; left: 0; right: 0;
      height: 0;
      background: linear-gradient(to top, rgba(200,155,60,0.055), transparent);
      transition: height 0.4s;
    }
    .prod-card:hover::after { height: 100%; }
    .prod-num { font-size: 0.62rem; letter-spacing: 0.22em; color: var(--muted); margin-bottom: 1.5rem; opacity: 0.45; }
    .prod-ico {
      width: 42px; height: 42px;
      border: 1px solid rgba(200,155,60,0.22); border-radius: 2px;
      display: flex; align-items: center; justify-content: center; color: var(--gold);
      margin-bottom: 1.2rem; transition: all 0.3s;
    }
    .prod-card:hover .prod-ico { border-color: var(--gold); color: var(--gold-lt); }
    .prod-card h3 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.28rem; color: var(--cream); font-weight: 400; margin-bottom: 0.7rem;
    }
    .prod-card p { font-size: 0.8rem; color: var(--muted); line-height: 1.72; }

    /* ─── STATS ─── */
    .stats { background: var(--bg); position: relative; overflow: hidden; }
    .stats::before {
      content: ''; position: absolute; inset: 0;
      background: radial-gradient(ellipse 50% 80% at 50% 50%, rgba(200,155,60,0.04) 0%, transparent 65%);
    }
    .stats-grid {
      display: grid; grid-template-columns: repeat(4, 1fr);
      border: 1px solid var(--border); position: relative;
    }
    .stat-item {
      padding: 3rem 2.5rem; border-right: 1px solid var(--border); position: relative;
    }
    .stat-item:last-child { border-right: none; }
    .stat-item::before {
      content: ''; position: absolute; top: 0; left: 0; right: 0;
      height: 1px; background: linear-gradient(90deg, transparent, var(--gold), transparent);
    }
    .stat-val {
      font-family: 'Cormorant Garamond', serif;
      font-size: 3.4rem; font-weight: 600; color: var(--gold); line-height: 1; margin-bottom: 0.55rem;
    }
    .stat-sfx { font-size: 1.8rem; }
    .stat-lbl { font-size: 0.78rem; color: var(--muted); letter-spacing: 0.04em; line-height: 1.55; }

    /* ─── WHY ─── */
    .why { background: var(--bg); }
    .why-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: start; margin-top: 3.75rem; }
    .feat-item {
      display: flex; gap: 1.5rem; padding: 2rem 0;
      border-bottom: 1px solid rgba(200,155,60,0.1);
    }
    .feat-item:first-child { padding-top: 0; }
    .feat-n {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.1rem; color: var(--gold); opacity: 0.38; min-width: 26px; padding-top: 4px;
    }
    .feat-body h4 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.22rem; color: var(--cream); font-weight: 400; margin-bottom: 0.5rem;
    }
    .feat-body p { font-size: 0.83rem; color: var(--muted); line-height: 1.78; }
    .why-right { position: sticky; top: 7rem; }
    .why-card {
      background: var(--bg2); border: 1px solid var(--border); border-radius: 3px;
      padding: 2.75rem; position: relative; overflow: hidden;
    }
    .why-card::before {
      content: ''; position: absolute; inset: 0;
      background: radial-gradient(circle at top right, rgba(200,155,60,0.055), transparent 60%);
    }
    .why-quote {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.3rem; font-style: italic; color: var(--cream); line-height: 1.65;
      margin-bottom: 2rem; position: relative; padding-left: 1.75rem;
    }
    .why-quote::before {
      content: '\201C'; font-size: 5rem; color: var(--gold); opacity: 0.18;
      position: absolute; left: -0.4rem; top: -1.2rem; line-height: 1;
      font-style: normal; font-family: 'Cormorant Garamond', serif;
    }
    .why-sig { font-size: 0.78rem; color: var(--muted); letter-spacing: 0.06em; line-height: 1.6; }
    .why-sig strong { display: block; color: var(--cream); font-weight: 500; font-size: 0.83rem; margin-bottom: 0.15rem; }

    /* ─── CLIENTS ─── */
    .clients { background: var(--bg2); }
    .clients-grid {
      display: grid; grid-template-columns: repeat(4, 1fr);
      gap: 1px; background: var(--border);
      margin-top: 3.5rem; border: 1px solid var(--border);
    }
    .client-card {
      background: var(--bg2); padding: 2.25rem 1.75rem;
      display: flex; flex-direction: column; gap: 0.6rem;
      transition: background 0.3s;
    }
    .client-card:hover { background: var(--bg3); }
    .client-ico {
      width: 34px; height: 34px; border: 1px solid var(--border); border-radius: 2px;
      display: flex; align-items: center; justify-content: center;
      color: var(--gold); margin-bottom: 0.4rem; opacity: 0.7;
    }
    .client-card h4 { font-size: 0.84rem; color: var(--cream); font-weight: 500; line-height: 1.4; }
    .client-card span { font-size: 0.7rem; color: var(--muted); letter-spacing: 0.04em; }

    /* ─── CTA ─── */
    .cta-section { background: var(--bg); text-align: center; position: relative; overflow: hidden; }
    .cta-section::before {
      content: ''; position: absolute; inset: 0;
      background: radial-gradient(ellipse 60% 60% at 50% 50%, rgba(200,155,60,0.07) 0%, transparent 65%);
    }
    .cta-inner { position: relative; z-index: 2; }
    .cta-inner .sec-eyebrow { justify-content: center; }
    .cta-inner .sec-title { max-width: 640px; margin: 0 auto 1.4rem; }
    .cta-desc { max-width: 440px; margin: 0 auto 2.75rem; font-size: 0.93rem; color: var(--muted); line-height: 1.8; }
    .cta-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }

    /* ─── CONTACT ─── */
    .contact { background: var(--bg2); }
    .contact-grid { display: grid; grid-template-columns: 1fr 1.25fr; gap: 5rem; align-items: start; }
    .ci-item { display: flex; gap: 1.2rem; padding: 1.5rem 0; border-bottom: 1px solid rgba(200,155,60,0.1); }
    .ci-item:first-child { padding-top: 0; }
    .ci-ico {
      width: 36px; height: 36px; border: 1px solid var(--border); border-radius: 2px;
      display: flex; align-items: center; justify-content: center; color: var(--gold);
      flex-shrink: 0; margin-top: 2px;
    }
    .ci-ico svg { width: 15px; height: 15px; }
    .ci-lbl { font-size: 0.62rem; letter-spacing: 0.18em; text-transform: uppercase; color: var(--gold); margin-bottom: 0.4rem; }
    .ci-val { font-size: 0.88rem; color: var(--muted); line-height: 1.65; }
    .form-wrap {
      background: var(--bg3); border: 1px solid var(--border); border-radius: 3px;
      padding: 2.5rem; position: relative; overflow: hidden;
    }
    .form-wrap::before {
      content: ''; position: absolute; top: 0; left: 0; right: 0;
      height: 2px; background: linear-gradient(90deg, var(--gold), transparent);
    }
    .form-head {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.4rem; color: var(--cream); font-weight: 400; margin-bottom: 0.4rem;
    }
    .form-sub { font-size: 0.78rem; color: var(--muted); margin-bottom: 2rem; }
    .form-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; }
    .fgrp { display: flex; flex-direction: column; gap: 0.45rem; }
    .fgrp label { font-size: 0.63rem; letter-spacing: 0.18em; text-transform: uppercase; color: var(--gold); opacity: 0.8; }
    .fgrp input, .fgrp textarea, .fgrp select {
      background: rgba(255,255,255,0.025); border: 1px solid rgba(200,155,60,0.18);
      border-radius: 2px; padding: 0.8rem 1rem; color: var(--text);
      font-family: 'DM Sans', sans-serif; font-size: 0.88rem; outline: none;
      transition: border-color 0.3s; width: 100%;
    }
    .fgrp input:focus, .fgrp textarea:focus, .fgrp select:focus { border-color: var(--gold); }
    .fgrp select option { background: var(--bg3); }
    .fgrp textarea { resize: vertical; min-height: 110px; }
    .form-fields { display: flex; flex-direction: column; gap: 1.2rem; }

    /* ─── FOOTER ─── */
    footer {
      background: var(--bg); border-top: 1px solid var(--border);
      padding: 3rem 5%;
    }
    .footer-row { display: flex; justify-content: space-between; align-items: center; }
    .foot-copy { font-size: 0.76rem; color: var(--muted); margin-top: 0.35rem; }
    .foot-copy span { color: var(--gold); }
    .foot-links { display: flex; gap: 2rem; }
    .foot-links a {
      font-size: 0.72rem; color: var(--muted); text-decoration: none;
      letter-spacing: 0.12em; text-transform: uppercase; transition: color 0.3s;
    }
    .foot-links a:hover { color: var(--gold); }

    /* ─── SCROLL REVEAL ─── */
    .r {
      opacity: 0; transform: translateY(28px);
      transition: opacity 0.8s ease, transform 0.8s ease;
    }
    .r.vis { opacity: 1; transform: translateY(0); }
    .d1 { transition-delay: 0.1s; }
    .d2 { transition-delay: 0.2s; }
    .d3 { transition-delay: 0.3s; }
    .d4 { transition-delay: 0.4s; }

    /* ─── KEYFRAMES ─── */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(22px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
      from { opacity: 0; } to { opacity: 1; }
    }
    @keyframes spinSlow {
      from { transform: rotate(0deg); } to { transform: rotate(360deg); }
    }
    @keyframes marquee {
      from { transform: translateX(0); } to { transform: translateX(-50%); }
    }

    /* ─── RESPONSIVE ─── */
    @media (max-width: 960px) {
      .about-grid, .why-grid, .contact-grid { grid-template-columns: 1fr; gap: 3rem; }
      .prod-grid { grid-template-columns: 1fr 1fr; }
      .stats-grid { grid-template-columns: 1fr 1fr; }
      .stat-item:nth-child(2) { border-right: none; }
      .stat-item:nth-child(3) { border-right: 1px solid var(--border); border-top: 1px solid var(--border); }
      .stat-item:nth-child(4) { border-top: 1px solid var(--border); }
      .clients-grid { grid-template-columns: 1fr 1fr; }
      .hero-orb { display: none; }
      .prod-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
      .why-right { position: relative; top: auto; }
      .nav-links, .nav-actions { display: none; }
      .nav-burger { display: flex; }
      .mobile-only { display: block; }
    }
    @media (max-width: 600px) {
      section { padding: 5rem 5%; }
      .prod-grid, .clients-grid { grid-template-columns: 1fr; }
      .stats-grid { grid-template-columns: 1fr 1fr; }
      .stat-item:nth-child(3) { border-right: none; }
      .hero-actions, .cta-btns { flex-direction: column; }
      .about-nums { gap: 1.5rem; }
      .form-cols { grid-template-columns: 1fr; }
      .footer-row { flex-direction: column; gap: 2rem; align-items: flex-start; }
      .foot-links { flex-wrap: wrap; gap: 1.25rem; }
    }
  </style>
</head>
<body>

<!-- ═══ NAVBAR ═══ -->
<nav id="navbar">
  <div class="logo">
    <span class="logo-mark">MCA</span>
    <span class="logo-name">PT Megah Catur Abadi</span>
  </div>
  <ul class="nav-links">
    <li><a href="#tentang">Tentang</a></li>
    <li><a href="#produk">Produk</a></li>
    <li><a href="#keunggulan">Keunggulan</a></li>
    <li><a href="#klien">Klien</a></li>
    <li><a href="#kontak">Kontak</a></li>
    @auth
      <li class="mobile-only"><a href="{{ url('/products') }}" style="color: var(--gold);">Dashboard</a></li>
    @else
      <li class="mobile-only"><a href="{{ route('login') }}" style="color: var(--gold);">Login</a></li>
    @endauth
    <li class="mobile-only"><a href="#kontak" style="border: 1px solid rgba(200,155,60,0.3); padding: 0.55rem 1.4rem; border-radius: 2px; text-align: center; margin-top: 0.5rem; display: block; color: var(--text);">Konsultasi</a></li>
  </ul>
  
  <div class="nav-actions">
    @auth
      <a href="{{ url('/products') }}" class="nav-cta" style="background: var(--gold); color: var(--bg);">Dashboard</a>
    @else
      <a href="{{ route('login') }}" class="nav-cta">Login</a>
    @endauth
    <a href="#kontak" class="nav-cta-secondary">Konsultasi</a>
  </div>
  <div class="nav-burger" id="burger"><span></span><span></span><span></span></div>
</nav>

<!-- ═══ HERO ═══ -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-content">
    <div class="eyebrow"><span>Distributor Alat Kesehatan Resmi</span></div>
    <h1>Teknologi Medis<br>untuk <em>Kehidupan</em><br>yang Lebih Baik</h1>
    <p class="hero-desc">
      PT Megah Catur Abadi menghadirkan peralatan kesehatan berkualitas tinggi untuk rumah sakit, klinik, dan masyarakat luas — dengan layanan purna jual yang handal dan tim profesional berpengalaman di Surabaya.
    </p>
    <div class="hero-actions">
      <a href="#produk" class="btn-gold">
        Lihat Produk
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M2 6.5h9M8 3l3.5 3.5L8 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </a>
      <a href="#kontak" class="btn-outline">Hubungi Kami</a>
    </div>
  </div>

  <!-- Decorative orb -->
  <div class="hero-orb">
    <div class="orb-ring orb-r1"></div>
    <div class="orb-ring orb-r2"></div>
    <div class="orb-ring orb-r3"></div>
    <div class="orb-center">
      <div class="orb-text">MCA</div>
      <div class="orb-sub">Est. 2008</div>
    </div>
    <div class="orb-cross oc-top">
      <svg width="36" height="36" viewBox="0 0 36 36"><line x1="18" y1="4" x2="18" y2="32"/><line x1="4" y1="18" x2="32" y2="18"/><circle cx="18" cy="18" r="3"/></svg>
    </div>
    <div class="orb-cross oc-right">
      <svg width="36" height="36" viewBox="0 0 36 36"><line x1="18" y1="4" x2="18" y2="32"/><line x1="4" y1="18" x2="32" y2="18"/><circle cx="18" cy="18" r="3"/></svg>
    </div>
    <div class="orb-cross oc-bottom">
      <svg width="36" height="36" viewBox="0 0 36 36"><line x1="18" y1="4" x2="18" y2="32"/><line x1="4" y1="18" x2="32" y2="18"/><circle cx="18" cy="18" r="3"/></svg>
    </div>
    <div class="orb-cross oc-left">
      <svg width="36" height="36" viewBox="0 0 36 36"><line x1="18" y1="4" x2="18" y2="32"/><line x1="4" y1="18" x2="32" y2="18"/><circle cx="18" cy="18" r="3"/></svg>
    </div>
  </div>
</section>

<!-- ═══ MARQUEE ═══ -->
<div class="marquee-wrap">
  <div class="marquee-track">
    <span class="mq-item hi"><span class="dot"></span>USG & Ultrasonografi</span>
    <span class="mq-item"><span class="dot"></span>Ventilator ICU</span>
    <span class="mq-item"><span class="dot"></span>Patient Monitor</span>
    <span class="mq-item hi"><span class="dot"></span>Defibrillator</span>
    <span class="mq-item"><span class="dot"></span>Infusion Pump</span>
    <span class="mq-item"><span class="dot"></span>Autoclave Sterilisasi</span>
    <span class="mq-item hi"><span class="dot"></span>ECG & EKG</span>
    <span class="mq-item"><span class="dot"></span>Blood Gas Analyzer</span>
    <span class="mq-item"><span class="dot"></span>Laminar Air Flow</span>
    <span class="mq-item hi"><span class="dot"></span>X-Ray & Radiologi</span>
    <span class="mq-item"><span class="dot"></span>Alat Bedah</span>
    <span class="mq-item"><span class="dot"></span>Tensimeter Digital</span>
    <!-- duplicate for loop -->
    <span class="mq-item hi"><span class="dot"></span>USG & Ultrasonografi</span>
    <span class="mq-item"><span class="dot"></span>Ventilator ICU</span>
    <span class="mq-item"><span class="dot"></span>Patient Monitor</span>
    <span class="mq-item hi"><span class="dot"></span>Defibrillator</span>
    <span class="mq-item"><span class="dot"></span>Infusion Pump</span>
    <span class="mq-item"><span class="dot"></span>Autoclave Sterilisasi</span>
    <span class="mq-item hi"><span class="dot"></span>ECG & EKG</span>
    <span class="mq-item"><span class="dot"></span>Blood Gas Analyzer</span>
    <span class="mq-item"><span class="dot"></span>Laminar Air Flow</span>
    <span class="mq-item hi"><span class="dot"></span>X-Ray & Radiologi</span>
    <span class="mq-item"><span class="dot"></span>Alat Bedah</span>
    <span class="mq-item"><span class="dot"></span>Tensimeter Digital</span>
  </div>
</div>

<!-- ═══ ABOUT ═══ -->
<section id="tentang" class="about">
  <div class="about-grid">
    <div class="r">
      <div class="sec-eyebrow"><span>Tentang Kami</span></div>
      <h2 class="sec-title" style="margin-bottom:1.6rem;">Mitra Terpercaya<br><em>Dunia Medis</em> Indonesia</h2>
      <p class="about-desc">PT Megah Catur Abadi berdiri dengan satu misi: memastikan setiap fasilitas kesehatan dan individu di Indonesia memiliki akses ke peralatan medis berkualitas dunia.</p>
      <p class="about-desc">Berbasis di Surabaya, kami melayani rumah sakit pemerintah, swasta, klinik spesialis, puskesmas, apotek, hingga pelanggan perorangan — dengan standar kualitas dan pelayanan yang tidak pernah kami kompromikan.</p>
      <div class="about-rule"></div>
      <div class="about-nums">
        <div>
          <div class="about-num-val" data-cnt="15">0+</div>
          <div class="about-num-lbl">Tahun<br>Pengalaman</div>
        </div>
        <div>
          <div class="about-num-val" data-cnt="500">0+</div>
          <div class="about-num-lbl">Produk<br>Tersedia</div>
        </div>
        <div>
          <div class="about-num-val" data-cnt="200">0+</div>
          <div class="about-num-lbl">Klien<br>Terlayani</div>
        </div>
      </div>
    </div>
    <div class="r d2">
      <div class="about-card">
        <div class="about-card-ico">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
          </svg>
        </div>
        <h3>Terdaftar & Tersertifikasi Resmi</h3>
        <p>Seluruh produk yang kami distribusikan memiliki izin edar resmi Kementerian Kesehatan RI dan memenuhi standar internasional — menjamin keamanan dan keandalan di setiap lingkungan klinis.</p>
        <div class="about-card-deco"></div>
      </div>
      <div class="info-pair">
        <div class="info-cell">
          <div class="info-cell-lbl">Layanan Teknis</div>
          <div class="info-cell-val">Instalasi, kalibrasi & pelatihan oleh teknisi bersertifikat</div>
        </div>
        <div class="info-cell" style="border-left:1px solid var(--border);">
          <div class="info-cell-lbl">Garansi Produk</div>
          <div class="info-cell-val">Garansi resmi & suku cadang original tersedia</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ PRODUCTS ═══ -->
<section id="produk" class="products">
  <div class="prod-header r">
    <div>
      <div class="sec-eyebrow"><span>Kategori Produk</span></div>
      <h2 class="sec-title">Solusi Lengkap<br><em>Peralatan Medis</em></h2>
    </div>
    <p>Dari diagnostik hingga bedah, kami menyediakan rangkaian lengkap peralatan medis yang dibutuhkan fasilitas kesehatan modern.</p>
  </div>

  <div class="prod-grid">
    <!-- Card 1 -->
    <div class="prod-card r">
      <div class="prod-num">01</div>
      <div class="prod-ico">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="5"/>
          <line x1="12" y1="2" x2="12" y2="7"/><line x1="12" y1="17" x2="12" y2="22"/>
          <line x1="2" y1="12" x2="7" y2="12"/><line x1="17" y1="12" x2="22" y2="12"/>
        </svg>
      </div>
      <h3>Diagnostik & Pencitraan</h3>
      <p>USG portable & standar, X-Ray, ECG/EKG, dan perangkat diagnostik untuk pemeriksaan yang akurat dan efisien di setiap fasilitas.</p>
    </div>
    <!-- Card 2 -->
    <div class="prod-card r d1">
      <div class="prod-num">02</div>
      <div class="prod-ico">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
        </svg>
      </div>
      <h3>Monitoring Pasien</h3>
      <p>Patient monitor multi-parameter, pulse oximeter, vital sign monitor, dan sistem pemantauan ICU terintegrasi 24 jam.</p>
    </div>
    <!-- Card 3 -->
    <div class="prod-card r d2">
      <div class="prod-num">03</div>
      <div class="prod-ico">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
          <line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/>
        </svg>
      </div>
      <h3>ICU & Life Support</h3>
      <p>Ventilator ICU, CPAP/BiPAP, defibrillator, infusion pump syringe, dan seluruh peralatan life-support esensial ruang intensif.</p>
    </div>
    <!-- Card 4 -->
    <div class="prod-card r">
      <div class="prod-num">04</div>
      <div class="prod-ico">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3M3 16v3a2 2 0 0 0 2 2h3m8 0h3a2 2 0 0 0 2-2v-3"/>
        </svg>
      </div>
      <h3>Sterilisasi & Desinfeksi</h3>
      <p>Autoclave, UV sterilizer, laminar air flow cabinet, dan sistem sterilisasi modern sesuai panduan WHO dan Kemenkes RI.</p>
    </div>
    <!-- Card 5 -->
    <div class="prod-card r d1">
      <div class="prod-num">05</div>
      <div class="prod-ico">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/>
        </svg>
      </div>
      <h3>Peralatan Laboratorium</h3>
      <p>Blood gas analyzer, hematology analyzer, centrifuge medis, inkubator kultur, dan seluruh kebutuhan laboratorium klinis.</p>
    </div>
    <!-- Card 6 -->
    <div class="prod-card r d2">
      <div class="prod-num">06</div>
      <div class="prod-ico">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
      </div>
      <h3>Alat Medis Rumahan</h3>
      <p>Tensimeter digital, nebulizer, pulse oximeter, glucometer, dan peralatan perawatan mandiri berkualitas untuk konsumen langsung.</p>
    </div>
  </div>
</section>

<!-- ═══ STATS ═══ -->
<section class="stats">
  <div class="stats-grid">
    <div class="stat-item r">
      <div class="stat-val"><span class="ctr" data-t="15">0</span><span class="stat-sfx">+</span></div>
      <div class="stat-lbl">Tahun melayani<br>fasilitas kesehatan</div>
    </div>
    <div class="stat-item r d1">
      <div class="stat-val"><span class="ctr" data-t="500">0</span><span class="stat-sfx">+</span></div>
      <div class="stat-lbl">Produk medis<br>tersertifikasi resmi</div>
    </div>
    <div class="stat-item r d2">
      <div class="stat-val"><span class="ctr" data-t="200">0</span><span class="stat-sfx">+</span></div>
      <div class="stat-lbl">Rumah sakit & klinik<br>yang telah kami layani</div>
    </div>
    <div class="stat-item r d3">
      <div class="stat-val"><span class="ctr" data-t="24">0</span><span class="stat-sfx">/7</span></div>
      <div class="stat-lbl">Dukungan teknis<br>sepanjang waktu</div>
    </div>
  </div>
</section>

<!-- ═══ WHY US ═══ -->
<section id="keunggulan" class="why">
  <div class="sec-eyebrow r"><span>Keunggulan Kami</span></div>
  <h2 class="sec-title r" style="margin-bottom:0;">Mengapa Memilih<br><em>PT Megah Catur Abadi?</em></h2>
  <div class="why-grid">
    <div>
      <div class="feat-item r">
        <div class="feat-n">01</div>
        <div class="feat-body">
          <h4>Produk Berstandar Internasional</h4>
          <p>Semua produk kami berasal dari merek-merek terkemuka dunia dengan sertifikasi CE, ISO, dan izin edar Kemenkes RI yang terjamin, bukan barang tiruan.</p>
        </div>
      </div>
      <div class="feat-item r d1">
        <div class="feat-n">02</div>
        <div class="feat-body">
          <h4>Tenaga Teknis Bersertifikat</h4>
          <p>Tim biomedical engineer kami siap melakukan instalasi, kalibrasi, pelatihan penggunaan, dan perawatan berkala langsung di lokasi fasilitas Anda.</p>
        </div>
      </div>
      <div class="feat-item r d2">
        <div class="feat-n">03</div>
        <div class="feat-body">
          <h4>Harga Kompetitif & Transparan</h4>
          <p>Kami menawarkan harga yang kompetitif untuk semua segmen — dari pengadaan skala besar rumah sakit hingga pembelian satuan langsung oleh konsumen.</p>
        </div>
      </div>
      <div class="feat-item r d3">
        <div class="feat-n">04</div>
        <div class="feat-body">
          <h4>Garansi & Purna Jual Nyata</h4>
          <p>Setiap produk dilengkapi garansi resmi, suku cadang original, dan layanan perbaikan cepat agar operasional fasilitas Anda tidak pernah terganggu.</p>
        </div>
      </div>
      <div class="feat-item r">
        <div class="feat-n">05</div>
        <div class="feat-body">
          <h4>Konsultasi Kebutuhan Tanpa Biaya</h4>
          <p>Tim ahli kami siap membantu Anda memilih peralatan yang tepat sesuai kebutuhan klinis, anggaran yang tersedia, dan kapasitas fasilitas Anda.</p>
        </div>
      </div>
    </div>

    <div class="why-right r d2">
      <div class="why-card">
        <p class="why-quote">Sejak bermitra dengan PT Megah Catur Abadi, proses pengadaan alat kesehatan kami jauh lebih mudah dan cepat. Produk terpercaya, respons tim sangat sigap, dan purna jual yang benar-benar nyata.</p>
        <div class="why-sig">
          <strong>dr. Arief Santoso, Sp.An</strong>
          Direktur Medis — RS Mitra Sehat Surabaya
        </div>
      </div>
      <div class="info-pair" style="margin-top:1px;">
        <div class="info-cell">
          <div class="info-cell-lbl">Sertifikasi</div>
          <div class="info-cell-val">ISO 9001:2015</div>
        </div>
        <div class="info-cell" style="border-left:1px solid var(--border);">
          <div class="info-cell-lbl">Izin Resmi</div>
          <div class="info-cell-val">Kemenkes RI</div>
        </div>
        <div class="info-cell" style="border-top:1px solid var(--border);">
          <div class="info-cell-lbl">Standar</div>
          <div class="info-cell-val">CE & FDA</div>
        </div>
        <div class="info-cell" style="border-left:1px solid var(--border);border-top:1px solid var(--border);">
          <div class="info-cell-lbl">Jaminan</div>
          <div class="info-cell-val">Garansi Resmi</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ CLIENTS ═══ -->
<section id="klien" class="clients">
  <div class="sec-eyebrow r"><span>Klien Kami</span></div>
  <h2 class="sec-title r">Dipercaya oleh<br><em>Fasilitas Kesehatan</em> Terkemuka</h2>
  <div class="clients-grid">
    <div class="client-card r">
      <div class="client-ico">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline points="9,22 9,12 15,12 15,22"/>
        </svg>
      </div>
      <h4>RS Umum Daerah</h4>
      <span>Rumah Sakit Pemerintah</span>
    </div>
    <div class="client-card r d1">
      <div class="client-ico">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <rect x="2" y="3" width="20" height="14" rx="2"/>
          <path d="M8 21h8M12 17v4"/>
        </svg>
      </div>
      <h4>Klinik Spesialis</h4>
      <span>Klinik Swasta & Poliklinik</span>
    </div>
    <div class="client-card r d2">
      <div class="client-ico">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <h4>Puskesmas</h4>
      <span>Pusat Kesehatan Masyarakat</span>
    </div>
    <div class="client-card r d3">
      <div class="client-ico">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
      </div>
      <h4>RS Swasta & Internasional</h4>
      <span>Rumah Sakit Premium</span>
    </div>
    <div class="client-card r">
      <div class="client-ico">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
        </svg>
      </div>
      <h4>Laboratorium Klinik</h4>
      <span>Lab Diagnostik & Patologi</span>
    </div>
    <div class="client-card r d1">
      <div class="client-ico">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <rect x="3" y="3" width="18" height="18" rx="2"/>
          <line x1="12" y1="8" x2="12" y2="16"/>
          <line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
      </div>
      <h4>Apotek & Toko Medis</h4>
      <span>Retail Alat Kesehatan</span>
    </div>
    <div class="client-card r d2">
      <div class="client-ico">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
          <line x1="12" y1="12" x2="12" y2="16"/>
        </svg>
      </div>
      <h4>Institusi Pendidikan</h4>
      <span>Universitas & Akademi Medis</span>
    </div>
    <div class="client-card r d3">
      <div class="client-ico">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
      </div>
      <h4>Pelanggan Perorangan</h4>
      <span>Pembelian Langsung / Direct</span>
    </div>
  </div>
</section>

<!-- ═══ CTA ═══ -->
<section class="cta-section">
  <div class="cta-inner">
    <div class="sec-eyebrow r"><span>Mulai Kerjasama</span></div>
    <h2 class="sec-title r">Siap Meningkatkan Kualitas<br>Layanan <em>Kesehatan Anda?</em></h2>
    <p class="cta-desc r">Konsultasikan kebutuhan peralatan medis Anda dengan tim ahli kami. Kami siap memberikan solusi terbaik dengan harga yang kompetitif and dukungan penuh.</p>
    <div class="cta-btns r">
      <a href="#kontak" class="btn-gold">
        Konsultasi Gratis
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M2 6.5h9M8 3l3.5 3.5L8 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </a>
      <a href="https://wa.me/6281234567890" class="btn-outline" target="_blank">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="opacity:0.8;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
        WhatsApp
      </a>
    </div>
  </div>
</section>

<!-- ═══ CONTACT ═══ -->
<section id="kontak" class="contact">
  <div class="contact-grid">
    <!-- Info -->
    <div class="r">
      <div class="sec-eyebrow"><span>Kontak</span></div>
      <h2 class="sec-title" style="margin-bottom:2.5rem;">Hubungi<br><em>Kami</em></h2>
      <div class="ci-item">
        <div class="ci-ico">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
        </div>
        <div>
          <div class="ci-lbl">Alamat Kantor</div>
          <div class="ci-val">Jl. Raya Darmo No. 123<br>Surabaya, Jawa Timur 60241</div>
        </div>
      </div>
      <div class="ci-item">
        <div class="ci-ico">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 14a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 3h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 10.6a16 16 0 0 0 5.49 5.49l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21 18h.92z"/>
          </svg>
        </div>
        <div>
          <div class="ci-lbl">Telepon</div>
          <div class="ci-val">+62 31 555 1234<br>+62 812 3456 7890 (WhatsApp)</div>
        </div>
      </div>
      <div class="ci-item">
        <div class="ci-ico">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
            <polyline points="22,6 12,13 2,6"/>
          </svg>
        </div>
        <div>
          <div class="ci-lbl">Email</div>
          <div class="ci-val">info@megahcaturabadi.co.id<br>sales@megahcaturabadi.co.id</div>
        </div>
      </div>
      <div class="ci-item">
        <div class="ci-ico">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12,6 12,12 16,14"/>
          </svg>
        </div>
        <div>
          <div class="ci-lbl">Jam Operasional</div>
          <div class="ci-val">Senin – Jumat: 08.00 – 17.00 WIB<br>Sabtu: 08.00 – 13.00 WIB</div>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="r d2">
      <div class="form-wrap">
        <div class="form-head">Kirim Pesan</div>
        <div class="form-sub">Kami akan merespons dalam 1×24 jam kerja</div>
        <div class="form-fields">
          <div class="form-cols">
            <div class="fgrp">
              <label>Nama Lengkap</label>
              <input type="text" placeholder="Nama Anda" />
            </div>
            <div class="fgrp">
              <label>Instansi / Perusahaan</label>
              <input type="text" placeholder="RS / Klinik / Pribadi" />
            </div>
          </div>
          <div class="form-cols">
            <div class="fgrp">
              <label>No. WhatsApp</label>
              <input type="tel" placeholder="+62 812..." />
            </div>
            <div class="fgrp">
              <label>Alamat Email</label>
              <input type="email" placeholder="email@domain.com" />
            </div>
          </div>
          <div class="fgrp">
            <label>Kategori Kebutuhan</label>
            <select>
              <option value="">Pilih kategori produk</option>
              <option>Diagnostik & Pencitraan</option>
              <option>Monitoring Pasien</option>
              <option>ICU & Life Support</option>
              <option>Sterilisasi & Desinfeksi</option>
              <option>Peralatan Laboratorium</option>
              <option>Alat Medis Rumahan</option>
              <option>Konsultasi Umum</option>
            </select>
          </div>
          <div class="fgrp">
            <label>Pesan</label>
            <textarea placeholder="Ceritakan kebutuhan Anda secara detail — jenis alat, jumlah, anggaran, atau pertanyaan lainnya..."></textarea>
          </div>
          <button class="btn-gold" onclick="submitForm(this)" style="width:100%;justify-content:center;font-size:0.82rem;">
            Kirim Pesan
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M2 6.5h9M8 3l3.5 3.5L8 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ FOOTER ═══ -->
<footer>
  <div class="footer-row">
    <div>
      <div style="font-family:'Cormorant Garamond',serif;font-size:1.5rem;color:var(--gold);letter-spacing:0.16em;font-weight:600;line-height:1;">MCA</div>
      <div class="foot-copy">© 2026 <span>PT Megah Catur Abadi</span>. Semua hak dilindungi.</div>
    </div>
    <div class="foot-links">
      <a href="#tentang">Tentang</a>
      <a href="#produk">Produk</a>
      <a href="#keunggulan">Keunggulan</a>
      <a href="#klien">Klien</a>
      <a href="#kontak">Kontak</a>
    </div>
  </div>
</footer>

<script>
  // ── Navbar scroll effect ──
  const nav = document.getElementById('navbar');
  window.addEventListener('scroll', () => nav.classList.toggle('scrolled', window.scrollY > 60));

  // ── Scroll reveal ──
  const rEls = document.querySelectorAll('.r');
  const rObs = new IntersectionObserver(
    entries => entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('vis'); rObs.unobserve(e.target); } }),
    { threshold: 0.1, rootMargin: '0px 0px -36px 0px' }
  );
  rEls.forEach(el => rObs.observe(el));

  // ── Counter animation ──
  function runCounter(el, target, suffix) {
    const dur = 2000; const t0 = performance.now();
    const tick = now => {
      const p = Math.min((now - t0) / dur, 1);
      const ease = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.floor(ease * target);
      if (p < 1) requestAnimationFrame(tick); else el.textContent = target;
    };
    requestAnimationFrame(tick);
  }

  const ctrObs = new IntersectionObserver(
    entries => entries.forEach(e => {
      if (!e.isIntersecting) return;
      const t = parseInt(e.target.dataset.t || e.target.dataset.cnt);
      runCounter(e.target, t);
      ctrObs.unobserve(e.target);
    }),
    { threshold: 0.5 }
  );
  document.querySelectorAll('.ctr, [data-cnt]').forEach(el => ctrObs.observe(el));

  // ── Smooth anchors ──
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const href = a.getAttribute('href');
      if (href.startsWith('#')) {
        const tgt = document.querySelector(href);
        if (tgt) { e.preventDefault(); tgt.scrollIntoView({ behavior: 'smooth' }); }
      }
    });
  });

  // ── Mobile nav ──
  const burger = document.getElementById('burger');
  const navLinks = document.querySelector('.nav-links');
  
  burger.addEventListener('click', function() {
    const open = navLinks.style.display === 'flex';
    if (open) {
      navLinks.removeAttribute('style');
    } else {
      navLinks.style.cssText = 'display:flex;flex-direction:column;position:absolute;top:100%;left:0;right:0;background:rgba(6,8,16,0.97);padding:2rem 5%;gap:1.5rem;backdrop-filter:blur(18px);border-bottom:1px solid rgba(200,155,60,0.18);';
    }
  });

  // Close mobile nav when clicking any link
  navLinks.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      if (window.innerWidth <= 960) {
        navLinks.removeAttribute('style');
      }
    });
  });

  // ── Form submit ──
  function submitForm(btn) {
    const orig = btn.innerHTML;
    btn.innerHTML = 'Pesan Terkirim ✓';
    btn.style.background = '#3A7D6B';
    btn.disabled = true;
    setTimeout(() => { btn.innerHTML = orig; btn.style.background = ''; btn.disabled = false; }, 3500);
  }
</script>
</body>
</html>
