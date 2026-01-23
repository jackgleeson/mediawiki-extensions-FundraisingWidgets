<?php

namespace MediaWiki\Extension\FundraisingWidgets\Widgets;

use Parser;
use PPFrame;

class RabbitHole {

	private const VALID_THEMES = [ 'light', 'dark' ];

	/**
	 * Curated interesting Wikipedia facts and articles
	 */
	private const DISCOVERIES = [
		[
			'title' => 'Tardigrades',
			'fact' => 'These microscopic creatures can survive the vacuum of space, temperatures from -272°C to 150°C, radiation 1,000 times the lethal human dose, and have survived all five mass extinctions over 500 million years.',
			'url' => 'https://en.wikipedia.org/wiki/Tardigrade',
		],
		[
			'title' => 'Sharks Are Older Than Trees',
			'fact' => 'Sharks first appeared about 450 million years ago, while the earliest trees evolved around 350 million years ago. Sharks have survived all five mass extinctions.',
			'url' => 'https://en.wikipedia.org/wiki/Shark',
		],
		[
			'title' => 'Honey Never Spoils',
			'fact' => 'Archaeologists found 3,000-year-old honey in Egyptian tombs that was still perfectly edible. Its low moisture, high acidity, and natural hydrogen peroxide prevent bacterial growth.',
			'url' => 'https://en.wikipedia.org/wiki/Honey',
		],
		[
			'title' => 'Octopus Biology',
			'fact' => 'Octopuses have three hearts, blue copper-based blood, and can edit their own RNA to adapt to cold temperatures—a capability far beyond most other animals.',
			'url' => 'https://en.wikipedia.org/wiki/Octopus',
		],
		[
			'title' => 'Cleopatra and the Pyramids',
			'fact' => 'Cleopatra lived closer in time to the Moon landing than to the construction of the Great Pyramid. She was born 2,500 years after the pyramids but only 2,000 years before Apollo 11.',
			'url' => 'https://en.wikipedia.org/wiki/Cleopatra',
		],
		[
			'title' => 'Oxford and the Aztecs',
			'fact' => 'Oxford University is older than the Aztec Empire by over 200 years. Teaching began at Oxford in 1096, while the Aztec capital Tenochtitlan was founded in 1325.',
			'url' => 'https://en.wikipedia.org/wiki/University_of_Oxford',
		],
		[
			'title' => 'Bananas Are Berries',
			'fact' => 'Botanically, bananas are berries because they develop from a single ovary. Strawberries, raspberries, and blackberries are not true berries—they are aggregate fruits.',
			'url' => 'https://en.wikipedia.org/wiki/Berry_(botany)',
		],
		[
			'title' => 'A Day on Venus',
			'fact' => 'A day on Venus (243 Earth days) is longer than its year (225 Earth days). Venus also rotates backwards, so the Sun rises in the west and sets in the east.',
			'url' => 'https://en.wikipedia.org/wiki/Venus',
		],
		[
			'title' => 'Voyager Golden Record',
			'fact' => 'In 1977, NASA launched two golden records into space containing 115 images, greetings in 55 languages, and 90 minutes of music—a message for any extraterrestrial life that might find them.',
			'url' => 'https://en.wikipedia.org/wiki/Voyager_Golden_Record',
		],
		[
			'title' => 'Great Wall of China Myth',
			'fact' => 'Despite popular belief, the Great Wall of China is not visible from space with the naked eye. This myth existed before the Space Age, and astronauts have confirmed it is false.',
			'url' => 'https://en.wikipedia.org/wiki/Great_Wall_of_China',
		],
		[
			'title' => 'The Eiffel Tower Grows',
			'fact' => 'Due to thermal expansion of its iron structure, the Eiffel Tower can grow up to 15 centimeters taller in summer. It also tilts slightly away from the sun throughout the day.',
			'url' => 'https://en.wikipedia.org/wiki/Eiffel_Tower',
		],
		[
			'title' => 'Human and Chimpanzee DNA',
			'fact' => 'Humans and chimpanzees share approximately 98-99% of their DNA sequence. Yet that tiny 1-2% difference accounts for our bipedal stance, language ability, and complex technology.',
			'url' => 'https://en.wikipedia.org/wiki/Chimpanzee',
		],
	];

