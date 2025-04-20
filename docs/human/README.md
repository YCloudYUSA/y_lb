```markdown
# Y Layout Builder (y_lb)

## Project Purpose

The `y_lb` module provides integration and enhancements for Drupal's Layout Builder functionality, tailored for the "Website Services" project context. It aims to streamline the creation of flexible, component-based landing pages using a predefined set of layouts, blocks, and styling options.

## Key Features (Inferred from Codebase)

*   **Layout Builder Integration:** The core purpose is to configure and extend Drupal's Layout Builder.
*   **"Landing Page (Layout Builder)" Content Type:** Provides a dedicated `landing_page_lb` node type specifically designed for building pages with Layout Builder (`config/install/node.type.landing_page_lb.yml`).
*   **Custom Layouts:** Defines specific header (`ws_header`) and footer (`ws_footer`) layouts for use within Layout Builder (`y_lb.layouts.yml`, `src/Plugin/Layout/`).
*   **Custom Blocks:** Includes several custom blocks to be placed via Layout Builder:
    *   Site Logo (`SiteLogoBlock.php`)
    *   Site Name (`SiteNameBlock.php`)
    *   Search Bar (`SearchBarBlock.php`)
    *   Social Links (`SocialBlock.php`)
    *   Copyright (`CopyrightBlock.php`)
    *   Node Title (`NodeTitleBlock.php`)
    *   Node Share Buttons (`NodeShareBlock.php`)
    *   Node Tags (`NodeTagsBlock.php`)
    *   Node Locations (`NodeLocationsBlock.php`)
*   **"Y Styles" / "WS Styles" System:** Implements a theme/styling configuration system:
    *   Manages global and component-level styles (Colorways, Border Radius, Border Style, Text Alignment, Button Position, Button Fill) (`y_lb.ws_style.yml`, `y_lb.ws_style_option.yml`, `src/WSStyleManager.php`, `src/WSStyleOptionManager.php`).
    *   Provides UI elements for selecting these styles within Layout Builder configuration (`src/Element/WSThemeSettings.php`, `src/Form/YLBEntityViewDisplay.php`, `src/Form/YLBOverridesEntityForm.php`).
    *   Includes associated CSS/SCSS assets (`assets/`).
*   **Main Menu Enhancements:**
    *   Includes a submodule (`y_lb_main_menu_cta_block`) to add optional Call-to-Action blocks within the main menu dropdowns.
    *   Supports `<nolink>` URLs in main menu items for creating non-clickable parent items (`SMOKE_TESTS.md`).
    *   Provides custom main menu templates (`templates/menu--main.html.twig`).
*   **Configuration:** Installs default configuration for the landing page type, including pathauto patterns, metatag defaults, simple sitemap settings, and Layout Builder restrictions (`config/install/`).
*   **Dependencies:** Relies on Drupal's Layout Builder, Bootstrap Styles, Bootstrap Layout Builder, and various `lb_` block modules (like `lb_hero`, `lb_cards`, etc.) as specified in `composer.json`.

## Installation

1.  **Using Composer (Recommended):**
    ```bash
    composer require ycloudyusa/y_lb
    ```
    Composer will handle downloading the module and its specified dependencies.

2.  **Enable the Module:**
    Enable the module using Drush or the Drupal UI:
    ```bash
    drush en y_lb
    ```
    Or navigate to `/admin/modules` and enable "Y Layout Builder".

3.  **Enable Submodule (Optional):**
    If you need the main menu CTA block functionality, enable the submodule:
    ```bash
    drush en y_lb_main_menu_cta_block
    ```

Installation will import the necessary configuration defined in `config/install`.

## Basic Usage

1.  **Create Content:** Navigate to Content > Add content > Landing Page (Layout Builder).
2.  **Manage Layout:** After creating the basic node, use the "Layout" tab (usually accessible via the node view page or operations links) to manage the page structure.
3.  **Add Sections:** Add sections using available layouts (including the custom `ws_header` and `ws_footer`, and standard Bootstrap Layout Builder columns).
4.  **Add Blocks:** Within sections, add blocks. You can use standard Drupal blocks, blocks provided by dependencies (like `lb_hero`, `lb_cards`), and the custom blocks provided by this module (e.g., Site Logo, Node Title, Social Links).
5.  **Configure Styles:**
    *   **Global:** Configure default "Y Styles" (Colorway, Borders, etc.) by editing the entity view display for the "Landing Page (Layout Builder)" content type (e.g., `/admin/structure/types/manage/landing_page_lb/display/default`).
    *   **Per-Node Override:** On individual landing page nodes, you can check "Override default Y Styles" within the "Layout" tab's Y Styles section to apply different styles specific to that page.
6.  **Configure Main Menu:** Manage the "Main navigation" menu (`/admin/structure/menu/manage/main`). You can add `<nolink>` to the URL field for parent items that should only expand submenus. If the `y_lb_main_menu_cta_block` submodule is enabled, you can add CTA blocks to menu items via the menu item edit form.
7.  **Configure Social Links:** Set the site-wide social media links via the admin settings form at `/admin/openy/settings/y-lb`.
```