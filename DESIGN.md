---
name: G-English
description: Integrated English Academy & Brain Champion Competition Platform
colors:
  primary: "#0a4282"
  secondary: "#fbb900"
  neutral-bg: "#f8fafc"
  neutral-bg-dark: "#0f172a"
  surface-light: "#ffffff"
  surface-dark: "#1e293b"
  text-primary: "#0f172a"
  text-muted: "#64748b"
  success: "#10b981"
  info: "#0ea5e9"
  warning: "#f59e0b"
  danger: "#f43f5e"
typography:
  display:
    fontFamily: "Outfit, sans-serif"
    fontSize: "1.875rem"
    fontWeight: 800
    lineHeight: 1.2
    letterSpacing: "-0.025em"
  body:
    fontFamily: "Outfit, sans-serif"
    fontSize: "0.95rem"
    fontWeight: 500
    lineHeight: 1.5
    letterSpacing: "0em"
rounded:
  sm: "0.75rem"
  md: "1rem"
  lg: "1.25rem"
  pill: "9999px"
spacing:
  sm: "0.75rem"
  md: "1.25rem"
  lg: "1.85rem"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "#ffffff"
    rounded: "{rounded.pill}"
    padding: "0.65rem 1.35rem"
  button-secondary:
    backgroundColor: "{colors.secondary}"
    textColor: "{colors.primary}"
    rounded: "{rounded.pill}"
    padding: "0.65rem 1.35rem"
---

# Design System

## Overview
G-English uses a unified **Genza Blue (`#0a4282`)** and **Genza Gold (`#fbb900`)** design system across 6 Filament panels (`admin`, `tutor`, `student`, `parent`, `school`, `agency`), public registration pages (`/brain-champion`), and the fullscreen CBT exam engine.

## Colors
- **Primary (`#0a4282`):** Primary buttons, active navigation indicators, table headers/accents, focus rings, and text selection (light mode).
- **Secondary (`#fbb900`):** High-contrast accent badges, secondary CTAs (`#0a4282` text on `#fbb900`), dark-mode active tab borders, and dark-mode focus rings.
- **Semantic:** Emerald (`#10b981`) for success/present/paid, Sky (`#0ea5e9`) for info, Amber (`#f59e0b`) for warnings/doubt, Rose (`#f43f5e`) for errors/violations/locked states.
- **Surfaces:** Light mode uses `#f8fafc` canvas with `rgba(255, 255, 255, 0.92)` cards; dark mode uses `#0f172a` canvas with `rgba(30, 41, 59, 0.85)` cards and `rgba(255, 255, 255, 0.09)` borders.

## Typography
- **Font Family:** `Outfit` (weights 400–900) for all Filament panels, landing pages, and CBT UI.
- **Numeric & Monospace:** `ui-monospace, monospace` with `font-variant-numeric: tabular-nums` for CBT timers, competition codes (`BC-K4-001`), and numeric table columns.

## Layout
- **Panels (Operate Mode):** Standard Filament sidebar + topbar with `->spa()`, responsive horizontal overflow (`overflow-x: auto`) on `.fi-ta-ctn`, and sticky summary panels where needed.
- **Public Surfaces (Persuade Mode):** Centered `max-w-7xl` / `max-w-5xl` responsive grids (`grid-cols-1 md:grid-cols-2` / `md:grid-cols-3`).
- **CBT Fullscreen Shell:** `100vw` × `100vh` isolated fullscreen container hiding all Filament navigation chrome, pairing a sticky `#0a4282` topbar with a 2-column question + answer-mapping grid (or full-height Google Form iframe).

## Elevation & Depth
- Soft blurred shadows (`0 10px 25px -5px rgba(0, 0, 0, 0.06)`) paired with crisp `1px` borders; no hard-offset block shadows.
- Avoid `transform` or `backdrop-filter` on `.fi-section` / `.fi-ta-record` containers that host Floating UI dropdowns (`x-anchor`).

## Shapes
- **Buttons & Badges:** Pill (`9999px`).
- **Cards, Sections, Modals, Tables:** `1rem`–`1.25rem` (`rounded-2xl`).
- **Inputs & Option Cards:** `0.75rem`–`1rem`.

## Components
- **Primary Button (`.fi-btn-color-primary`):** `#0a4282` background, `#ffffff` text, `9999px` radius.
- **Secondary Button (`.fi-btn-color-secondary`):** `#fbb900` background, `#0a4282` text (high contrast), `9999px` radius.
- **Tables (`.fi-ta-table`):** Uppercase `0.75rem` headers, hover row elevation, tabular numeric cells, and pill action buttons.
- **Browser Surfaces:** Themed `::selection`, `caret-color`, `2px` `:focus-visible` outlines, and thin custom scrollbars.

## Do's and Don'ts
- **Do:** Keep `#0a4282` text on `#fbb900` buttons/badges for ≥4.5:1 contrast.
- **Do:** Use `__('...')` for all UI strings and test both Light and `.dark` modes.
- **Don't:** Use native browser `confirm()` / `alert()` inside fullscreen CBT flows.
- **Don't:** Add `backdrop-filter` or `transform` to `.fi-section` or `.fi-ta-record` (breaks Floating UI select popovers).
