<?php

namespace MediaWiki\Extension\FundraisingWidgets;

use SpecialPage;

class SpecialFundraisingWidgets extends SpecialPage {

	public function __construct() {
		parent::__construct( 'FundraisingWidgets' );
	}

	/**
	 * @param string|null $par
	 */
	public function execute( $par ) {
		$this->setHeaders();
		$this->getOutput()->setPageTitle( $this->msg( 'fundraisingwidgets-special-title' )->text() );
		$this->getOutput()->addModules( [ 'ext.fundraisingWidgets.showcase' ] );
		$this->getOutput()->addModuleStyles( [ 'ext.fundraisingWidgets', 'ext.fundraisingWidgets.showcase.styles' ] );

		$this->showIntroduction();
		$this->getOutput()->addHTML( '<div class="frw-showcase-grid">' );
		$this->showDonateButtonSection();
		$this->showBannerSection();
		$this->showImageSection();
		$this->showRabbitHoleSection();
		$this->getOutput()->addHTML( '</div>' );
	}

	private function showIntroduction(): void {
		$out = $this->getOutput();
		$out->addHTML(
			'<div class="frw-showcase-intro">' .
			'<p class="frw-showcase-intro-lead">' . $this->msg( 'fundraisingwidgets-special-intro' )->escaped() . '</p>' .
			'<div class="frw-showcase-intro-options">' .
			'<div class="frw-showcase-intro-option">' .
			'<h3>' . $this->msg( 'fundraisingwidgets-special-intro-mediawiki-title' )->escaped() . '</h3>' .
			'<p>' . $this->msg( 'fundraisingwidgets-special-intro-mediawiki-desc' )->escaped() . '</p>' .
			'</div>' .
			'<div class="frw-showcase-intro-option">' .
			'<h3>' . $this->msg( 'fundraisingwidgets-special-intro-js-title' )->escaped() . '</h3>' .
			'<p>' . $this->msg( 'fundraisingwidgets-special-intro-js-desc' )->escaped() . '</p>' .
			'</div>' .
			'</div>' .
			'</div>'
		);
	}

