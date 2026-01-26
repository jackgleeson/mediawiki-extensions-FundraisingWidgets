<?php

namespace MediaWiki\Extension\FundraisingWidgets;

use MediaWiki\Html\Html;
use SpecialPage;

class SpecialFundraisingWidgets extends SpecialPage {

	private const DONATE_URL = 'https://donate.wikimedia.org';

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
		$this->getOutput()->addHTML( Html::openElement( 'div', [ 'class' => 'frw-showcase-grid' ] ) );
		$this->showWikipediaButtonSection();
		$this->showDonateButtonSection();
		$this->showBannerSection();
		$this->showImageSection();
		$this->showRabbitHoleSection();
		$this->getOutput()->addHTML( Html::closeElement( 'div' ) );
	}

	private function showIntroduction(): void {
		$html = Html::rawElement( 'div', [ 'class' => 'frw-showcase-intro' ],
			Html::element( 'p',
				[ 'class' => 'frw-showcase-intro-lead' ],
				$this->msg( 'fundraisingwidgets-special-intro' )->text()
			) .
			Html::rawElement( 'div', [ 'class' => 'frw-showcase-intro-options' ],
				Html::rawElement( 'div', [ 'class' => 'frw-showcase-intro-option' ],
					Html::element( 'h3', [], $this->msg( 'fundraisingwidgets-special-intro-mediawiki-title' )->text() ) .
					Html::element( 'p', [], $this->msg( 'fundraisingwidgets-special-intro-mediawiki-desc' )->text() )
				) .
				Html::rawElement( 'div', [ 'class' => 'frw-showcase-intro-option' ],
					Html::element( 'h3', [], $this->msg( 'fundraisingwidgets-special-intro-js-title' )->text() ) .
					Html::element( 'p', [], $this->msg( 'fundraisingwidgets-special-intro-js-desc' )->text() )
				)
			)
		);
		$this->getOutput()->addHTML( $html );
	}

	private function showDonateButtonSection(): void {
		$out = $this->getOutput();

		$out->addHTML( Html::openElement( 'div', [ 'class' => 'frw-showcase-section', 'id' => 'frw-section-button' ] ) );
		$out->addHTML( Html::element( 'h2', [], $this->msg( 'fundraisingwidgets-special-button-title' )->text() ) );

		// Preview area with color picker
		$buttonPreview = Html::rawElement( 'a',
			[
				'href' => self::DONATE_URL,
				'class' => 'frw-donate-button frw-donate-button--medium frw-donate-button--yellow',
				'role' => 'button'
			],
			Html::element( 'span', [ 'class' => 'frw-button-text' ], 'Support Wikipedia' )
		);

		$out->addHTML(
			Html::rawElement( 'div', [ 'class' => 'frw-showcase-preview', 'id' => 'frw-button-preview-container' ],
				Html::rawElement( 'div', [ 'class' => 'frw-preview-area', 'id' => 'frw-button-preview' ], $buttonPreview ) .
				$this->buildColorPicker( 'frw-button-colors', 'yellow' )
			)
		);

		// Configuration form
		$out->addHTML(
			Html::rawElement( 'div', [ 'class' => 'frw-showcase-config' ],
				Html::rawElement( 'div', [ 'class' => 'frw-config-form' ],
					$this->buildTextField( 'frw-button-text', 'fundraisingwidgets-config-text', 'Support Wikipedia' ) .
					$this->buildSelectField( 'frw-button-size', 'fundraisingwidgets-config-size', [
						'small' => 'fundraisingwidgets-size-small',
						'medium' => 'fundraisingwidgets-size-medium',
						'large' => 'fundraisingwidgets-size-large',
					], 'medium' ) .
					$this->buildTextField( 'frw-button-link', 'fundraisingwidgets-config-button-link', self::DONATE_URL, true )
				)
			)
		);

		// Generated code
		$wikitextCode = '{{#fundraising-button: size=medium | text=Support Wikipedia | color=yellow }}';
		$jsCode = '<script src="' . $this->getEmbedScriptUrl() . '"></script>' . "\n" .
			'<div class="frw-embed" data-widget="button" data-size="medium" data-text="Support Wikipedia" data-color="yellow"></div>';

		$out->addHTML( $this->buildCodeSection( 'frw-button', $wikitextCode, $jsCode ) );
		$out->addHTML( Html::closeElement( 'div' ) );
	}

	private function showBannerSection(): void {
		$out = $this->getOutput();
		$defaultMessage = 'If Wikipedia has given you useful knowledge this year, please give back. There are no small contributions: every edit counts, every donation counts.';

		$out->addHTML( Html::openElement( 'div', [ 'class' => 'frw-showcase-section', 'id' => 'frw-section-banner' ] ) );
		$out->addHTML( Html::element( 'h2', [], $this->msg( 'fundraisingwidgets-special-banner-title' )->text() ) );

		// Preview area
		$out->addHTML(
			Html::rawElement( 'div', [ 'class' => 'frw-showcase-preview' ],
				Html::rawElement( 'div', [ 'class' => 'frw-preview-area frw-preview-area--wide', 'id' => 'frw-banner-preview' ],
					$this->renderBannerPreview()
				)
			)
		);

		// Configuration form
		$out->addHTML(
			Html::rawElement( 'div', [ 'class' => 'frw-showcase-config' ],
				Html::rawElement( 'div', [ 'class' => 'frw-config-form' ],
					$this->buildTextareaField( 'frw-banner-message', 'fundraisingwidgets-config-message', $defaultMessage, true ) .
					$this->buildTextField( 'frw-banner-button-text', 'fundraisingwidgets-config-button-text', 'Donate' ) .
					$this->buildSelectField( 'frw-banner-logo', 'fundraisingwidgets-config-logo', [
						'globe' => 'fundraisingwidgets-logo-globe',
						'globe-hands' => 'fundraisingwidgets-logo-globe-hands',
						'combined' => 'fundraisingwidgets-logo-combined',
						'wordmark' => 'fundraisingwidgets-logo-wordmark',
						'wmf' => 'fundraisingwidgets-logo-wmf',
						'none' => 'fundraisingwidgets-logo-none',
					], 'globe' ) .
					$this->buildCheckboxField( 'frw-banner-dismissible', 'fundraisingwidgets-config-dismissible', true ) .
					$this->buildTextField( 'frw-banner-button-link', 'fundraisingwidgets-config-button-link', self::DONATE_URL, true )
				)
			)
		);

		// Generated code
		$wikitextCode = '{{#fundraising-banner: message=' . $defaultMessage . ' | logo=globe | dismissible=true }}';
		$jsCode = '<script src="' . $this->getEmbedScriptUrl() . '"></script>' . "\n" .
			'<div class="frw-embed" data-widget="banner" data-message="' . $defaultMessage . '" data-logo="globe" data-dismissible="true"></div>';

		$out->addHTML( $this->buildCodeSection( 'frw-banner', $wikitextCode, $jsCode ) );
		$out->addHTML( Html::closeElement( 'div' ) );
	}

	private function renderBannerPreview(): string {
		$basePath = $this->getConfig()->get( 'ExtensionAssetsPath' ) . '/FundraisingWidgets/resources/images/';

		$closeButton = Html::rawElement( 'button',
			[ 'type' => 'button', 'class' => 'frw-banner-close', 'aria-label' => 'Close' ],
			Html::element( 'span', [ 'aria-hidden' => 'true' ], '×' )
		);

		$logo = Html::rawElement( 'div', [ 'class' => 'frw-banner-logo frw-banner-logo--globe' ],
			Html::element( 'img', [
				'src' => $basePath . 'Wikipedia-logo-v2.svg',
				'class' => 'frw-logo frw-logo-globe',
				'alt' => 'Wikipedia'
			] )
		);

		$content = Html::rawElement( 'div', [ 'class' => 'frw-banner-content' ],
			Html::element( 'p', [ 'class' => 'frw-banner-message' ],
				'If Wikipedia has given you useful knowledge this year, please give back. There are no small contributions: every edit counts, every donation counts.'
			) .
			Html::rawElement( 'a',
				[ 'href' => self::DONATE_URL, 'class' => 'frw-donate-button frw-donate-button--medium', 'role' => 'button' ],
				Html::element( 'span', [ 'class' => 'frw-button-text' ], 'Donate' )
			)
		);

		return Html::rawElement( 'div', [ 'class' => 'frw-banner frw-banner--dismissible', 'role' => 'banner' ],
			$closeButton . $logo . $content
		);
	}

	private function showImageSection(): void {
		$out = $this->getOutput();
		$defaultCaption = 'You mean I can donate to this thing!!!';

		$out->addHTML( Html::openElement( 'div', [ 'class' => 'frw-showcase-section', 'id' => 'frw-section-image' ] ) );
		$out->addHTML( Html::element( 'h2', [], $this->msg( 'fundraisingwidgets-special-image-title' )->text() ) );

		// Preview area
		$out->addHTML(
			Html::rawElement( 'div', [ 'class' => 'frw-showcase-preview' ],
				Html::rawElement( 'div', [ 'class' => 'frw-preview-area', 'id' => 'frw-image-preview' ],
					$this->renderImagePreview()
				)
			)
		);

		// Configuration form
		$out->addHTML(
			Html::rawElement( 'div', [ 'class' => 'frw-showcase-config' ],
				Html::rawElement( 'div', [ 'class' => 'frw-config-form' ],
					$this->buildSelectField( 'frw-image-bg', 'fundraisingwidgets-config-image', [
						'landscape' => 'fundraisingwidgets-image-landscape',
						'eagle' => 'fundraisingwidgets-image-eagle',
						'mountain' => 'fundraisingwidgets-image-mountain',
						'snow-leopard' => 'fundraisingwidgets-image-snow-leopard',
						'frog' => 'fundraisingwidgets-image-frog',
						'cat-lizard' => 'fundraisingwidgets-image-cat-lizard',
						'regal-cat' => 'fundraisingwidgets-image-regal-cat',
					], 'snow-leopard' ) .
					$this->buildSelectField( 'frw-image-size', 'fundraisingwidgets-config-size', [
						'small' => 'fundraisingwidgets-size-small',
						'medium' => 'fundraisingwidgets-size-medium',
						'large' => 'fundraisingwidgets-size-large',
					], 'medium' ) .
					$this->buildSelectField( 'frw-image-position', 'fundraisingwidgets-config-button-position', [
						'top-left' => 'fundraisingwidgets-position-top-left',
						'top-right' => 'fundraisingwidgets-position-top-right',
						'bottom-left' => 'fundraisingwidgets-position-bottom-left',
						'bottom-right' => 'fundraisingwidgets-position-bottom-right',
					], 'bottom-right' ) .
					$this->buildColorPickerField( 'frw-image-colors', 'fundraisingwidgets-config-button-color' ) .
					$this->buildTextField( 'frw-image-caption', 'fundraisingwidgets-config-caption', $defaultCaption, true,
						'Caption text (leave empty to show button on image)' ) .
					$this->buildTextField( 'frw-image-button-link', 'fundraisingwidgets-config-button-link', self::DONATE_URL )
				)
			)
		);

		// Generated code
		$wikitextCode = '{{#fundraising-image: image=snow-leopard | size=medium | button-color=blue | caption=' . $defaultCaption . ' }}';
		$jsCode = '<script src="' . $this->getEmbedScriptUrl() . '"></script>' . "\n" .
			'<div class="frw-embed" data-widget="image" data-image="snow-leopard" data-size="medium" data-button-color="blue" data-caption="' . $defaultCaption . '"></div>';

		$out->addHTML( $this->buildCodeSection( 'frw-image', $wikitextCode, $jsCode ) );
		$out->addHTML( Html::closeElement( 'div' ) );
	}

	private function renderImagePreview(): string {
		$imageUrl = 'https://upload.wikimedia.org/wikipedia/commons/f/f3/Snow_Leopard_Looking_Up.jpg';

		$captionBar = Html::rawElement( 'div', [ 'class' => 'frw-image-caption-bar' ],
			Html::element( 'p', [ 'class' => 'frw-image-caption' ], 'You mean I can donate to this thing!!!' ) .
			Html::rawElement( 'a',
				[ 'href' => self::DONATE_URL, 'class' => 'frw-donate-button frw-donate-button--medium frw-donate-button--blue', 'role' => 'button' ],
				Html::element( 'span', [ 'class' => 'frw-button-text' ], $this->msg( 'fundraisingwidgets-button-donate' )->text() )
			)
		);

		return Html::rawElement( 'div',
			[
				'class' => 'frw-image-widget frw-image-widget--medium frw-image-widget--has-caption',
				'style' => 'background-image: url(\'' . htmlspecialchars( $imageUrl, ENT_QUOTES ) . '\');'
			],
			Html::element( 'div', [ 'class' => 'frw-image-overlay' ] ) . $captionBar
		);
	}

	private function showRabbitHoleSection(): void {
		$out = $this->getOutput();

		$out->addHTML( Html::openElement( 'div', [ 'class' => 'frw-showcase-section', 'id' => 'frw-section-rabbithole' ] ) );
		$out->addHTML( Html::element( 'h2', [], $this->msg( 'fundraisingwidgets-special-rabbithole-title' )->text() ) );

		// Preview area
		$out->addHTML(
			Html::rawElement( 'div', [ 'class' => 'frw-showcase-preview' ],
				Html::rawElement( 'div', [ 'class' => 'frw-preview-area', 'id' => 'frw-rabbithole-preview' ],
					$this->renderRabbitHolePreview()
				)
			)
		);

		// Configuration form
		$out->addHTML(
			Html::rawElement( 'div', [ 'class' => 'frw-showcase-config' ],
				Html::rawElement( 'div', [ 'class' => 'frw-config-form' ],
					$this->buildSelectField( 'frw-rabbithole-theme', 'fundraisingwidgets-config-theme', [
						'light' => 'fundraisingwidgets-theme-light',
						'dark' => 'fundraisingwidgets-theme-dark',
					], 'light' ) .
					$this->buildSelectField( 'frw-rabbithole-donate-after', 'fundraisingwidgets-config-donate-after', [
						'2' => null,
						'3' => null,
						'4' => null,
						'5' => null,
					], '3' ) .
					$this->buildTextField( 'frw-rabbithole-button-text', 'fundraisingwidgets-config-button-text', '', false, 'Discover something new' ) .
					$this->buildTextField( 'frw-rabbithole-button-link', 'fundraisingwidgets-config-button-link', self::DONATE_URL )
				)
			)
		);

		// Generated code
		$wikitextCode = '{{#fundraising-rabbithole: theme=light | donate-after=3 }}';
		$jsCode = '<script src="' . $this->getEmbedScriptUrl() . '"></script>' . "\n" .
			'<div class="frw-embed" data-widget="rabbithole" data-theme="light" data-donate-after="3"></div>';

		$out->addHTML( $this->buildCodeSection( 'frw-rabbithole', $wikitextCode, $jsCode ) );
		$out->addHTML( Html::closeElement( 'div' ) );
	}

	private function renderRabbitHolePreview(): string {
		$intro = Html::rawElement( 'div', [ 'class' => 'frw-rabbit-hole-intro' ],
			Html::element( 'h3', [ 'class' => 'frw-rabbit-hole-title' ],
				$this->msg( 'fundraisingwidgets-rabbithole-title' )->text()
			) .
			Html::element( 'p', [ 'class' => 'frw-rabbit-hole-subtitle' ],
				$this->msg( 'fundraisingwidgets-rabbithole-subtitle' )->text()
			)
		);

		$discovery = Html::element( 'div', [ 'class' => 'frw-rabbit-hole-discovery frw-rabbit-hole-discovery--hidden' ] );

		$donate = Html::rawElement( 'div', [ 'class' => 'frw-rabbit-hole-donate frw-rabbit-hole-donate--hidden' ],
			Html::element( 'p', [ 'class' => 'frw-rabbit-hole-donate-message' ],
				$this->msg( 'fundraisingwidgets-rabbithole-donate-message' )->text()
			) .
			Html::rawElement( 'a',
				[ 'href' => self::DONATE_URL, 'class' => 'frw-donate-button frw-donate-button--medium frw-donate-button--green', 'role' => 'button' ],
				Html::element( 'span', [ 'class' => 'frw-button-text' ], $this->msg( 'fundraisingwidgets-button-donate' )->text() )
			)
		);

		$content = Html::rawElement( 'div', [ 'class' => 'frw-rabbit-hole-content' ],
			$intro . $discovery . $donate
		);

		$footer = Html::rawElement( 'div', [ 'class' => 'frw-rabbit-hole-footer' ],
			Html::element( 'button',
				[ 'type' => 'button', 'class' => 'frw-rabbit-hole-button' ],
				$this->msg( 'fundraisingwidgets-rabbithole-discover' )->text()
			) .
			Html::element( 'span', [ 'class' => 'frw-rabbit-hole-counter' ] )
		);

		return Html::rawElement( 'div', [ 'class' => 'frw-rabbit-hole frw-rabbit-hole--light' ],
			$content . $footer
		);
	}

	// =========================================================================
	// Helper methods for building form elements
	// =========================================================================

	private function getEmbedScriptUrl(): string {
		return $this->getConfig()->get( 'Server' ) .
			$this->getConfig()->get( 'ExtensionAssetsPath' ) .
			'/FundraisingWidgets/resources/embed.js';
	}

	private function buildColorPicker( string $id, string $default = 'blue'): string {
		$colors = [ 'blue', 'purple', 'green', 'red', 'yellow' ];
		$buttons = '';
		foreach ( $colors as $color ) {
			$class = 'frw-color-option frw-color-option--' . $color;
			if ( $color === $default ) {
				$class .= ' frw-color-option--selected';
			}
			$buttons .= Html::element( 'button', [
				'type' => 'button',
				'class' => $class,
				'data-color' => $color,
				'title' => ucfirst( $color )
			] );
		}
		return Html::rawElement( 'div', [ 'class' => 'frw-color-picker', 'id' => $id ], $buttons );
	}

	private function buildTextField(
		string $id,
		string $labelMsg,
		string $value,
		bool $fullWidth = false,
		?string $placeholder = null
	): string {
		$fieldClass = 'frw-config-field' . ( $fullWidth ? ' frw-config-field--full' : '' );
		return Html::rawElement( 'div', [ 'class' => $fieldClass ],
			Html::element( 'label', [ 'for' => $id ], $this->msg( $labelMsg )->text() ) .
			Html::element( 'input', [
				'type' => 'text',
				'id' => $id,
				'class' => 'frw-config-input',
				'value' => $value,
				'placeholder' => $placeholder ?? $value
			] )
		);
	}

	private function buildTextareaField(
		string $id,
		string $labelMsg,
		string $value,
		bool $fullWidth = false
	): string {
		$fieldClass = 'frw-config-field' . ( $fullWidth ? ' frw-config-field--full' : '' );
		return Html::rawElement( 'div', [ 'class' => $fieldClass ],
			Html::element( 'label', [ 'for' => $id ], $this->msg( $labelMsg )->text() ) .
			Html::element( 'textarea', [
				'id' => $id,
				'class' => 'frw-config-input',
				'rows' => '2'
			], $value )
		);
	}

	private function buildSelectField(
		string $id,
		string $labelMsg,
		array $options,
		string $selected
	): string {
		$optionsHtml = '';
		foreach ( $options as $value => $msgKey ) {
			$attrs = [ 'value' => $value ];
			if ( $value === $selected ) {
				$attrs['selected'] = true;
			}
			$label = $msgKey ? $this->msg( $msgKey )->text() : $value;
			$optionsHtml .= Html::element( 'option', $attrs, $label );
		}

		return Html::rawElement( 'div', [ 'class' => 'frw-config-field' ],
			Html::element( 'label', [ 'for' => $id ], $this->msg( $labelMsg )->text() ) .
			Html::rawElement( 'select', [ 'id' => $id, 'class' => 'frw-config-input' ], $optionsHtml )
		);
	}

	private function buildCheckboxField( string $id, string $labelMsg, bool $checked ): string {
		$attrs = [ 'type' => 'checkbox', 'id' => $id ];
		if ( $checked ) {
			$attrs['checked'] = true;
		}

		return Html::rawElement( 'div', [ 'class' => 'frw-config-field' ],
			Html::rawElement( 'label', [],
				Html::element( 'input', $attrs ) .
				' ' . $this->msg( $labelMsg )->text()
			)
		);
	}

	private function buildColorPickerField( string $id, string $labelMsg ): string {
		return Html::rawElement( 'div', [ 'class' => 'frw-config-field' ],
			Html::element( 'label', [], $this->msg( $labelMsg )->text() ) .
			$this->buildColorPicker( $id )
		);
	}

	private function buildCodeSection( string $prefix, string $wikitextCode, string $jsCode ): string {
		$tabs = Html::rawElement( 'div', [ 'class' => 'frw-format-tabs' ],
			Html::element( 'button',
				[ 'type' => 'button', 'class' => 'frw-format-tab frw-format-tab--active', 'data-format' => 'wikitext' ],
				'MediaWiki'
			) .
			Html::element( 'button',
				[ 'type' => 'button', 'class' => 'frw-format-tab', 'data-format' => 'javascript' ],
				'JavaScript'
			) .
			Html::element( 'button',
				[ 'type' => 'button', 'class' => 'frw-format-tab', 'data-format' => 'android' ],
				'Android'
			) .
			Html::element( 'button',
				[ 'type' => 'button', 'class' => 'frw-format-tab', 'data-format' => 'ios' ],
				'iOS'
			)
		);

		$wikitextContent = Html::rawElement( 'div',
			[ 'class' => 'frw-format-content frw-format-content--active', 'data-format' => 'wikitext' ],
			Html::element( 'pre', [ 'id' => $prefix . '-code-wikitext', 'class' => 'frw-code-output' ], $wikitextCode ) .
			Html::element( 'button',
				[ 'type' => 'button', 'class' => 'frw-copy-button', 'data-target' => $prefix . '-code-wikitext' ],
				$this->msg( 'fundraisingwidgets-special-copy' )->text()
			)
		);

		$jsContent = Html::rawElement( 'div',
			[ 'class' => 'frw-format-content', 'data-format' => 'javascript' ],
			Html::element( 'pre', [ 'id' => $prefix . '-code-js', 'class' => 'frw-code-output' ], $jsCode ) .
			Html::element( 'button',
				[ 'type' => 'button', 'class' => 'frw-copy-button', 'data-target' => $prefix . '-code-js' ],
				$this->msg( 'fundraisingwidgets-special-copy' )->text()
			)
		);

		$androidContent = Html::rawElement( 'div',
			[ 'class' => 'frw-format-content', 'data-format' => 'android' ],
			Html::element( 'pre',
				[ 'class' => 'frw-code-output frw-code-output--coming-soon' ],
				$this->msg( 'fundraisingwidgets-coming-soon' )->text()
			)
		);

		$iosContent = Html::rawElement( 'div',
			[ 'class' => 'frw-format-content', 'data-format' => 'ios' ],
			Html::element( 'pre',
				[ 'class' => 'frw-code-output frw-code-output--coming-soon' ],
				$this->msg( 'fundraisingwidgets-coming-soon' )->text()
			)
		);

		return Html::rawElement( 'div', [ 'class' => 'frw-showcase-code', 'id' => $prefix . '-code-section' ],
			$tabs . $wikitextContent . $jsContent . $androidContent . $iosContent
		);
	}

	/**
	 * @return string
	 */
	protected function getGroupName() {
		return 'wiki';
	}

	private function showWikipediaButtonSection(): void {
		$out = $this->getOutput();

		$out->addHTML(
			Html::openElement(
				'div',
				[
					'class' => 'frw-showcase-section',
					'id' => 'frw-section-wikipedia-button',
				]
			)
		);
		$out->addHTML(
			Html::element( 'h2', [], $this->msg( 'fundraisingwidgets-special-wikipedia-button-title' )->text() )
		);

		// Preview area with color picker
		$buttonPreview = Html::rawElement(
			'a',
			[
				'href' => 'https://wikipedia.org',
				'class' => 'frw-wikipedia-button frw-wikipedia-button--medium frw-wikipedia-button--blue',
				'role' => 'button',
			],
			Html::element( 'span', [ 'class' => 'frw-button-text' ], 'Built on Wikipedia' )
		);

		$out->addHTML(
			Html::rawElement(
				'div',
				[
					'class' => 'frw-showcase-preview',
					'id' => 'frw-wikipedia-button-preview-container',
				],
				Html::rawElement(
					'div',
					[
						'class' => 'frw-preview-area',
						'id' => 'frw-wikipedia-button-preview',
					],
					$buttonPreview
				) . $this->buildcolorPicker( 'frw-wikipedia-button-colors' )
			)
		);

		// Configuration form
		$out->addHtml(
			Html::rawElement(
				'div',
				[ 'class' => 'frw-showcase-config' ],
				Html::rawElement(
					'div',
					[ 'class' => 'frw-config-form' ],
					$this->buildTextField(
						'frw-wikipedia-button-text',
						'fundraisingwidgets-config-text',
						'Built on Wikipedia'
					) .
					$this->buildSelectField( 'frw-wikipedia-button-size', 'fundraisingwidgets-config-size', [
						'small' => 'fundraisingwidgets-size-small',
						'medium' => 'fundraisingwidgets-size-medium',
						'large' => 'fundraisingwidgets-size-large',
					], 'medium' ) .
					$this->buildTextField(
						'frw-wikipedia-button-link',
						'fundraisingwidgets-config-button-link',
						'https://www.wikipedia.org',
						true
					)
				)
			)
		);

		$wikiTextCode = '{{#fundraising-wikipedia-button: size=medium | text=Built on Wikipedia | color=blue }}';
		$jsCode = '<script src="' .
			$this->getEmbedScriptUrl() .
			'"></script>' .
			"\n" .
			'<div class="frw-embed" data-widget="fundraising-wikipedia-button" data-size="medium" data-text="Built on Wikipedia" data-color="blue"></div>';

		$out->addHTML( $this->buildCodeSection( 'frw-wikipedia-button', $wikiTextCode, $jsCode ) );
		$out->addHtml( Html::closeElement( 'div' ) );
	}

}
