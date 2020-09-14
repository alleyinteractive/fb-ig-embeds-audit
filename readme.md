# Facebook/Instagram Embeds Audit
[About]

## How to use
This plugin adds a new [WP CLI](https://wp-cli.org/) command, `wp fb-ig-embeds-audit list`.

```bash
wp fb-ig-embeds-audit list
wp fb-ig-embeds-audit list --post_id=123
wp fb-ig-embeds-audit list --format=json
```

```php
/**
 * Get all http references in post_content.
 *
 * ## OPTIONS
 *
 * [--post_id]
 * : A specific post ID to find embeds within.
 *
 * [--format=<format>]
 * : Render output in a particular format.
 * ---
 * default: table
 * options:
 *   - table
 *   - csv
 *   - json
 *   - count
 *   - yaml
 * ---
 *
 * ## EXAMPLES
 *
 *     wp fb-ig-embeds-audit list
 *     wp fb-ig-embeds-audit list --post_id=123
 *     wp fb-ig-embeds-audit list --format=json
 *
 * @param array $args The command args.
 * @param array $assoc_args The command associated  args.
 */
 ```
