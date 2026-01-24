<?php

namespace MediaWiki\Extension\FundraisingWidgets\Widgets;

use Parser;

class DonateButton {

	private const VALID_SIZES = [ 'small', 'medium', 'large' ];
	private const VALID_COLORS = [ 'blue', 'purple', 'green', 'red', 'yellow' ];

	/**
	 * Render the donate button widget
	 *
	 * @param Parser $parser
	 * @param mixed ...$args
	 * @return array
	 */
	public static function render( Parser $parser, ...$args ): array {
		$parser->getOutput()->addModules( [ 'ext.fundraisingWidgets.js' ] );
		$parser->getOutput()->addModuleStyles( [ 'ext.fundraisingWidgets' ] );

		$params = self::parseArgs( $args );

		$size = in_array( $params['size'], self::VALID_SIZES, true )
			? $params['size']
			: 'medium';

		$color = in_array( $params['color'], self::VALID_COLORS, true )
			? $params['color']
			: 'blue';

		$text = $params['text'] ?: 'Support Wikipedia';
		$link = $params['button-link'] ?: 'https://donate.wikimedia.org';
		$showIcons = $params['icons'] === 'true';

		$iconHtml = '';
		if ( $showIcons ) {
			$iconHtml = '<span class="frw-button-icons">' .
				'<span class="frw-icon frw-icon-card"></span>' .
				'<span class="frw-icon frw-icon-paypal"></span>' .
				'</span>';
		}

		$html = sprintf(
			'<a href="%s" class="frw-donate-button frw-donate-button--%s frw-donate-button--%s" role="button">%s<span class="frw-button-text">%s</span></a>',
			htmlspecialchars( $link, ENT_QUOTES ),
			htmlspecialchars( $size, ENT_QUOTES ),
			htmlspecialchars( $color, ENT_QUOTES ),
			$iconHtml,
			htmlspecialchars( $text, ENT_QUOTES )
		);

		return [ $html, 'noparse' => true, 'isHTML' => true ];
	}

	/**
	 * Parse parser function arguments into key-value pairs
	 *
	 * @param array $args
	 * @return array
	 */
	private static function parseArgs( array $args ): array {
		$params = [
			'size' => 'medium',
			'color' => 'blue',
			'text' => '',
			'button-link' => '',
			'icons' => 'false',
		];

		foreach ( $args as $arg ) {
			$parts = explode( '=', $arg, 2 );
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
