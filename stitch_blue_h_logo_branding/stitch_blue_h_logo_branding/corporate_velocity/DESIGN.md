---
name: Corporate Velocity
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#424656'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#727687'
  outline-variant: '#c2c6d8'
  surface-tint: '#0054d6'
  primary: '#0050cb'
  on-primary: '#ffffff'
  primary-container: '#0066ff'
  on-primary-container: '#f8f7ff'
  inverse-primary: '#b3c5ff'
  secondary: '#545f73'
  on-secondary: '#ffffff'
  secondary-container: '#d5e0f8'
  on-secondary-container: '#586377'
  tertiary: '#006645'
  on-tertiary: '#ffffff'
  tertiary-container: '#008259'
  on-tertiary-container: '#e1ffec'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dae1ff'
  primary-fixed-dim: '#b3c5ff'
  on-primary-fixed: '#001849'
  on-primary-fixed-variant: '#003fa4'
  secondary-fixed: '#d8e3fb'
  secondary-fixed-dim: '#bcc7de'
  on-secondary-fixed: '#111c2d'
  on-secondary-fixed-variant: '#3c475a'
  tertiary-fixed: '#6ffbbe'
  tertiary-fixed-dim: '#4edea3'
  on-tertiary-fixed: '#002113'
  on-tertiary-fixed-variant: '#005236'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
typography:
  display-lg:
    fontFamily: Hanken Grotesk
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Hanken Grotesk
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  title-sm:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 22px
  body-sm:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 20px
  label-uppercase:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
  table-header:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '600'
    lineHeight: 18px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 4px
  xs: 8px
  sm: 12px
  md: 16px
  lg: 24px
  xl: 32px
  container-max: 1440px
  sidebar-width: 260px
---

## Brand & Style

The design system is engineered for efficiency, clarity, and institutional trust within the HR management space. It targets HR administrators and executives who require a high-density, high-utility interface that remains legible during long work sessions.

The aesthetic follows a **Corporate / Modern** approach. It prioritizes information hierarchy through a disciplined use of white space, subtle tonal layering, and the primary brand blue to guide user action. The visual language is structured and professional, leaning on clean lines and a "software-as-a-service" (SaaS) purity that eliminates unnecessary decorative elements in favor of functional clarity. The goal is to evoke a sense of reliability and organized precision.

## Colors

This color palette is anchored by the vibrant primary blue (#0066FF), used strategically for primary actions, active navigation states, and brand identifiers. 

- **Primary Blue:** Used for high-priority buttons, selection states, and focus indicators.
- **Secondary (Slate):** Employed for the side navigation and primary headers to provide a grounded, high-contrast structural frame.
- **Surface & Backgrounds:** A clean, cool-grey background ensures cards and content areas pop.
- **Semantic Colors:** Emerald (#10B981) for success/positive growth, Rose (#F43F5E) for errors or negative data alerts, and Amber (#F59E0B) for warnings.

## Typography

The typography system utilizes a dual-font approach to balance character with utility. **Hanken Grotesk** is used for headlines and dashboard titles to provide a modern, sharp tech-forward feel. **Inter** is the workhorse for body text, tables, and labels due to its exceptional legibility at small sizes and high information density.

Scale headlines down on mobile devices; for instance, `display-lg` (32px) should reflow to 24px on mobile viewports to ensure text does not wrap aggressively. All data entry fields and table rows use `body-md` or `body-sm` to maximize visible content.

## Layout & Spacing

The design system employs a **Fluid Grid** model with a standard 12-column layout for desktop. 

- **Sidebar Layout:** A fixed-width left navigation (260px) is the primary anchor. 
- **Gutters & Margins:** Use a 24px gutter between cards and a 32px margin for the main content container.
- **Density:** The system uses "Compact-Modern" spacing. Vertical padding in tables is kept to 12px to allow more rows to be visible above the fold.
- **Breakpoints:** 
  - Mobile (<768px): Sidebar collapses to a hamburger menu, cards stack vertically, 16px margins.
  - Tablet (768px - 1024px): Sidebar can be minimized to icons, 2-column card layouts.
  - Desktop (>1024px): Full 12-column availability with fixed sidebar.

## Elevation & Depth

Visual hierarchy is established through **Tonal Layers** and extremely soft **Ambient Shadows**.

1.  **Level 0 (Background):** The base canvas uses the `background_main` color (#F8FAFC).
2.  **Level 1 (Cards/Surface):** Pure white (#FFFFFF) with a 1px border (#E2E8F0). This is the default state for dashboard widgets and table containers.
3.  **Level 2 (Dropdowns/Modals):** These elements use a diffused shadow (0px 10px 15px -3px rgba(0, 0, 0, 0.05)) to suggest they are floating above the main interface.

Avoid heavy shadows or dark outlines. Depth should feel natural and light, focusing on separation through subtle background-color shifts rather than high-contrast shadows.

## Shapes

The design system adopts a **Rounded** (0.5rem) shape language. This softens the professional aesthetic, making the HR tool feel modern and accessible rather than "legacy" or rigid.

- **Standard Buttons & Inputs:** 8px (0.5rem) corner radius.
- **Large Cards:** 12px (0.75rem) corner radius for a distinct container feel.
- **Status Chips:** Full pill-shape (100px) to distinguish them from interactive buttons.
- **Selection Indicators:** Use a vertical bar (4px width) with rounded ends on the left side of active sidebar menu items.

## Components

### Side Navigation
The sidebar uses the `secondary` slate color. Active items should feature a subtle blue background tint (e.g., #0066FF at 10% opacity) and a primary blue accent bar on the left edge. Icons should be line-art style with a 2px stroke width.

### Dashboard Cards
Cards are the primary container for data visualization. They must include a consistent header section with a `title-sm` and an optional "More" action menu. Top-level metric cards (e.g., "Total Employees") should feature a colored icon circle on the right side to provide visual shorthand for the metric type.

### Tables
Tables are the heart of the system.
- **Header:** Light grey background (#F1F5F9), uppercase labels, and sort icons.
- **Rows:** 1px bottom border only. Hover state should trigger a very subtle blue tint.
- **Actions:** Use small, square-ish buttons (32x32px) for "Edit" and "Delete" actions within rows to conserve space.

### Buttons
- **Primary:** Solid #0066FF with white text.
- **Secondary:** White background with #E2E8F0 border and slate text.
- **Danger:** Solid #F43F5E for destructive actions like "Delete Member."

### Input Fields
Inputs use a white background, 1px border, and a 4px blue glow/ring on focus. Placeholder text should be `neutral_color_hex` at 50% opacity.