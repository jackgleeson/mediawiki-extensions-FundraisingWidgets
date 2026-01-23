/**
 * FundraisingWidgets - Standalone Embed Script
 * Allows embedding fundraising widgets on external sites
 *
 * BROWSER COMPATIBILITY NOTE:
 * This script intentionally uses ES5 syntax (var, function declarations,
 * string concatenation, etc.) instead of ES6+ features (let/const, arrow
 * functions, template literals, etc.) to ensure compatibility with older
 * browsers like IE11. Since this script runs on third-party sites where
 * we cannot control the browser environment, we prioritize broad compatibility.
 */
( function ( global ) {
	'use strict';

	var FundraisingWidgets = {
		// Base URL for assets (will be auto-detected from script src)
		baseUrl: '',

		// CSS loaded flag
		cssLoaded: false,

		// Wikimedia Commons image URLs for external embedding
		commonsImages: {
			globe: 'https://upload.wikimedia.org/wikipedia/commons/8/80/Wikipedia-logo-v2.svg',
			globeHands: 'https://upload.wikimedia.org/wikipedia/commons/1/1f/Wikipedia_mini_globe_handheld.jpg',
			wordmark: 'https://donate.wikimedia.org/static/images/mobile/copyright/wikipedia-wordmark-en.svg',
			wmf: 'https://upload.wikimedia.org/wikipedia/donate/6/60/Wmf_logo_horiz_K.svg'
		},

		/**
		 * Initialize all widgets on the page
		 */
		init: function () {
			this.detectBaseUrl();
			this.loadStyles();
			this.renderWidgets();
		},

		/**
		 * Detect base URL from the script src
		 */
		detectBaseUrl: function () {
			var scripts = document.getElementsByTagName( 'script' );
			for ( var i = 0; i < scripts.length; i++ ) {
				var src = scripts[ i ].src || '';
				if ( src.indexOf( 'embed.js' ) !== -1 ) {
					this.baseUrl = src.replace( /\/resources\/embed\.js.*$/, '' );
					break;
				}
			}
		},

		/**
		 * Load widget styles
		 */
		loadStyles: function () {
			if ( this.cssLoaded ) {
				return;
			}

			var cssFiles = [
				'/resources/ext.fundraisingWidgets/variables.css',
				'/resources/ext.fundraisingWidgets/donateButton.css',
				'/resources/ext.fundraisingWidgets/banner.css',
				'/resources/ext.fundraisingWidgets/imageWidget.css',
				'/resources/ext.fundraisingWidgets/rabbitHole.css'
			];

			for ( var i = 0; i < cssFiles.length; i++ ) {
				var link = document.createElement( 'link' );
				link.rel = 'stylesheet';
				link.href = this.baseUrl + cssFiles[ i ];
				document.head.appendChild( link );
			}

			this.cssLoaded = true;
		},

		/**
		 * Render all widgets found in the DOM
		 */
		renderWidgets: function () {
			var containers = document.querySelectorAll( '.frw-embed' );
			for ( var i = 0; i < containers.length; i++ ) {
				this.renderWidget( containers[ i ] );
			}
		},

		/**
		 * Render a single widget
		 */
		renderWidget: function ( container ) {
			var widgetType = container.getAttribute( 'data-widget' );

			switch ( widgetType ) {
				case 'button':
					this.renderButton( container );
					break;
				case 'banner':
					this.renderBanner( container );
					break;
				case 'image':
					this.renderImage( container );
					break;
				case 'rabbithole':
					this.renderRabbitHole( container );
					break;
				default:
					console.warn( 'FundraisingWidgets: Unknown widget type "' + widgetType + '"' );
			}
		},

		/**
		 * Render a donate button widget
		 */
		renderButton: function ( container ) {
			var size = container.getAttribute( 'data-size' ) || 'medium';
			var text = container.getAttribute( 'data-text' ) || 'Support Wikipedia';
			var color = container.getAttribute( 'data-color' ) || 'blue';
			var link = container.getAttribute( 'data-link' ) || 'https://donate.wikimedia.org';

			var button = document.createElement( 'a' );
			button.href = link;
			button.className = 'frw-donate-button frw-donate-button--' + this.escapeAttr( size ) + ' frw-donate-button--' + this.escapeAttr( color );
			button.setAttribute( 'role', 'button' );

			var span = document.createElement( 'span' );
			span.className = 'frw-button-text';
			span.textContent = text;

			button.appendChild( span );
			container.appendChild( button );
		},

		/**
		 * Render a banner widget
		 */
		renderBanner: function ( container ) {
			var message = container.getAttribute( 'data-message' ) || 'If Wikipedia has given you useful knowledge this year, please give back.';
			var buttonText = container.getAttribute( 'data-button-text' ) || 'Donate';
			var buttonLink = container.getAttribute( 'data-button-link' ) || 'https://donate.wikimedia.org';
			var logo = container.getAttribute( 'data-logo' ) || 'globe';
			var dismissible = container.getAttribute( 'data-dismissible' ) === 'true';

			var banner = document.createElement( 'div' );
			banner.className = 'frw-banner' + ( dismissible ? ' frw-banner--dismissible' : '' );
			banner.setAttribute( 'role', 'banner' );

			// Close button
			if ( dismissible ) {
				var closeBtn = document.createElement( 'button' );
				closeBtn.type = 'button';
				closeBtn.className = 'frw-banner-close';
				closeBtn.setAttribute( 'aria-label', 'Close' );

				var closeSpan = document.createElement( 'span' );
				closeSpan.setAttribute( 'aria-hidden', 'true' );
				closeSpan.innerHTML = '&times;';
				closeBtn.appendChild( closeSpan );

				closeBtn.addEventListener( 'click', function () {
					banner.classList.add( 'frw-banner--hidden' );
				} );

				banner.appendChild( closeBtn );
			}

			// Logo - use Wikimedia Commons URLs
			if ( logo !== 'none' ) {
				var logoContainer = document.createElement( 'div' );
				logoContainer.className = 'frw-banner-logo frw-banner-logo--' + this.escapeAttr( logo );

				switch ( logo ) {
					case 'globe':
						logoContainer.innerHTML = '<img src="' + this.commonsImages.globe + '" class="frw-logo frw-logo-globe" alt="Wikipedia">';
						break;
					case 'globe-hands':
						logoContainer.innerHTML = '<img src="' + this.commonsImages.globeHands + '" class="frw-logo frw-logo-globe-hands" alt="Wikipedia">';
						break;
					case 'wordmark':
						logoContainer.innerHTML = '<img src="' + this.commonsImages.wordmark + '" class="frw-logo frw-logo-wordmark" alt="Wikipedia">';
						break;
					case 'combined':
						logoContainer.innerHTML = '<img src="' + this.commonsImages.globe + '" class="frw-logo frw-logo-globe" alt="">' +
							'<img src="' + this.commonsImages.wordmark + '" class="frw-logo frw-logo-wordmark" alt="Wikipedia">';
						break;
					case 'wmf':
						logoContainer.innerHTML = '<img src="' + this.commonsImages.wmf + '" class="frw-logo frw-logo-wmf" alt="Wikimedia Foundation">';
						break;
				}

				banner.appendChild( logoContainer );
			}

			// Content
			var content = document.createElement( 'div' );
			content.className = 'frw-banner-content';

			var messagePara = document.createElement( 'p' );
			messagePara.className = 'frw-banner-message';
			messagePara.textContent = message;
			content.appendChild( messagePara );

			var donateBtn = document.createElement( 'a' );
			donateBtn.href = buttonLink;
			donateBtn.className = 'frw-donate-button frw-donate-button--medium';
			donateBtn.setAttribute( 'role', 'button' );

			var btnSpan = document.createElement( 'span' );
			btnSpan.className = 'frw-button-text';
			btnSpan.textContent = buttonText;
			donateBtn.appendChild( btnSpan );

			content.appendChild( donateBtn );
			banner.appendChild( content );
			container.appendChild( banner );
		},

		/**
		 * Render an image widget
		 */
		renderImage: function ( container ) {
			var image = container.getAttribute( 'data-image' ) || 'snow-leopard';
			var size = container.getAttribute( 'data-size' ) || 'medium';
			var position = container.getAttribute( 'data-button-position' ) || 'bottom-right';
			var color = container.getAttribute( 'data-button-color' ) || 'blue';
			var caption = container.getAttribute( 'data-caption' );
			if ( caption === null ) {
				caption = 'You mean I can donate to this thing!!!';
			}
			var buttonText = container.getAttribute( 'data-button-text' ) || 'Donate';
			var buttonLink = container.getAttribute( 'data-button-link' ) || 'https://donate.wikimedia.org';

			var validPositions = [ 'top-left', 'top-right', 'bottom-left', 'bottom-right' ];
			var validColors = [ 'blue', 'purple', 'green', 'red', 'yellow' ];

			if ( validPositions.indexOf( position ) === -1 ) {
				position = 'bottom-right';
			}
			if ( validColors.indexOf( color ) === -1 ) {
				color = 'blue';
			}

			var imageUrls = {
				landscape: 'https://upload.wikimedia.org/wikipedia/commons/8/81/Parque_Eagle_River%2C_Anchorage%2C_Alaska%2C_Estados_Unidos%2C_2017-09-01%2C_DD_02.jpg',
				eagle: 'https://upload.wikimedia.org/wikipedia/commons/0/09/001_Wild_Golden_Eagle_and_Majinghorn_Pfyn-Finges_Photo_by_Giles_Laurent.jpg',
				mountain: 'https://upload.wikimedia.org/wikipedia/commons/0/01/Pointe_de_Nantaux_05.jpg',
				'snow-leopard': 'https://upload.wikimedia.org/wikipedia/commons/f/f3/Snow_Leopard_Looking_Up.jpg',
				frog: 'https://upload.wikimedia.org/wikipedia/commons/3/35/Strawberry_poison_dart_frog_%2870539%29.jpg',
				'cat-lizard': 'https://upload.wikimedia.org/wikipedia/commons/7/72/Cat_playing_with_a_lizard.jpg',
				'regal-cat': 'https://upload.wikimedia.org/wikipedia/commons/4/4d/Cat_November_2010-1a.jpg'
			};

			var imageUrl = imageUrls[ image ] || imageUrls[ 'snow-leopard' ];
			var hasCaption = caption.length > 0;

			var widget = document.createElement( 'div' );
			widget.className = 'frw-image-widget frw-image-widget--' + this.escapeAttr( size );
			if ( hasCaption ) {
				widget.className += ' frw-image-widget--has-caption';
			} else {
				widget.className += ' frw-image-widget--button-' + this.escapeAttr( position );
			}
			widget.style.backgroundImage = 'url(\'' + imageUrl + '\')';

			var buttonClass = 'frw-donate-button frw-donate-button--medium frw-donate-button--' + this.escapeAttr( color );

			var overlay = document.createElement( 'div' );
			overlay.className = 'frw-image-overlay';

			// If no caption, put button in overlay
			if ( !hasCaption ) {
				var donateBtn = document.createElement( 'a' );
				donateBtn.href = buttonLink;
				donateBtn.className = buttonClass;
				donateBtn.setAttribute( 'role', 'button' );

				var btnSpan = document.createElement( 'span' );
				btnSpan.className = 'frw-button-text';
				btnSpan.textContent = buttonText;
				donateBtn.appendChild( btnSpan );

				overlay.appendChild( donateBtn );
			}

			widget.appendChild( overlay );

			// If caption, create caption bar with button
			if ( hasCaption ) {
				var captionBar = document.createElement( 'div' );
				captionBar.className = 'frw-image-caption-bar';

				var captionEl = document.createElement( 'p' );
				captionEl.className = 'frw-image-caption';
				captionEl.textContent = caption;
				captionBar.appendChild( captionEl );

				var donateBtn2 = document.createElement( 'a' );
				donateBtn2.href = buttonLink;
				donateBtn2.className = buttonClass;
				donateBtn2.setAttribute( 'role', 'button' );

				var btnSpan2 = document.createElement( 'span' );
				btnSpan2.className = 'frw-button-text';
				btnSpan2.textContent = buttonText;
				donateBtn2.appendChild( btnSpan2 );

				captionBar.appendChild( donateBtn2 );
				widget.appendChild( captionBar );
			}

			container.appendChild( widget );
		},

		/**
		 * Render a rabbit hole widget
		 */
		renderRabbitHole: function ( container ) {
			var theme = container.getAttribute( 'data-theme' ) || 'light';
			var donateAfter = parseInt( container.getAttribute( 'data-donate-after' ), 10 ) || 3;
			var buttonText = container.getAttribute( 'data-button-text' ) || 'Discover something new';
			var donateLink = container.getAttribute( 'data-donate-link' ) || 'https://donate.wikimedia.org';
			var self = this;

			// Curated interesting Wikipedia facts and articles
			var discoveries = [
				{
					title: 'Tardigrades',
					fact: 'These microscopic creatures can survive the vacuum of space, temperatures from -272°C to 150°C, radiation 1,000 times the lethal human dose, and have survived all five mass extinctions over 500 million years.',
					url: 'https://en.wikipedia.org/wiki/Tardigrade'
				},
				{
					title: 'Sharks Are Older Than Trees',
					fact: 'Sharks first appeared about 450 million years ago, while the earliest trees evolved around 350 million years ago. Sharks have survived all five mass extinctions.',
					url: 'https://en.wikipedia.org/wiki/Shark'
				},
				{
					title: 'Honey Never Spoils',
					fact: 'Archaeologists found 3,000-year-old honey in Egyptian tombs that was still perfectly edible. Its low moisture, high acidity, and natural hydrogen peroxide prevent bacterial growth.',
					url: 'https://en.wikipedia.org/wiki/Honey'
				},
				{
					title: 'Octopus Biology',
					fact: 'Octopuses have three hearts, blue copper-based blood, and can edit their own RNA to adapt to cold temperatures—a capability far beyond most other animals.',
					url: 'https://en.wikipedia.org/wiki/Octopus'
				},
				{
					title: 'Cleopatra and the Pyramids',
					fact: 'Cleopatra lived closer in time to the Moon landing than to the construction of the Great Pyramid. She was born 2,500 years after the pyramids but only 2,000 years before Apollo 11.',
					url: 'https://en.wikipedia.org/wiki/Cleopatra'
				},
				{
					title: 'Oxford and the Aztecs',
					fact: 'Oxford University is older than the Aztec Empire by over 200 years. Teaching began at Oxford in 1096, while the Aztec capital Tenochtitlan was founded in 1325.',
					url: 'https://en.wikipedia.org/wiki/University_of_Oxford'
				},
				{
					title: 'Bananas Are Berries',
					fact: 'Botanically, bananas are berries because they develop from a single ovary. Strawberries, raspberries, and blackberries are not true berries—they are aggregate fruits.',
					url: 'https://en.wikipedia.org/wiki/Berry_(botany)'
				},
				{
					title: 'A Day on Venus',
					fact: 'A day on Venus (243 Earth days) is longer than its year (225 Earth days). Venus also rotates backwards, so the Sun rises in the west and sets in the east.',
					url: 'https://en.wikipedia.org/wiki/Venus'
				},
				{
					title: 'Voyager Golden Record',
					fact: 'In 1977, NASA launched two golden records into space containing 115 images, greetings in 55 languages, and 90 minutes of music—a message for any extraterrestrial life that might find them.',
					url: 'https://en.wikipedia.org/wiki/Voyager_Golden_Record'
				},
				{
					title: 'Great Wall of China Myth',
					fact: 'Despite popular belief, the Great Wall of China is not visible from space with the naked eye. This myth existed before the Space Age, and astronauts have confirmed it is false.',
					url: 'https://en.wikipedia.org/wiki/Great_Wall_of_China'
				},
				{
					title: 'The Eiffel Tower Grows',
					fact: 'Due to thermal expansion of its iron structure, the Eiffel Tower can grow up to 15 centimeters taller in summer. It also tilts slightly away from the sun throughout the day.',
					url: 'https://en.wikipedia.org/wiki/Eiffel_Tower'
				},
				{
					title: 'Human and Chimpanzee DNA',
					fact: 'Humans and chimpanzees share approximately 98-99% of their DNA sequence. Yet that tiny 1-2% difference accounts for our bipedal stance, language ability, and complex technology.',
					url: 'https://en.wikipedia.org/wiki/Chimpanzee'
				}
			];

			var seenIndices = [];
			var clickCount = 0;

			// Create widget structure
			var widget = document.createElement( 'div' );
			widget.className = 'frw-rabbit-hole frw-rabbit-hole--' + this.escapeAttr( theme );

			var content = document.createElement( 'div' );
			content.className = 'frw-rabbit-hole-content';

			// Intro section
			var intro = document.createElement( 'div' );
			intro.className = 'frw-rabbit-hole-intro';
			intro.innerHTML = '<h3 class="frw-rabbit-hole-title">Wikipedia Rabbit Hole</h3>' +
				'<p class="frw-rabbit-hole-subtitle">Click to discover something fascinating</p>';

			// Discovery section
			var discovery = document.createElement( 'div' );
			discovery.className = 'frw-rabbit-hole-discovery frw-rabbit-hole-discovery--hidden';

			// Donate section
			var donate = document.createElement( 'div' );
			donate.className = 'frw-rabbit-hole-donate frw-rabbit-hole-donate--hidden';
			donate.innerHTML = '<p class="frw-rabbit-hole-donate-message">Enjoying the journey? Wikipedia is free, but not free to run. Help keep knowledge accessible for everyone.</p>' +
				'<a href="' + this.escapeAttr( donateLink ) + '" class="frw-donate-button frw-donate-button--medium frw-donate-button--green" role="button">' +
				'<span class="frw-button-text">Donate</span></a>';

			content.appendChild( intro );
			content.appendChild( discovery );
			content.appendChild( donate );

			// Footer with button and counter
			var footer = document.createElement( 'div' );
			footer.className = 'frw-rabbit-hole-footer';

			var button = document.createElement( 'button' );
			button.type = 'button';
			button.className = 'frw-rabbit-hole-button';
			button.textContent = buttonText;

			var counter = document.createElement( 'span' );
			counter.className = 'frw-rabbit-hole-counter';

			footer.appendChild( button );
			footer.appendChild( counter );

			widget.appendChild( content );
			widget.appendChild( footer );

			/**
			 * Get a random discovery that hasn't been seen yet
			 */
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

			/**
			 * Show a discovery
			 */
			function showDiscovery( disc ) {
				discovery.innerHTML =
					'<h4 class="frw-rabbit-hole-discovery-title">' +
					'<a href="' + self.escapeAttr( disc.url ) + '" target="_blank" rel="noopener">' +
					self.escapeHtml( disc.title ) +
					'</a></h4>' +
					'<p class="frw-rabbit-hole-discovery-fact">' + self.escapeHtml( disc.fact ) + '</p>' +
					'<a href="' + self.escapeAttr( disc.url ) + '" class="frw-rabbit-hole-discovery-link" target="_blank" rel="noopener">' +
					'Read more on Wikipedia →</a>';

				discovery.classList.remove( 'frw-rabbit-hole-discovery--hidden' );
				discovery.style.animation = 'none';
				discovery.offsetHeight;
				discovery.style.animation = '';
			}

			/**
			 * Show the donate prompt
			 */
			function showDonatePrompt() {
				intro.classList.add( 'frw-rabbit-hole-intro--hidden' );
				discovery.classList.add( 'frw-rabbit-hole-discovery--hidden' );
				donate.classList.remove( 'frw-rabbit-hole-donate--hidden' );
				button.textContent = 'Keep exploring';
			}

			/**
			 * Update the counter display
			 */
			function updateCounter() {
				if ( clickCount > 0 ) {
					counter.textContent = clickCount + ' discovered';
				}
			}

			// Handle button clicks
			button.addEventListener( 'click', function () {
				clickCount++;

				if ( clickCount === 1 ) {
					intro.classList.add( 'frw-rabbit-hole-intro--hidden' );
				}

				// Show donate prompt after every N discoveries (not on the Nth click)
				if ( clickCount % ( donateAfter + 1 ) === 0 ) {
					showDonatePrompt();
				} else {
					donate.classList.add( 'frw-rabbit-hole-donate--hidden' );
					var disc = getRandomDiscovery();
					showDiscovery( disc );
					button.textContent = 'Discover another';
				}

				updateCounter();
			} );

			container.appendChild( widget );
		},

		/**
		 * Escape characters for CSS class names
		 */
		escapeAttr: function ( str ) {
			return String( str ).replace( /[^a-zA-Z0-9-_]/g, '' );
		},

		/**
		 * Escape HTML special characters
		 */
		escapeHtml: function ( text ) {
			var div = document.createElement( 'div' );
			div.textContent = text;
			return div.innerHTML;
		}
	};

	// Expose to global scope
	global.FundraisingWidgets = FundraisingWidgets;

	// Auto-initialize when DOM is ready
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			FundraisingWidgets.init();
		} );
	} else {
		FundraisingWidgets.init();
	}

} )( typeof window !== 'undefined' ? window : this );
