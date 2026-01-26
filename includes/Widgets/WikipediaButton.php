<?php

namespace MediaWiki\Extension\FundraisingWidgets\Widgets;

use Parser;

class WikipediaButton {
	use UrlValidator;
	private const VALID_SIZES = [ 'small', 'medium', 'large'];
	private const VALID_COLORS = [ 'blue', 'purple', 'green', 'red', 'yellow'];
	private const DEFAULT_URL = 'https://www.wikipedia.org';

	public static function render ( Parser $parser, ...$args): array {
		$parser->getOutput()->addModuleStyles([ 'ext.fundraisingWidgets']);

		$params = self::parseArgs( $args );

		$size = in_array( $params['size'], self::VALID_SIZES, true )
			? $params['size']
			: 'medium';

		$color = in_array( $params['color'], self::VALID_COLORS, true )
			? $params['color']
			: 'blue';

		$text = $params['text'] ?: 'Built on Wikipedia';
		$link = self::sanitizeUrl($params['button-link'], self::DEFAULT_URL);

		$html = sprintf(
			'<a href="%s" class="frw-wikipedia-button frw-wikipedia-button--%s frw-wikipedia-button--%s" role="button" target="_blank" rel="noopener"><span class="frw-button-text">%s</span></a>',
			htmlspecialchars( $link, ENT_QUOTES ),
			htmlspecialchars( $size, ENT_QUOTES ),
			htmlspecialchars( $color, ENT_QUOTES ),
			htmlspecialchars( $text, ENT_QUOTES )
		);

		return [ $html, 'noparse' => true, 'isHTML' => true];

	}

	private static function parseArgs( array $args ) {
		$params = [
			'size' => 'medium',
			'color' => 'blue',
			'text' => '',
			'button-link' => '',
		];

		foreach( $args as $arg ) {
			$parts = explode('=', $arg, 2);
			if(count($parts) === 2) {
				$key = trim( $parts[0]);
				$value = trim( $parts[1]);
				if(isset($params[$key])) {
					$params[$key] = $value;
				}
			}
		}

		return $params;
	}

}
