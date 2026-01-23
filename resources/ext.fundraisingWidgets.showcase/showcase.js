/**
 * FundraisingWidgets - Showcase Page JavaScript
 * Handles live preview updates and code generation
 */
( function () {
	'use strict';

	var serverUrl = mw.config.get( 'wgServer' );
	var extensionPath = mw.config.get( 'wgExtensionAssetsPath' ) + '/FundraisingWidgets';

	/**
	 * Initialize format tab switching
	 */
	function initFormatTabs() {
		var tabContainers = document.querySelectorAll( '.frw-showcase-code' );

		tabContainers.forEach( function ( container ) {
			var tabs = container.querySelectorAll( '.frw-format-tab' );
			var contents = container.querySelectorAll( '.frw-format-content' );

			tabs.forEach( function ( tab ) {
				tab.addEventListener( 'click', function () {
					var format = tab.dataset.format;

					// Update tab active state
					tabs.forEach( function ( t ) {
						t.classList.remove( 'frw-format-tab--active' );
					} );
					tab.classList.add( 'frw-format-tab--active' );

					// Update content visibility
					contents.forEach( function ( content ) {
						if ( content.dataset.format === format ) {
							content.classList.add( 'frw-format-content--active' );
						} else {
							content.classList.remove( 'frw-format-content--active' );
						}
					} );
				} );
			} );
		} );
	}

	/**
	 * Initialize donate button configurator
	 */
	function initButtonConfigurator() {
		var sizeSelect = document.getElementById( 'frw-button-size' );
		var textInput = document.getElementById( 'frw-button-text' );
		var preview = document.getElementById( 'frw-button-preview' );
		var codeOutputWikitext = document.getElementById( 'frw-button-code-wikitext' );
		var codeOutputJs = document.getElementById( 'frw-button-code-js' );
		var colorPicker = document.getElementById( 'frw-button-colors' );

		if ( !sizeSelect || !preview ) {
			return;
		}

		var currentColor = 'blue';

		function updateButtonPreview() {
			var size = sizeSelect.value;
			var text = textInput.value || 'Support Wikipedia';

			preview.innerHTML = '<a href="https://donate.wikimedia.org" ' +
				'class="frw-donate-button frw-donate-button--' + escapeHtml( size ) + ' frw-donate-button--' + escapeHtml( currentColor ) + '" role="button">' +
				'<span class="frw-button-text">' + escapeHtml( text ) + '</span>' +
				'</a>';

			// Update wikitext code output
			var wikitextCode = '{{#fundraising-button: size=' + size + ' | text=' + text + ' | color=' + currentColor + ' }}';
			codeOutputWikitext.textContent = wikitextCode;

			// Update JavaScript code output
			var jsCode = '<script src="' + serverUrl + extensionPath + '/resources/embed.js"></script>\n' +
				'<div class="frw-embed" data-widget="button" data-size="' + size + '" data-text="' + text + '" data-color="' + currentColor + '"></div>';
			codeOutputJs.textContent = jsCode;
		}

		// Color picker functionality
		if ( colorPicker ) {
			var colorButtons = colorPicker.querySelectorAll( '.frw-color-option' );
			colorButtons.forEach( function ( btn ) {
				btn.addEventListener( 'click', function () {
					// Update selection
					colorButtons.forEach( function ( b ) {
						b.classList.remove( 'frw-color-option--selected' );
					} );
					btn.classList.add( 'frw-color-option--selected' );
					currentColor = btn.dataset.color;
					updateButtonPreview();
				} );
			} );
		}

		sizeSelect.addEventListener( 'change', updateButtonPreview );
		textInput.addEventListener( 'input', updateButtonPreview );

		// Initial render
		updateButtonPreview();
	}

	/**
	 * Initialize banner configurator
	 */
	function initBannerConfigurator() {
		var messageInput = document.getElementById( 'frw-banner-message' );
		var buttonTextInput = document.getElementById( 'frw-banner-button-text' );
		var buttonLinkInput = document.getElementById( 'frw-banner-button-link' );
		var dismissibleCheckbox = document.getElementById( 'frw-banner-dismissible' );
		var logoSelect = document.getElementById( 'frw-banner-logo' );
		var preview = document.getElementById( 'frw-banner-preview' );
		var codeOutputWikitext = document.getElementById( 'frw-banner-code-wikitext' );
		var codeOutputJs = document.getElementById( 'frw-banner-code-js' );

		if ( !messageInput || !preview ) {
			return;
		}

		var basePath = mw.config.get( 'wgExtensionAssetsPath' ) + '/FundraisingWidgets/resources/images/';

		// Wikimedia Commons URLs for consistent external access
		var commonsImages = {
			globe: 'https://upload.wikimedia.org/wikipedia/commons/8/80/Wikipedia-logo-v2.svg',
			globeHands: 'https://upload.wikimedia.org/wikipedia/commons/1/1f/Wikipedia_mini_globe_handheld.jpg',
			wordmark: 'https://donate.wikimedia.org/static/images/mobile/copyright/wikipedia-wordmark-en.svg',
			wmf: 'https://upload.wikimedia.org/wikipedia/donate/6/60/Wmf_logo_horiz_K.svg'
		};

		function getLogoHtml( logoType ) {
			if ( logoType === 'none' ) {
				return '';
			}

			var html = '<div class="frw-banner-logo frw-banner-logo--' + logoType + '">';

			switch ( logoType ) {
				case 'globe':
					html += '<img src="' + commonsImages.globe + '" class="frw-logo frw-logo-globe" alt="Wikipedia">';
					break;
				case 'globe-hands':
					html += '<img src="' + commonsImages.globeHands + '" class="frw-logo frw-logo-globe-hands" alt="Wikipedia">';
					break;
				case 'wordmark':
					html += '<img src="' + commonsImages.wordmark + '" class="frw-logo frw-logo-wordmark" alt="Wikipedia">';
					break;
				case 'combined':
					html += '<img src="' + commonsImages.globe + '" class="frw-logo frw-logo-globe" alt="">' +
						'<img src="' + commonsImages.wordmark + '" class="frw-logo frw-logo-wordmark" alt="Wikipedia">';
					break;
				case 'wmf':
					html += '<img src="' + commonsImages.wmf + '" class="frw-logo frw-logo-wmf" alt="Wikimedia Foundation">';
					break;
			}

			html += '</div>';
			return html;
		}

		function updateBannerPreview() {
			var message = messageInput.value || 'If Wikipedia has given you useful knowledge this year, please give back. There are no small contributions: every edit counts, every donation counts.';
			var buttonText = buttonTextInput.value || 'Donate';
			var buttonLink = buttonLinkInput.value || 'https://donate.wikimedia.org';
			var dismissible = dismissibleCheckbox.checked;
			var logoType = logoSelect.value;

			var closeButton = '';
			if ( dismissible ) {
				closeButton = '<button type="button" class="frw-banner-close" aria-label="Close">' +
					'<span aria-hidden="true">&times;</span>' +
					'</button>';
			}

			var logoHtml = getLogoHtml( logoType );

			preview.innerHTML = '<div class="frw-banner' + ( dismissible ? ' frw-banner--dismissible' : '' ) + '" role="banner">' +
				closeButton +
				logoHtml +
				'<div class="frw-banner-content">' +
				'<p class="frw-banner-message">' + escapeHtml( message ) + '</p>' +
				'<a href="' + escapeHtml( buttonLink ) + '" class="frw-donate-button frw-donate-button--medium" role="button">' +
				'<span class="frw-button-text">' + escapeHtml( buttonText ) + '</span>' +
				'</a>' +
				'</div>' +
				'</div>';

			// Re-initialize banner close functionality
			initBannerCloseButtons( preview );

			// Update wikitext code output
			var wikitextCode = '{{#fundraising-banner: message=' + message;
			if ( buttonText !== 'Donate' ) {
				wikitextCode += ' | button-text=' + buttonText;
			}
			if ( buttonLink !== 'https://donate.wikimedia.org' ) {
				wikitextCode += ' | button-link=' + buttonLink;
			}
			wikitextCode += ' | logo=' + logoType;
			wikitextCode += ' | dismissible=' + ( dismissible ? 'true' : 'false' );
			wikitextCode += ' }}';
			codeOutputWikitext.textContent = wikitextCode;

			// Update JavaScript code output
			var jsCode = '<script src="' + serverUrl + extensionPath + '/resources/embed.js"></script>\n' +
				'<div class="frw-embed" data-widget="banner" data-message="' + escapeAttr( message ) + '"';
			if ( buttonText !== 'Donate' ) {
				jsCode += ' data-button-text="' + escapeAttr( buttonText ) + '"';
			}
			if ( buttonLink !== 'https://donate.wikimedia.org' ) {
				jsCode += ' data-button-link="' + escapeAttr( buttonLink ) + '"';
			}
			jsCode += ' data-logo="' + logoType + '" data-dismissible="' + ( dismissible ? 'true' : 'false' ) + '"></div>';
			codeOutputJs.textContent = jsCode;
		}

		messageInput.addEventListener( 'input', updateBannerPreview );
		buttonTextInput.addEventListener( 'input', updateBannerPreview );
		buttonLinkInput.addEventListener( 'input', updateBannerPreview );
		dismissibleCheckbox.addEventListener( 'change', updateBannerPreview );
		logoSelect.addEventListener( 'change', updateBannerPreview );

		// Initialize close button on initial preview
		initBannerCloseButtons( preview );
	}

	/**
	 * Initialize banner close buttons within a container
	 */
	function initBannerCloseButtons( container ) {
		var closeButtons = container.querySelectorAll( '.frw-banner-close' );
		closeButtons.forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				var banner = button.closest( '.frw-banner' );
				if ( banner ) {
					banner.classList.add( 'frw-banner--hidden' );
				}
			} );
		} );
	}

	/**
	 * Initialize image widget configurator
	 */
	function initImageConfigurator() {
		var imageSelect = document.getElementById( 'frw-image-bg' );
		var sizeSelect = document.getElementById( 'frw-image-size' );
		var positionSelect = document.getElementById( 'frw-image-position' );
		var captionInput = document.getElementById( 'frw-image-caption' );
		var colorPicker = document.getElementById( 'frw-image-colors' );
		var preview = document.getElementById( 'frw-image-preview' );
		var codeOutputWikitext = document.getElementById( 'frw-image-code-wikitext' );
		var codeOutputJs = document.getElementById( 'frw-image-code-js' );

		if ( !imageSelect || !preview ) {
			return;
		}

		var currentColor = 'blue';

		var imageUrls = {
			landscape: 'https://upload.wikimedia.org/wikipedia/commons/8/81/Parque_Eagle_River%2C_Anchorage%2C_Alaska%2C_Estados_Unidos%2C_2017-09-01%2C_DD_02.jpg',
			eagle: 'https://upload.wikimedia.org/wikipedia/commons/0/09/001_Wild_Golden_Eagle_and_Majinghorn_Pfyn-Finges_Photo_by_Giles_Laurent.jpg',
			mountain: 'https://upload.wikimedia.org/wikipedia/commons/0/01/Pointe_de_Nantaux_05.jpg',
			'snow-leopard': 'https://upload.wikimedia.org/wikipedia/commons/f/f3/Snow_Leopard_Looking_Up.jpg',
			frog: 'https://upload.wikimedia.org/wikipedia/commons/3/35/Strawberry_poison_dart_frog_%2870539%29.jpg',
			'cat-lizard': 'https://upload.wikimedia.org/wikipedia/commons/7/72/Cat_playing_with_a_lizard.jpg',
			'regal-cat': 'https://upload.wikimedia.org/wikipedia/commons/4/4d/Cat_November_2010-1a.jpg'
		};

		function updateImagePreview() {
			var image = imageSelect.value;
			var size = sizeSelect.value;
			var position = positionSelect.value;
			var caption = captionInput.value;
			var imageUrl = imageUrls[ image ] || imageUrls[ 'snow-leopard' ];

			var hasCaption = caption.length > 0;
			var widgetClass = 'frw-image-widget frw-image-widget--' + escapeHtml( size );
			if ( hasCaption ) {
				widgetClass += ' frw-image-widget--has-caption';
			} else {
				widgetClass += ' frw-image-widget--button-' + escapeHtml( position );
			}

			var buttonClass = 'frw-donate-button frw-donate-button--medium frw-donate-button--' + escapeHtml( currentColor );

			var captionBarHtml = '';
			var buttonHtml = '';

			if ( hasCaption ) {
				captionBarHtml = '<div class="frw-image-caption-bar">' +
					'<p class="frw-image-caption">' + escapeHtml( caption ) + '</p>' +
					'<a href="https://donate.wikimedia.org" class="' + buttonClass + '" role="button">' +
					'<span class="frw-button-text">You mean I can donate to this thing!!!</span>' +
					'</a>' +
					'</div>';
			} else {
				buttonHtml = '<a href="https://donate.wikimedia.org" class="' + buttonClass + '" role="button">' +
					'<span class="frw-button-text">You mean I can donate to this thing!!!</span>' +
					'</a>';
			}

			preview.innerHTML = '<div class="' + widgetClass + '" ' +
				'style="background-image: url(\'' + imageUrl + '\');">' +
				'<div class="frw-image-overlay">' +
				buttonHtml +
				'</div>' +
				captionBarHtml +
				'</div>';

			// Update wikitext code output
			var wikitextCode = '{{#fundraising-image: image=' + image + ' | size=' + size;
			if ( !hasCaption ) {
				wikitextCode += ' | button-position=' + position;
			}
			wikitextCode += ' | button-color=' + currentColor;
			if ( caption ) {
				wikitextCode += ' | caption=' + caption;
			}
			wikitextCode += ' }}';
			codeOutputWikitext.textContent = wikitextCode;

			// Update JavaScript code output
			var jsCode = '<script src="' + serverUrl + extensionPath + '/resources/embed.js"></script>\n' +
				'<div class="frw-embed" data-widget="image" data-image="' + image + '" data-size="' + size + '"';
			if ( !hasCaption ) {
				jsCode += ' data-button-position="' + position + '"';
			}
			jsCode += ' data-button-color="' + currentColor + '"';
			if ( caption ) {
				jsCode += ' data-caption="' + escapeAttr( caption ) + '"';
			}
			jsCode += '></div>';
			codeOutputJs.textContent = jsCode;
		}

		// Color picker functionality
		if ( colorPicker ) {
			var colorButtons = colorPicker.querySelectorAll( '.frw-color-option' );
			colorButtons.forEach( function ( btn ) {
				btn.addEventListener( 'click', function () {
					colorButtons.forEach( function ( b ) {
						b.classList.remove( 'frw-color-option--selected' );
					} );
					btn.classList.add( 'frw-color-option--selected' );
					currentColor = btn.dataset.color;
					updateImagePreview();
				} );
			} );
		}

		imageSelect.addEventListener( 'change', updateImagePreview );
		sizeSelect.addEventListener( 'change', updateImagePreview );
		positionSelect.addEventListener( 'change', updateImagePreview );
		captionInput.addEventListener( 'input', updateImagePreview );

		// Initial render
		updateImagePreview();
	}

	/**
	 * Initialize rabbit hole configurator
	 */
	function initRabbitHoleConfigurator() {
		var themeSelect = document.getElementById( 'frw-rabbithole-theme' );
		var donateAfterSelect = document.getElementById( 'frw-rabbithole-donate-after' );
		var buttonTextInput = document.getElementById( 'frw-rabbithole-button-text' );
		var donateLinkInput = document.getElementById( 'frw-rabbithole-donate-link' );
		var preview = document.getElementById( 'frw-rabbithole-preview' );
		var codeOutputWikitext = document.getElementById( 'frw-rabbithole-code-wikitext' );
		var codeOutputJs = document.getElementById( 'frw-rabbithole-code-js' );

		if ( !themeSelect || !preview ) {
			return;
		}

		// Curated discoveries for preview functionality
		var discoveries = [
			{ title: 'Tardigrades', fact: 'These microscopic creatures can survive the vacuum of space, temperatures from -272°C to 150°C, radiation 1,000 times the lethal human dose, and have survived all five mass extinctions.', url: 'https://en.wikipedia.org/wiki/Tardigrade' },
			{ title: 'Sharks Are Older Than Trees', fact: 'Sharks first appeared about 450 million years ago, while the earliest trees evolved around 350 million years ago. Sharks have survived all five mass extinctions.', url: 'https://en.wikipedia.org/wiki/Shark' },
			{ title: 'Honey Never Spoils', fact: 'Archaeologists found 3,000-year-old honey in Egyptian tombs that was still perfectly edible. Its low moisture, high acidity, and natural hydrogen peroxide prevent bacterial growth.', url: 'https://en.wikipedia.org/wiki/Honey' },
			{ title: 'Octopus Biology', fact: 'Octopuses have three hearts, blue copper-based blood, and can edit their own RNA to adapt to cold temperatures—a capability far beyond most other animals.', url: 'https://en.wikipedia.org/wiki/Octopus' },
			{ title: 'Cleopatra and the Pyramids', fact: 'Cleopatra lived closer in time to the Moon landing than to the construction of the Great Pyramid. She was born 2,500 years after the pyramids but only 2,000 years before Apollo 11.', url: 'https://en.wikipedia.org/wiki/Cleopatra' },
			{ title: 'A Day on Venus', fact: 'A day on Venus (243 Earth days) is longer than its year (225 Earth days). Venus also rotates backwards, so the Sun rises in the west and sets in the east.', url: 'https://en.wikipedia.org/wiki/Venus' }
		];

		var seenIndices = [];
		var clickCount = 0;

		function getRandomDiscovery() {
			if ( seenIndices.length >= discoveries.length ) {
				seenIndices = [];
			}
			var availableIndices = [];
			for ( var i = 0; i < discoveries.length; i++ ) {
				if ( seenIndices.indexOf( i ) === -1 ) {
					availableIndices.push( i );
				}
			}
			var randomIndex = availableIndices[ Math.floor( Math.random() * availableIndices.length ) ];
			seenIndices.push( randomIndex );
			return discoveries[ randomIndex ];
		}

		function updateRabbitHolePreview() {
			var theme = themeSelect.value;
			var donateAfter = parseInt( donateAfterSelect.value, 10 ) || 3;
			var buttonText = buttonTextInput.value || 'Discover something new';
			var donateLink = donateLinkInput.value || 'https://donate.wikimedia.org';

			preview.innerHTML = '<div class="frw-rabbit-hole frw-rabbit-hole--' + escapeHtml( theme ) + '">' +
				'<div class="frw-rabbit-hole-content">' +
				'<div class="frw-rabbit-hole-intro">' +
				'<h3 class="frw-rabbit-hole-title">Wikipedia Rabbit Hole</h3>' +
				'<p class="frw-rabbit-hole-subtitle">Click to discover something fascinating</p>' +
				'</div>' +
				'<div class="frw-rabbit-hole-discovery frw-rabbit-hole-discovery--hidden"></div>' +
				'<div class="frw-rabbit-hole-donate frw-rabbit-hole-donate--hidden">' +
				'<p class="frw-rabbit-hole-donate-message">Enjoying the journey? Wikipedia is free, but not free to run. Help keep knowledge accessible for everyone.</p>' +
				'<a href="' + escapeHtml( donateLink ) + '" class="frw-donate-button frw-donate-button--medium frw-donate-button--green" role="button">' +
				'<span class="frw-button-text">Donate</span>' +
				'</a>' +
				'</div>' +
				'</div>' +
				'<div class="frw-rabbit-hole-footer">' +
				'<button type="button" class="frw-rabbit-hole-button">' + escapeHtml( buttonText ) + '</button>' +
				'<span class="frw-rabbit-hole-counter"></span>' +
				'</div>' +
				'</div>';

			// Reset state
			seenIndices = [];
			clickCount = 0;

			// Add button click handler for live preview
			var button = preview.querySelector( '.frw-rabbit-hole-button' );
			var intro = preview.querySelector( '.frw-rabbit-hole-intro' );
			var discovery = preview.querySelector( '.frw-rabbit-hole-discovery' );
			var donate = preview.querySelector( '.frw-rabbit-hole-donate' );
			var counter = preview.querySelector( '.frw-rabbit-hole-counter' );

			button.addEventListener( 'click', function () {
				clickCount++;

				if ( clickCount === 1 ) {
					intro.classList.add( 'frw-rabbit-hole-intro--hidden' );
				}

				if ( clickCount > 0 && clickCount % donateAfter === 0 ) {
					intro.classList.add( 'frw-rabbit-hole-intro--hidden' );
					discovery.classList.add( 'frw-rabbit-hole-discovery--hidden' );
					donate.classList.remove( 'frw-rabbit-hole-donate--hidden' );
					button.textContent = 'Keep exploring';
				} else {
					donate.classList.add( 'frw-rabbit-hole-donate--hidden' );
					var disc = getRandomDiscovery();
					discovery.innerHTML =
						'<h4 class="frw-rabbit-hole-discovery-title">' +
						'<a href="' + escapeHtml( disc.url ) + '" target="_blank" rel="noopener">' +
						escapeHtml( disc.title ) +
						'</a></h4>' +
						'<p class="frw-rabbit-hole-discovery-fact">' + escapeHtml( disc.fact ) + '</p>' +
						'<a href="' + escapeHtml( disc.url ) + '" class="frw-rabbit-hole-discovery-link" target="_blank" rel="noopener">' +
						'Read more on Wikipedia →</a>';
					discovery.classList.remove( 'frw-rabbit-hole-discovery--hidden' );
					discovery.style.animation = 'none';
					discovery.offsetHeight;
					discovery.style.animation = '';
					button.textContent = 'Discover another';
				}

				if ( clickCount > 0 ) {
					counter.textContent = clickCount + ' discovered';
				}
			} );

			// Update wikitext code output
			var wikitextCode = '{{#fundraising-rabbithole: theme=' + theme + ' | donate-after=' + donateAfter;
			if ( buttonText !== 'Discover something new' ) {
				wikitextCode += ' | button-text=' + buttonText;
			}
			if ( donateLink !== 'https://donate.wikimedia.org' ) {
				wikitextCode += ' | donate-link=' + donateLink;
			}
			wikitextCode += ' }}';
			codeOutputWikitext.textContent = wikitextCode;

			// Update JavaScript code output
			var jsCode = '<script src="' + serverUrl + extensionPath + '/resources/embed.js"></script>\n' +
				'<div class="frw-embed" data-widget="rabbithole" data-theme="' + theme + '" data-donate-after="' + donateAfter + '"';
			if ( buttonText !== 'Discover something new' ) {
				jsCode += ' data-button-text="' + escapeAttr( buttonText ) + '"';
			}
			if ( donateLink !== 'https://donate.wikimedia.org' ) {
				jsCode += ' data-donate-link="' + escapeAttr( donateLink ) + '"';
			}
			jsCode += '></div>';
			codeOutputJs.textContent = jsCode;
		}

		themeSelect.addEventListener( 'change', updateRabbitHolePreview );
		donateAfterSelect.addEventListener( 'change', updateRabbitHolePreview );
		buttonTextInput.addEventListener( 'input', updateRabbitHolePreview );
		donateLinkInput.addEventListener( 'input', updateRabbitHolePreview );

		// Initial render
		updateRabbitHolePreview();
	}

	/**
	 * Initialize copy buttons
	 */
	function initCopyButtons() {
		var copyButtons = document.querySelectorAll( '.frw-copy-button' );

		copyButtons.forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				var targetId = button.dataset.target;
				var target = document.getElementById( targetId );

				if ( target ) {
					var text = target.textContent;

					navigator.clipboard.writeText( text ).then( function () {
						var originalText = button.textContent;
						button.textContent = 'Copied!';
						button.classList.add( 'frw-copy-button--copied' );

						setTimeout( function () {
							button.textContent = originalText;
							button.classList.remove( 'frw-copy-button--copied' );
						}, 2000 );
					} ).catch( function () {
						// Fallback for older browsers
						var textarea = document.createElement( 'textarea' );
						textarea.value = text;
						textarea.style.position = 'fixed';
						textarea.style.opacity = '0';
						document.body.appendChild( textarea );
						textarea.select();
						document.execCommand( 'copy' );
						document.body.removeChild( textarea );

						button.textContent = 'Copied!';
						button.classList.add( 'frw-copy-button--copied' );
						setTimeout( function () {
							button.textContent = 'Copy code';
							button.classList.remove( 'frw-copy-button--copied' );
						}, 2000 );
					} );
				}
			} );
		} );
	}

	/**
	 * Escape HTML entities
	 */
	function escapeHtml( text ) {
		var div = document.createElement( 'div' );
		div.textContent = text;
		return div.innerHTML;
	}

	/**
	 * Escape for HTML attributes (double quotes)
	 */
	function escapeAttr( text ) {
		return escapeHtml( text ).replace( /"/g, '&quot;' );
	}

	/**
	 * Initialize all configurators
	 */
	function init() {
		initFormatTabs();
		initButtonConfigurator();
		initBannerConfigurator();
		initImageConfigurator();
		initRabbitHoleConfigurator();
		initCopyButtons();
	}

	// Run initialization when DOM is ready
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
