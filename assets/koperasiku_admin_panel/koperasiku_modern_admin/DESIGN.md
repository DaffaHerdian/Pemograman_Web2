---
name: KoperasiKu Modern Admin
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
  on-surface-variant: '#3e4947'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#6e7977'
  outline-variant: '#bdc9c6'
  surface-tint: '#006a63'
  primary: '#005c55'
  on-primary: '#ffffff'
  primary-container: '#0f766e'
  on-primary-container: '#a3faef'
  inverse-primary: '#80d5cb'
  secondary: '#006b5f'
  on-secondary: '#ffffff'
  secondary-container: '#6df5e1'
  on-secondary-container: '#006f64'
  tertiary: '#2d5951'
  on-tertiary: '#ffffff'
  tertiary-container: '#467169'
  on-tertiary-container: '#c5f3ea'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#9cf2e8'
  primary-fixed-dim: '#80d5cb'
  on-primary-fixed: '#00201d'
  on-primary-fixed-variant: '#00504a'
  secondary-fixed: '#71f8e4'
  secondary-fixed-dim: '#4fdbc8'
  on-secondary-fixed: '#00201c'
  on-secondary-fixed-variant: '#005048'
  tertiary-fixed: '#bdece2'
  tertiary-fixed-dim: '#a2d0c6'
  on-tertiary-fixed: '#00201c'
  on-tertiary-fixed-variant: '#224e47'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
typography:
  display-lg:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
  headline-lg:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-md:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  headline-sm:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
  label-lg:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
  label-sm:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '600'
    lineHeight: 14px
    letterSpacing: 0.5px
  currency-display:
    fontFamily: Inter
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1.5rem
  margin: 2rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2rem
---

## Brand & Style

This design system establishes a reliable, highly legible, and structured administrative environment tailored for savings and loan cooperative management (*Koperasi Simpan Pinjam*). Operating in an academic and institutional financial context, the system balances the rigorous utility of Material Design with modern SaaS clarity.

The visual style blends **Corporate / Modern** discipline with **Tonal Layering**:
- **Clarity & Institutional Trust:** High-contrast text legibility, explicit tabular alignment, and calm financial teal tones instill user confidence during complex financial auditing and record-keeping.
- **Academic Rigor:** Structured, consistent, and predictable interface patterns meeting Web Programming II standards—prioritizing semantic HTML structure, strict spacing rhythms, and accessible state indications over decorative clutter.
- **Composed Density:** Balanced information density suited for data-heavy views (loan disbursement schedules, member ledgers, transaction records) without visual fatigue.

## Colors

The color system delivers an authoritative financial identity while providing clear feedback states for ledger balances and approval workflows.

### Surface & Canvas
- **App Background (`#F8FAFC`):** Slate 50 provides a cool, low-strain backdrop that sets off pure white elevated surfaces.
- **Surface (`#FFFFFF`):** Base canvas for dashboard cards, modal dialogs, data tables, and elevated dropdowns.
- **Border (`#E2E8F0`):** Slate 200 defines clean component boundaries, table row separators, and structural dividers.

### Primary Brand Spectrum
- **Primary (`#0F766E` / Teal 700):** Anchor tone for active navigation states, primary buttons, major summary metrics, and focused interactive controls.
- **Primary Container / Light (`#CCFBF1` / Teal 50):** Soft highlight background for selected table rows, active sidebar items, and subtle badges.
- **Secondary (`#14B8A6` / Teal 500):** Accent tone for interactive highlights, progress indicators, and actionable secondary states.

### Typography & Content
- **Main Text (`#0F172A` / Slate 900):** Maximum contrast for primary metrics, headings, and data values.
- **Secondary Text (`#64748B` / Slate 500):** Subdued contrast for field labels, metadata, timestamps, and column headers.

