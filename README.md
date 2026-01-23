# FundraisingWidgets

A MediaWiki extension providing reusable widget components for fundraising banners, buttons, and interactive elements.

## Requirements

- MediaWiki 1.39.0 or later

## Installation

1. Clone or download this repository into your MediaWiki `extensions/` directory:
   ```bash
   cd extensions/
   git clone https://github.com/jackgleeson/mediawiki-extensions-FundraisingWidgets.git FundraisingWidgets
   ```

2. Add the following to your `LocalSettings.php`:
   ```php
   wfLoadExtension( 'FundraisingWidgets' );
   ```

3. Navigate to `Special:FundraisingWidgets` to access the widget configurator.

## Widgets

### Donate Button

A customizable donation button with multiple sizes and colors.

```wikitext
{{#fundraising-button: size=medium | text=Support Wikipedia | color=blue }}
```

**Parameters:**
- `size` - small, medium, large
- `text` - Button text (default: "Support Wikipedia")
- `color` - blue, purple, green, red, yellow
- `link` - Destination URL (default: https://donate.wikimedia.org)

### Fundraising Banner

A banner with logo, message, and call-to-action button.

```wikitext
{{#fundraising-banner: message=Your message here | logo=globe | dismissible=true }}
```

**Parameters:**
- `message` - Banner message text
- `button-text` - Button text (default: "Donate")
- `button-link` - Button destination URL
- `logo` - globe, globe-hands, combined, wordmark, wmf, none
- `dismissible` - true/false

### Image Widget

An image with an overlay donate button.

```wikitext
{{#fundraising-image: image=landscape | size=medium | button-position=bottom-right | button-color=blue }}
```

**Parameters:**
- `image` - landscape, eagle, mountain, snow-leopard, frog, cat-lizard, regal-cat
- `size` - small, medium, large
- `button-position` - top-left, top-right, bottom-left, bottom-right
- `button-color` - blue, purple, green, red, yellow
- `caption` - Optional caption text (moves button to caption bar)
- `button-text` - Button text (default: "Donate")
- `button-link` - Button destination URL

### Wikipedia Rabbit Hole

An interactive widget that displays random Wikipedia facts, with a donate prompt after a configurable number of discoveries.

```wikitext
{{#fundraising-rabbithole: theme=light | donate-after=3 }}
```

**Parameters:**
- `theme` - light, dark
- `donate-after` - Number of discoveries before showing donate prompt (default: 3)
- `button-text` - Initial button text (default: "Discover something new")
- `donate-link` - Donation URL (default: https://donate.wikimedia.org)

## External Embedding (JavaScript)

Widgets can be embedded on external sites using the JavaScript embed script:

```html
<script src="https://your-wiki.org/extensions/FundraisingWidgets/resources/embed.js"></script>
<div class="frw-embed" data-widget="button" data-size="medium" data-text="Support Wikipedia" data-color="blue"></div>
```

The script auto-initializes when the DOM is ready. Available widget types:
- `button`
- `banner`
- `image`
- `rabbithole`

## Widget Configurator

Visit `Special:FundraisingWidgets` on your wiki to:
- Preview widgets with live configuration
- Generate MediaWiki parser function code
- Generate JavaScript embed code for external sites

## Styling

All widget styles use `!important` declarations to protect against style conflicts when embedded on external sites. The extension uses Wikimedia Commons URLs for images to ensure availability on external sites.

## Browser Compatibility

The `embed.js` script is written in ES5 syntax to ensure compatibility with older browsers, including Internet Explorer 11. Since this script runs on third-party sites where we cannot control the browser environment, we prioritize broad compatibility over modern syntax.

This means the embed script intentionally uses:
- `var` instead of `let`/`const`
- Traditional `function` declarations instead of arrow functions
- String concatenation instead of template literals
- `indexOf()` instead of `includes()`
- `for` loops instead of `for...of`

The MediaWiki-side JavaScript (`init.js`) follows the same conventions for consistency, though MediaWiki's ResourceLoader could handle transpilation if needed.

## License

GPL-2.0-or-later

## Author

Wikimedia Foundation
