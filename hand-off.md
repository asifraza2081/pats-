# PATS (Pakistan Application Testing System) - Project Hand-off

## Project Overview
PATS is a Laravel 11 based web application designed for recruitment, applicant tracking, and testing management (Test Centers, Batches, Candidates, Payments, Roll Numbers, and Results). The front-end is heavily dependent on **Blade templates** and the **Tabler UI** framework (built on Bootstrap 5).

## Current State & Recent Accomplishments
1. **Applicant Geographic Heatmap Fix:**
   - Completely removed the broken `jsVectorMap` dependency on the admin dashboard (`admin/dashboard.blade.php`).
   - Implemented a highly optimized, zero-dependency **pure inline SVG** map of Pakistan.
   - Re-generated map coordinates from Highcharts GeoJSON via `convert_geojson.php`.
   - The map accurately reflects official Pakistani borders: FATA is merged into Khyber Pakhtunkhwa (PK-KP), and Indian Illegally Occupied Jammu & Kashmir (IIOJK / PK-II) is properly modeled with a dashed "disputed" border and dedicated tooltips.
   - Interactivity (hover events, color interpolation for application metrics, floating tooltips) is handled via vanilla JavaScript inside the Blade view.
2. **Tabler UI Migration & Audit:**
   - The application layout successfully uses Tabler's grid (`page`, `page-wrapper`, `container-xl`).
   - **Zero** legacy Bootstrap 4 classes (`pl-3`, `float-left`) remain in active views.

## Immediate Next Steps (The Objective)
The current goal is to achieve **100% structural and functional parity with official Tabler documentation**. 
A comprehensive audit has just been completed, revealing the following tasks for the next phase:

1. **Purge `pats-core.css`:**
   - The file `public/assets/css/pats-core.css` contains massive custom CSS overrides (`--pats-teal`, `.card-pats`, `.notice-board`, hardcoded gradients, custom shadows) that bypass Tabler's built-in functionality.
   - **Action:** Remove these parallel classes. Replace instances of `.card-pats` with native `.card` across all Blade views. Migrate necessary theming directly to SCSS variable overrides if a build step exists, or rely solely on Tabler's utility classes.
2. **Eliminate Inline Styles:**
   - There are approximately 150 instances of structural inline styles (`style="..."`) in the active Blade templates.
   - **Action:** Sweep the views and replace these with Tabler's native utility classes (e.g., `m-3`, `w-100`, `text-center`, `fs-*`).
3. **Clean Up Orphaned Code:**
   - Delete unused legacy layouts such as `resources/views/layouts/admin.blade.php` (which uses deprecated structural classes and Bootstrap Icons instead of Tabler Icons).

## Key Files & Locations
- **Main Dashboard:** `resources/views/admin/dashboard.blade.php` (Contains the new SVG map logic).
- **Map Data:** `public/assets/maps/pakistan_svg_data.json` (Source of truth for map rendering).
- **Custom CSS (To be purged/refactored):** `public/assets/css/pats-core.css`
- **Main Layouts:** `resources/views/layouts/dashboard.blade.php` (Active Tabler layout) vs `layouts/admin.blade.php` (Legacy).

## Agent Instructions for Resuming
When picking up this project, your primary focus should be executing the **Tabler Standardization** plan. Do not write custom CSS unless absolutely necessary; always defer to Tabler's utility classes and component structures to ensure clean dark-mode toggling and long-term maintainability.