### Semantic State Colors
- **Success (`#16A34A`):** Paid installments, approved loans, active member status. Pair with `#DCFCE7` (Green 50) for status badges.
- **Warning (`#F59E0B`):** Pending approvals, grace period dues. Pair with `#FEF3C7` (Amber 50) for status badges.
- **Danger (`#DC2626`):** Defaulted payments, rejected loans, overdue notices. Pair with `#FEE2E2` (Red 50) for status badges.
- **Info (`#2563EB`):** System notifications, ledger audit entries. Pair with `#DBEAFE` (Blue 50) for status badges.

## Typography

The type system uses **Inter** across all UI tiers. Because this system manages loans, savings yields, and ledger calculations, numeric legibility is critical.

- **Tabular Figures:** Apply CSS `font-variant-numeric: tabular-nums` or `font-feature-settings: "tnum"` globally to all tables, summary cards, and financial input fields to ensure numbers align neatly across columns.
- **Hierarchy Rules:**
  - `display-lg` / `currency-display`: Reserved for top-level financial KPI metrics (e.g., *Total Simpanan*, *Sisa Kas*).
  - `headline-lg` and `headline-md`: Used exclusively for page titles, section headers, and modal headers.
  - `headline-sm`: Standard table card headers and collapsible panel titles.
  - `body-md` and `body-sm`: Default for data tables, descriptions, and helper hints.
  - `label-sm`: Micro-copy, badge status tags, and uppercase table column headers with increased tracking.

## Layout & Spacing

The layout is built upon an 8-point base grid and a responsive admin application shell:

### Layout Shell Architecture
- **Collapsible Sidebar Navigation:**
  - Expanded width: `260px` (standard desktop state showing icon, text label, and expand arrow).
  - Collapsed width: `72px` (icon-only mode with floating tooltips).
  - Background: `#FFFFFF` surface bounded by a continuous `1px solid #E2E8F0` right border.
- **Top Header Bar:**
  - Fixed height: `64px`.
  - Spans the viewport width adjacent to the sidebar, hosting global search, quick-action triggers, notifications, and administrator profile menus.
- **Main Canvas:**
  - Background: `#F8FAFC`.
  - Content fluidly conforms to `max-width: 1440px` with `margin: 2rem` padding on desktop.

### Breakpoints & Adaptive Reflow
- **Desktop (≥ 1024px):** Full multi-column dashboard. 12-column grid layout for KPI metric cards (span 3 or 4 columns) and charts/tables (span 8/4 or 6/6 columns).
- **Tablet (768px – 1023px):** Sidebar auto-collapses to rail mode (`72px`) or overlay drawer. Grid adapts to 6 columns; metric cards reflow to 2 columns each.
- **Mobile (< 768px):** Sidebar fully hidden behind an off-canvas drawer triggered by a header hamburger icon. Grid collapses to single-column stacking with horizontal scroll preserved for data tables via wrapping containers. Outer margin reduces to `1rem`.

## Elevation & Depth

This design system uses subtle Material Design-inspired elevation combined with crisp surface containment (`#E2E8F0` borders). This maintains a professional dashboard look without heavy drop-shadow clutter.

### Elevation Levels
- **Level 0 (Flat Canvas / Cards):**
  - Used for base dashboard panels and content cards.
  - `box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.05), 0 1px 2px -1px rgba(15, 23, 42, 0.05);`
  - Border: `1px solid #E2E8F0`.
- **Level 1 (Hover / Interactive Cards):**
  - Applied when hovering over actionable table rows, clickable cards, or floating action buttons.
  - `box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.07), 0 2px 4px -2px rgba(15, 23, 42, 0.05);`
- **Level 2 (Popovers, Dropdowns, Datepickers):**
  - Floating interface overlays that require separation from the card surface.
  - `box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.08), 0 4px 6px -4px rgba(15, 23, 42, 0.04);`
- **Level 3 (Modal Dialogs & Side Drawers):**
  - Loan approval modals, member registration dialogs.
  - `box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 8px 10px -6px rgba(15, 23, 42, 0.05);`
  - Paired with an overlay backdrop of `rgba(15, 23, 42, 0.4)`.

