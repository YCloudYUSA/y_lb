```markdown
# AI Priorities Prompt Helper for y_lb

This document provides guidelines for human editors reviewing the AI-generated `Priorities.md` file for the `y_lb` (YMCA Layout Builder) project. The AI bases its analysis *only* on the provided code structure, dependencies, file names, comments, and function/class names. Use these points to ensure the priorities reflect the specific context and implicit goals of this project, which the AI might overlook.

**Key Project Context:** This module (`y_lb`) provides a Drupal Layout Builder integration specifically tailored for YMCA Website Services. It integrates various custom and contributed Layout Builder blocks (`lb_*`, `ws_*`) and enforces a YMCA-specific styling system (`WSStyleManager`, `WSStyleOptionManager`, Colorways, Borders, etc.).

## Human Editor Checklist & Considerations:

1.  **Core Value Proposition - YMCA Branding & Styling:**
    *   **AI Blind Spot:** The AI sees the `WSStyleManager`, `WSStyleOptionManager`, colorway CSS, and related Y Style configuration (`y_lb.ws_style.yml`, `y_lb.ws_style_option.yml`), but may not grasp its central importance.
    *   **Editor Action:** Ensure the AI's priorities reflect that a primary goal is providing a **consistent, YMCA-branded visual experience** through Layout Builder. Prioritize the stability, functionality, and configurability of the custom "Y Styles" system (Colorways, Borders, Typography, Buttons, Alignment). Does the AI prioritize tasks related to maintaining or enhancing this system?

2.  **Layout Builder Admin Experience:**
    *   **AI Blind Spot:** While the AI sees Layout Builder integration code (`YLBEntityViewDisplay.php`, `YLBOverridesEntityForm.php`, `ChooseBlockController.php`), it doesn't inherently understand the target user (YMCA site administrators) or the importance of their workflow.
    *   **Editor Action:** Does the AI prioritize improving the *usability* of the Layout Builder interface for admins? Consider the custom `ChooseBlockController` and the LB UI overrides (`YLB*` forms) – are fixes or enhancements here given appropriate weight?

3.  **Integration with `lb_*` and `ws_*` Components:**
    *   **AI Blind Spot:** The AI lists many `lb_*` and `ws_*` dependencies in `composer.json` but doesn't know which are most critical or frequently used by YMCAs.
    *   **Editor Action:** Review the list of integrated components (Hero, Carousel, Grid CTA, Tabs, etc.). Are there known issues or high-demand features related to specific components that the AI might under-prioritize? Ensure the priorities reflect the need for seamless integration and consistent styling *across* these components via the Y Styles system.

4.  **Header/Footer Customization:**
    *   **AI Blind Spot:** The AI sees custom layout plugins (`WsHeaderLayout.php`, `WsFooterLayout.php`) and specific blocks (`SiteLogoBlock.php`, `SiteNameBlock.php`, `CopyrightBlock.php`, `SocialBlock.php`), but might treat them as standard features.
    *   **Editor Action:** These are critical site-wide elements. Verify the AI prioritizes the correct functioning, configuration, and styling (especially logo variations and social links) of these custom header and footer layouts and their associated blocks.

5.  **Menu Functionality (Main Menu & CTA):**
    *   **AI Blind Spot:** The AI sees the `y_lb_main_menu_cta_block` sub-module and `menu--main.html.twig` but might not understand the specific business need for the complex menu structure (multi-level dropdowns) and the integrated CTA block.
    *   **Editor Action:** Referencing `SMOKE_TESTS.md`, ensure the AI prioritizes the correct rendering and behavior of the main menu, including the `<nolink>` feature and the specific functionality of the CTA block within the dropdowns.

6.  **Dependencies and Patches:**
    *   **AI Blind Spot:** The AI sees the patches in `composer.json` but doesn't grasp the potential maintenance burden or risk associated with them.
    *   **Editor Action:** Evaluate if reducing reliance on patches (especially core patches) should be a higher priority than the AI suggests. Does the AI prioritize keeping core dependencies like `drupal/layout_builder` and `drupal/bootstrap_styles` up-to-date?

7.  **Configuration Management:**
    *   **AI Blind Spot:** The AI sees numerous config files in `config/install` and `config/optional`.
    *   **Editor Action:** Ensure the integrity and correct installation/updating (`y_lb.install`, update hooks) of default configurations (content types, view displays, fields, Bootstrap Styles settings) are reflected in the priorities.

8.  **Frontend Assets (CSS/JS):**
    *   **AI Blind Spot:** The AI sees SCSS compilation (`package.json`, `assets/scss/`) and JS files (`assets/js/`).
    *   **Editor Action:** While the AI might identify refactoring potential, ensure build process stability and frontend performance (related to CSS/JS loading defined in `y_lb.libraries.yml`) are considered in the priorities.

Remember: The goal is to refine the AI's output, adding the YMCA-specific business context and user focus that the code structure alone doesn't fully convey.
```