@extends('layouts.app')

@push('styles')
<style>

:root {
    --blue: #1a6ef5;
    --blue-light: #4a90f8;
    --yellow: #f5c518;
    --teal: #00c4cc;
    --bg: #eef4fd;
    --card: #ffffff;
    --text: #0d1b2a;
    --muted: #6b7a90;
    --border: #d6e4f7;
    --shadow: 0 8px 40px rgba(26, 110, 245, 0.10);
}
/* ── HERO LAYOUT ── */
.hero {
    /* display: grid;
    grid-template-columns: 1fr 420px;
    gap: 60px; */
    align-items: center;
    min-height: calc(100vh - 73px);
    padding: 60px 10px 60px 10px;
    position: relative;
    overflow: hidden;
    background: #f7f7f7;
}

/* bg blobs */
/* .hero::before {
    content: '';
    position: absolute;
    width: 600px;
    height: 600px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(26, 110, 245, 0.12) 0%, transparent 70%);
    top: -100px;
    left: -100px;
    pointer-events: none;
} */

.hero::after {
    content: '';
    position: absolute;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(0, 196, 204, 0.10) 0%, transparent 70%);
    bottom: -80px;
    left: 300px;
    pointer-events: none;
}

/* ── LEFT SIDE ── */
.hero-left {
    position: relative;
    z-index: 1;
}

.hero-img-div {
    width: 100%;
    padding-top: 66.66%;
    /* 3:2 aspect ratio */
    position: relative;
    margin-bottom: 32px;
}

.hero-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 20px;
    /* box-shadow: 0 20px 60px rgba(26, 110, 245, 0.12); */
}

/* floating UI cards */
.ui-scene {
    position: relative;
    height: 380px;
    margin-bottom: 32px;
}

/* person circle */
.avatar-circle {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: linear-gradient(135deg, #b8d4f8 0%, #dbeeff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    box-shadow: 0 0 0 16px rgba(26, 110, 245, 0.08), 0 0 0 32px rgba(26, 110, 245, 0.04);
}

.avatar-circle svg {
    width: 160px;
    height: 220px;
}

/* dashed orbit */
.orbit {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 320px;
    height: 320px;
    border-radius: 50%;
    border: 2px dashed rgba(26, 110, 245, 0.25);
}

/* dot accents */
.dot {
    position: absolute;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--teal);
}

.dot:nth-child(2) {
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
}

.dot:nth-child(3) {
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--blue);
}

.dot:nth-child(4) {
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: var(--yellow);
}

.dot:nth-child(5) {
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: var(--blue-light);
}

/* floating cards */
.float-card {
    position: absolute;
    /* background: var(--card); */
    border-radius: 14px;
    padding: 12px 16px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
    font-size: 0.78rem;
    animation: floatUp 3s ease-in-out infinite alternate;
}

.float-card:nth-child(6) {
    left: 0;
    top: 20px;
    animation-delay: 0s;
}

.float-card:nth-child(7) {
    right: 0;
    top: 30px;
    animation-delay: .8s;
}

.float-card:nth-child(8) {
    right: 10px;
    bottom: 10px;
    animation-delay: 1.5s;
}

@keyframes floatUp {
    from {
        transform: translateY(0);
    }

    to {
        transform: translateY(-8px);
    }
}

.recruit-btn {
    background: var(--yellow);
    color: #000;
    font-weight: 700;
    font-family: 'Syne', sans-serif;
    font-size: 0.82rem;
    padding: 8px 18px;
    border-radius: 8px;
    display: inline-block;
    margin-bottom: 8px;
}

.company-card {
    min-width: 160px;
}

.company-name {
    font-weight: 700;
    font-size: 0.88rem;
    color: var(--text);
    margin-bottom: 4px;
}

.company-card ul {
    list-style: none;
}

.company-card ul li {
    color: var(--muted);
    font-size: 0.78rem;
    line-height: 1.6;
}

.company-card ul li::before {
    content: '• ';
    color: var(--blue);
}

.feature-list {
    min-width: 180px;
}

.feature-item {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 8px;
}

.feature-item:last-child {
    margin-bottom: 0;
}

.feat-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: #eef4fd;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.8rem;
}

.feat-text strong {
    display: block;
    font-size: 0.8rem;
    color: var(--text);
    font-weight: 600;
}

.feat-text span {
    font-size: 0.7rem;
    color: var(--muted);
}

.stat-card {
    /* background: var(--blue); */
    color: #fff;
    border: none;
    min-width: 130px;
    text-align: center;
}

.stat-num {
    font-family: 'Syne', sans-serif;
    font-weight: 800;
    font-size: 1.6rem;
}

.stat-label {
    font-size: 0.72rem;
    opacity: 0.85;
}

