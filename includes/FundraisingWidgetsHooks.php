<?php

namespace MediaWiki\Extension\FundraisingWidgets;

use MediaWiki\Extension\FundraisingWidgets\Widgets\DonateButton;
use MediaWiki\Extension\FundraisingWidgets\Widgets\FundraisingBanner;
use MediaWiki\Extension\FundraisingWidgets\Widgets\FundraisingImage;
use MediaWiki\Extension\FundraisingWidgets\Widgets\RabbitHole;
use MediaWiki\Extension\FundraisingWidgets\Widgets\WikipediaButton;
use Parser;

class FundraisingWidgetsHooks {

	/**
	 * Register parser function hooks
	 *
	 * @param Parser $parser
	 */
	public static function onParserFirstCallInit( Parser $parser ): void {
		$parser->setFunctionHook(
			'fundraising-wikipedia-button',
			[ WikipediaButton::class, 'render']
		);

		$parser->setFunctionHook(
			'fundraising-button',
			[ DonateButton::class, 'render' ]
		);

		$parser->setFunctionHook(
			'fundraising-banner',
			[ FundraisingBanner::class, 'render' ]
		);

		$parser->setFunctionHook(
			'fundraising-image',
			[ FundraisingImage::class, 'render' ]
		);

		$parser->setFunctionHook(
			'fundraising-rabbithole',
			[ RabbitHole::class, 'render' ]
		);
	}
}