## Shapes

The design system implements balanced geometry:
- **Base Components (Inputs, Buttons, Badges):** `rounded-md` (`0.375rem` / 6px) to `rounded` (`0.5rem` / 8px) for crisp control outlines.
- **Card Containers & Panels:** `rounded-xl` (`0.75rem` to `1rem` / 12px - 16px) offering gentle curvature that softens the dense accounting and data views.
- **Status Badges & Avatar Containers:** `rounded-full` (pill shape) for distinct separation from input fields and structural boxes.

## Components

### 1. Buttons
- **Primary Button:** Background `#0F766E`, text `#FFFFFF`, font-weight 500. On hover: `#0D645D`. Focus: outline with 2px offset in `#14B8A6`.
- **Secondary / Outline Button:** Background `#FFFFFF`, border `1px solid #E2E8F0`, text `#0F172A`. On hover: `#F8FAFC` background with border `#CBD5E1`.
- **Ghost / Action Button:** No border or background. Text `#64748B`. On hover: `#CCFBF1` background with `#0F766E` text. Used for table action icons (edit, view, delete).
- **Heights:** Default `40px`, compact (in-table) `32px`, large form submit `48px`.

### 2. Form Inputs & Selects
- **Text Inputs & Dropdowns:** Height `40px`, background `#FFFFFF`, border `1px solid #E2E8F0`, border-radius `0.5rem`, padding `0 0.75rem`. Text: `#0F172A`, placeholder: `#94A3B8`.
- **Focus State:** Border color `#0F766E`, box-shadow `0 0 0 3px rgba(15, 118, 110, 0.15)`.
- **Form Group:** Standard vertical stack with label (`label-lg`, `#0F172A`), control, and optional helper text (`body-sm`, `#64748B`). Mandatory fields marked with `#DC2626` asterisk.
- **Currency Field Prefix:** Integrated neutral addon block (`Rp`) styled in `#F1F5F9` background and `#64748B` text, seamlessly attached to the left of the input.

### 3. Cards & Panels
- **Container Structure:** Background `#FFFFFF`, border `1px solid #E2E8F0`, border-radius `rounded-xl` (`1rem`), padding `1.5rem`.
- **Header Slot:** Bottom border `1px solid #F1F5F9`, padding bottom `1rem`, flex row hosting the card title (`headline-sm`) and contextual actions (e.g., *Filter*, *Export CSV*).

### 4. Status Badges
- Compact pill design: `padding: 0.25rem 0.625rem`, border-radius `9999px`, font-size `12px`, font-weight `600`.
- **Lunas / Disetujui (Success):** Background `#DCFCE7`, text `#16A34A`.
- **Menunggu Verifikasi (Pending):** Background `#FEF3C7`, text `#B45309`.
- **Ditolak / Terlambat (Danger):** Background `#FEE2E2`, text `#DC2626`.
- **Draft / Informasi (Info):** Background `#DBEAFE`, text `#1E40AF`.

### 5. Data Tables (Ledger & Loan Schedules)
- **Header:** Background `#F8FAFC`, text uppercase `label-sm` (`#64748B`), border-bottom `1px solid #E2E8F0`, padding `0.75rem 1rem`.
- **Body Rows:** Alternating hover state `#F8FAFC`, transition background `150ms ease`. Border-bottom `1px solid #F1F5F9`. Cell padding `0.875rem 1rem`.
- **Numeric Alignment:** Right-align all currency (`Rp`) and percentage columns, ensuring tabular figures are enforced.

### 6. Checkboxes & Radio Controls
- Base size `18px`, border `1.5px solid #CBD5E1`, border-radius `4px` (checkbox) or `50%` (radio).
- Active checked state: Background `#0F766E`, border-color `#0F766E`, check icon in pure white `#FFFFFF`.