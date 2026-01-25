<?php

namespace MediaWiki\Extension\FundraisingWidgets\Widgets;

use Parser;

class FundraisingBanner {
	use UrlValidator;

	private const VALID_LOGO_TYPES = [ 'globe', 'globe-hands', 'wordmark', 'combined', 'wmf', 'none' ];

	/**
	 * Render the fundraising banner widget
	 *
	 * @param Parser $parser
	 * @param mixed ...$args
	 * @return array
	 */
	public static function render( Parser $parser, ...$args ): array {
		$parser->getOutput()->addModules( [ 'ext.fundraisingWidgets.js' ] );
		$parser->getOutput()->addModuleStyles( [ 'ext.fundraisingWidgets' ] );

		$params = self::parseArgs( $args );

		$message = $params['message'] ?: wfMessage( 'fundraisingwidgets-banner-default-message' )->text();
		$dismissible = $params['dismissible'] === 'true';
		$buttonText = $params['button-text'] ?: wfMessage( 'fundraisingwidgets-button-donate' )->text();
		$buttonLink = self::sanitizeUrl( $params['button-link'] );
		$logoType = in_array( $params['logo'], self::VALID_LOGO_TYPES, true )
			? $params['logo']
			: 'combined';

		$logoHtml = self::renderLogo( $logoType );

		$closeButton = '';
		if ( $dismissible ) {
			$closeButton = '<button type="button" class="frw-banner-close" ' .
				'aria-label="' . htmlspecialchars( wfMessage( 'fundraisingwidgets-banner-close' )->text(), ENT_QUOTES ) . '">' .
				'<span aria-hidden="true">&times;</span>' .
				'</button>';
		}

		$html = sprintf(
			'<div class="frw-banner%s" role="banner">' .
			'%s' .
			'%s' .
			'<div class="frw-banner-content">' .
			'<p class="frw-banner-message">%s</p>' .
			'<a href="%s" class="frw-donate-button frw-donate-button--medium" role="button">' .
			'<span class="frw-button-text">%s</span>' .
			'</a>' .
			'</div>' .
			'</div>',
			$dismissible ? ' frw-banner--dismissible' : '',
			$closeButton,
			$logoHtml,
			htmlspecialchars( $message, ENT_QUOTES ),
			htmlspecialchars( $buttonLink, ENT_QUOTES ),
			htmlspecialchars( $buttonText, ENT_QUOTES )
		);

		return [ $html, 'noparse' => true, 'isHTML' => true ];
	}

	/**
	 * Render logo HTML based on type
	 *
	 * @param string $logoType
	 * @return string
	 */
	private static function renderLogo( string $logoType ): string {
		if ( $logoType === 'none' ) {
			return '';
		}

		$logoHtml = '<div class="frw-banner-logo frw-banner-logo--' . htmlspecialchars( $logoType, ENT_QUOTES ) . '">';

		switch ( $logoType ) {
			case 'globe':
				$logoHtml .= '<img src="" class="frw-logo frw-logo-globe" alt="Wikipedia" data-logo="globe">';
				break;
			case 'globe-hands':
				$logoHtml .= '<img src="" class="frw-logo frw-logo-globe-hands" alt="Wikipedia" data-logo="globe-hands">';
				break;
			case 'wordmark':
				$logoHtml .= '<img src="" class="frw-logo frw-logo-wordmark" alt="Wikipedia" data-logo="wordmark">';
				break;
			case 'combined':
				$logoHtml .= '<img src="" class="frw-logo frw-logo-globe" alt="" data-logo="globe">' .
					'<img src="" class="frw-logo frw-logo-wordmark" alt="Wikipedia" data-logo="wordmark">';
				break;
			case 'wmf':
				$logoHtml .= '<img src="" class="frw-logo frw-logo-wmf" alt="Wikimedia Foundation" data-logo="wmf">';
				break;
		}

		$logoHtml .= '</div>';
		return $logoHtml;
	}

	/**
	 * Parse parser function arguments into key-value pairs
	 *
	 * @param array $args
	 * @return array
	 */
	private static function parseArgs( array $args ): array {
		$params = [
			'message' => '',
			'dismissible' => 'false',
			'button-text' => '',
			'button-link' => '',
			'logo' => 'globe',
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
