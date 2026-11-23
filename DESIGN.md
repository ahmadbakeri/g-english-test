# G-English Design System (`DESIGN.md`)

Single source of truth for G-English v2 visual craft, aligned with `docs/DESIGN_SYSTEM.md`.

## 1. Brand & Semantic Tokens
- **Primary (`Genza Blue`):** `#0a4282` (`--genza-primary`) — primary actions, active states, focus rings, table/card accents.
- **Secondary (`Genza Gold`):** `#fbb900` (`--genza-secondary`) — high-contrast accent badges, dark-mode active indicators, secondary CTAs (`#0a4282` text on `#fbb900`).
- **Semantic:**
  - `success`: Emerald (`#10b981`)
  - `info`: Sky (`#0ea5e9`)
  - `warning`: Amber (`#f59e0b`)
  - `danger`: Rose (`#f43f5e`)
  - `gray`: Slate (`#64748b` / `#0f172a`)

## 2. Typography
- **UI & Display:** `'Outfit', sans-serif` (weights 400–900).
- **Timers & Codes:** `ui-monospace, monospace` with `font-variant-numeric: tabular-nums`.
- **Table Numeric Cells:** `font-variant-numeric: tabular-nums`.

## 3. Surfaces & Depth
- **Light Mode:** `#f8fafc` page canvas, `rgba(255, 255, 255, 0.92)` cards/tables, `1px solid rgba(15, 23, 42, 0.08)` borders.
- **Dark Mode:** `#0f172a` page canvas, `rgba(15, 23, 42, 0.78)`–`rgba(30, 41, 59, 0.85)` cards/tables, `1px solid rgba(255, 255, 255, 0.09)` borders.
- **Interactive Controls:** Pill buttons (`border-radius: 9999px`), pill badges, 2px accessible focus-visible rings, and themed text selection/scrollbars.
