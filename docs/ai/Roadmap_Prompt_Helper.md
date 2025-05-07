Okay, here is the content for `docs/ai/Roadmap_Prompt_Helper.md`, generated based *only* on the provided code structure, dependencies, file names, and other artifacts of the `y_lb` project.

```markdown
# AI Roadmap Review Helper for y_lb

## Purpose

This document provides guidance for human editors reviewing the AI-generated `Roadmap.md` for the **Y Layout Builder (y_lb)** project. The goal is to ensure the roadmap aligns with the project's apparent purpose, features, and architecture, as inferred *solely* from the existing codebase provided.

## General Review Guidelines

1.  **Grounding in Code:** Every roadmap item should ideally relate back to the existing codebase. Ask:
    *   Does this feature build upon existing plugins (`src/Plugin/Block`, `src/Plugin/Layout`, `src/Plugin/BootstrapStyles`)?
    *   Does it enhance the styling system (`y_lb.ws_style.yml`, `y_lb.ws_style_option.yml`, `WSStyle*` classes)?
    *   Does it improve integration with core Layout Builder or its dependencies (`composer.json`)?
    *   Does it relate to the custom content type (`node.type.landing_page_lb.yml`) or its configuration?
    *   Does it improve the specific features like the main menu CTA (`y_lb_main_menu_cta_block`)?

2.  **Specificity:** Replace generic AI suggestions with specifics relevant to `y_lb`.
    *   Instead of "Improve UI", suggest "Refine the Layout Builder Block Selection UI (`ChooseBlockController.php`)" or "Enhance the front-end presentation of `ws_header`/`ws_footer` layouts".
    *   Instead of "Add New Features", suggest "Develop new YMCA-specific Layout Builder blocks (e.g., for Programs, Facilities based on `composer.json` dependencies)" or "Expand the WS Style Options (`y_lb.ws_style_option.yml`)".

3.  **Plausibility:** Given that `y_lb` seems to be a layer integrating Layout Builder with Bootstrap Styles and many specific LB block modules for a YMCA context, does the proposed roadmap item fit this vision? Does it make sense as a *next step* based on the current features (custom layouts, blocks, styling system, menu enhancements)?

4.  **Dependencies:** The `composer.json` lists many dependencies (Bootstrap Styles, numerous `lb_` block modules, OpenY modules, Drupal core features). Roadmap items related to managing these dependencies, ensuring compatibility, or leveraging them further are highly relevant.

## Refining Common AI-Generated Roadmap Items

*   **Performance Optimization:**
    *   *Helper:* Look at the number of CSS/JS assets (`assets/`, `y_lb.libraries.yml`). Could asset loading be optimized? Are the custom layout plugins (`WsHeaderLayout`, `WsFooterLayout`) efficient? Does the styling system add significant overhead? Make suggestions specific to these areas.

*   **Security Enhancements:**
    *   *Helper:* Primarily relates to keeping Drupal core and *all* dependencies (`composer.json`) updated. Specific `y_lb` code (custom blocks, forms) might need review, but dependency management is the most obvious security task evident from the code.

*   **New Features:**
    *   *Helper:* This requires the most human input. Based on the existing structure:
        *   **More Blocks:** Are there obvious gaps in YMCA-specific blocks compared to the dependencies listed (`y_branch`, `y_camp`, `y_program`, `ws_event`, etc.)?
        *   **Styling:** Expand the `WSStyle` system (more colorways, border options, typography controls?). Add more `ws_style_option.yml` definitions.
        *   **Layouts:** Introduce more custom layouts beyond header/footer? Add more configuration options to existing layouts?
        *   **Integrations:** Improve how `y_lb` works with specific dependencies listed in `composer.json`?

*   **UI/UX Improvements:**
    *   *Helper:* Focus on the *admin* experience (Layout Builder UI, custom forms like `YLBOverridesEntityForm`, `WSThemeSettings` element) and the *front-end* experience (clarity of layouts, block presentation defined in `templates/`, usability of features like the main menu CTA).

*   **Testing:**
    *   *Helper:* The `SMOKE_TESTS.md` exists. Suggest expanding tests to cover:
        *   Each custom block in `src/Plugin/Block/`.
        *   The application of various WS Styles.
        *   The functionality of custom layouts (`ws_header`, `ws_footer`).
        *   The main menu CTA block functionality.

*   **Documentation:**
    *   *Helper:* Focus documentation on how to *use* the `y_lb` features: configuring the WS Styles, using the custom blocks, implementing the `landing_page_lb` content type, understanding the main menu CTA.

## Final Check

Ensure the refined roadmap reflects `y_lb`'s role as a specialized Layout Builder integration for YMCA sites, focusing on branding, styling, custom components, and usability within that specific context. Avoid generic software goals unless they can be directly tied to the `y_lb` codebase.
```