# y_lb
Layout Builder for YMCA Website Services distribution

## Y Styles

Y Styles allows site administrators to configure global design settings (color scheme, border radius, etc.) for Layout Builder content types, and optionally allow editors to override these styles per-node.

### Enable Y Styles per content type

To allow editors to customize Y Styles on individual nodes:

```bash
drush cset core.entity_view_display.node.[content_type].default third_party_settings.y_lb.allow_style true -y
drush cr
```

Replace `[content_type]` with your content type machine name (e.g., `landing_page_lb`, `article_lb`, `lb_event`).

### Troubleshooting

If Y Styles panel is not appearing in Layout Builder when editing a node, verify that `allow_style` is enabled:

```bash
drush cget core.entity_view_display.node.[content_type].default third_party_settings.y_lb
```

Should show `allow_style: true` in the output.
