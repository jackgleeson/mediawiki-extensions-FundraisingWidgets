<?php

namespace MediaWiki\Extension\FundraisingWidgets\Widgets;

/**
 * Trait for validating URLs in fundraising widgets
 *
 * Ensures only safe URL protocols are allowed to prevent javascript: and other
 * potentially dangerous URL schemes.
 */
trait UrlValidator {

	/** @var string Default donation URL */
	private static string $defaultDonateUrl = 'https://donate.wikimedia.org';

	/** @var array Allowed URL protocols */
	private static array $allowedProtocols = [ 'https', 'http' ];

	/**
	 * Validate and sanitize a URL
	 *
	 * Returns the URL if it uses an allowed protocol, otherwise returns the default.
	 *
	 * @param string $url The URL to validate
	 * @param string|null $default Optional custom default URL
	 * @return string A safe URL
	 */
	private static function sanitizeUrl( string $url, ?string $default = null ): string {
		$default = $default ?? self::$defaultDonateUrl;

		if ( $url === '' ) {
			return $default;
		}

		// Parse the URL to extract the scheme
		$parsed = parse_url( $url );

		// If parsing failed or no scheme, reject it
		if ( $parsed === false || !isset( $parsed['scheme'] ) ) {
			return $default;
		}

		// Check if the scheme is in our allowed list
		$scheme = strtolower( $parsed['scheme'] );
		if ( !in_array( $scheme, self::$allowedProtocols, true ) ) {
			return $default;
		}

		return $url;
	}
}