/* hero headline */
.hero-headline {
    font-family: 'Syne', sans-serif;
    font-size: clamp(2.2rem, 4vw, 3rem);
    font-weight: 800;
    color: var(--text);
    line-height: 1.15;
    margin-bottom: 12px;
}

.hero-headline .accent {
    color: var(--blue);
}

.hero-sub {
    color: var(--muted);
    font-size: 1rem;
    font-weight: 400;
    display: flex;
    align-items: center;
    gap: 6px;
}

.ai-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: rgba(26, 110, 245, 0.1);
    color: var(--blue);
    font-weight: 600;
    font-size: 0.78rem;
    padding: 3px 10px;
    border-radius: 20px;
    border: 1px solid rgba(26, 110, 245, 0.2);
}

/* ── LOGIN CARD ── */
.login-card {
    background: var(--card);
    border-radius: 20px;
    /* box-shadow: 0 20px 60px rgba(26, 110, 245, 0.12); */
    border: 1px solid var(--border);
    overflow: hidden;
    animation: slideIn .6s cubic-bezier(.22, 1, .36, 1) both;
    position: relative;
    z-index: 2;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.tab-row {
    display: flex;
    border-bottom: 1px solid var(--border);
}

.tab {
    flex: 1;
    text-align: center;
    padding: 18px;
    font-family: 'Syne', sans-serif;
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--muted);
    cursor: pointer;
    transition: color .2s;
    position: relative;
}

.tab.active {
    color: var(--blue);
}

.tab.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--blue);
    border-radius: 2px 2px 0 0;
}

.login-body {
    padding: 28px 28px 24px;
}

.login-hint {
    font-size: 0.82rem;
    color: var(--muted);
    margin-bottom: 24px;
}

.field {
    margin-bottom: 18px;
}

.field label {
    display: block;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 6px;
}

.field input {
    width: 100%;
    padding: 12px 14px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.88rem;
    color: var(--text);
    background: #f8fbff;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}

.field input::placeholder {
    color: #b0bec5;
}

.field input:focus {
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(26, 110, 245, 0.1);
    background: #fff;
}

.pwd-wrap {
    position: relative;
}

.pwd-wrap input {
    padding-right: 44px;
}

.pwd-eye {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: var(--muted);
    font-size: 1rem;
}

.options-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
}

.remember {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 0.82rem;
    color: var(--muted);
    cursor: pointer;
}

.remember input[type=checkbox] {
    width: 15px;
    height: 15px;
    accent-color: var(--blue);
    cursor: pointer;
}

.forgot {
    font-size: 0.82rem;
    color: var(--blue);
    text-decoration: none;
    font-weight: 500;
}

.forgot:hover {
    text-decoration: underline;
}

.login-btn {
    width: 100%;
    padding: 14px;
    background: var(--blue);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-family: 'Syne', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    letter-spacing: .3px;
    transition: background .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 4px 20px rgba(26, 110, 245, 0.3);
    margin-bottom: 20px;
}

.login-btn:hover {
    background: #1260e0;
    transform: translateY(-1px);
    box-shadow: 0 8px 28px rgba(26, 110, 245, 0.35);
}

.login-btn:active {
    transform: translateY(0);
}

.register-row {
    text-align: center;
    font-size: 0.82rem;
    color: var(--muted);
}

.register-row a {
    color: var(--blue);
    font-weight: 600;
    text-decoration: none;
}

.register-row a:hover {
    text-decoration: underline;
}

/* divider */
.divider {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 18px 0;
}

.divider hr {
    flex: 1;
    border: none;
    border-top: 1px solid var(--border);
}

.divider span {
    font-size: 0.72rem;
    color: var(--muted);
    white-space: nowrap;
}

.social-row {
    display: flex;
    gap: 10px;
    margin-bottom: 18px;
}

.social-btn {
    flex: 1;
    padding: 10px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    font-size: 0.8rem;
    font-family: 'DM Sans', sans-serif;
    font-weight: 500;
    cursor: pointer;
    color: var(--text);
    transition: border-color .2s, box-shadow .2s;
}

.social-btn:hover {
    border-color: var(--blue);
    box-shadow: 0 0 0 2px rgba(26, 110, 245, 0.08);
}

.social-btn svg {
    width: 16px;
    height: 16px;
}

.hero-text {
    position: relative;
    z-index: 1;
}

.hero-text h1 {
    font-family: 'Syne', sans-serif;
    font-size: clamp(2.2rem, 4vw, 3rem);
    font-weight: 800;
    color: var(--text);
    line-height: 1.15;
    margin-bottom: 12px;
    text-align: center;
}

.hero-text h1 span {
    color: var(--blue);
}

