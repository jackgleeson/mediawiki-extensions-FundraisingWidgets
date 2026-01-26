/**
 * FundraisingWidgets - Showcase Page JavaScript
 * Handles live preview updates and code generation
 */
( function () {
	'use strict';

	var serverUrl = mw.config.get( 'wgServer' );
	var extensionPath = mw.config.get( 'wgExtensionAssetsPath' ) + '/FundraisingWidgets';
	var DEFAULT_DONATE_URL = 'https://donate.wikimedia.org';
	var DEFAULT_WIKIPEDIA_URL = 'https://www.wikipedia.org';
	var ALLOWED_PROTOCOLS = [ 'https:', 'http:' ];

	/**
	 * Sanitise a URL to prevent javascript: and other dangerous protocols
	 *
	 * @param {string} url The URL to sanitise
	 * @return {string} A safe URL
	 */
	function sanitiseUrl( url ) {
		if ( !url || url === '' ) {
			return DEFAULT_DONATE_URL;
		}

		var parser = document.createElement( 'a' );
		parser.href = url;

		var protocol = parser.protocol.toLowerCase();
		if ( ALLOWED_PROTOCOLS.indexOf( protocol ) === -1 ) {
			return DEFAULT_DONATE_URL;
		}

		return url;
	}

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
		var linkInput = document.getElementById( 'frw-button-link' );
		var preview = document.getElementById( 'frw-button-preview' );
		var codeOutputWikitext = document.getElementById( 'frw-button-code-wikitext' );
		var codeOutputJs = document.getElementById( 'frw-button-code-js' );
		var colorPicker = document.getElementById( 'frw-button-colors' );

		if ( !sizeSelect || !preview ) {
			return;
		}

		var currentColor = 'yellow';

		function updateButtonPreview() {
			var size = sizeSelect.value;
			var text = textInput.value || 'Support Wikipedia';
			var link = sanitiseUrl( linkInput && linkInput.value );

			preview.innerHTML = '<a href="' + escapeHtml( link ) + '" ' +
				'class="frw-donate-button frw-donate-button--' + escapeHtml( size ) + ' frw-donate-button--' + escapeHtml( currentColor ) + '" role="button" target="_blank" rel="noopener">' +
				'<span class="frw-button-text">' + escapeHtml( text ) + '</span>' +
				'</a>';

			// Update wikitext code output
			var wikitextCode = '{{#fundraising-button: size=' + size + ' | text=' + text + ' | color=' + currentColor;
			if ( link !== 'https://donate.wikimedia.org' ) {
				wikitextCode += ' | button-link=' + link;
			}
			wikitextCode += ' }}';
			codeOutputWikitext.textContent = wikitextCode;

			// Update JavaScript code output
			var jsCode = '<script src="' + serverUrl + extensionPath + '/resources/embed.js"></script>\n' +
				'<div class="frw-embed" data-widget="button" data-size="' + size + '" data-text="' + text + '" data-color="' + currentColor + '"';
			if ( link !== 'https://donate.wikimedia.org' ) {
				jsCode += ' data-button-link="' + escapeAttr( link ) + '"';
			}
			jsCode += '></div>';
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
		linkInput.addEventListener( 'input', updateButtonPreview );

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
			var buttonLink = sanitiseUrl( buttonLinkInput && buttonLinkInput.value );
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
				'<a href="' + escapeHtml( buttonLink ) + '" class="frw-donate-button frw-donate-button--medium" role="button" target="_blank" rel="noopener">' +
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
		var buttonLinkInput = document.getElementById( 'frw-image-button-link' );
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
			var buttonLink = sanitiseUrl( buttonLinkInput && buttonLinkInput.value );
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
					'<a href="' + escapeHtml( buttonLink ) + '" class="' + buttonClass + '" role="button" target="_blank" rel="noopener">' +
					'<span class="frw-button-text">Donate</span>' +
					'</a>' +
					'</div>';
			} else {
				buttonHtml = '<a href="' + escapeHtml( buttonLink ) + '" class="' + buttonClass + '" role="button" target="_blank" rel="noopener">' +
					'<span class="frw-button-text">Donate</span>' +
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
			if ( buttonLink !== 'https://donate.wikimedia.org' ) {
				wikitextCode += ' | button-link=' + buttonLink;
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
			if ( buttonLink !== 'https://donate.wikimedia.org' ) {
				jsCode += ' data-button-link="' + escapeAttr( buttonLink ) + '"';
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
		buttonLinkInput.addEventListener( 'input', updateImagePreview );

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
		var buttonLinkInput = document.getElementById( 'frw-rabbithole-button-link' );
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
			{ title: 'A Day on Venus', fact: 'A day on Venus (243 Earth days) is longer than its year (225 Earth days). Venus also rotates backwards, so the Sun rises in the west and sets in the east.', url: 'https://en.wikipedia.org/wiki/Venus' },
			{ title: 'The Dancing Plague of 1518', fact: 'In July 1518, hundreds of people in Strasbourg danced uncontrollably for days without rest. Some collapsed from exhaustion, strokes, or heart attacks. The cause remains a mystery.', url: 'https://en.wikipedia.org/wiki/Dancing_plague_of_1518' },
			{ title: 'Mantis Shrimp Vision', fact: 'Mantis shrimp have 16 types of color receptors compared to humans\' three. They can see ultraviolet light, polarized light, and have the most complex eyes in the animal kingdom.', url: 'https://en.wikipedia.org/wiki/Mantis_shrimp' },
			{ title: 'Immortal Jellyfish', fact: 'Turritopsis dohrnii can revert to its juvenile polyp stage after reaching maturity, essentially restarting its life cycle. It\'s the only known animal capable of complete biological immortality.', url: 'https://en.wikipedia.org/wiki/Turritopsis_dohrnii' },
			{ title: 'Diamond Rain on Saturn', fact: 'On Saturn and Jupiter, atmospheric pressure is so intense that it may crush carbon atoms into diamonds. Scientists believe it literally rains diamonds in their atmospheres.', url: 'https://en.wikipedia.org/wiki/Saturn' },
			{ title: 'The Antikythera Mechanism', fact: 'This 2,000-year-old Greek device is considered the world\'s first analog computer. It predicted astronomical positions and eclipses decades in advance using a complex system of gears.', url: 'https://en.wikipedia.org/wiki/Antikythera_mechanism' },
			{ title: 'Crows Remember Faces', fact: 'Crows can recognize and remember human faces for years. They communicate threats to other crows and can hold grudges against people who have wronged them.', url: 'https://en.wikipedia.org/wiki/Crow' },
			{ title: 'Neutron Star Density', fact: 'A teaspoon of neutron star material would weigh about 6 billion tons—roughly the weight of Mount Everest. Neutron stars are the collapsed cores of massive supergiant stars.', url: 'https://en.wikipedia.org/wiki/Neutron_star' },
			{ title: 'The Wow! Signal', fact: 'In 1977, a radio telescope detected a powerful 72-second signal from deep space. Astronomer Jerry Ehman circled it and wrote "Wow!" on the printout. Its origin remains unexplained.', url: 'https://en.wikipedia.org/wiki/Wow!_signal' },
			{ title: 'Slime Mold Intelligence', fact: 'Despite having no brain or nervous system, slime molds can solve mazes, learn patterns, and find the most efficient routes between food sources. They\'ve even recreated Tokyo\'s rail network.', url: 'https://en.wikipedia.org/wiki/Slime_mold' },
			{ title: 'The Fermi Paradox', fact: 'Given the high probability of extraterrestrial life in the universe, why haven\'t we detected any signs of it? This contradiction between expectation and reality puzzles scientists to this day.', url: 'https://en.wikipedia.org/wiki/Fermi_paradox' },
			{ title: 'The Overview Effect', fact: 'Astronauts often experience a profound cognitive shift when viewing Earth from space. This "overview effect" creates feelings of awe and a new perspective on the fragility of our planet.', url: 'https://en.wikipedia.org/wiki/Overview_effect' },
			{ title: 'Elephant Memory', fact: 'Elephants can remember locations, individuals, and events for decades. They\'ve been observed visiting the bones of deceased family members years after death, gently touching them with their trunks.', url: 'https://en.wikipedia.org/wiki/Elephant_cognition' }
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
			var buttonLink = sanitiseUrl( buttonLinkInput && buttonLinkInput.value );

			preview.innerHTML = '<div class="frw-rabbit-hole frw-rabbit-hole--' + escapeHtml( theme ) + '">' +
				'<div class="frw-rabbit-hole-content">' +
				'<div class="frw-rabbit-hole-intro">' +
				'<h3 class="frw-rabbit-hole-title">Wikipedia Rabbit Hole</h3>' +
				'<p class="frw-rabbit-hole-subtitle">Click to discover something fascinating</p>' +
				'</div>' +
				'<div class="frw-rabbit-hole-discovery frw-rabbit-hole-discovery--hidden"></div>' +
				'<div class="frw-rabbit-hole-donate frw-rabbit-hole-donate--hidden">' +
				'<p class="frw-rabbit-hole-donate-message">Enjoying the journey? Wikipedia is free, but not free to run. Help keep knowledge accessible for everyone.</p>' +
				'<a href="' + escapeHtml( buttonLink ) + '" class="frw-donate-button frw-donate-button--medium frw-donate-button--green" role="button" target="_blank" rel="noopener">' +
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

				// Show donate prompt after every N discoveries (not on the Nth click)
				if ( clickCount % ( donateAfter + 1 ) === 0 ) {
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
			if ( buttonLink !== 'https://donate.wikimedia.org' ) {
				wikitextCode += ' | button-link=' + buttonLink;
			}
			wikitextCode += ' }}';
			codeOutputWikitext.textContent = wikitextCode;

			// Update JavaScript code output
			var jsCode = '<script src="' + serverUrl + extensionPath + '/resources/embed.js"></script>\n' +
				'<div class="frw-embed" data-widget="rabbithole" data-theme="' + theme + '" data-donate-after="' + donateAfter + '"';
			if ( buttonText !== 'Discover something new' ) {
				jsCode += ' data-button-text="' + escapeAttr( buttonText ) + '"';
			}
			if ( buttonLink !== 'https://donate.wikimedia.org' ) {
				jsCode += ' data-button-link="' + escapeAttr( buttonLink ) + '"';
			}
			jsCode += '></div>';
			codeOutputJs.textContent = jsCode;
		}

		themeSelect.addEventListener( 'change', updateRabbitHolePreview );
		donateAfterSelect.addEventListener( 'change', updateRabbitHolePreview );
		buttonTextInput.addEventListener( 'input', updateRabbitHolePreview );
		buttonLinkInput.addEventListener( 'input', updateRabbitHolePreview );

		// Initial render
		updateRabbitHolePreview();
	}

	function initWikipediaButtonConfigurator() {
		var sizeSelect = document.getElementById( 'frw-wikipedia-button-size');
		var textInput = document.getElementById( 'frw-wikipedia-button-text' );
		var linkInput = document.getElementById( 'frw-wikipedia-button-link' );
		var preview = document.getElementById( 'frw-wikipedia-button-preview' );
		var codeOutputWikitext = document.getElementById( 'frw-wikipedia-button-code-wikitext' );
		var codeOutputJs = document.getElementById( 'frw-wikipedia-button-code-js' );
		var colorPicker = document.getElementById( 'frw-wikipedia-button-colors' );

		if ( !sizeSelect || !preview ) {
			return;
		}

		var currentColor = 'blue';

		function updateWikipediaButtonPreview() {
			var size = sizeSelect.value;
			var text = textInput.value || 'Built on Wikipedia';
			var link = sanitiseUrl( linkInput && linkInput.value );

			preview.innerHTML = '<a href="' + escapeHtml( link ) + '" ' +
				'class="frw-wikipedia-button frw-wikipedia-button--' + escapeHtml( size ) + ' frw-wikipedia-button--' + escapeHtml( currentColor ) + '" role="button" target="_blank" rel="noopener">' +
				'<span class="frw-button-text">' + escapeHtml( text ) + '</span>' +
				'</a>';

			// Update wikitext code output
			var wikiTextCode = '{{#fundraising-wikipedia-button: size=' + size + ' | text=' + text + ' | color=' + currentColor;
			if (link !== DEFAULT_WIKIPEDIA_URL) {
				wikiTextCode += ' | button-link=' + link;
			}
			wikiTextCode += ' }}';
			codeOutputWikitext.textContent = wikiTextCode;

			// Update JavaScript code output
			var jsCode = '<script src="' + serverUrl + extensionPath + '/resources/embed.js"></script>\n' +
				'<div class="frw-embed" data-widget="fundraising-wikipedia-button" data-size="' + size + '" data-text="' + escapeAttr( text ) + '" data-color="' + currentColor + '"';
			if ( link !== DEFAULT_WIKIPEDIA_URL ) {
				jsCode += ' data-button-link="' + escapeAttr( link ) + '"';
			}
			jsCode += '></div>';
			codeOutputJs.textContent = jsCode;
		}

		// Color picker functionality
		if( colorPicker ) {
			var colorButtons = colorPicker.querySelectorAll( '.frw-color-option' );
			colorButtons.forEach( function (btn) {
				btn.addEventListener( 'click', function() {
					colorButtons.forEach( function ( b ) {
						b.classList.remove('frw-color-option--selected');
					});
					btn.classList.add( 'frw-color-option--selected');
					currentColor = btn.dataset.color;
					updateWikipediaButtonPreview();
				});
			} );
		}

		sizeSelect.addEventListener( 'change', updateWikipediaButtonPreview);
		textInput.addEventListener( 'input', updateWikipediaButtonPreview);
		linkInput.addEventListener( 'input', updateWikipediaButtonPreview);

		// Initial render
		updateWikipediaButtonPreview();
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
		initWikipediaButtonConfigurator();
		initCopyButtons();
	}

	// Run initialization when DOM is ready
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