	private function showDonateButtonSection(): void {
		$out = $this->getOutput();

		$out->addHTML( '<div class="frw-showcase-section" id="frw-section-button">' );
		$out->addHTML( '<h2>' . $this->msg( 'fundraisingwidgets-special-button-title' )->escaped() . '</h2>' );

		// Preview area with color picker
		$out->addHTML( '<div class="frw-showcase-preview" id="frw-button-preview-container">' );
		$out->addHTML( '<div class="frw-preview-area" id="frw-button-preview">' );
		$out->addHTML(
			'<a href="https://donate.wikimedia.org" class="frw-donate-button frw-donate-button--medium" role="button">' .
			'<span class="frw-button-text">Support Wikipedia</span>' .
			'</a>'
		);
		$out->addHTML( '</div>' );
		// Color picker
		$out->addHTML( '<div class="frw-color-picker" id="frw-button-colors">' );
		$out->addHTML( '<button type="button" class="frw-color-option frw-color-option--blue frw-color-option--selected" data-color="blue" title="Blue"></button>' );
		$out->addHTML( '<button type="button" class="frw-color-option frw-color-option--purple" data-color="purple" title="Purple"></button>' );
		$out->addHTML( '<button type="button" class="frw-color-option frw-color-option--green" data-color="green" title="Green"></button>' );
		$out->addHTML( '<button type="button" class="frw-color-option frw-color-option--red" data-color="red" title="Red"></button>' );
		$out->addHTML( '<button type="button" class="frw-color-option frw-color-option--yellow" data-color="yellow" title="Yellow"></button>' );
		$out->addHTML( '</div>' );
		$out->addHTML( '</div>' );

		// Configuration form
		$out->addHTML( '<div class="frw-showcase-config">' );
		$out->addHTML(
			'<div class="frw-config-form">' .
			'<div class="frw-config-field">' .
			'<label for="frw-button-text">' . $this->msg( 'fundraisingwidgets-config-text' )->escaped() . '</label>' .
			'<input type="text" id="frw-button-text" class="frw-config-input" value="Support Wikipedia" placeholder="Support Wikipedia">' .
			'</div>' .
			'<div class="frw-config-field">' .
			'<label for="frw-button-size">' . $this->msg( 'fundraisingwidgets-config-size' )->escaped() . '</label>' .
			'<select id="frw-button-size" class="frw-config-input">' .
			'<option value="small">' . $this->msg( 'fundraisingwidgets-size-small' )->escaped() . '</option>' .
			'<option value="medium" selected>' . $this->msg( 'fundraisingwidgets-size-medium' )->escaped() . '</option>' .
			'<option value="large">' . $this->msg( 'fundraisingwidgets-size-large' )->escaped() . '</option>' .
			'</select>' .
			'</div>' .
			'<div class="frw-config-field frw-config-field--full">' .
			'<label for="frw-button-link">' . $this->msg( 'fundraisingwidgets-config-button-link' )->escaped() . '</label>' .
			'<input type="text" id="frw-button-link" class="frw-config-input" value="https://donate.wikimedia.org" placeholder="https://donate.wikimedia.org">' .
			'</div>' .
			'</div>'
		);
		$out->addHTML( '</div>' );

		// Generated code with format tabs
		$out->addHTML( '<div class="frw-showcase-code" id="frw-button-code-section">' );
		$out->addHTML( '<div class="frw-format-tabs">' );
		$out->addHTML( '<button type="button" class="frw-format-tab frw-format-tab--active" data-format="wikitext">MediaWiki</button>' );
		$out->addHTML( '<button type="button" class="frw-format-tab" data-format="javascript">JavaScript</button>' );
		$out->addHTML( '<button type="button" class="frw-format-tab" data-format="android">Android</button>' );
		$out->addHTML( '<button type="button" class="frw-format-tab" data-format="ios">iOS</button>' );
		$out->addHTML( '</div>' );
		// MediaWiki format
		$out->addHTML( '<div class="frw-format-content frw-format-content--active" data-format="wikitext">' );
		$out->addHTML( '<pre id="frw-button-code-wikitext" class="frw-code-output">{{#fundraising-button: size=medium | text=Support Wikipedia | color=blue }}</pre>' );
		$out->addHTML( '<button type="button" class="frw-copy-button" data-target="frw-button-code-wikitext">' .
			$this->msg( 'fundraisingwidgets-special-copy' )->escaped() . '</button>' );
		$out->addHTML( '</div>' );
		// JavaScript format
		$out->addHTML( '<div class="frw-format-content" data-format="javascript">' );
		$out->addHTML( '<pre id="frw-button-code-js" class="frw-code-output">' . htmlspecialchars( '<script src="' . $this->getConfig()->get( 'Server' ) . $this->getConfig()->get( 'ExtensionAssetsPath' ) . '/FundraisingWidgets/resources/embed.js"></script>
<div class="frw-embed" data-widget="button" data-size="medium" data-text="Support Wikipedia" data-color="blue"></div>' ) . '</pre>' );
		$out->addHTML( '<button type="button" class="frw-copy-button" data-target="frw-button-code-js">' .
			$this->msg( 'fundraisingwidgets-special-copy' )->escaped() . '</button>' );
		$out->addHTML( '</div>' );
		// Android format
		$out->addHTML( '<div class="frw-format-content" data-format="android">' );
		$out->addHTML( '<pre class="frw-code-output frw-code-output--coming-soon">' . $this->msg( 'fundraisingwidgets-coming-soon' )->escaped() . '</pre>' );
		$out->addHTML( '</div>' );
		// iOS format
		$out->addHTML( '<div class="frw-format-content" data-format="ios">' );
		$out->addHTML( '<pre class="frw-code-output frw-code-output--coming-soon">' . $this->msg( 'fundraisingwidgets-coming-soon' )->escaped() . '</pre>' );
		$out->addHTML( '</div>' );
		$out->addHTML( '</div>' );

		$out->addHTML( '</div>' );
	}

	private function showBannerSection(): void {
		$out = $this->getOutput();

		$out->addHTML( '<div class="frw-showcase-section" id="frw-section-banner">' );
		$out->addHTML( '<h2>' . $this->msg( 'fundraisingwidgets-special-banner-title' )->escaped() . '</h2>' );

		// Preview area (at top to align with button section)
		$out->addHTML( '<div class="frw-showcase-preview">' );
		$out->addHTML( '<div class="frw-preview-area frw-preview-area--wide" id="frw-banner-preview">' );
		$this->renderBannerPreview( $out );
		$out->addHTML( '</div>' );
		$out->addHTML( '</div>' );

		// Configuration form
		$out->addHTML( '<div class="frw-showcase-config">' );
		$out->addHTML(
			'<div class="frw-config-form">' .
			'<div class="frw-config-field frw-config-field--full">' .
			'<label for="frw-banner-message">' . $this->msg( 'fundraisingwidgets-config-message' )->escaped() . '</label>' .
			'<textarea id="frw-banner-message" class="frw-config-input" rows="2">If Wikipedia has given you useful knowledge this year, please give back. There are no small contributions: every edit counts, every donation counts.</textarea>' .
			'</div>' .
			'<div class="frw-config-field">' .
			'<label for="frw-banner-button-text">' . $this->msg( 'fundraisingwidgets-config-button-text' )->escaped() . '</label>' .
			'<input type="text" id="frw-banner-button-text" class="frw-config-input" value="Donate" placeholder="Donate">' .
			'</div>' .
			'<div class="frw-config-field">' .
			'<label for="frw-banner-logo">' . $this->msg( 'fundraisingwidgets-config-logo' )->escaped() . '</label>' .
			'<select id="frw-banner-logo" class="frw-config-input">' .
			'<option value="globe" selected>' . $this->msg( 'fundraisingwidgets-logo-globe' )->escaped() . '</option>' .
			'<option value="globe-hands">' . $this->msg( 'fundraisingwidgets-logo-globe-hands' )->escaped() . '</option>' .
			'<option value="combined">' . $this->msg( 'fundraisingwidgets-logo-combined' )->escaped() . '</option>' .
			'<option value="wordmark">' . $this->msg( 'fundraisingwidgets-logo-wordmark' )->escaped() . '</option>' .
			'<option value="wmf">' . $this->msg( 'fundraisingwidgets-logo-wmf' )->escaped() . '</option>' .
			'<option value="none">' . $this->msg( 'fundraisingwidgets-logo-none' )->escaped() . '</option>' .
			'</select>' .
			'</div>' .
			'<div class="frw-config-field">' .
			'<label>' .
			'<input type="checkbox" id="frw-banner-dismissible" checked>' .
			' ' . $this->msg( 'fundraisingwidgets-config-dismissible' )->escaped() .
			'</label>' .
			'</div>' .
			'<div class="frw-config-field frw-config-field--full">' .
			'<label for="frw-banner-button-link">' . $this->msg( 'fundraisingwidgets-config-button-link' )->escaped() . '</label>' .
			'<input type="text" id="frw-banner-button-link" class="frw-config-input" value="https://donate.wikimedia.org" placeholder="https://donate.wikimedia.org">' .
			'</div>' .
			'</div>'
		);
		$out->addHTML( '</div>' );

		// Generated code with format tabs
		$out->addHTML( '<div class="frw-showcase-code" id="frw-banner-code-section">' );
		$out->addHTML( '<div class="frw-format-tabs">' );
		$out->addHTML( '<button type="button" class="frw-format-tab frw-format-tab--active" data-format="wikitext">MediaWiki</button>' );
		$out->addHTML( '<button type="button" class="frw-format-tab" data-format="javascript">JavaScript</button>' );
		$out->addHTML( '<button type="button" class="frw-format-tab" data-format="android">Android</button>' );
		$out->addHTML( '<button type="button" class="frw-format-tab" data-format="ios">iOS</button>' );
		$out->addHTML( '</div>' );
		// MediaWiki format
		$out->addHTML( '<div class="frw-format-content frw-format-content--active" data-format="wikitext">' );
		$out->addHTML( '<pre id="frw-banner-code-wikitext" class="frw-code-output">{{#fundraising-banner: message=If Wikipedia has given you useful knowledge this year, please give back. There are no small contributions: every edit counts, every donation counts. | logo=globe | dismissible=true }}</pre>' );
		$out->addHTML( '<button type="button" class="frw-copy-button" data-target="frw-banner-code-wikitext">' .
			$this->msg( 'fundraisingwidgets-special-copy' )->escaped() . '</button>' );
		$out->addHTML( '</div>' );
		// JavaScript format
		$out->addHTML( '<div class="frw-format-content" data-format="javascript">' );
		$out->addHTML( '<pre id="frw-banner-code-js" class="frw-code-output">' . htmlspecialchars( '<script src="' . $this->getConfig()->get( 'Server' ) . $this->getConfig()->get( 'ExtensionAssetsPath' ) . '/FundraisingWidgets/resources/embed.js"></script>
<div class="frw-embed" data-widget="banner" data-message="If Wikipedia has given you useful knowledge this year, please give back. There are no small contributions: every edit counts, every donation counts." data-logo="globe" data-dismissible="true"></div>' ) . '</pre>' );
		$out->addHTML( '<button type="button" class="frw-copy-button" data-target="frw-banner-code-js">' .
			$this->msg( 'fundraisingwidgets-special-copy' )->escaped() . '</button>' );
		$out->addHTML( '</div>' );
		// Android format
		$out->addHTML( '<div class="frw-format-content" data-format="android">' );
		$out->addHTML( '<pre class="frw-code-output frw-code-output--coming-soon">' . $this->msg( 'fundraisingwidgets-coming-soon' )->escaped() . '</pre>' );
		$out->addHTML( '</div>' );
		// iOS format
		$out->addHTML( '<div class="frw-format-content" data-format="ios">' );
		$out->addHTML( '<pre class="frw-code-output frw-code-output--coming-soon">' . $this->msg( 'fundraisingwidgets-coming-soon' )->escaped() . '</pre>' );
		$out->addHTML( '</div>' );
		$out->addHTML( '</div>' );

		$out->addHTML( '</div>' );
	}

	private function renderBannerPreview( $out ): void {
		$basePath = $this->getConfig()->get( 'ExtensionAssetsPath' ) . '/FundraisingWidgets/resources/images/';
		$out->addHTML(
			'<div class="frw-banner frw-banner--dismissible" role="banner">' .
			'<button type="button" class="frw-banner-close" aria-label="Close">' .
			'<span aria-hidden="true">&times;</span>' .
			'</button>' .
			'<div class="frw-banner-logo frw-banner-logo--globe">' .
			'<img src="' . htmlspecialchars( $basePath . 'Wikipedia-logo-v2.svg' ) . '" class="frw-logo frw-logo-globe" alt="Wikipedia">' .
			'</div>' .
			'<div class="frw-banner-content">' .
			'<p class="frw-banner-message">If Wikipedia has given you useful knowledge this year, please give back. There are no small contributions: every edit counts, every donation counts.</p>' .
			'<a href="https://donate.wikimedia.org" class="frw-donate-button frw-donate-button--medium" role="button">' .
			'<span class="frw-button-text">Donate</span>' .
			'</a>' .
			'</div>' .
			'</div>'
		);
	}

	private function showImageSection(): void {
		$out = $this->getOutput();

		$out->addHTML( '<div class="frw-showcase-section" id="frw-section-image">' );
		$out->addHTML( '<h2>' . $this->msg( 'fundraisingwidgets-special-image-title' )->escaped() . '</h2>' );

		// Preview area
		$out->addHTML( '<div class="frw-showcase-preview">' );
		$out->addHTML( '<div class="frw-preview-area" id="frw-image-preview">' );
		$this->renderImagePreview( $out );
		$out->addHTML( '</div>' );
		$out->addHTML( '</div>' );

		// Configuration form
		$out->addHTML( '<div class="frw-showcase-config">' );
		$out->addHTML(
			'<div class="frw-config-form">' .
			'<div class="frw-config-field">' .
			'<label for="frw-image-bg">' . $this->msg( 'fundraisingwidgets-config-image' )->escaped() . '</label>' .
			'<select id="frw-image-bg" class="frw-config-input">' .
			'<option value="landscape">' . $this->msg( 'fundraisingwidgets-image-landscape' )->escaped() . '</option>' .
			'<option value="eagle">' . $this->msg( 'fundraisingwidgets-image-eagle' )->escaped() . '</option>' .
			'<option value="mountain">' . $this->msg( 'fundraisingwidgets-image-mountain' )->escaped() . '</option>' .
			'<option value="snow-leopard" selected>' . $this->msg( 'fundraisingwidgets-image-snow-leopard' )->escaped() . '</option>' .
			'<option value="frog">' . $this->msg( 'fundraisingwidgets-image-frog' )->escaped() . '</option>' .
			'<option value="cat-lizard">' . $this->msg( 'fundraisingwidgets-image-cat-lizard' )->escaped() . '</option>' .
			'<option value="regal-cat">' . $this->msg( 'fundraisingwidgets-image-regal-cat' )->escaped() . '</option>' .
			'</select>' .
			'</div>' .
			'<div class="frw-config-field">' .
			'<label for="frw-image-size">' . $this->msg( 'fundraisingwidgets-config-size' )->escaped() . '</label>' .
			'<select id="frw-image-size" class="frw-config-input">' .
			'<option value="small">' . $this->msg( 'fundraisingwidgets-size-small' )->escaped() . '</option>' .
			'<option value="medium" selected>' . $this->msg( 'fundraisingwidgets-size-medium' )->escaped() . '</option>' .
			'<option value="large">' . $this->msg( 'fundraisingwidgets-size-large' )->escaped() . '</option>' .
			'</select>' .
			'</div>' .
			'<div class="frw-config-field">' .
			'<label for="frw-image-position">' . $this->msg( 'fundraisingwidgets-config-button-position' )->escaped() . '</label>' .
			'<select id="frw-image-position" class="frw-config-input">' .
			'<option value="top-left">' . $this->msg( 'fundraisingwidgets-position-top-left' )->escaped() . '</option>' .
			'<option value="top-right">' . $this->msg( 'fundraisingwidgets-position-top-right' )->escaped() . '</option>' .
			'<option value="bottom-left">' . $this->msg( 'fundraisingwidgets-position-bottom-left' )->escaped() . '</option>' .
			'<option value="bottom-right" selected>' . $this->msg( 'fundraisingwidgets-position-bottom-right' )->escaped() . '</option>' .
			'</select>' .
			'</div>' .
			'<div class="frw-config-field">' .
			'<label>' . $this->msg( 'fundraisingwidgets-config-button-color' )->escaped() . '</label>' .
			'<div class="frw-color-picker frw-color-picker--inline" id="frw-image-colors">' .
			'<button type="button" class="frw-color-option frw-color-option--blue frw-color-option--selected" data-color="blue" title="Blue"></button>' .
			'<button type="button" class="frw-color-option frw-color-option--purple" data-color="purple" title="Purple"></button>' .
			'<button type="button" class="frw-color-option frw-color-option--green" data-color="green" title="Green"></button>' .
			'<button type="button" class="frw-color-option frw-color-option--red" data-color="red" title="Red"></button>' .
			'<button type="button" class="frw-color-option frw-color-option--yellow" data-color="yellow" title="Yellow"></button>' .
			'</div>' .
			'</div>' .
			'<div class="frw-config-field frw-config-field--full">' .
			'<label for="frw-image-caption">' . $this->msg( 'fundraisingwidgets-config-caption' )->escaped() . '</label>' .
			'<input type="text" id="frw-image-caption" class="frw-config-input" value="You mean I can donate to this thing!!!" placeholder="Caption text (leave empty to show button on image)">' .
			'</div>' .
			'<div class="frw-config-field">' .
			'<label for="frw-image-button-link">' . $this->msg( 'fundraisingwidgets-config-button-link' )->escaped() . '</label>' .
			'<input type="text" id="frw-image-button-link" class="frw-config-input" value="https://donate.wikimedia.org" placeholder="https://donate.wikimedia.org">' .
			'</div>' .
			'</div>'
		);
		$out->addHTML( '</div>' );

		// Generated code with format tabs
		$out->addHTML( '<div class="frw-showcase-code" id="frw-image-code-section">' );
		$out->addHTML( '<div class="frw-format-tabs">' );
		$out->addHTML( '<button type="button" class="frw-format-tab frw-format-tab--active" data-format="wikitext">MediaWiki</button>' );
		$out->addHTML( '<button type="button" class="frw-format-tab" data-format="javascript">JavaScript</button>' );
		$out->addHTML( '<button type="button" class="frw-format-tab" data-format="android">Android</button>' );
		$out->addHTML( '<button type="button" class="frw-format-tab" data-format="ios">iOS</button>' );
		$out->addHTML( '</div>' );
		// MediaWiki format
		$out->addHTML( '<div class="frw-format-content frw-format-content--active" data-format="wikitext">' );
		$out->addHTML( '<pre id="frw-image-code-wikitext" class="frw-code-output">{{#fundraising-image: image=snow-leopard | size=medium | button-color=blue | caption=You mean I can donate to this thing!!! }}</pre>' );
		$out->addHTML( '<button type="button" class="frw-copy-button" data-target="frw-image-code-wikitext">' .
			$this->msg( 'fundraisingwidgets-special-copy' )->escaped() . '</button>' );
		$out->addHTML( '</div>' );
		// JavaScript format
		$out->addHTML( '<div class="frw-format-content" data-format="javascript">' );
		$out->addHTML( '<pre id="frw-image-code-js" class="frw-code-output">' . htmlspecialchars( '<script src="' . $this->getConfig()->get( 'Server' ) . $this->getConfig()->get( 'ExtensionAssetsPath' ) . '/FundraisingWidgets/resources/embed.js"></script>
<div class="frw-embed" data-widget="image" data-image="snow-leopard" data-size="medium" data-button-color="blue" data-caption="You mean I can donate to this thing!!!"></div>' ) . '</pre>' );
		$out->addHTML( '<button type="button" class="frw-copy-button" data-target="frw-image-code-js">' .
			$this->msg( 'fundraisingwidgets-special-copy' )->escaped() . '</button>' );
		$out->addHTML( '</div>' );
		// Android format
		$out->addHTML( '<div class="frw-format-content" data-format="android">' );
		$out->addHTML( '<pre class="frw-code-output frw-code-output--coming-soon">' . $this->msg( 'fundraisingwidgets-coming-soon' )->escaped() . '</pre>' );
		$out->addHTML( '</div>' );
		// iOS format
		$out->addHTML( '<div class="frw-format-content" data-format="ios">' );
		$out->addHTML( '<pre class="frw-code-output frw-code-output--coming-soon">' . $this->msg( 'fundraisingwidgets-coming-soon' )->escaped() . '</pre>' );
		$out->addHTML( '</div>' );
		$out->addHTML( '</div>' );

		$out->addHTML( '</div>' );
	}

	private function renderImagePreview( $out ): void {
		$imageUrl = 'https://upload.wikimedia.org/wikipedia/commons/f/f3/Snow_Leopard_Looking_Up.jpg';
		$out->addHTML(
			'<div class="frw-image-widget frw-image-widget--medium frw-image-widget--has-caption" style="background-image: url(\'' . htmlspecialchars( $imageUrl ) . '\');">' .
			'<div class="frw-image-overlay"></div>' .
			'<div class="frw-image-caption-bar">' .
			'<p class="frw-image-caption">You mean I can donate to this thing!!!</p>' .
			'<a href="https://donate.wikimedia.org" class="frw-donate-button frw-donate-button--medium frw-donate-button--blue" role="button">' .
			'<span class="frw-button-text">' . $this->msg( 'fundraisingwidgets-button-donate' )->escaped() . '</span>' .
			'</a>' .
			'</div>' .
			'</div>'
		);
	}

	private function showRabbitHoleSection(): void {
		$out = $this->getOutput();

		$out->addHTML( '<div class="frw-showcase-section" id="frw-section-rabbithole">' );
		$out->addHTML( '<h2>' . $this->msg( 'fundraisingwidgets-special-rabbithole-title' )->escaped() . '</h2>' );

		// Preview area
		$out->addHTML( '<div class="frw-showcase-preview">' );
		$out->addHTML( '<div class="frw-preview-area" id="frw-rabbithole-preview">' );
		$this->renderRabbitHolePreview( $out );
		$out->addHTML( '</div>' );
		$out->addHTML( '</div>' );

		// Configuration form
		$out->addHTML( '<div class="frw-showcase-config">' );
		$out->addHTML(
			'<div class="frw-config-form">' .
			'<div class="frw-config-field">' .
			'<label for="frw-rabbithole-theme">' . $this->msg( 'fundraisingwidgets-config-theme' )->escaped() . '</label>' .
			'<select id="frw-rabbithole-theme" class="frw-config-input">' .
			'<option value="light" selected>' . $this->msg( 'fundraisingwidgets-theme-light' )->escaped() . '</option>' .
			'<option value="dark">' . $this->msg( 'fundraisingwidgets-theme-dark' )->escaped() . '</option>' .
			'</select>' .
			'</div>' .
			'<div class="frw-config-field">' .
			'<label for="frw-rabbithole-donate-after">' . $this->msg( 'fundraisingwidgets-config-donate-after' )->escaped() . '</label>' .
			'<select id="frw-rabbithole-donate-after" class="frw-config-input">' .
			'<option value="2">2</option>' .
			'<option value="3" selected>3</option>' .
			'<option value="4">4</option>' .
			'<option value="5">5</option>' .
			'</select>' .
			'</div>' .
			'<div class="frw-config-field">' .
			'<label for="frw-rabbithole-button-text">' . $this->msg( 'fundraisingwidgets-config-button-text' )->escaped() . '</label>' .
			'<input type="text" id="frw-rabbithole-button-text" class="frw-config-input" value="" placeholder="Discover something new">' .
			'</div>' .
			'<div class="frw-config-field">' .
			'<label for="frw-rabbithole-button-link">' . $this->msg( 'fundraisingwidgets-config-button-link' )->escaped() . '</label>' .
			'<input type="text" id="frw-rabbithole-button-link" class="frw-config-input" value="https://donate.wikimedia.org" placeholder="https://donate.wikimedia.org">' .
			'</div>' .
			'</div>'
		);
		$out->addHTML( '</div>' );

		// Generated code with format tabs
		$out->addHTML( '<div class="frw-showcase-code" id="frw-rabbithole-code-section">' );
		$out->addHTML( '<div class="frw-format-tabs">' );
		$out->addHTML( '<button type="button" class="frw-format-tab frw-format-tab--active" data-format="wikitext">MediaWiki</button>' );
		$out->addHTML( '<button type="button" class="frw-format-tab" data-format="javascript">JavaScript</button>' );
		$out->addHTML( '<button type="button" class="frw-format-tab" data-format="android">Android</button>' );
		$out->addHTML( '<button type="button" class="frw-format-tab" data-format="ios">iOS</button>' );
		$out->addHTML( '</div>' );
		// MediaWiki format
		$out->addHTML( '<div class="frw-format-content frw-format-content--active" data-format="wikitext">' );
		$out->addHTML( '<pre id="frw-rabbithole-code-wikitext" class="frw-code-output">{{#fundraising-rabbithole: theme=light | donate-after=3 }}</pre>' );
		$out->addHTML( '<button type="button" class="frw-copy-button" data-target="frw-rabbithole-code-wikitext">' .
			$this->msg( 'fundraisingwidgets-special-copy' )->escaped() . '</button>' );
		$out->addHTML( '</div>' );
		// JavaScript format
		$out->addHTML( '<div class="frw-format-content" data-format="javascript">' );
		$out->addHTML( '<pre id="frw-rabbithole-code-js" class="frw-code-output">' . htmlspecialchars( '<script src="' . $this->getConfig()->get( 'Server' ) . $this->getConfig()->get( 'ExtensionAssetsPath' ) . '/FundraisingWidgets/resources/embed.js"></script>
<div class="frw-embed" data-widget="rabbithole" data-theme="light" data-donate-after="3"></div>' ) . '</pre>' );
		$out->addHTML( '<button type="button" class="frw-copy-button" data-target="frw-rabbithole-code-js">' .
			$this->msg( 'fundraisingwidgets-special-copy' )->escaped() . '</button>' );
		$out->addHTML( '</div>' );
		// Android format
		$out->addHTML( '<div class="frw-format-content" data-format="android">' );
		$out->addHTML( '<pre class="frw-code-output frw-code-output--coming-soon">' . $this->msg( 'fundraisingwidgets-coming-soon' )->escaped() . '</pre>' );
		$out->addHTML( '</div>' );
		// iOS format
		$out->addHTML( '<div class="frw-format-content" data-format="ios">' );
		$out->addHTML( '<pre class="frw-code-output frw-code-output--coming-soon">' . $this->msg( 'fundraisingwidgets-coming-soon' )->escaped() . '</pre>' );
		$out->addHTML( '</div>' );
		$out->addHTML( '</div>' );

		$out->addHTML( '</div>' );
	}

	private function renderRabbitHolePreview( $out ): void {
		$out->addHTML(
			'<div class="frw-rabbit-hole frw-rabbit-hole--light">' .
			'<div class="frw-rabbit-hole-content">' .
			'<div class="frw-rabbit-hole-intro">' .
			'<h3 class="frw-rabbit-hole-title">' . $this->msg( 'fundraisingwidgets-rabbithole-title' )->escaped() . '</h3>' .
			'<p class="frw-rabbit-hole-subtitle">' . $this->msg( 'fundraisingwidgets-rabbithole-subtitle' )->escaped() . '</p>' .
			'</div>' .
			'<div class="frw-rabbit-hole-discovery frw-rabbit-hole-discovery--hidden"></div>' .
			'<div class="frw-rabbit-hole-donate frw-rabbit-hole-donate--hidden">' .
			'<p class="frw-rabbit-hole-donate-message">' . $this->msg( 'fundraisingwidgets-rabbithole-donate-message' )->escaped() . '</p>' .
			'<a href="https://donate.wikimedia.org" class="frw-donate-button frw-donate-button--medium frw-donate-button--green" role="button">' .
			'<span class="frw-button-text">' . $this->msg( 'fundraisingwidgets-button-donate' )->escaped() . '</span>' .
			'</a>' .
			'</div>' .
			'</div>' .
			'<div class="frw-rabbit-hole-footer">' .
			'<button type="button" class="frw-rabbit-hole-button">' . $this->msg( 'fundraisingwidgets-rabbithole-discover' )->escaped() . '</button>' .
			'<span class="frw-rabbit-hole-counter"></span>' .
			'</div>' .
			'</div>'
		);
	}

	/**
	 * @return string
	 */
	protected function getGroupName() {
		return 'wiki';
	}
}