.hero-text p {
    color: var(--muted);
    font-size: 1rem;
    font-weight: 400;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.features {
    background-color: #fff;
    padding: 50px 0px;
}

.features h2 {
    font-family: 'Syne', sans-serif;
    font-size: clamp(1.8rem, 3vw, 2.5rem);
    font-weight: 800;
    color: var(--text);
    line-height: 1.15;
    margin-bottom: 40px;
    text-align: center;
}

.feature-cards {
    display: flex;
    gap: 30px;
    justify-content: center;
    flex-wrap: wrap;
}

.feature-cards .card {
    background: var(--card);
    border-radius: 14px;
    padding: 20px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
    width: 280px;
    text-align: center;
}

.feature-cards .card:hover {
    box-shadow: 0 30px 80px rgba(26, 110, 245, 0.15);
}

.feature-cards .card i {
    font-size: 1.5rem;
    color: var(--blue);
    margin-bottom: 12px;
}

.feature-cards .card h3 {
    font-size: 1.2rem;
    color: var(--text);
    margin-bottom: 8px;
}

.feature-cards .card p {
    font-size: 0.9rem;
    color: var(--muted);
}

.section-3 {
    background-color: #fff;
    padding: 50px 0px;
}

.img-div {
    width: 100%;
    height: auto;
    display: flex;
    justify-content: center;
    padding: 20px;
    background-color: #ccc;
    border-radius: 10px;
}

.img-div img {
    width: 100%;
    height: auto;
    border-radius: 10px;
}

.content {
    display: flex;
    align-items: center;
    /* vertical center */
    justify-content: center;
    /* horizontal center */
    gap: 40px;
}

.content-div {
    flex: 1;
    padding: 20px;
}

.content-div h2 {
    font-family: 'Syne', sans-serif;
    font-size: clamp(1.8rem, 3vw, 2.5rem);
    font-weight: 800;
    color: var(--text);
    line-height: 1.15;
    margin-bottom: 20px;
}

.content-div p {
    font-size: 1rem;
    color: var(--muted);
    margin-bottom: 20px;
}

.content-div .learn-more {
    text-decoration: none;
    background: var(--blue);
    color: #fff;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-family: 'DM Sans', sans-serif;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: background .2s, transform .15s;
}

.mobile-app {
    background-color: #f7f8fb;
    padding: 50px 0px;
}

.mobile-app-icon {
    display: inline-block;
    background-color: #e8eaf0;
    color: #555;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 8px 18px;
    border-radius: 20px;
}

.mobile-app-content-div h2 {
    font-family: 'Syne', sans-serif;
    font-size: clamp(1.8rem, 3vw, 2.5rem);
    font-weight: 800;
    color: var(--text);
    line-height: 1.15;
    margin-bottom: 20px;
}

.mobile-app-content-div p {
    font-size: 1rem;
    color: var(--muted);
    margin-bottom: 20px;
}

.mobile-app-content-div .download-btn {
    text-decoration: none;
    background: var(--blue);
    color: #fff;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-family: 'DM Sans', sans-serif;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: background .2s, transform .15s;
}

.app-div {
    display: flex;
    gap: 40px;
}


.app-div .scan-div p {
    font-size: 1rem;
    color: var(--muted);
}

.app-div .scan-div img {
    width: 100px;
    height: auto;
    border-radius: 10px;
}

.mob-app-div {
    background: #ffffff;
    padding: 18px 20px;
    border-radius: 14px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    max-width: 280px;
}

.mob-app-div p {
    margin: 0 0 12px 0;
    font-size: 16px;
    font-weight: 600;
    color: #222;
}

.app-btns {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.app-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    border-radius: 10px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    color: #fff;
    transition: 0.3s ease;
}

/* Google Play button */
.app-btn .fa-google-play {
    font-size: 18px;
}

.app-btn:nth-child(1) {
    background: linear-gradient(135deg, #0F9D58, #34A853);
}

/* App Store button */
.app-btn:nth-child(2) {
    background: linear-gradient(135deg, #000000, #434343);
}

.app-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
}

.mob-img-col {
    display: flex;
    justify-content: center;
    /* right side e niye jabe */
    align-items: center;
}

.mob-section-img {
    width: 200px;
    height: auto;
}


.section-tabs {
    padding: 50px 0px;
    /*max-width: 900px;*/
    margin: 0 auto;
}

.demo {
    max-width: 700px;
    margin: 0 auto 60px;
}

.demo h3 {
    margin-bottom: 16px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #999;
}

.panel-content {
    padding: 24px;
    background: #fff;
    border-radius: 8px;
}

.panel-content h2 {
    margin-bottom: 8px;
    font-size: 20px;
    color: var(--color-heading, #014A99);
}

.panel-content p {
    font-size: 15px;
    line-height: 1.6;
    color: var(--color-text, #4D6B8C);
}

/* ========================================= */
/* [Widget] Tabs — Core                      */
/* ========================================= */

/* Scrollbar hidden */
[data-widget="tabs"] .tabs-controls::-webkit-scrollbar {
    height: 0;
}

[data-widget="tabs"] .tabs-controls::-webkit-scrollbar-track,
[data-widget="tabs"] .tabs-controls::-webkit-scrollbar-thumb {
    display: none;
}

/* Controls wrapper */
[data-widget="tabs"] .tabs-controls {
    margin-bottom: var(--gap-500, 24px);
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: var(--gap-500, 24px);
    overflow-x: auto;
    scrollbar-width: none;
    /* Firefox */
}

/* Individual control button */
[data-widget="tabs"] .tabs-control {
    width: auto;
    position: relative;
    padding-block: var(--gap-200, 8px);
    font-family: var(--ff-heading, sans-serif);
    font-size: var(--fs-300, 13px);
    color: var(--color-text, #4D6B8C);
    text-transform: uppercase;
    cursor: pointer;
    flex-shrink: 0;
    background: none;
    border: none;
    outline: none;
}

/* Underline indicator (default) */
[data-widget="tabs"] .tabs-control[aria-selected]::before {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 0;
    background-color: var(--color-accent, #014A99);
    transition: var(--trans, all 250ms ease-in-out);
}

/* Active state */
[data-widget="tabs"] .tabs-control[aria-selected="true"] {
    color: var(--color-heading, #014A99);
}

[data-widget="tabs"] .tabs-control[aria-selected="true"]::before {
    height: 2px;
}

/* Panel visibility */
[data-widget="tabs"] .tabs-panel[aria-selected="false"] {
    display: none;
}

[data-widget="tabs"] .tabs-panel[aria-selected="true"] {
    display: flex;
}

/* Description text */
[data-widget="tabs"] .tabs-panel .item__description {
    color: var(--color-text, #4D6B8C);
}

/* Desktop: allow buttons to shrink */
@media (min-width: 1025px) {
    [data-widget="tabs"] .tabs-control {
        flex-shrink: 1;
    }
}

/* ========================================= */
/* [Widget] Tabs — Fancy Indicator           */
/* ========================================= */

/* Controls wrapper with pill background */
[data-widget="tabs"] [data-type="fancy"].tabs-controls {
    width: auto;
    position: relative;
    display: inline-flex;
    gap: 0;
    background-color: var(--color-background-alt, #F3F4F8);
    border-radius: var(--br-elements, 32px);
    isolation: isolate;
}

/* Sliding indicator (pseudo-element driven by JS vars) */
[data-widget="tabs"] [data-type="fancy"].tabs-controls::before {
    content: "";
    position: absolute;
    top: 0;
    left: var(--active-button-offset, 0px);
    width: var(--active-button-width, 0px);
    height: 100%;
    background-color: var(--color-accent, #014A99);
    border-radius: var(--br-elements, 32px);
    transition: var(--trans, all 250ms ease-in-out);
    transition-duration: var(--trans-duration-500, 375ms);
    z-index: -1;
}

/* Fancy control buttons */
[data-widget="tabs"] [data-type="fancy"].tabs-controls .tabs-control {
    padding-inline: var(--gap-700, 48px);
    height: var(--height-form-items, 56px);
    font-family: var(--ff-heading-500, sans-serif);
    font-size: var(--fs-h4, 14px);
    color: var(--color-text-accent, #014A99);
    transition-delay: calc(var(--trans-duration-500, 375ms) / 2);
}

[data-widget="tabs"] [data-type="fancy"].tabs-controls .tabs-control[aria-selected="true"] {
    color: var(--color-heading-white, #FFFFFF);
}

/* Remove default underline on fancy controls */
[data-widget="tabs"] [data-type="fancy"].tabs-controls .tabs-control::before {
    content: unset;
}







.hc-section {
    background: #f4f6fb;
    padding: 3.5rem 3rem;
    border-radius: 24px;
    width: 100%;
}

.hc-badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    background: #e0e7ff;
    color: #4338ca;
    padding: 4px 14px;
    border-radius: 20px;
    margin-bottom: 1rem;
}

.hc-heading {
    font-family: 'DM Serif Display', serif;
    font-size: 36px;
    font-weight: 400;
    color: #1a1d2e;
    line-height: 1.2;
    margin-bottom: 0.6rem;
}

.hc-heading em {
    font-style: italic;
    color: #5b6ef5;
}

.hc-sub {
    font-size: 15px;
    color: #6b7280;
    font-weight: 300;
    margin-bottom: 2.5rem;
    max-width: 520px;
}

.hc-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 2.5rem;
}

.hc-card {
    background: #ffffff;
    border-radius: 18px;
    padding: 1.75rem 1.5rem;
    border: 1px solid #e8ecf8;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: default;
}

.hc-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 36px rgba(91, 110, 245, 0.1);
}

.hc-step-num {
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #9ca3af;
    margin-bottom: 1rem;
}

.hc-icon-wrap {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.25rem;
}

.hc-icon-wrap.blue {
    background: #eef0fd;
}

.hc-icon-wrap.teal {
    background: #e8f8f2;
}

.hc-icon-wrap.coral {
    background: #fdf0eb;
}

.hc-icon-wrap svg {
    width: 26px;
    height: 26px;
}

.hc-icon-wrap.blue svg {
    stroke: #5b6ef5;
}

.hc-icon-wrap.teal svg {
    stroke: #10b981;
}

.hc-icon-wrap.coral svg {
    stroke: #f97316;
}

.hc-card h3 {
    font-size: 15px;
    font-weight: 500;
    color: #1a1d2e;
    margin-bottom: 0.6rem;
}

.hc-card p {
    font-size: 13.5px;
    color: #6b7280;
    line-height: 1.7;
    font-weight: 300;
}

.hc-cta-wrap {
    text-align: center;
}

.hc-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #5b6ef5;
    color: #ffffff;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    font-weight: 500;
    padding: 13px 30px;
    border-radius: 50px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.2s ease, transform 0.15s ease;
}

.hc-btn:hover {
    background: #4455e0;
    transform: scale(1.03);
}

.hc-btn svg {
    width: 16px;
    height: 16px;
    stroke: #ffffff;
}

/* Responsive */
@media (max-width: 700px) {
    .hc-grid {
        grid-template-columns: 1fr;
    }

    .hc-section {
        padding: 2.5rem 1.5rem;
    }

    .hc-heading {
        font-size: 28px;
    }
}


.help-block{
    color: red;
    padding: 10px 0px;
}

#email{
    margin-top: 15px;
}


/* responsive */
@media (max-width: 900px) {
    nav {
        padding: 16px 24px;
    }

    .nav-links {
        display: none;
    }

    .hero {
        grid-template-columns: 1fr;
        padding: 32px 24px;
        gap: 32px;
        min-height: auto;
    }

    .ui-scene {
        height: 280px;
    }

    .avatar-circle {
        width: 160px;
        height: 160px;
    }

    .orbit {
        width: 240px;
        height: 240px;
    }
}
</style>
@endpush

@section('content')
@include('includes.header')

<section class="hero">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-7">
                    <div class="hero-left">
                        <div class="hero-img-div">
                            <img src="{{ asset('employee-zone/hero-1.jpeg') }}"
                            alt="Employer Zone" class="hero-img">
                        </div>
                        <div class="hero-text">
                            <h1>Hire the <span>Best Fit</span></h1>
                            <p>
                                Make hiring faster & effortless with Medojob.
                            </p>
                        </div>
                        
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="login-card">
                        <div class="tab-row">
                            <div class="tab active" onclick="switchTab(this)">Employer Login</div>
                        </div>
                        <div class="login-body">
                            <form class="form-horizontal" method="POST" action="{{ route('company.login') }}">
                                {{ csrf_field() }}
                                    <input type="hidden" name="candidate_or_employer" value="employer" />
                            <p class="login-hint">Login with your registered Email &amp; Password</p>

                            <div class="field">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" placeholder="Enter email address" required>
                                @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="field">
                                <label for="password">Password</label>
                                <div class="pwd-wrap">
                                    <input type="password" name="password" id="password" placeholder="Enter your Password" required>
                                    <span class="pwd-eye" onclick="togglePwd()">👁</span>
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                
                            </div>

                            <div class="options-row">
                                <label class="remember">
                                    <input type="checkbox"> Remember Me
                                </label>
                                <a href="{{ route('company.password.request') }}" class="forgot">Forgot Password?</a>
                            </div>

                            <div class="divider">
                                <hr><span>or continue with</span>
                                <hr>
                            </div>
                            <div class="social-row">
                                <a class="social-btn" href="{{ url('login/employer/google')}}">
                                    <svg viewBox="0 0 24 24">
                                        <path
                                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                            fill="#4285F4" />
                                        <path
                                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                            fill="#34A853" />
                                        <path
                                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"
                                            fill="#FBBC05" />
                                        <path
                                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                            fill="#EA4335" />
                                    </svg>
                                    Google
                                </a>
                                <a class="social-btn" href="#">
                                    <svg viewBox="0 0 24 24">
                                        <path
                                            d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"
                                            fill="#0077B5" />
                                    </svg>
                                    LinkedIn
                                </a>
                            </div>

                            <button class="login-btn" type="submit">Login</button>
                            <p class="register-row">Don't have an account? <a href="{{ url('company-register') }}">Register Now</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LEFT -->
        

        <!-- RIGHT: Login Card -->
        

    </section>

    <section class="features">
        <h2>Manage your hiring from start to finish</h2>
        <div class="feature-cards">
            <div class="card">
                <i class="fas fa-magnifying-glass"></i>
                <h3>Post a Job</h3>
                <p>Get started with a healthcare job post that reaches qualified Canadian professionals across Alberta and beyond.</p>
            </div>
            <div class="card">
                <i class="fas fa-rocket"></i>
                <h3>Find Qualified Candidates</h3>
                <p>Use our screening tools to filter by credentials, licensure, specialization, and clinical experience.</p>
            </div>
            <div class="card">
                <i class="fas fa-chart-line"></i>
                <h3>Make Connections</h3>
                <p>Message, invite, and interview candidates directly through Medojob — no additional tools needed.</p>
            </div>
            <div class="card">
                <i class="fas fa-chart-line"></i>
                <h3>Hire Confidently</h3>
                <p>Built-in resources guide you through every step — from credential verification to onboarding.</p>
            </div>
        </div>
    </section>

    <section class="section-tabs">

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h3>Fancy Indicator</h3>
                    <section data-widget="tabs" data-tabs-animate="400, 80">
                        <div class="tabs-controls" data-type="fancy">
                            <button type="button" class="tabs-control" aria-selected="true" aria-controls="fancy-first">Overview</button>
                            <button type="button" class="tabs-control" aria-selected="false" aria-controls="fancy-second">Features</button>
                            <button type="button" class="tabs-control" aria-selected="false" aria-controls="fancy-third">Pricing</button>
                        </div>
                        <div class="tabs-panels">
                            <div class="tabs-panel" id="fancy-first" aria-selected="true">
                                <div class="panel-content">
                                    <p>Fancy tabs use a sliding background indicator that follows the active button.</p>
                                </div>
                            </div>
                            <div class="tabs-panel" id="fancy-second" aria-selected="false">
                                <div class="panel-content">
                                    <p>The indicator width and position are driven by CSS custom properties set via JS.</p>
                                </div>
                            </div>
                            <div class="tabs-panel" id="fancy-third" aria-selected="false">
                                <div class="panel-content">
                                    <p>Works with direction-aware animations and all other features.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        
    </section>

    <section class="hc-section">
  <span class="hc-badge">How it works</span>
  <h2 class="hc-heading">Simple steps to find your<br><em>next opportunity</em></h2>
  <p class="hc-sub">Access healthcare jobs from across Canada through our streamlined search platform</p>
 
  <div class="hc-grid">
 
    <!-- Card 1 -->
    <div class="hc-card">
      <div class="hc-step-num">Step 01</div>
      <div class="hc-icon-wrap blue">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="10" cy="8" r="4"/>
          <path d="M2 20c0-4 3.6-7 8-7s8 3 8 7"/>
          <circle cx="18.5" cy="7.5" r="2.5"/>
          <line x1="18.5" y1="5.5" x2="18.5" y2="6.5"/>
          <line x1="18.5" y1="8.5" x2="18.5" y2="9.5"/>
          <line x1="16.5" y1="7.5" x2="17.5" y2="7.5"/>
          <line x1="19.5" y1="7.5" x2="20.5" y2="7.5"/>
        </svg>
      </div>
      <h3>Set up your free profile</h3>
      <p>Create your professional profile with your healthcare specialty, experience level, and location preferences to personalise your job search experience.</p>
    </div>
 
    <!-- Card 2 -->
    <div class="hc-card">
      <div class="hc-step-num">Step 02</div>
      <div class="hc-icon-wrap teal">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="4" width="14" height="12" rx="2"/>
          <line x1="7" y1="8" x2="13" y2="8"/>
          <line x1="7" y1="11" x2="11" y2="11"/>
          <circle cx="19" cy="17" r="3"/>
          <line x1="21.2" y1="19.2" x2="23" y2="21"/>
        </svg>
      </div>
      <h3>Browse healthcare opportunities</h3>
      <p>Search jobs from hospitals, clinics, and healthcare facilities across Canada. Filter by location, specialty, experience level, and posting date.</p>
    </div>
 
    <!-- Card 3 -->
    <div class="hc-card">
      <div class="hc-step-num">Step 03</div>
      <div class="hc-icon-wrap coral">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
          <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <h3>Connect with employers</h3>
      <p>Apply directly with healthcare employers through their preferred application process. Track jobs you're interested in through your profile.</p>
    </div>
 
  </div>
 
  <div class="hc-cta-wrap">
    <a href="#" class="hc-btn">
      Get started today
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="5" y1="12" x2="19" y2="12"/>
        <polyline points="13 6 19 12 13 18"/>
      </svg>
    </a>
  </div>
</section>

    <section class="section-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="img-div">
                        <img src="{{ asset('employee-zone/hero-1.jpeg') }}" alt="Employer Zone" class="section-img">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="content">
                        <div class="content-div">
                            <h2>Streamline Your Hiring Process</h2>
                            <p>Our platform provides everything you need to manage your hiring from start to finish, ensuring a seamless experience for both employers and candidates.</p>
                            <a href="#" class="learn-more">Learn More</a>
                        </div>
                    </div>
                    
                    
                </div>
            </div>
        </div>
    </section>

    <section class="section-3">
        <div class="container-fluid">
            <div class="row">
                
                <div class="col-md-6">
                    <div class="content">
                        <div class="content-div">
                            <h2>Streamline Your Hiring Process</h2>
                            <p>Our platform provides everything you need to manage your hiring from start to finish, ensuring a seamless experience for both employers and candidates.</p>
                            <a href="#" class="learn-more">Learn More</a>
                        </div>
                    </div>
                    
                    
                </div>
                <div class="col-md-6">
                    <div class="img-div">
                        <img src="{{ asset('employee-zone/hero-1.jpeg') }}" alt="Employer Zone" class="section-img">
                    </div>
                </div>
            </div>
        </div>
    </section>



@include('includes.footer')
@push('scripts')
<script>
    function init__tabs(selector = '[data-widget="tabs"]'){
	let tabs = document.querySelectorAll(selector);
	if(!tabs.length) return;

	// Accessibility: skip animations when the user prefers reduced motion (OS-level setting).
	const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	// Helper for fancy indicator
	function updateFancyIndicator(tabsRoot, tab){
		const width  = tab.offsetWidth;
		const offset = tab.offsetLeft;

		tabsRoot.style.setProperty('--active-button-width', width + 'px');
		tabsRoot.style.setProperty('--active-button-offset', offset + 'px');
	}

	// Cubic-bezier evaluator (matches CSS easing functions exactly)
	function cubicBezier(p1x, p1y, p2x, p2y){
		return function(t){
			let start = 0, end = 1;
			for(let i = 0; i < 20; i++){
				const mid = (start + end) / 2;
				const mt  = 1 - mid;
				const x   = 3 * p1x * mt * mt * mid + 3 * p2x * mt * mid * mid + mid * mid * mid;
				if(x < t) start = mid;
				else end = mid;
			}
			const mid = (start + end) / 2;
			const mt  = 1 - mid;
			return 3 * p1y * mt * mt * mid + 3 * p2y * mt * mid * mid + mid * mid * mid;
		};
	}

	const cssEaseOut = cubicBezier(0, 0, 0.58, 1);

	// Helper for tracking active tab into view
	function scrollTabIntoView(container, button, duration){
		if(!container || !button) return;

		const containerRect = container.getBoundingClientRect();
		const buttonRect    = button.getBoundingClientRect();

		const buttonCenter    = buttonRect.left + buttonRect.width / 2;
		const containerCenter = containerRect.left + container.clientWidth / 2;

		const maxScroll    = container.scrollWidth - container.clientWidth;
		const targetScroll = Math.max(0, Math.min(container.scrollLeft + (buttonCenter - containerCenter), maxScroll));

		if(container._scrollRaf) cancelAnimationFrame(container._scrollRaf);

		if(duration){
			const startScroll = container.scrollLeft;
			const distance    = targetScroll - startScroll;
			const startTime   = performance.now();

			function step(currentTime){
				const elapsed  = currentTime - startTime;
				const progress = Math.min(elapsed / duration, 1);
				const eased    = cssEaseOut(progress);

				container.scrollLeft = startScroll + distance * eased;

				if(progress < 1) container._scrollRaf = requestAnimationFrame(step);
				else container._scrollRaf = null;
			}

			container._scrollRaf = requestAnimationFrame(step);
		} else {
			container.scrollTo({
				left: targetScroll,
				behavior: 'smooth'
			});
		}
	}

	// Helper for scroll-to-target (standalone version, no config dependency)
	function scrollToTarget(id, gap = 20){
		let target = document.querySelector(id);
		if(!target) return;
		let offsetPosition = target.getBoundingClientRect().top + window.scrollY - gap;
		window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
	}

	tabs.forEach(item => {
		let tabs_controlers = item.querySelectorAll('.tabs-control');
		let tabs_scrollTop  = item.dataset.scrollTop === 'true';
		let tabs_autoplay   = item.dataset.tabsAutoplay === 'true';
		let tab_index = 0;

		let tabs_imageSlider;
		let tabs_withImages = item.dataset.type === 'tabs-with-images';
		if(tabs_withImages){
			tabs_imageSlider = item.closest('[data-component="tabs"]')?.querySelector('[data-widget="swiper"] .swiper');
		}

		// Fancy tabs
		let tabsControlsWrap = item.querySelector('.tabs-controls');
		let isFancy      = tabsControlsWrap?.getAttribute('data-type') === 'fancy';
		let hasTrack     = item.hasAttribute('data-tabs-track') && item.getAttribute('data-tabs-track') !== 'false';
		let animValue    = item.getAttribute('data-tabs-animate');
		let hasAnimation = animValue !== null && animValue !== 'false';
		let animDuration = 500;
		let animOffset   = 100;
		if(hasAnimation && animValue !== '' && animValue !== 'true'){
			const parts = animValue.split(',');
			const d = parseInt(parts[0]?.trim());
			const o = parseInt(parts[1]?.trim());
			if(!isNaN(d) && !isNaN(o)){
				animDuration = d;
				animOffset   = o;
			} else {
				console.log(`[Tabs] Invalid data-tabs-animate value: "${animValue}". Expected format: "duration, offset" (e.g. "350, 100"). Falling back to defaults: ${animDuration}ms duration, ${animOffset}px offset.`);
			}
		} else if(hasAnimation){
			console.log(`[Tabs] data-tabs-animate using defaults: ${animDuration}ms duration, ${animOffset}px offset. You can also pass custom values: data-tabs-animate="duration, offset" (e.g. "350, 100").`);
		}

		// Init fancy indicator (1st tab)
		if(isFancy && tabs_controlers.length){
			updateFancyIndicator(item, tabs_controlers[0]);
		}

		tabs_controlers.forEach((tab, idx) => {

			tab.addEventListener('click', (e) => {

				let dft_tab = e.currentTarget;
				let dft_tab__aria_controls = dft_tab.getAttribute('aria-controls');
				let prevIndex = tab_index;
				tab_index = idx;

				// Reset Previous Active Tab
				let tab_prev_active = item.querySelector('.tabs-control[aria-selected="true"]');
				tab_prev_active?.setAttribute('aria-selected','false');

				// Activate This Tab
				dft_tab.setAttribute('aria-selected','true');

				// Fancy indicator update
				if(isFancy){
					updateFancyIndicator(item, dft_tab);
				}

				// Scroll active tab into view
				if(hasTrack){
					scrollTabIntoView(tabsControlsWrap, dft_tab, hasAnimation && !reduceMotion ? animDuration : null);
				}

				// Reset Previous Active Panel
				let panel_prev_active = item.querySelector('.tabs-panel[aria-selected="true"]');
				panel_prev_active?.setAttribute('aria-selected','false');

				// Activate This Panel
				let dft_panel = item.querySelector(`#${dft_tab__aria_controls}`);
				dft_panel?.setAttribute('aria-selected','true');

				// Direction-aware panel animation
				if(hasAnimation && !reduceMotion && dft_panel && prevIndex !== idx){
					const direction = idx > prevIndex ? 1 : -1;
					dft_panel.animate([
						{ opacity: 0, transform: `translateX(${animOffset * direction}px)` },
						{ opacity: 1, transform: 'translateX(0)' }
					], {
						duration: animDuration,
						easing: 'ease-out'
					});
				}

				// Scroll Top on Change Tab
				if(tabs_scrollTop){
					let tabs_controls_height = item.querySelector('.tabs-controls').offsetHeight;
					let offset = tabs_controls_height + 40;
					scrollToTarget(`#${dft_tab__aria_controls}`, offset);
				}

				// Tabs with synchronized images
				if(tabs_imageSlider){
					tabs_imageSlider.swiper.slideTo(idx);
					ScrollTrigger.refresh();
				}
			});
		});

		// Autoplay
		if(tabs_autoplay){

			let speed = item.dataset.autoplaySpeed ? item.dataset.autoplaySpeed : 5000;
			let paused = false;
			let timer;

			['mouseenter', 'focus'].forEach(evt =>
				item.addEventListener(evt, () => paused = true)
			);
			['mouseleave', 'blur'].forEach(evt =>
				item.addEventListener(evt, () => paused = false)
			);

			let observer = new IntersectionObserver(entries => {
				entries.forEach(entry => {

					if(entry.isIntersecting){
						timer = setInterval(() => {
							if(!paused){
								tab_index++;
								if(tab_index === tabs_controlers.length){
									tab_index = 0;
								}
								tabs_controlers[tab_index].click();
							}
						}, speed);
					} else {
						clearInterval(timer);
					}
				});
			});

			observer.observe(item);
		}

		// Recalculate fancy indicator on resize
		if(isFancy){
			window.addEventListener('resize', () => {
				let activeTab = item.querySelector('.tabs-control[aria-selected="true"]');
				if(activeTab){
					updateFancyIndicator(item, activeTab);
				}
			});
		}

	});
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
	init__tabs();
});
</script>
@endpush
@endsection
