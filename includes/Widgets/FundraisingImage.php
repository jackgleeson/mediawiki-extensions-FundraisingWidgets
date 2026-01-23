<?php

namespace MediaWiki\Extension\FundraisingWidgets\Widgets;

use Parser;
use PPFrame;

class FundraisingImage {

	private const VALID_IMAGES = [ 'landscape', 'eagle', 'mountain', 'snow-leopard', 'frog', 'cat-lizard', 'regal-cat' ];
	private const VALID_SIZES = [ 'small', 'medium', 'large' ];
	private const VALID_PLACEMENTS = [ 'top-left', 'top-right', 'bottom-left', 'bottom-right' ];
	private const VALID_COLORS = [ 'blue', 'purple', 'green', 'red', 'yellow' ];

	private const IMAGE_URLS = [
		'landscape' => 'https://upload.wikimedia.org/wikipedia/commons/8/81/Parque_Eagle_River%2C_Anchorage%2C_Alaska%2C_Estados_Unidos%2C_2017-09-01%2C_DD_02.jpg',
		'eagle' => 'https://upload.wikimedia.org/wikipedia/commons/0/09/001_Wild_Golden_Eagle_and_Majinghorn_Pfyn-Finges_Photo_by_Giles_Laurent.jpg',
		'mountain' => 'https://upload.wikimedia.org/wikipedia/commons/0/01/Pointe_de_Nantaux_05.jpg',
		'snow-leopard' => 'https://upload.wikimedia.org/wikipedia/commons/f/f3/Snow_Leopard_Looking_Up.jpg',
		'frog' => 'https://upload.wikimedia.org/wikipedia/commons/3/35/Strawberry_poison_dart_frog_%2870539%29.jpg',
		'cat-lizard' => 'https://upload.wikimedia.org/wikipedia/commons/7/72/Cat_playing_with_a_lizard.jpg',
		'regal-cat' => 'https://upload.wikimedia.org/wikipedia/commons/4/4d/Cat_November_2010-1a.jpg',
	];

	/**
	 * Render the fundraising image widget
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

		$image = in_array( $params['image'], self::VALID_IMAGES, true )
			? $params['image']
			: 'snow-leopard';
		$size = in_array( $params['size'], self::VALID_SIZES, true )
			? $params['size']
			: 'medium';
		$placement = in_array( $params['button-position'], self::VALID_PLACEMENTS, true )
			? $params['button-position']
			: 'bottom-right';
		$color = in_array( $params['button-color'], self::VALID_COLORS, true )
			? $params['button-color']
			: 'blue';
		$caption = $params['caption'] !== '' ? $params['caption'] : 'You mean I can donate to this thing!!!';
		$buttonText = $params['button-text'] ?: wfMessage( 'fundraisingwidgets-button-donate' )->text();
		$buttonLink = $params['button-link'] ?: 'https://donate.wikimedia.org';

		$imageUrl = self::IMAGE_URLS[$image] ?? self::IMAGE_URLS['snow-leopard'];

		$hasCaption = !empty( $caption );
		$widgetClass = 'frw-image-widget frw-image-widget--' . htmlspecialchars( $size, ENT_QUOTES );
		if ( !$hasCaption ) {
			$widgetClass .= ' frw-image-widget--button-' . htmlspecialchars( $placement, ENT_QUOTES );
		}
		if ( $hasCaption ) {
			$widgetClass .= ' frw-image-widget--has-caption';
		}

		$buttonClass = 'frw-donate-button frw-donate-button--medium frw-donate-button--' . htmlspecialchars( $color, ENT_QUOTES );

		$captionBarHtml = '';
		if ( $hasCaption ) {
			$captionBarHtml = sprintf(
				'<div class="frw-image-caption-bar">' .
				'<p class="frw-image-caption">%s</p>' .
				'<a href="%s" class="%s" role="button">' .
				'<span class="frw-button-text">%s</span>' .
				'</a>' .
				'</div>',
				htmlspecialchars( $caption, ENT_QUOTES ),
				htmlspecialchars( $buttonLink, ENT_QUOTES ),
				$buttonClass,
				htmlspecialchars( $buttonText, ENT_QUOTES )
			);
		}

		$buttonHtml = '';
		if ( !$hasCaption ) {
			$buttonHtml = sprintf(
				'<a href="%s" class="%s" role="button">' .
				'<span class="frw-button-text">%s</span>' .
				'</a>',
				htmlspecialchars( $buttonLink, ENT_QUOTES ),
				$buttonClass,
				htmlspecialchars( $buttonText, ENT_QUOTES )
			);
		}

		$html = sprintf(
			'<div class="%s" style="background-image: url(\'%s\');">' .
			'<div class="frw-image-overlay">' .
			'%s' .
			'</div>' .
			'%s' .
			'</div>',
			$widgetClass,
			htmlspecialchars( $imageUrl, ENT_QUOTES ),
			$buttonHtml,
			$captionBarHtml
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
			'image' => 'snow-leopard',
			'size' => 'medium',
			'button-position' => 'bottom-right',
			'button-color' => 'blue',
			'caption' => 'You mean I can donate to this thing!!!',
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