	/**
	 * Render the rabbit hole widget
	 *
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @param array $args
	 * @return array
	 */
	public static function render( Parser $parser, PPFrame $frame, array $args ): array {
		$parser->getOutput()->addModules( [ 'ext.fundraisingWidgets.js' ] );
		$parser->getOutput()->addModuleStyles( [ 'ext.fundraisingWidgets' ] );

		$params = self::parseArgs( $frame, $args );

		$theme = in_array( $params['theme'], self::VALID_THEMES, true )
			? $params['theme']
			: 'light';
		$donateAfter = max( 1, min( 10, (int)$params['donate-after'] ) );
		$buttonText = $params['button-text'] ?: wfMessage( 'fundraisingwidgets-rabbithole-discover' )->text();
		$donateLink = $params['button-link'] ?: 'https://donate.wikimedia.org';

		// Encode discoveries as JSON for JavaScript
		$discoveriesJson = htmlspecialchars( json_encode( self::DISCOVERIES ), ENT_QUOTES );

		$html = sprintf(
			'<div class="frw-rabbit-hole frw-rabbit-hole--%s" data-donate-after="%d" data-button-link="%s" data-discoveries="%s">' .
			'<div class="frw-rabbit-hole-content">' .
			'<div class="frw-rabbit-hole-intro">' .
			'<h3 class="frw-rabbit-hole-title">%s</h3>' .
			'<p class="frw-rabbit-hole-subtitle">%s</p>' .
			'</div>' .
			'<div class="frw-rabbit-hole-discovery frw-rabbit-hole-discovery--hidden"></div>' .
			'<div class="frw-rabbit-hole-donate frw-rabbit-hole-donate--hidden">' .
			'<p class="frw-rabbit-hole-donate-message">%s</p>' .
			'<a href="%s" class="frw-donate-button frw-donate-button--medium frw-donate-button--green" role="button">' .
			'<span class="frw-button-text">%s</span>' .
			'</a>' .
			'</div>' .
			'</div>' .
			'<div class="frw-rabbit-hole-footer">' .
			'<button type="button" class="frw-rabbit-hole-button">%s</button>' .
			'<span class="frw-rabbit-hole-counter"></span>' .
			'</div>' .
			'</div>',
			htmlspecialchars( $theme, ENT_QUOTES ),
			$donateAfter,
			htmlspecialchars( $donateLink, ENT_QUOTES ),
			$discoveriesJson,
			htmlspecialchars( wfMessage( 'fundraisingwidgets-rabbithole-title' )->text(), ENT_QUOTES ),
			htmlspecialchars( wfMessage( 'fundraisingwidgets-rabbithole-subtitle' )->text(), ENT_QUOTES ),
			htmlspecialchars( wfMessage( 'fundraisingwidgets-rabbithole-donate-message' )->text(), ENT_QUOTES ),
			htmlspecialchars( $donateLink, ENT_QUOTES ),
			htmlspecialchars( wfMessage( 'fundraisingwidgets-button-donate' )->text(), ENT_QUOTES ),
			htmlspecialchars( $buttonText, ENT_QUOTES )
		);

		return [ $html, 'noparse' => true, 'isHTML' => true ];
	}

	/**
	 * Parse parser function arguments into key-value pairs
	 *
	 * @param PPFrame $frame
	 * @param array $args
	 * @return array
	 */
	private static function parseArgs( PPFrame $frame, array $args ): array {
		$params = [
			'theme' => 'light',
			'donate-after' => '3',
			'button-text' => '',
			'button-link' => '',
		];

		foreach ( $args as $arg ) {
			$expanded = $frame->expand( $arg );
			$parts = explode( '=', $expanded, 2 );
			if ( count( $parts ) === 2 ) {
				$key = trim( $parts[0] );
				$value = trim( $parts[1] );
				if ( isset( $params[$key] ) ) {
					$params[$key] = $value;
				}
			}
		}

		return $params;
	}
}
