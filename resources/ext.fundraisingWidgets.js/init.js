/**
 * FundraisingWidgets - JavaScript Initialization
 */
( function () {
	'use strict';

	/**
	 * Initialize banner dismiss functionality
	 */
	function initBannerDismiss() {
		var closeButtons = document.querySelectorAll( '.frw-banner-close' );

		closeButtons.forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				var banner = button.closest( '.frw-banner' );
				if ( banner ) {
					banner.classList.add( 'frw-banner--hidden' );
					banner.setAttribute( 'aria-hidden', 'true' );
				}
			} );
		} );
	}

	/**
	 * Initialize logo image paths using Wikimedia Commons URLs
	 */
	function initLogos() {
		var logos = document.querySelectorAll( '.frw-logo[data-logo]' );

		// Use Wikimedia Commons URLs for consistent external access
		var logoUrls = {
			globe: 'https://upload.wikimedia.org/wikipedia/commons/8/80/Wikipedia-logo-v2.svg',
			'globe-hands': 'https://upload.wikimedia.org/wikipedia/commons/1/1f/Wikipedia_mini_globe_handheld.jpg',
			wordmark: 'https://donate.wikimedia.org/static/images/mobile/copyright/wikipedia-wordmark-en.svg',
			wmf: 'https://upload.wikimedia.org/wikipedia/donate/6/60/Wmf_logo_horiz_K.svg'
		};

		logos.forEach( function ( logo ) {
			var logoType = logo.dataset.logo;
			if ( logoUrls[ logoType ] ) {
				logo.src = logoUrls[ logoType ];
			}
		} );
	}

	/**
	 * Initialize rabbit hole widget functionality
	 */
	function initRabbitHole() {
		var widgets = document.querySelectorAll( '.frw-rabbit-hole' );

		widgets.forEach( function ( widget ) {
			var discoveries = [];
			var seenIndices = [];
			var clickCount = 0;
			var donateAfter = parseInt( widget.dataset.donateAfter, 10 ) || 3;
			var donateLink = widget.dataset.donateLink || 'https://donate.wikimedia.org';

			// Parse discoveries from data attribute
			try {
				discoveries = JSON.parse( widget.dataset.discoveries || '[]' );
			} catch ( e ) {
				console.error( 'Failed to parse discoveries:', e );
				return;
			}

			var introEl = widget.querySelector( '.frw-rabbit-hole-intro' );
			var discoveryEl = widget.querySelector( '.frw-rabbit-hole-discovery' );
			var donateEl = widget.querySelector( '.frw-rabbit-hole-donate' );
			var buttonEl = widget.querySelector( '.frw-rabbit-hole-button' );
			var counterEl = widget.querySelector( '.frw-rabbit-hole-counter' );

			if ( !buttonEl || discoveries.length === 0 ) {
				return;
			}

			/**
			 * Get a random discovery that hasn't been seen yet
			 */
			function getRandomDiscovery() {
				// If all seen, reset
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
			function showDiscovery( discovery ) {
				discoveryEl.innerHTML =
					'<h4 class="frw-rabbit-hole-discovery-title">' +
					'<a href="' + escapeHtml( discovery.url ) + '" target="_blank" rel="noopener">' +
					escapeHtml( discovery.title ) +
					'</a></h4>' +
					'<p class="frw-rabbit-hole-discovery-fact">' + escapeHtml( discovery.fact ) + '</p>' +
					'<a href="' + escapeHtml( discovery.url ) + '" class="frw-rabbit-hole-discovery-link" target="_blank" rel="noopener">' +
					'Read more on Wikipedia →</a>';

				// Force re-animation
				discoveryEl.classList.remove( 'frw-rabbit-hole-discovery--hidden' );
				discoveryEl.style.animation = 'none';
				discoveryEl.offsetHeight; // Trigger reflow
				discoveryEl.style.animation = '';
			}

			/**
			 * Show the donate prompt
			 */
			function showDonatePrompt() {
				introEl.classList.add( 'frw-rabbit-hole-intro--hidden' );
				discoveryEl.classList.add( 'frw-rabbit-hole-discovery--hidden' );
				donateEl.classList.remove( 'frw-rabbit-hole-donate--hidden' );
				buttonEl.textContent = 'Keep exploring';
			}

			/**
			 * Update the counter display
			 */
			function updateCounter() {
				if ( clickCount > 0 ) {
					counterEl.textContent = clickCount + ' discovered';
				}
			}

			/**
			 * Escape HTML special characters
			 */
			function escapeHtml( text ) {
				var div = document.createElement( 'div' );
				div.textContent = text;
				return div.innerHTML;
			}

			/**
			 * Handle button click
			 */
			buttonEl.addEventListener( 'click', function () {
				clickCount++;

				// Hide intro on first click
				if ( clickCount === 1 ) {
					introEl.classList.add( 'frw-rabbit-hole-intro--hidden' );
				}

				// Show donate prompt after every N discoveries (not on the Nth click)
				if ( clickCount % ( donateAfter + 1 ) === 0 ) {
					showDonatePrompt();
				} else {
					// Show a discovery
					donateEl.classList.add( 'frw-rabbit-hole-donate--hidden' );
					var discovery = getRandomDiscovery();
					showDiscovery( discovery );
					buttonEl.textContent = 'Discover another';
				}

				updateCounter();
			} );
		} );
	}

	/**
	 * Initialize all widgets when DOM is ready
	 */
	function init() {
		initBannerDismiss();
		initLogos();
		initRabbitHole();
	}

	// Run initialization when DOM is ready
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
