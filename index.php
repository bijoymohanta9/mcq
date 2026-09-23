<?php
    ob_start();
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath . '/lib/Session.php');
    Session::init();

    // ইউজার ইতোমধ্যে লগইন করা থাকলে তাকে exam.php-তে পাঠিয়ে দেওয়া হবে
    if (Session::get("login") == true) {
        header("Location: exam.php");
        exit();
    }
?>

<!doctype html>
<html lang="bn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Online Exam System – Job Prep & Assessment</title>
<meta name="description" content="বিসিএস, শিক্ষক নিবন্ধন, প্রাথমিক সহকারী শিক্ষক, ব্যাংক জবস ও সাব অ্যাসিস্ট্যান্ট ইঞ্জিনিয়ার পরীক্ষার মডেল টেস্ট।">

<!-- Tailwind CSS CDN (same as your existing pages) -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Google Font (same as your existing pages) -->
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
<script>
  tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Hind Siliguri"', 'system-ui', 'sans-serif'] } } } };
</script>

<style>
  :root{
    box-sizing:border-box;
    padding-top:env(safe-area-inset-top,0px);
    padding-bottom:env(safe-area-inset-bottom,0px);
    color-scheme:light;
  }
  html{scroll-behavior:smooth;scroll-padding-top:calc(env(safe-area-inset-top,0px) + 84px);}
  body{font-family:'Hind Siliguri',system-ui,sans-serif;background:#f1f5f9;color:#1e293b;overflow-x:hidden;}
  *:focus-visible{outline:2px solid #4f46e5;outline-offset:2px;border-radius:6px;}

  .ic{fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;flex:none;}
  .dots{background-image:radial-gradient(#fff 1px,transparent 1px);background-size:16px 16px;}

  /* ---------- page loader (like Udvash's loading mask) ---------- */
  #loader{position:fixed;inset:0;z-index:200;background:#fff;display:flex;align-items:center;justify-content:center;animation:loaderOut .5s ease .9s forwards;}
  #loader .ring{width:46px;height:46px;border-radius:9999px;border:4px solid #e0e7ff;border-top-color:#4f46e5;animation:spin .8s linear infinite;}
  @keyframes spin{to{transform:rotate(360deg)}}
  @keyframes loaderOut{to{opacity:0;visibility:hidden;pointer-events:none}}

  /* ---------- header ---------- */
  #progress{position:absolute;left:0;bottom:-1px;height:3px;width:100%;background:linear-gradient(90deg,#4f46e5,#f59e0b);transform:scaleX(0);transform-origin:left;}
  #header.scrolled{box-shadow:0 8px 24px -12px rgba(15,23,42,.25);}
  .navlink{position:relative;}
  .navlink::after{content:'';position:absolute;left:12px;right:12px;bottom:4px;height:2px;border-radius:2px;background:#4f46e5;transform:scaleX(0);transition:transform .3s;}
  .navlink:hover::after{transform:scaleX(1);}
  .pulse-link{animation:navpulse 1.8s ease-in-out infinite;}
  @keyframes navpulse{0%,100%{color:#4338ca;text-shadow:0 0 0 rgba(79,70,229,0)}50%{color:#d97706;text-shadow:0 0 10px rgba(245,158,11,.55)}}
  .join-btn{animation:ring 2.2s infinite;}
  @keyframes ring{0%{box-shadow:0 0 0 0 rgba(79,70,229,.5)}70%{box-shadow:0 0 0 12px rgba(79,70,229,0)}100%{box-shadow:0 0 0 0 rgba(79,70,229,0)}}
  .shine{position:relative;overflow:hidden;}
  .shine::after{content:'';position:absolute;top:0;left:-70%;width:45%;height:100%;background:linear-gradient(100deg,transparent,rgba(255,255,255,.55),transparent);transform:skewX(-20deg);transition:left .7s;}
  .shine:hover::after{left:130%;}

  /* hamburger (Udvash style: three lines -> X) */
  .burger span{display:block;height:2px;width:22px;background:#334155;border-radius:2px;transition:transform .35s,opacity .25s;}
  .burger.open span:nth-child(1){transform:translateY(7px) rotate(45deg);}
  .burger.open span:nth-child(2){opacity:0;}
  .burger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg);}
  #mnav{max-height:0;overflow:hidden;transition:max-height .45s ease;}
  #mnav.open{max-height:520px;}

  /* ---------- hero carousel ---------- */
  .slides{display:grid;}
  .slide{grid-area:1/1;opacity:0;visibility:hidden;transition:opacity .9s ease,visibility .9s;}
  .slide.active{opacity:1;visibility:visible;z-index:1;}
  .slide .a{opacity:0;transform:translateY(26px);}
  .slide.active .a{animation:up .8s cubic-bezier(.2,.7,.2,1) forwards;animation-delay:calc(var(--i,0)*130ms + 250ms);}
  .slide .card-in{opacity:0;transform:translateX(70px) scale(.96);}
  .slide.active .card-in{animation:up .9s cubic-bezier(.2,.7,.2,1) .35s forwards;}
  @keyframes up{to{opacity:1;transform:none}}
  .floaty{animation:float 5s ease-in-out infinite;}
  @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}
  .blob{position:absolute;border-radius:9999px;filter:blur(2px);animation:float 7s ease-in-out infinite;}
  .pbar{width:0;}
  .slide.active .pbar{animation:fillw 3s ease .8s forwards;}
  @keyframes fillw{to{width:var(--w)}}
  .opt{transition:background .4s,border-color .4s,color .4s;}
  .opt .tick{opacity:0;transform:scale(.4);transition:.4s;}
  .slide.solved .opt.ok{background:#ecfdf5;border-color:#10b981;color:#047857;}
  .slide.solved .opt.ok .tick{opacity:1;transform:scale(1);}
  .dot{width:10px;height:10px;border-radius:9999px;background:rgba(255,255,255,.4);position:relative;overflow:hidden;transition:width .4s;border:0;padding:0;cursor:pointer;}
  .dot.active{width:46px;}
  .dot i{position:absolute;inset:0;background:#fff;transform:scaleX(0);transform-origin:left;}
  .dot.active i{animation:dotfill 6s linear forwards;}
  #hero:hover .dot.active i{animation-play-state:paused;}
  @keyframes dotfill{to{transform:scaleX(1)}}

  /* ---------- ticker ---------- */
  .marquee{display:flex;width:max-content;animation:marq 32s linear infinite;}
  .marquee:hover{animation-play-state:paused;}
  @keyframes marq{to{transform:translateX(-50%)}}

  /* ---------- scroll reveal ---------- */
  .reveal{opacity:0;transform:translateY(28px);transition:opacity .7s ease,transform .7s cubic-bezier(.2,.7,.2,1);transition-delay:var(--d,0ms);}
  .reveal-l{transform:translateX(-46px);}
  .reveal-r{transform:translateX(46px);}
  .reveal.in{opacity:1;transform:none;}
  .uline{width:64px;height:4px;border-radius:9999px;background:linear-gradient(90deg,#4f46e5,#f59e0b);margin:10px auto 0;transform:scaleX(0);transition:transform .8s ease .2s;}
  .in .uline,.uline.in{transform:scaleX(1);}
  .pop{opacity:0;transform:translateY(22px);animation:up .6s ease forwards;animation-delay:var(--d,0ms);}

  /* ---------- course cards ---------- */
  .card-icon{transition:transform .5s;}
  .course-card:hover .card-icon{transform:rotate(-8deg) scale(1.12);}
  .tile:hover .tile-ic{transform:translateY(-4px) rotate(-6deg);}
  .tile-ic{transition:transform .35s;}

  /* ---------- bars & rings in feature blocks ---------- */
  .bar{width:0;transition:width 1.4s cubic-bezier(.2,.7,.2,1) .3s;}
  .in .bar{width:var(--w);}
  .ringp{stroke-dasharray:283;stroke-dashoffset:283;transition:stroke-dashoffset 1.8s ease .4s;}
  .in .ringp{stroke-dashoffset:calc(283 - 283 * var(--p));}
  .skel{background:linear-gradient(90deg,#e2e8f0 25%,#f1f5f9 50%,#e2e8f0 75%);background-size:200% 100%;animation:sk 1.6s linear infinite;}
  @keyframes sk{to{background-position:-200% 0}}

  /* ---------- steps ---------- */
  .stepline{transform:scaleX(0);transform-origin:left;transition:transform 1.4s ease .3s;}
  .in .stepline{transform:scaleX(1);}

  /* ---------- modal ---------- */
  #loginModal.show{display:flex;}
  .modal-card{animation:pop .35s cubic-bezier(.2,.8,.2,1);}
  @keyframes pop{from{opacity:0;transform:translateY(20px) scale(.96)}to{opacity:1;transform:none}}

  /* ---------- toast & back to top ---------- */
  #toast{position:fixed;left:50%;bottom:calc(env(safe-area-inset-bottom,0px) + 24px);transform:translate(-50%,30px);opacity:0;transition:.35s;z-index:150;pointer-events:none;}
  #toast.show{transform:translate(-50%,0);opacity:1;}
  #toTop{position:fixed;right:18px;bottom:calc(env(safe-area-inset-bottom,0px) + 18px);z-index:90;transform:translateY(90px);transition:transform .35s;}
  #toTop.show{transform:none;}

  @media (prefers-reduced-motion:reduce){
    *,*::before,*::after{animation-duration:.01ms !important;animation-iteration-count:1 !important;transition-duration:.01ms !important;}
    .reveal,.slide .a,.slide .card-in,.pop{opacity:1;transform:none;}
    .dot.active i{animation:none !important;transform:scaleX(1);}
  }
</style>
</head>

<body>

<!-- ============ SVG icon set (inline, no external icon font needed) ============ -->
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <symbol id="i-cap" viewBox="0 0 24 24"><path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/><path d="M22 10v6"/></symbol>
  <symbol id="i-brief" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></symbol>
  <symbol id="i-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/></symbol>
  <symbol id="i-file" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></symbol>
  <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
  <symbol id="i-gem" viewBox="0 0 24 24"><path d="M6 3h12l4 6-10 13L2 9z"/><path d="M11 3 8 9l4 13 4-13-3-6"/><path d="M2 9h20"/></symbol>
  <symbol id="i-trophy" viewBox="0 0 24 24"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></symbol>
  <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></symbol>
  <symbol id="i-check" viewBox="0 0 24 24"><path d="m5 12 5 5L20 7"/></symbol>
  <symbol id="i-checkc" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
  <symbol id="i-book" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></symbol>
  <symbol id="i-bank" viewBox="0 0 24 24"><path d="M3 21h18"/><path d="M3 10h18"/><path d="m5 6 7-3 7 3"/><path d="M4 10v11"/><path d="M20 10v11"/><path d="M8 14v3"/><path d="M12 14v3"/><path d="M16 14v3"/></symbol>
  <symbol id="i-wallet" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01"/><path d="M18 12h.01"/></symbol>
  <symbol id="i-wrench" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94z"/></symbol>
  <symbol id="i-cart" viewBox="0 0 24 24"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2 2h2l2.7 12.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6L22 7H5"/></symbol>
  <symbol id="i-zap" viewBox="0 0 24 24"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></symbol>
  <symbol id="i-chart" viewBox="0 0 24 24"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></symbol>
  <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></symbol>
  <symbol id="i-laptop" viewBox="0 0 24 24"><path d="M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.3 2.5a1 1 0 0 1-.9 1.5H3.6a1 1 0 0 1-.9-1.5L4 16"/></symbol>
  <symbol id="i-star" viewBox="0 0 24 24"><path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/></symbol>
  <symbol id="i-down" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></symbol>
  <symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></symbol>
  <symbol id="i-mail" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 7L2 7"/></symbol>
  <symbol id="i-lock" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></symbol>
  <symbol id="i-globe" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></symbol>
  <symbol id="i-up" viewBox="0 0 24 24"><path d="m18 15-6-6-6 6"/></symbol>
  <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></symbol>
</svg>

<div id="loader"><div class="ring"></div></div>

<!-- ============================================================
     HEADER  (your header style + Udvash-style "Join Now" pill)
     ============================================================ -->
<header id="header" class="bg-white/95 backdrop-blur border-b border-slate-200 sticky z-50 transition-shadow" style="top:env(safe-area-inset-top,0px)">
  <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-3">

    <a href="index.php" class="flex items-center gap-3 min-w-0">
      <div class="bg-indigo-600 text-white p-2.5 rounded-xl shadow-md flex-none">
        <svg class="ic w-5 h-5"><use href="#i-cap"/></svg>
      </div>
      <div class="min-w-0">
        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight leading-none truncate">Online Exam System</h1>
        <span class="hidden sm:block text-xs text-slate-500">Job Prep &amp; Assessment</span>
      </div>
    </a>

    <!-- Desktop menu -->
    <nav class="hidden lg:flex items-center gap-1">
      <a href="#courses" class="navlink px-3 py-2 text-sm font-semibold text-slate-700 hover:text-indigo-600 transition">পরীক্ষাসমূহ</a>
      <a href="circulars.php" class="navlink pulse-link px-3 py-2 text-sm font-bold flex items-center gap-1.5">
        <svg class="ic w-4 h-4"><use href="#i-brief"/></svg> সরকারি সার্কুলার
      </a>
      <a href="#plans" class="navlink px-3 py-2 text-sm font-semibold text-slate-700 hover:text-indigo-600 transition">সাবস্ক্রিপশন প্ল্যান</a>
      <a href="#services" class="navlink px-3 py-2 text-sm font-semibold text-slate-700 hover:text-indigo-600 transition">সেবাসমূহ</a>
      <a href="leaderboard.php" class="navlink px-3 py-2 text-sm font-semibold text-slate-700 hover:text-indigo-600 transition flex items-center gap-1.5">
        <svg class="ic w-4 h-4 text-amber-500"><use href="#i-trophy"/></svg> Leaderboard
      </a>
    </nav>

    <!-- Login + Join Now -->
    <div class="flex items-center gap-2">
      <button type="button" data-open-login class="hidden sm:flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-full transition">
        <svg class="ic w-4 h-4"><use href="#i-user"/></svg> Login
      </button>
      <a href="register.php" class="join-btn shine inline-flex items-center gap-1.5 rounded-full px-4 sm:px-5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-indigo-800 hover:from-indigo-700 hover:to-indigo-900 transition">
        Join Now <svg class="ic w-4 h-4"><use href="#i-arrow"/></svg>
      </a>
      <button type="button" id="burger" class="burger lg:hidden flex flex-col gap-[5px] p-2 ml-1" aria-label="মেনু খুলুন" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>

  <!-- Mobile menu -->
  <div id="mnav" class="lg:hidden bg-white border-t border-slate-100">
    <div class="px-4 py-3 flex flex-col">
      <a href="#courses" class="py-2.5 text-sm font-semibold text-slate-700 border-b border-slate-100">পরীক্ষাসমূহ</a>
      <a href="circulars.php" class="py-2.5 text-sm font-bold text-indigo-600 border-b border-slate-100 flex items-center gap-2"><svg class="ic w-4 h-4"><use href="#i-brief"/></svg> সরকারি সার্কুলার</a>
      <a href="#plans" class="py-2.5 text-sm font-semibold text-slate-700 border-b border-slate-100">সাবস্ক্রিপশন প্ল্যান</a>
      <a href="#services" class="py-2.5 text-sm font-semibold text-slate-700 border-b border-slate-100">সেবাসমূহ</a>
      <a href="leaderboard.php" class="py-2.5 text-sm font-semibold text-slate-700 border-b border-slate-100 flex items-center gap-2"><svg class="ic w-4 h-4 text-amber-500"><use href="#i-trophy"/></svg> Leaderboard</a>
      <div class="flex gap-2 pt-3">
        <button type="button" data-open-login class="flex-1 py-2.5 text-sm font-bold text-indigo-600 bg-indigo-50 rounded-full">Login</button>
        <a href="register.php" class="flex-1 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-full text-center">Signup Free</a>
      </div>
    </div>
  </div>
  <div id="progress"></div>
</header>

<main>

<!-- ============================================================
     HERO CAROUSEL  (Udvash-style sliding banner)
     ============================================================ -->
<section id="hero" class="relative overflow-hidden">
  <div id="slides" class="slides"></div>

  <button type="button" id="prev" class="hidden md:flex absolute left-4 top-1/2 -translate-y-1/2 z-10 w-11 h-11 rounded-full bg-white/15 hover:bg-white/30 text-white items-center justify-center backdrop-blur transition" aria-label="আগের স্লাইড">
    <svg class="ic w-5 h-5 rotate-180"><use href="#i-arrow"/></svg>
  </button>
  <button type="button" id="next" class="hidden md:flex absolute right-4 top-1/2 -translate-y-1/2 z-10 w-11 h-11 rounded-full bg-white/15 hover:bg-white/30 text-white items-center justify-center backdrop-blur transition" aria-label="পরের স্লাইড">
    <svg class="ic w-5 h-5"><use href="#i-arrow"/></svg>
  </button>
  <div id="dots" class="absolute bottom-5 left-1/2 -translate-x-1/2 z-10 flex items-center gap-2"></div>
</section>

<!-- Ticker -->
<div class="bg-indigo-950 text-indigo-100 overflow-hidden py-2.5 text-sm">
  <div class="marquee" id="marquee"></div>
</div>

<!-- ============================================================
     COURSES  (Udvash "programs" section: filters + cards)
     ============================================================ -->
<section id="courses" class="py-14 sm:py-20">
  <div class="max-w-7xl mx-auto px-4">
    <div class="text-center mb-8 reveal">
      <h2 class="text-2xl sm:text-4xl font-bold text-slate-900 tracking-tight">সময়োপযোগী পরীক্ষা ও কোর্সসমূহ</h2>
      <div class="uline"></div>
      <p class="text-slate-600 text-sm mt-4 max-w-xl mx-auto">আপনার কাঙ্ক্ষিত পরীক্ষার কোর্সটি বেছে নিন। মেয়াদ বদলালে নিচের দামও সাথে সাথে বদলে যাবে।</p>
    </div>

    <!-- Filters -->
    <div class="grid md:grid-cols-3 gap-3 mb-8 reveal" style="--d:100ms">
      <select id="fGroup" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="all">সব ধরনের পরীক্ষা</option>
        <option value="govt">সরকারি চাকরি</option>
        <option value="teacher">শিক্ষক নিয়োগ</option>
        <option value="bank">ব্যাংক</option>
        <option value="eng">ইঞ্জিনিয়ারিং</option>
      </select>
      <select id="fDur" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="3_months">ট্রায়াল প্যাক – ৩ মাস</option>
        <option value="6_months" selected>স্ট্যান্ডার্ড – ৬ মাস</option>
        <option value="1_year">প্রিমিয়াম – ১ বছর</option>
      </select>
      <div class="flex items-center rounded-xl border border-slate-300 bg-white shadow-sm focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500">
        <span class="px-3.5 text-slate-400"><svg class="ic w-4 h-4"><use href="#i-search"/></svg></span>
        <input id="fSearch" type="text" placeholder="পরীক্ষার নাম খুঁজুন" class="w-full py-3 pr-4 bg-transparent text-sm focus:outline-none">
      </div>
    </div>

    <div id="grid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6"></div>

    <div id="notFound" class="hidden text-center py-12">
      <p class="text-lg font-bold text-rose-600">কোনো পরীক্ষা পাওয়া যায়নি</p>
      <p class="text-sm text-slate-500 mt-1">ফিল্টার বদলে বা অন্য নাম লিখে আবার চেষ্টা করুন।</p>
    </div>
  </div>
</section>

<!-- ============================================================
     COUNTERS
     ============================================================ -->
<section class="bg-gradient-to-br from-indigo-600 to-indigo-900 text-white relative overflow-hidden">
  <div class="absolute inset-0 dots opacity-10"></div>
  <div class="relative max-w-7xl mx-auto px-4 py-12 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
    <div class="reveal"><div class="text-4xl sm:text-5xl font-black" data-count="5">০</div><p class="text-indigo-200 text-sm mt-1">পরীক্ষার ক্যাটাগরি</p></div>
    <div class="reveal" style="--d:100ms"><div class="text-4xl sm:text-5xl font-black" data-count="3">০</div><p class="text-indigo-200 text-sm mt-1">সাবস্ক্রিপশন প্ল্যান</p></div>
    <div class="reveal" style="--d:200ms"><div class="text-4xl sm:text-5xl font-black" data-count="125" data-pre="৳ ">০</div><p class="text-indigo-200 text-sm mt-1">প্রতি মাসে খরচ শুরু</p></div>
    <div class="reveal" style="--d:300ms"><div class="text-4xl sm:text-5xl font-black" data-count="12" data-suf=" মাস">০</div><p class="text-indigo-200 text-sm mt-1">সর্বোচ্চ মেয়াদ</p></div>
  </div>
</section>

<!-- ============================================================
     SERVICES  (Udvash "সেবা পরিক্রমা" icon grid)
     ============================================================ -->
<section id="services" class="py-14 sm:py-20">
  <div class="max-w-7xl mx-auto px-4">
    <div class="text-center mb-10 reveal">
      <h2 class="text-2xl sm:text-4xl font-bold text-slate-900 tracking-tight">এক প্ল্যাটফর্মে সব সুবিধা</h2>
      <div class="uline"></div>
    </div>
    <div id="tiles" class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6"></div>
  </div>
</section>

<!-- ============================================================
     FEATURE BLOCK 1  (Udvash "সকল শাখায় সমান সেবা" style)
     ============================================================ -->
<section class="pb-14 sm:pb-20">
  <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-2 gap-10 items-center">
    <div class="reveal reveal-l order-2 md:order-1">
      <h3 class="text-sm font-semibold text-indigo-600 mb-1">যেকোনো জায়গা থেকে</h3>
      <h2 class="text-2xl sm:text-4xl font-bold text-slate-900 tracking-tight leading-tight mb-4">নিজের সময়ে, নিজের ডিভাইসে মডেল টেস্ট দিন</h2>
      <p class="text-slate-600 leading-relaxed">মোবাইল, ট্যাবলেট বা কম্পিউটার — ব্রাউজার খুলে লগইন করলেই পরীক্ষা দেওয়া যায়। পেমেন্ট সম্পন্ন হওয়ার সাথে সাথেই সকল মডেল টেস্ট একটিভ হয়ে যায়, অপেক্ষা করতে হয় না।</p>
      <ul class="mt-5 space-y-2.5 text-sm text-slate-700">
        <li class="flex items-center gap-2"><svg class="ic w-5 h-5 text-emerald-600"><use href="#i-checkc"/></svg> পেমেন্টের পরেই সব পরীক্ষা চালু</li>
        <li class="flex items-center gap-2"><svg class="ic w-5 h-5 text-emerald-600"><use href="#i-checkc"/></svg> বিষয়ভিত্তিক ও পূর্ণাঙ্গ দুই ধরনের পরীক্ষা</li>
        <li class="flex items-center gap-2"><svg class="ic w-5 h-5 text-emerald-600"><use href="#i-checkc"/></svg> ফলাফল দেখা যায় Exam Records-এ</li>
      </ul>
      <a href="register.php" class="shine mt-7 inline-flex items-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3 shadow-lg transition">ফ্রি সাইনআপ করুন <svg class="ic w-4 h-4"><use href="#i-arrow"/></svg></a>
    </div>

    <div class="reveal reveal-r order-1 md:order-2">
      <div class="relative">
        <div class="absolute -inset-4 bg-gradient-to-br from-indigo-100 to-amber-100 rounded-3xl -rotate-2"></div>
        <div class="relative bg-white rounded-2xl border border-slate-200 shadow-xl p-6">
          <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-rose-400"></span><span class="w-3 h-3 rounded-full bg-amber-400"></span><span class="w-3 h-3 rounded-full bg-emerald-400"></span></div>
            <span class="text-xs text-slate-400">ফলাফল (নমুনা)</span>
          </div>
          <div class="flex items-center gap-6">
            <div class="relative w-32 h-32 flex-none" style="--p:.82">
              <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                <circle cx="50" cy="50" r="45" fill="none" stroke="#e0e7ff" stroke-width="9"/>
                <circle class="ringp" cx="50" cy="50" r="45" fill="none" stroke="#4f46e5" stroke-width="9" stroke-linecap="round"/>
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center"><span class="text-2xl font-black text-slate-900">৮২%</span><span class="text-[11px] text-slate-500">সঠিক উত্তর</span></div>
            </div>
            <div class="flex-1 space-y-3">
              <div><div class="flex justify-between text-xs text-slate-500 mb-1"><span>বাংলা</span><span>৯০%</span></div><div class="h-2 rounded-full bg-slate-100"><div class="bar h-2 rounded-full bg-indigo-500" style="--w:90%"></div></div></div>
              <div><div class="flex justify-between text-xs text-slate-500 mb-1"><span>গণিত</span><span>৭৫%</span></div><div class="h-2 rounded-full bg-slate-100"><div class="bar h-2 rounded-full bg-emerald-500" style="--w:75%"></div></div></div>
              <div><div class="flex justify-between text-xs text-slate-500 mb-1"><span>সাধারণ জ্ঞান</span><span>৮০%</span></div><div class="h-2 rounded-full bg-slate-100"><div class="bar h-2 rounded-full bg-amber-500" style="--w:80%"></div></div></div>
            </div>
          </div>
          <div class="mt-5 space-y-2"><div class="skel h-3 rounded w-11/12"></div><div class="skel h-3 rounded w-8/12"></div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     FEATURE BLOCK 2  (Udvash "থেমে থাকবে না প্রস্তুতি" style)
     ============================================================ -->
<section class="pb-14 sm:pb-20">
  <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-2 gap-10 items-center">
    <div class="reveal reveal-l">
      <div class="bg-white rounded-2xl border border-slate-200 shadow-xl p-6">
        <div class="flex items-center justify-between mb-4">
          <h4 class="font-bold text-slate-900 flex items-center gap-2"><svg class="ic w-5 h-5 text-amber-500"><use href="#i-trophy"/></svg> Leaderboard</h4>
          <span class="text-xs bg-slate-100 text-slate-500 rounded-full px-3 py-1">নমুনা তালিকা</span>
        </div>
        <ul class="space-y-3">
          <li class="flex items-center gap-3"><span class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 font-bold text-sm flex items-center justify-center">১</span><span class="w-28 text-sm font-medium text-slate-700">পরীক্ষার্থী ক</span><div class="flex-1 h-2.5 rounded-full bg-slate-100"><div class="bar h-2.5 rounded-full bg-amber-500" style="--w:96%"></div></div><span class="text-xs text-slate-500 w-9 text-right">৯৬</span></li>
          <li class="flex items-center gap-3"><span class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold text-sm flex items-center justify-center">২</span><span class="w-28 text-sm font-medium text-slate-700">পরীক্ষার্থী খ</span><div class="flex-1 h-2.5 rounded-full bg-slate-100"><div class="bar h-2.5 rounded-full bg-indigo-500" style="--w:91%"></div></div><span class="text-xs text-slate-500 w-9 text-right">৯১</span></li>
          <li class="flex items-center gap-3"><span class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold text-sm flex items-center justify-center">৩</span><span class="w-28 text-sm font-medium text-slate-700">পরীক্ষার্থী গ</span><div class="flex-1 h-2.5 rounded-full bg-slate-100"><div class="bar h-2.5 rounded-full bg-indigo-500" style="--w:87%"></div></div><span class="text-xs text-slate-500 w-9 text-right">৮৭</span></li>
          <li class="flex items-center gap-3"><span class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold text-sm flex items-center justify-center">৪</span><span class="w-28 text-sm font-medium text-slate-700">পরীক্ষার্থী ঘ</span><div class="flex-1 h-2.5 rounded-full bg-slate-100"><div class="bar h-2.5 rounded-full bg-indigo-400" style="--w:80%"></div></div><span class="text-xs text-slate-500 w-9 text-right">৮০</span></li>
        </ul>
      </div>
    </div>
    <div class="reveal reveal-r">
      <h3 class="text-sm font-semibold text-indigo-600 mb-1">নিজেকে যাচাই করুন</h3>
      <h2 class="text-2xl sm:text-4xl font-bold text-slate-900 tracking-tight leading-tight mb-4">লিডারবোর্ডে নিজের অবস্থান জানুন</h2>
      <p class="text-slate-600 leading-relaxed">পরীক্ষা দিন, ফলাফল দেখুন, তারপর লিডারবোর্ডে অন্য পরীক্ষার্থীদের সাথে নিজের অবস্থান মিলিয়ে নিন। প্রতিটি পরীক্ষার রেকর্ড Exam Records-এ জমা থাকে, তাই আগের ফলের সাথে তুলনা করাও সহজ।</p>
      <div class="mt-7 flex flex-wrap gap-3">
        <a href="leaderboard.php" class="shine inline-flex items-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold px-6 py-3 shadow-lg transition"><svg class="ic w-4 h-4"><use href="#i-trophy"/></svg> Leaderboard দেখুন</a>
        <a href="exam_history.php" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold px-6 py-3 transition"><svg class="ic w-4 h-4"><use href="#i-clock"/></svg> Exam Records</a>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     PLANS  (your 3 duration options, same prices)
     ============================================================ -->
<section id="plans" class="py-14 sm:py-20 bg-white border-y border-slate-200">
  <div class="max-w-5xl mx-auto px-4">
    <div class="text-center mb-10 reveal">
      <h2 class="text-2xl sm:text-4xl font-bold text-slate-900 tracking-tight">সাবস্ক্রিপশন প্ল্যান</h2>
      <div class="uline"></div>
      <p class="text-slate-600 text-sm mt-4 max-w-xl mx-auto">মেয়াদ যত বেশি, মাসিক খরচ তত কম। সব দামে ট্যাক্স যুক্ত আছে।</p>
    </div>
    <div class="grid md:grid-cols-3 gap-6">
      <div class="reveal" style="--d:0ms">
        <div class="h-full p-6 bg-white border border-slate-200 rounded-2xl text-center hover:-translate-y-2 hover:shadow-xl transition-all duration-300 flex flex-col">
          <span class="text-xs font-semibold text-slate-500">ট্রায়াল প্যাক</span>
          <h5 class="text-2xl font-bold text-slate-800 mt-1">৩ মাস</h5>
          <p class="text-4xl font-black text-indigo-600 my-3">৳ ৫০০</p>
          <span class="text-xs text-slate-400">প্রতি মাসে ৳ ১৬৬</span>
          <a href="subscription.php" class="mt-6 rounded-xl border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white font-bold py-2.5 transition">প্ল্যান নিন</a>
        </div>
      </div>
      <div class="reveal" style="--d:120ms">
        <div class="relative h-full p-6 bg-indigo-50/40 border-2 border-indigo-600 rounded-2xl text-center shadow-xl md:-translate-y-3 hover:-translate-y-5 transition-all duration-300 flex flex-col overflow-hidden">
          <span class="absolute top-0 right-0 bg-indigo-600 text-white text-[11px] font-bold px-3 py-1 rounded-bl-xl">পপুলার</span>
          <span class="text-xs font-semibold text-slate-500">স্ট্যান্ডার্ড</span>
          <h5 class="text-2xl font-bold text-slate-800 mt-1">৬ মাস</h5>
          <p class="text-4xl font-black text-indigo-600 my-3">৳ ৯০০</p>
          <span class="text-xs text-slate-400">প্রতি মাসে ৳ ১৫০</span>
          <a href="subscription.php" class="shine mt-6 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 shadow-lg transition">প্ল্যান নিন</a>
        </div>
      </div>
      <div class="reveal" style="--d:240ms">
        <div class="relative h-full p-6 bg-white border border-slate-200 rounded-2xl text-center hover:-translate-y-2 hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden">
          <span class="absolute top-0 right-0 bg-amber-500 text-white text-[11px] font-bold px-3 py-1 rounded-bl-xl">সেরা ছাড়</span>
          <span class="text-xs font-semibold text-slate-500">প্রিমিয়াম প্যাক</span>
          <h5 class="text-2xl font-bold text-slate-800 mt-1">১ বছর (১২ মাস)</h5>
          <p class="text-4xl font-black text-indigo-600 my-3">৳ ১,৫০০</p>
          <span class="text-xs text-slate-400">প্রতি মাসে ৳ ১২৫</span>
          <a href="subscription.php" class="mt-6 rounded-xl border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white font-bold py-2.5 transition">প্ল্যান নিন</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     STEPS  (a real sequence, so numbering is used here)
     ============================================================ -->
<section class="py-14 sm:py-20">
  <div class="max-w-6xl mx-auto px-4">
    <div class="text-center mb-12 reveal">
      <h2 class="text-2xl sm:text-4xl font-bold text-slate-900 tracking-tight">চার ধাপে শুরু করুন</h2>
      <div class="uline"></div>
    </div>
    <div class="relative reveal">
      <div class="hidden md:block absolute top-8 left-[12.5%] right-[12.5%] h-1 bg-slate-200 rounded-full overflow-hidden"><div class="stepline h-full bg-gradient-to-r from-indigo-600 to-amber-500"></div></div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-8 relative">
        <div class="text-center"><div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg"><svg class="ic w-7 h-7"><use href="#i-user"/></svg></div><p class="mt-4 text-xs font-bold text-indigo-600">ধাপ ১</p><h4 class="font-bold text-slate-800">ফ্রি সাইনআপ</h4><p class="text-xs text-slate-500 mt-1">নাম ও ইমেইল দিয়ে একাউন্ট খুলুন</p></div>
        <div class="text-center"><div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg"><svg class="ic w-7 h-7"><use href="#i-book"/></svg></div><p class="mt-4 text-xs font-bold text-indigo-600">ধাপ ২</p><h4 class="font-bold text-slate-800">পরীক্ষা ও মেয়াদ বাছুন</h4><p class="text-xs text-slate-500 mt-1">৩ মাস, ৬ মাস বা ১ বছর</p></div>
        <div class="text-center"><div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg"><svg class="ic w-7 h-7"><use href="#i-cart"/></svg></div><p class="mt-4 text-xs font-bold text-indigo-600">ধাপ ৩</p><h4 class="font-bold text-slate-800">পেমেন্ট করুন</h4><p class="text-xs text-slate-500 mt-1">পেমেন্টের সাথে সাথে একটিভ</p></div>
        <div class="text-center"><div class="w-16 h-16 mx-auto rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-lg"><svg class="ic w-7 h-7"><use href="#i-file"/></svg></div><p class="mt-4 text-xs font-bold text-amber-600">ধাপ ৪</p><h4 class="font-bold text-slate-800">পরীক্ষা দিন</h4><p class="text-xs text-slate-500 mt-1">ফলাফল ও লিডারবোর্ড দেখুন</p></div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     FINAL CTA
     ============================================================ -->
<section class="px-4 pb-16">
  <div class="reveal max-w-5xl mx-auto relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 to-indigo-900 text-white px-6 py-12 sm:px-12 text-center shadow-2xl">
    <div class="absolute inset-0 dots opacity-10"></div>
    <div class="blob w-40 h-40 bg-white/10 -top-10 -left-10"></div>
    <div class="blob w-56 h-56 bg-amber-400/20 -bottom-20 -right-16" style="animation-delay:-3s"></div>
    <div class="relative">
      <h2 class="text-2xl sm:text-4xl font-bold tracking-tight">আজই আপনার প্রস্তুতি শুরু করুন</h2>
      <p class="text-indigo-200 mt-3 max-w-xl mx-auto text-sm sm:text-base">একাউন্ট খুলতে কোনো খরচ নেই। পরীক্ষা ও মেয়াদ বেছে নিয়ে যখন ইচ্ছা সাবস্ক্রাইব করুন।</p>
      <div class="mt-8 flex flex-wrap justify-center gap-3">
        <a href="register.php" class="shine inline-flex items-center gap-2 rounded-full bg-white text-indigo-700 font-bold px-7 py-3 shadow-lg hover:scale-105 transition">Signup Free <svg class="ic w-4 h-4"><use href="#i-arrow"/></svg></a>
        <button type="button" data-open-login class="inline-flex items-center gap-2 rounded-full border border-white/40 hover:bg-white/10 font-bold px-7 py-3 transition">Login</button>
      </div>
    </div>
  </div>
</section>

</main>

<!-- ============================================================
     FOOTER  (Udvash-style columns)
     ============================================================ -->
<footer class="bg-slate-900 text-slate-300">
  <div class="max-w-7xl mx-auto px-4 py-12 grid md:grid-cols-4 gap-10">
    <div class="md:col-span-2">
      <div class="flex items-center gap-3">
        <div class="bg-indigo-600 text-white p-2.5 rounded-xl"><svg class="ic w-5 h-5"><use href="#i-cap"/></svg></div>
        <div><p class="font-bold text-white leading-none">Online Exam System</p><span class="text-xs text-slate-400">Job Prep &amp; Assessment</span></div>
      </div>
      <p class="text-sm text-slate-400 mt-4 max-w-sm leading-relaxed">বিসিএস, শিক্ষক নিবন্ধন, প্রাথমিক সহকারী শিক্ষক, ব্যাংক জবস ও সাব অ্যাসিস্ট্যান্ট ইঞ্জিনিয়ার — সব পরীক্ষার মডেল টেস্ট এক জায়গায়।</p>
    </div>
    <div>
      <h4 class="text-white font-bold mb-3">Explore</h4>
      <ul class="space-y-2 text-sm">
        <li><a href="exam.php" class="hover:text-white transition">Take Exam</a></li>
        <li><a href="circulars.php" class="hover:text-white transition">সরকারি সার্কুলার</a></li>
        <li><a href="leaderboard.php" class="hover:text-white transition">Leaderboard</a></li>
        <li><a href="subscription.php" class="hover:text-white transition">Buy Plan</a></li>
      </ul>
    </div>
    <div>
      <h4 class="text-white font-bold mb-3">Account</h4>
      <ul class="space-y-2 text-sm">
        <li><button type="button" data-open-login class="hover:text-white transition">Login</button></li>
        <li><a href="register.php" class="hover:text-white transition">Register</a></li>
        <li><a href="profile.php" class="hover:text-white transition">Profile</a></li>
        <li><a href="exam_history.php" class="hover:text-white transition">Exam Records</a></li>
      </ul>
    </div>
  </div>
  <div class="border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col sm:flex-row justify-between items-center gap-2 text-xs text-slate-500">
      <p>&copy; 2026 Online Exam System. All rights reserved.</p>
      <p class="font-medium text-slate-400">Developed by VFC Tecnologies</p>
    </div>
  </div>
</footer>

<button type="button" id="toTop" class="w-11 h-11 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg flex items-center justify-center" aria-label="উপরে যান">
  <svg class="ic w-5 h-5"><use href="#i-up"/></svg>
</button>

<div id="toast" class="bg-slate-900 text-white text-sm px-5 py-3 rounded-xl shadow-2xl max-w-[90vw] text-center"></div>

<!-- ============================================================
     LOGIN MODAL  (same fields, ids and message boxes as your login page,
     so your existing js/main.js keeps working)
     ============================================================ -->
<div id="loginModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" role="dialog" aria-modal="true" aria-label="Login">
  <div class="modal-card relative w-full max-w-4xl bg-white rounded-2xl shadow-xl border border-slate-200/80 overflow-hidden grid md:grid-cols-2 max-h-[92vh] overflow-y-auto">
    <button type="button" id="closeLogin" class="absolute top-3 right-3 z-10 w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center" aria-label="বন্ধ করুন"><svg class="ic w-4 h-4"><use href="#i-x"/></svg></button>

    <div class="hidden md:flex flex-col justify-center items-center bg-gradient-to-br from-indigo-600 to-indigo-800 p-8 text-white relative">
      <div class="absolute inset-0 dots opacity-10"></div>
      <div class="relative z-10 text-center">
        <div class="floaty w-24 h-24 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-6 backdrop-blur-sm border border-white/20"><svg class="ic w-10 h-10 text-indigo-100"><use href="#i-laptop"/></svg></div>
        <h2 class="text-2xl font-bold mb-2">Examination Portal</h2>
        <p class="text-indigo-200 text-sm max-w-xs leading-relaxed">Log in to access your dashboard, complete assessments, and review results.</p>
      </div>
    </div>

    <div class="p-6 sm:p-10 flex flex-col justify-center">
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">User Login</h2>
        <p class="text-slate-500 text-sm mt-1">Please enter your credentials to continue</p>
      </div>

      <!-- Shown only when a guest clicks a page that needs login -->
      <div id="loginNotice" class="hidden mb-5 bg-indigo-50 border border-indigo-200 text-indigo-800 px-4 py-3 rounded-lg text-sm leading-relaxed flex gap-2">
        <svg class="ic w-5 h-5 mt-0.5 text-indigo-600"><use href="#i-lock"/></svg>
        <span id="loginNoticeText"></span>
      </div>

      <form action="" method="post" class="space-y-4" id="loginForm">
        <input type="hidden" name="redirect" id="redirect" value="">
        <div>
          <label for="email" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Email Address</label>
          <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 bg-slate-50">
            <span class="px-3.5 text-slate-400"><svg class="ic w-4 h-4"><use href="#i-mail"/></svg></span>
            <input name="email" type="text" id="email" placeholder="example@domain.com" class="w-full py-2.5 pr-4 bg-transparent text-sm text-slate-800 focus:outline-none border-none outline-none">
          </div>
        </div>
        <div>
          <label for="password" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Password</label>
          <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 bg-slate-50">
            <span class="px-3.5 text-slate-400"><svg class="ic w-4 h-4"><use href="#i-lock"/></svg></span>
            <input name="password" type="password" id="password" placeholder="••••••••" class="w-full py-2.5 pr-4 bg-transparent text-sm text-slate-800 focus:outline-none border-none outline-none">
          </div>
        </div>
        <div class="pt-2">
          <input type="submit" id="loginsubmit" value="Login" class="shine w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg transition shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 text-sm cursor-pointer">
        </div>
      </form>

      <p class="text-center text-sm text-slate-500 mt-6">New User? <a href="register.php" class="font-semibold text-indigo-600 hover:underline">Signup Free</a></p>

      <div class="mt-4 space-y-2">
        <div class="empty bg-amber-50 border border-amber-200 text-amber-700 px-4 py-2.5 rounded-lg text-xs font-medium flex items-center gap-2" style="display:none;">Field must not be empty!</div>
        <div class="error bg-rose-50 border border-rose-200 text-rose-700 px-4 py-2.5 rounded-lg text-xs font-medium flex items-center gap-2" style="display:none;">Email or Password not matched!</div>
        <div class="disable bg-slate-100 border border-slate-300 text-slate-700 px-4 py-2.5 rounded-lg text-xs font-medium flex items-center gap-2" style="display:none;">User Id disabled!</div>
      </div>
    </div>
  </div>
</div>

<script>
/* ==========================================================
   Helpers
   ========================================================== */
const $  = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => [...r.querySelectorAll(s)];
const toBn = n => String(n).replace(/\d/g, d => '০১২৩৪৫৬৭৮৯'[d]);
const icon = (id, cls = 'w-5 h-5') => `<svg class="ic ${cls}"><use href="#i-${id}"/></svg>`;

/* ==========================================================
   Data  (in your PHP project this comes from the categories table)
   ========================================================== */
const CATS = [
  { id: 1, group: 'govt',    tone: 'indigo',  ic: 'book',   title: 'বিসিএস প্রিলিমিনারি',        short: 'বিসিএস প্রিলিমিনারি সিলেবাস অনুযায়ী বিষয়ভিত্তিক ও পূর্ণাঙ্গ প্রশ্নব্যাংক মডেল টেস্ট।' },
  { id: 2, group: 'teacher', tone: 'emerald', ic: 'bank',   title: 'শিক্ষক নিবন্ধন (NTRCA)',     short: 'শিক্ষক নিবন্ধন (NTRCA) সিলেবাস অনুযায়ী বিষয়ভিত্তিক ও পূর্ণাঙ্গ প্রশ্নব্যাংক মডেল টেস্ট।' },
  { id: 3, group: 'teacher', tone: 'amber',   ic: 'cap',    title: 'প্রাথমিক সহকারী শিক্ষক',    short: 'প্রাথমিক সহকারী শিক্ষক সিলেবাস অনুযায়ী বিষয়ভিত্তিক ও পূর্ণাঙ্গ প্রশ্নব্যাংক মডেল টেস্ট।' },
  { id: 4, group: 'bank',    tone: 'indigo',  ic: 'wallet', title: 'ব্যাংক জবস',                 short: 'ব্যাংক জবস সিলেবাস অনুযায়ী বিষয়ভিত্তিক ও পূর্ণাঙ্গ প্রশ্নব্যাংক মডেল টেস্ট।' },
  { id: 5, group: 'eng',     tone: 'emerald', ic: 'wrench', title: 'Sub Assistant Engineer',     short: 'Sub Assistant Engineer সিলেবাস অনুযায়ী বিষয়ভিত্তিক ও পূর্ণাঙ্গ প্রশ্নব্যাংক মডেল টেস্ট।' },
];
const DUR = {
  '3_months': { label: '৩ মাস',  price: 500,  monthly: 166 },
  '6_months': { label: '৬ মাস',  price: 900,  monthly: 150 },
  '1_year':   { label: '১ বছর', price: 1500, monthly: 125 },
};
const TONE = {
  indigo:  { grad: 'from-indigo-600 to-indigo-800',  text: 'text-indigo-600',  btn: 'bg-indigo-600 hover:bg-indigo-700',   tile: 'bg-indigo-100 text-indigo-600' },
  emerald: { grad: 'from-emerald-500 to-emerald-700', text: 'text-emerald-600', btn: 'bg-emerald-600 hover:bg-emerald-700', tile: 'bg-emerald-100 text-emerald-600' },
  amber:   { grad: 'from-amber-500 to-orange-600',    text: 'text-amber-600',   btn: 'bg-amber-500 hover:bg-amber-600',     tile: 'bg-amber-100 text-amber-600' },
};

/* ==========================================================
   HERO slides
   ========================================================== */
const SLIDES = [
  { bg: 'from-indigo-600 via-indigo-700 to-indigo-900', cat: 'বিসিএস প্রিলিমিনারি', title: 'বিসিএস প্রিলিমিনারি মডেল টেস্ট',
    desc: 'সিলেবাস অনুযায়ী বিষয়ভিত্তিক ও পূর্ণাঙ্গ প্রশ্নব্যাংক মডেল টেস্ট দিয়ে নিজেকে তৈরি করুন।',
    q: 'বাংলাদেশের মুক্তিযুদ্ধ কত সালে শুরু হয়?', opts: ['১৯৭০', '১৯৭১', '১৯৭২', '১৯৭৫'], ans: 1, w: '24%', n: 12 },
  { bg: 'from-emerald-600 via-emerald-700 to-teal-900', cat: 'শিক্ষক নিবন্ধন (NTRCA)', title: 'NTRCA শিক্ষক নিবন্ধন প্রস্তুতি',
    desc: 'শিক্ষক নিবন্ধন সিলেবাস ধরে বিষয়ভিত্তিক পরীক্ষা দিন, আর পূর্ণাঙ্গ মডেল টেস্টে নিজেকে যাচাই করুন।',
    q: '‘বলাকা’ কাব্যগ্রন্থের রচয়িতা কে?', opts: ['কাজী নজরুল ইসলাম', 'জীবনানন্দ দাশ', 'রবীন্দ্রনাথ ঠাকুর', 'সুকান্ত ভট্টাচার্য'], ans: 2, w: '38%', n: 19 },
  { bg: 'from-amber-500 via-orange-600 to-orange-800', cat: 'প্রাথমিক সহকারী শিক্ষক', title: 'প্রাথমিক সহকারী শিক্ষক পরীক্ষার প্রস্তুতি',
    desc: 'প্রাথমিক সহকারী শিক্ষক সিলেবাসের প্রতিটি বিষয়ে প্রশ্নব্যাংক ভিত্তিক মডেল টেস্ট।',
    q: '৭ × ৮ = কত?', opts: ['৫৪', '৫৬', '৬৩', '৬৪'], ans: 1, w: '16%', n: 8 },
  { bg: 'from-indigo-600 via-violet-700 to-indigo-900', cat: 'ব্যাংক জবস', title: 'ব্যাংক জবস মডেল টেস্ট',
    desc: 'ব্যাংক জবস সিলেবাস অনুযায়ী বিষয়ভিত্তিক ও পূর্ণাঙ্গ পরীক্ষায় গতি ও নির্ভুলতা বাড়ান।',
    q: '২০০ টাকার ১৫% কত?', opts: ['২০ টাকা', '৩০ টাকা', '৪০ টাকা', '৫০ টাকা'], ans: 1, w: '46%', n: 23 },
  { bg: 'from-teal-600 via-emerald-700 to-indigo-900', cat: 'Sub Assistant Engineer', title: 'Sub Assistant Engineer মডেল টেস্ট',
    desc: 'সিলেবাস অনুযায়ী বিষয়ভিত্তিক ও পূর্ণাঙ্গ প্রশ্নব্যাংক মডেল টেস্টে প্রস্তুতি সম্পূর্ণ করুন।',
    q: 'ওহমের সূত্র অনুযায়ী ভোল্টেজ (V) = ?', opts: ['I ÷ R', 'I × R', 'R ÷ I', 'I + R'], ans: 1, w: '30%', n: 15 },
];
const LETTERS = ['ক', 'খ', 'গ', 'ঘ'];

function slideHTML(s, idx) {
  const opts = s.opts.map((o, i) => `
    <div class="opt ${i === s.ans ? 'ok' : ''} flex items-center gap-3 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-700">
      <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 text-xs font-bold flex items-center justify-center flex-none">${LETTERS[i]}</span>
      <span class="flex-1">${o}</span>
      ${i === s.ans ? `<span class="tick text-emerald-600">${icon('checkc', 'w-5 h-5')}</span>` : ''}
    </div>`).join('');
  return `
  <div class="slide ${idx === 0 ? 'active' : ''} bg-gradient-to-br ${s.bg} text-white relative overflow-hidden">
    <div class="absolute inset-0 dots opacity-10"></div>
    <div class="blob w-44 h-44 bg-white/10 -top-12 -left-10"></div>
    <div class="blob w-64 h-64 bg-white/10 bottom-0 right-1/3" style="animation-delay:-2s"></div>
    <div class="relative max-w-7xl mx-auto px-4 pt-12 pb-20 lg:pt-16 lg:pb-24 grid lg:grid-cols-2 gap-10 items-center">
      <div>
        <span class="a inline-flex items-center gap-2 bg-white/15 border border-white/25 rounded-full px-4 py-1.5 text-xs font-semibold backdrop-blur" style="--i:0">${icon('star', 'w-3.5 h-3.5')} ${s.cat}</span>
        <h2 class="a mt-5 text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight tracking-tight" style="--i:1">${s.title}</h2>
        <p class="a mt-4 text-indigo-100 text-base sm:text-lg max-w-lg leading-relaxed" style="--i:2">${s.desc}</p>
        <div class="a mt-8 flex flex-wrap gap-3" style="--i:3">
          <a href="register.php" class="shine join-btn inline-flex items-center gap-2 rounded-full bg-white text-indigo-700 font-bold px-7 py-3 shadow-lg hover:scale-105 transition">Join Now ${icon('arrow', 'w-4 h-4')}</a>
          <a href="#plans" class="inline-flex items-center gap-2 rounded-full border border-white/40 hover:bg-white/10 font-semibold px-7 py-3 transition">প্ল্যান দেখুন</a>
        </div>
        <div class="a mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm text-indigo-100" style="--i:4">
          <span class="flex items-center gap-1.5">${icon('checkc', 'w-4 h-4')} বিষয়ভিত্তিক পরীক্ষা</span>
          <span class="flex items-center gap-1.5">${icon('checkc', 'w-4 h-4')} পূর্ণাঙ্গ মডেল টেস্ট</span>
          <span class="flex items-center gap-1.5">${icon('checkc', 'w-4 h-4')} পেমেন্টের পরেই একটিভ</span>
        </div>
      </div>
      <div class="card-in hidden lg:block">
        <div class="floaty max-w-md ml-auto bg-white text-slate-800 rounded-2xl shadow-2xl p-5">
          <div class="flex items-center justify-between mb-3">
            <div><p class="text-sm font-bold text-slate-900">মডেল টেস্ট ০১</p><p class="text-xs text-slate-500">${s.cat}</p></div>
            <span class="flex items-center gap-1.5 bg-rose-50 text-rose-600 rounded-full px-3 py-1 text-xs font-bold">${icon('clock', 'w-3.5 h-3.5')} <span class="timer">৩৯:৫৯</span></span>
          </div>
          <div class="h-1.5 rounded-full bg-slate-100 mb-1"><div class="pbar h-1.5 rounded-full bg-indigo-600" style="--w:${s.w}"></div></div>
          <p class="text-[11px] text-slate-400 mb-4">প্রশ্ন ${toBn(s.n)} / ${toBn(50)}</p>
          <p class="font-bold text-slate-900 mb-3">${s.q}</p>
          <div class="space-y-2">${opts}</div>
        </div>
      </div>
    </div>
  </div>`;
}

const slidesEl = $('#slides'), dotsEl = $('#dots');
slidesEl.innerHTML = SLIDES.map(slideHTML).join('');
dotsEl.innerHTML = SLIDES.map((_, i) => `<button type="button" class="dot ${i === 0 ? 'active' : ''}" aria-label="স্লাইড ${toBn(i + 1)}"><i></i></button>`).join('');
let cur = 0, solveTimer;
function go(n) {
  const S = $$('.slide'), D = $$('.dot');
  S[cur].classList.remove('active', 'solved'); D[cur].classList.remove('active');
  cur = (n + S.length) % S.length;
  S[cur].classList.add('active'); D[cur].classList.add('active');
  clearTimeout(solveTimer);
  solveTimer = setTimeout(() => S[cur].classList.add('solved'), 1800);
}
solveTimer = setTimeout(() => $$('.slide')[0].classList.add('solved'), 2200);
$('#next').onclick = () => go(cur + 1);
$('#prev').onclick = () => go(cur - 1);
dotsEl.addEventListener('click', e => { const b = e.target.closest('.dot'); if (b) go($$('.dot').indexOf(b)); });
/* autoplay is driven by the dot's fill animation, so hovering pauses both together */
dotsEl.addEventListener('animationend', e => { if (e.target.matches('.dot.active i')) go(cur + 1); });
let tx = 0;
$('#hero').addEventListener('touchstart', e => tx = e.touches[0].clientX, { passive: true });
$('#hero').addEventListener('touchend', e => { const d = e.changedTouches[0].clientX - tx; if (Math.abs(d) > 50) go(cur + (d < 0 ? 1 : -1)); }, { passive: true });

/* ticking exam timer in the mock card */
let secs = 39 * 60 + 59;
setInterval(() => {
  secs = secs > 0 ? secs - 1 : 39 * 60 + 59;
  const t = toBn(String(Math.floor(secs / 60)).padStart(2, '0') + ':' + String(secs % 60).padStart(2, '0'));
  $$('.timer').forEach(el => el.textContent = t);
}, 1000);

/* ==========================================================
   Ticker
   ========================================================== */
const tickItems = ['বিসিএস প্রিলিমিনারি', 'শিক্ষক নিবন্ধন (NTRCA)', 'প্রাথমিক সহকারী শিক্ষক', 'ব্যাংক জবস', 'Sub Assistant Engineer', 'সরকারি সার্কুলার'];
const tickHTML = tickItems.map(t => `<span class="flex items-center gap-3 px-6 whitespace-nowrap">${icon('star', 'w-3.5 h-3.5 text-amber-400')} ${t}</span>`).join('');
$('#marquee').innerHTML = tickHTML + tickHTML;

/* ==========================================================
   Course cards + filters
   ========================================================== */
const gridEl = $('#grid');
function cardHTML(c, i, dur) {
  const t = TONE[c.tone], d = DUR[dur];
  const feats = [
    'সিলেবাস অনুযায়ী বিষয়ভিত্তিক পরীক্ষা',
    'পূর্ণাঙ্গ প্রশ্নব্যাংক মডেল টেস্ট',
    'পেমেন্টের সাথে সাথে সব পরীক্ষা একটিভ',
    'ফলাফল Exam Records-এ সংরক্ষণ',
    'Leaderboard-এ অবস্থান জানার সুযোগ',
  ];
  if (c.free) {
    return `
    <div class="pop" style="--d:${i * 90}ms"><article class="course-card h-full bg-white rounded-2xl border border-slate-200 shadow-md hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 overflow-hidden flex flex-col">
      <div class="relative h-36 bg-gradient-to-br from-slate-700 to-slate-900 text-white overflow-hidden flex items-center justify-center">
        <div class="absolute inset-0 dots opacity-10"></div><div class="absolute -right-6 -top-6 w-28 h-28 rounded-full bg-white/10"></div>
        <div class="card-icon relative w-16 h-16 rounded-2xl bg-white/15 border border-white/25 flex items-center justify-center">${icon('brief', 'w-8 h-8')}</div>
        <span class="absolute top-3 right-3 text-[11px] font-bold bg-emerald-500 rounded-full px-3 py-0.5">সবার জন্য</span>
      </div>
      <div class="p-5 flex-1 flex flex-col">
        <h3 class="text-lg font-bold text-slate-900 mb-3">${c.title}</h3>
        <ul class="space-y-2 text-sm text-slate-600 mb-4 flex-1">
          <li class="flex gap-2">${icon('check', 'w-4 h-4 mt-0.5 text-slate-700')} লগইন ছাড়াই দেখা যায়</li>
          <li class="flex gap-2">${icon('check', 'w-4 h-4 mt-0.5 text-slate-700')} সরকারি চাকরির সার্কুলার এক জায়গায়</li>
        </ul>
        <div class="border-t border-dashed border-slate-200 pt-3"><span class="text-2xl font-black text-slate-800">ফ্রি</span></div>
      </div>
      <div class="px-5 pb-5"><a href="circulars.php" class="shine block text-center rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 transition">সার্কুলার দেখুন</a></div>
    </article></div>`;
  }
  return `
    <div class="pop" style="--d:${i * 90}ms"><article class="course-card h-full bg-white rounded-2xl border border-slate-200 shadow-md hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 overflow-hidden flex flex-col">
      <div class="relative h-36 bg-gradient-to-br ${t.grad} text-white overflow-hidden flex items-center justify-center">
        <div class="absolute inset-0 dots opacity-10"></div><div class="absolute -right-6 -top-6 w-28 h-28 rounded-full bg-white/10"></div><div class="absolute -left-8 -bottom-10 w-32 h-32 rounded-full bg-white/10"></div>
        <div class="card-icon relative w-16 h-16 rounded-2xl bg-white/15 border border-white/25 backdrop-blur flex items-center justify-center">${icon(c.ic, 'w-8 h-8')}</div>
        <span class="absolute top-3 right-3 text-[11px] font-bold bg-white/20 rounded-full px-3 py-0.5">বিষয়ভিত্তিক পরীক্ষা</span>
      </div>
      <div class="p-5 flex-1 flex flex-col">
        <h3 class="text-lg font-bold text-slate-900 mb-3">${c.title}</h3>
        <ul class="space-y-2 text-sm text-slate-600 mb-4 flex-1">${feats.map(f => `<li class="flex gap-2">${icon('check', `w-4 h-4 mt-0.5 ${t.text}`)} ${f}</li>`).join('')}</ul>
        <div class="flex items-end justify-between border-t border-dashed border-slate-200 pt-3">
          <div><span class="text-xs text-slate-400">${d.label}-এর জন্য</span><div class="text-2xl font-black ${t.text}">৳ ${toBn(d.price.toLocaleString('en-US'))}</div></div>
          <span class="text-xs text-slate-400 pb-1">প্রতি মাসে ৳ ${toBn(d.monthly)}</span>
        </div>
      </div>
      <div class="px-5 pb-5 flex gap-2">
        <a href="#plans" data-protect="বিস্তারিত দেখতে" class="flex-1 text-center rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold py-2.5 text-sm transition">বিস্তারিত</a>
        <a href="checkout.php?category_id=${c.id}&plan=${dur}&amount=${d.price}" class="shine flex-1 text-center rounded-xl ${t.btn} text-white font-bold py-2.5 text-sm shadow-md transition">Enroll Now</a>
      </div>
    </article></div>`;
}
const ALL = [...CATS, { id: 0, group: 'govt', free: true, title: 'সরকারি সার্কুলার' }];
function renderCards() {
  const g = $('#fGroup').value, dur = $('#fDur').value, q = $('#fSearch').value.trim().toLowerCase();
  const list = ALL.filter(c => (g === 'all' || c.group === g) && (!q || c.title.toLowerCase().includes(q)));
  gridEl.innerHTML = list.map((c, i) => cardHTML(c, i, dur)).join('');
  $('#notFound').classList.toggle('hidden', list.length > 0);
}
['fGroup', 'fDur'].forEach(id => $('#' + id).addEventListener('change', renderCards));
$('#fSearch').addEventListener('input', renderCards);
renderCards();

/* ==========================================================
   Service tiles
   ========================================================== */
const TILES = [
  { ic: 'file',   t: 'Take Exam',              s: 'সরাসরি পরীক্ষা শুরু',           href: 'exam.php',         c: 'bg-indigo-100 text-indigo-600' },
  { ic: 'book',   t: 'বিষয়ভিত্তিক পরীক্ষা',    s: 'একটি বিষয় ধরে অনুশীলন',        href: '#courses', protect: 'বিষয়ভিত্তিক পরীক্ষা দেখতে', c: 'bg-emerald-100 text-emerald-600' },
  { ic: 'chart',  t: 'পূর্ণাঙ্গ মডেল টেস্ট',    s: 'পুরো সিলেবাসে যাচাই',           href: '#courses', protect: 'পূর্ণাঙ্গ মডেল টেস্ট দেখতে', c: 'bg-amber-100 text-amber-600' },
  { ic: 'clock',  t: 'Exam Records',           s: 'আগের সব পরীক্ষার ফল',          href: 'exam_history.php', c: 'bg-indigo-100 text-indigo-600' },
  { ic: 'trophy', t: 'Leaderboard',            s: 'অন্যদের সাথে অবস্থান মিলান',    href: 'leaderboard.php',  c: 'bg-amber-100 text-amber-600' },
  { ic: 'brief',  t: 'সরকারি সার্কুলার',        s: 'নতুন চাকরির খবর',              href: 'circulars.php',    c: 'bg-emerald-100 text-emerald-600' },
  { ic: 'zap',    t: 'দ্রুত একটিভেশন',         s: 'পেমেন্টের সাথে সাথে চালু',      href: 'subscription.php', c: 'bg-indigo-100 text-indigo-600' },
  { ic: 'user',   t: 'নিজের প্রোফাইল',          s: 'তথ্য ও সাবস্ক্রিপশন এক জায়গায়', href: 'profile.php',      c: 'bg-emerald-100 text-emerald-600' },
];
$('#tiles').innerHTML = TILES.map((x, i) => `
  <a href="${x.href}" ${x.protect ? `data-protect="${x.protect}"` : ''} class="tile reveal group block bg-white rounded-2xl border border-slate-200 p-5 text-center shadow-sm hover:shadow-xl hover:-translate-y-1.5 hover:border-indigo-300 transition-all duration-300" style="--d:${(i % 4) * 90}ms">
    <div class="tile-ic w-14 h-14 mx-auto rounded-2xl ${x.c} flex items-center justify-center mb-3">${icon(x.ic, 'w-7 h-7')}</div>
    <h4 class="font-bold text-slate-800 text-sm sm:text-base">${x.t}</h4>
    <p class="text-xs text-slate-500 mt-1">${x.s}</p>
  </a>`).join('');

/* ==========================================================
   Scroll reveal + counters
   ========================================================== */
function runCount(el) {
  const to = +el.dataset.count, pre = el.dataset.pre || '', suf = el.dataset.suf || '';
  let s = null;
  (function f(t) {
    if (!s) s = t;
    const p = Math.min((t - s) / 1600, 1), e = 1 - Math.pow(1 - p, 3);
    el.textContent = pre + toBn(Math.round(to * e)) + suf;
    if (p < 1) requestAnimationFrame(f);
  })(performance.now());
}
const io = new IntersectionObserver(es => es.forEach(e => {
  if (!e.isIntersecting) return;
  e.target.classList.add('in');
  $$('[data-count]', e.target).forEach(runCount);
  io.unobserve(e.target);
}), { threshold: .18 });
$$('.reveal').forEach(el => io.observe(el));

/* ==========================================================
   Header, progress bar, back-to-top, mobile menu
   ========================================================== */
const header = $('#header'), prog = $('#progress'), toTop = $('#toTop');
addEventListener('scroll', () => {
  const y = scrollY, max = document.documentElement.scrollHeight - innerHeight;
  header.classList.toggle('scrolled', y > 8);
  prog.style.transform = `scaleX(${max > 0 ? y / max : 0})`;
  toTop.classList.toggle('show', y > 500);
}, { passive: true });
toTop.onclick = () => scrollTo({ top: 0, behavior: 'smooth' });

const burger = $('#burger'), mnav = $('#mnav');
burger.onclick = () => {
  const open = burger.classList.toggle('open');
  mnav.classList.toggle('open', open);
  burger.setAttribute('aria-expanded', open);
};
mnav.addEventListener('click', e => { if (e.target.closest('a[href^="#"]')) { burger.classList.remove('open'); mnav.classList.remove('open'); } });

/* ==========================================================
   Login modal
   ========================================================== */
const modal = $('#loginModal');
const openLogin = (why, next) => {
  const box = $('#loginNotice');
  if (why) {
    $('#loginNoticeText').innerHTML = why + ' আগে লগইন করতে হবে। একাউন্ট না থাকলে <a href="register.php" class="font-bold underline">ফ্রি সাইনআপ</a> করুন।';
    box.classList.remove('hidden');
  } else box.classList.add('hidden');
  $('#redirect').value = next || '';
  $$('.empty,.error,.disable', modal).forEach(x => x.style.display = 'none');
  modal.classList.add('show'); document.body.style.overflow = 'hidden';
  setTimeout(() => $('#email').focus(), 150);
  burger.classList.remove('open'); mnav.classList.remove('open');
};
const closeLogin = () => { modal.classList.remove('show'); document.body.style.overflow = ''; };
document.addEventListener('click', e => { if (e.target.closest('[data-open-login]')) openLogin(); });
$('#closeLogin').onclick = closeLogin;
modal.addEventListener('click', e => { if (e.target === modal) closeLogin(); });
addEventListener('keydown', e => { if (e.key === 'Escape') closeLogin(); });

/* DEMO ONLY: remove this block in your real site; your js/main.js handles the login form there. */

    /* ==========================================================
   AJAX Login Implementation
   ========================================================== */
addEventListener('keydown', e => { if (e.key === 'Escape') closeLogin(); });

/* ==========================================================
   AJAX Login Implementation
   ========================================================== */
/* ==========================================================
   AJAX Login Implementation (Updated for Modal)
   ========================================================== */
const loginForm = $('#loginForm');

if (loginForm) {
  loginForm.addEventListener('submit', function (e) {
    e.preventDefault();

    // আগের অ্যালার্ট মেসেজ হাইড করা
    $$('.empty, .error, .disable', modal).forEach(x => x.style.display = 'none');

    const email = $('#email') ? $('#email').value.trim() : '';
    const password = $('#password') ? $('#password').value.trim() : '';
    const redirect = $('#redirect') ? $('#redirect').value : '';

    // ১. ভ্যালিডেশন
    if (!email || !password) {
      if ($('.empty', modal)) $('.empty', modal).style.display = 'flex';
      return;
    }

    // ২. Request Data প্রস্তুত করা
    const formData = new URLSearchParams();
    formData.append('email', email);
    formData.append('password', password);

    fetch('getlogin.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: formData.toString()
    })
    .then(response => response.text())
    .then(data => {
      const res = data.trim();
      console.log("Login Response:", res);

      if (res === "empty") {
        if ($('.empty', modal)) $('.empty', modal).style.display = 'flex';
      } else if (res === "error") {
        if ($('.error', modal)) $('.error', modal).style.display = 'flex';
      } else if (res === "disable") {
        if ($('.disable', modal)) $('.disable', modal).style.display = 'flex';
      } 
      // ৩. সফল লগইন ও সাবস্ক্রিপশন লজিক
      else if (res === "success_exam") {
        // যদি ইউজার আগে থেকে কোনো নির্দিষ্ট পেজে যেতে চেয়ে থাকে (redirect variable), সেখানে যাবে, নয়তো exam.php-তে যাবে
        window.location.href = redirect || "exam.php";
      } else if (res === "success_subscription") {
        // সাবস্ক্রিপশন না থাকলে subscription.php-তে নিয়ে যাবে
        window.location.href = "subscription.php";
      } else {
        // সেফটি ফলব্যাক (অন্য কোনো URL রিটার্ন করলে)
        window.location.href = redirect || res || "exam.php";
      }
    })
    .catch(err => {
      console.error('Login Error:', err);
      if ($('.error', modal)) $('.error', modal).style.display = 'flex';
    });
  });
}

/* ==========================================================
   GUEST MODE: pages that need an account ask the visitor to log in first.
   Public pages (circulars.php, register.php) open normally.
   NOTE: this only guides the visitor. Real protection must also be done in PHP
   on each page, e.g.  if (!isset($_SESSION['user_id'])) { header('Location: index.php?login=1'); exit; }
   ========================================================== */
const PROTECT = [
  [/exam_history\.php/,        'Exam Records দেখতে'],
  [/exam\.php/,                'পরীক্ষা দিতে'],
  [/profile\.php/,             'প্রোফাইল দেখতে'],
  [/leaderboard\.php/,         'Leaderboard দেখতে'],
  [/subscription\.php|checkout\.php/, 'প্ল্যান নিতে'],
];
const toastEl = $('#toast'); let tt;
function toast(msg) { toastEl.textContent = msg; toastEl.classList.add('show'); clearTimeout(tt); tt = setTimeout(() => toastEl.classList.remove('show'), 2600); }
document.addEventListener('click', e => {
  const a = e.target.closest('a[href]');
  if (!a) return;
  const h = a.getAttribute('href');
  let why = a.dataset.protect;
  if (!why) { const m = PROTECT.find(([re]) => re.test(h)); if (m) why = m[1]; }
  if (why) { e.preventDefault(); openLogin(why, h); return; }
  /* DEMO ONLY: the public .php pages do not exist in this preview */
  //if (/\.php/.test(h)) { e.preventDefault(); toast('ডেমো: এই লিংক আপনার সাইটে ' + h.split('?')[0] + ' পেজে যাবে'); }
});
/* open the login popup automatically when the page is opened as index.php?login=1 */
if (/[?&]login=1/.test(location.search)) openLogin('এই পেজ দেখতে');
</script>
</body>
</html>