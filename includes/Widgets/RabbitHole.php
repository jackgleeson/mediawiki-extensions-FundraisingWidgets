<?php

namespace MediaWiki\Extension\FundraisingWidgets\Widgets;

use Parser;

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
		[
			'title' => 'The Dancing Plague of 1518',
			'fact' => 'In July 1518, hundreds of people in Strasbourg danced uncontrollably for days without rest. Some collapsed from exhaustion, strokes, or heart attacks. The cause remains a mystery to this day.',
			'url' => 'https://en.wikipedia.org/wiki/Dancing_plague_of_1518',
		],
		[
			'title' => 'Mantis Shrimp Vision',
			'fact' => 'Mantis shrimp have 16 types of color receptors compared to humans\' three. They can see ultraviolet light, polarized light, and have the most complex eyes in the animal kingdom.',
			'url' => 'https://en.wikipedia.org/wiki/Mantis_shrimp',
		],
		[
			'title' => 'Lake Baikal',
			'fact' => 'Lake Baikal in Siberia contains about 22% of the world\'s fresh surface water—more than all the North American Great Lakes combined. It\'s also the world\'s oldest lake at 25 million years.',
			'url' => 'https://en.wikipedia.org/wiki/Lake_Baikal',
		],
		[
			'title' => 'Immortal Jellyfish',
			'fact' => 'Turritopsis dohrnii can revert to its juvenile polyp stage after reaching maturity, essentially restarting its life cycle. It\'s the only known animal capable of complete biological immortality.',
			'url' => 'https://en.wikipedia.org/wiki/Turritopsis_dohrnii',
		],
		[
			'title' => 'The Library of Alexandria',
			'fact' => 'The ancient Library of Alexandria was one of the largest libraries in the ancient world, possibly holding 400,000 scrolls. Its destruction over centuries represents one of history\'s greatest losses of knowledge.',
			'url' => 'https://en.wikipedia.org/wiki/Library_of_Alexandria',
		],
		[
			'title' => 'Hippos Can\'t Swim',
			'fact' => 'Despite spending most of their time in water, hippos cannot swim or float. Their bones are too dense. Instead, they walk or bounce along the bottom of rivers and lakes.',
			'url' => 'https://en.wikipedia.org/wiki/Hippopotamus',
		],
		[
			'title' => 'Diamond Rain on Saturn',
			'fact' => 'On Saturn and Jupiter, atmospheric pressure is so intense that it may crush carbon atoms into diamonds. Scientists believe it literally rains diamonds in their atmospheres.',
			'url' => 'https://en.wikipedia.org/wiki/Saturn',
		],
		[
			'title' => 'The Antikythera Mechanism',
			'fact' => 'This 2,000-year-old Greek device is considered the world\'s first analog computer. It predicted astronomical positions and eclipses decades in advance using a complex system of gears.',
			'url' => 'https://en.wikipedia.org/wiki/Antikythera_mechanism',
		],
		[
			'title' => 'Crows Remember Faces',
			'fact' => 'Crows can recognize and remember human faces for years. They communicate threats to other crows and can hold grudges against people who have wronged them.',
			'url' => 'https://en.wikipedia.org/wiki/Crow',
		],
		[
			'title' => 'Neutron Star Density',
			'fact' => 'A teaspoon of neutron star material would weigh about 6 billion tons—roughly the weight of Mount Everest. Neutron stars are the collapsed cores of massive supergiant stars.',
			'url' => 'https://en.wikipedia.org/wiki/Neutron_star',
		],
		[
			'title' => 'The Wow! Signal',
			'fact' => 'In 1977, a radio telescope detected a powerful 72-second signal from deep space. Astronomer Jerry Ehman circled it and wrote "Wow!" on the printout. Its origin remains unexplained.',
			'url' => 'https://en.wikipedia.org/wiki/Wow!_signal',
		],
		[
			'title' => 'Trees Communicate Underground',
			'fact' => 'Trees share nutrients and information through underground fungal networks called mycorrhizal networks. Scientists call this the "Wood Wide Web"—older trees even nurture their seedlings.',
			'url' => 'https://en.wikipedia.org/wiki/Mycorrhizal_network',
		],
		[
			'title' => 'Vesuvius and Pompeii',
			'fact' => 'When Mount Vesuvius erupted in 79 AD, it released 100,000 times the thermal energy of the Hiroshima bomb. The city of Pompeii was buried under 4-6 meters of volcanic ash and pumice.',
			'url' => 'https://en.wikipedia.org/wiki/Eruption_of_Mount_Vesuvius_in_79_AD',
		],
		[
			'title' => 'Slime Mold Intelligence',
			'fact' => 'Despite having no brain or nervous system, slime molds can solve mazes, learn patterns, and find the most efficient routes between food sources. They\'ve even recreated Tokyo\'s rail network.',
			'url' => 'https://en.wikipedia.org/wiki/Slime_mold',
		],
		[
			'title' => 'The Fermi Paradox',
			'fact' => 'Given the high probability of extraterrestrial life in the universe, why haven\'t we detected any signs of it? This contradiction between expectation and reality puzzles scientists to this day.',
			'url' => 'https://en.wikipedia.org/wiki/Fermi_paradox',
		],
		[
			'title' => 'Lightning Creates Glass',
			'fact' => 'When lightning strikes sand, it can fuse the grains into hollow glass tubes called fulgurites. These structures can extend several meters into the ground and preserve the lightning\'s path.',
			'url' => 'https://en.wikipedia.org/wiki/Fulgurite',
		],
		[
			'title' => 'The Overview Effect',
			'fact' => 'Astronauts often experience a profound cognitive shift when viewing Earth from space. This "overview effect" creates feelings of awe and a new perspective on the fragility of our planet.',
			'url' => 'https://en.wikipedia.org/wiki/Overview_effect',
		],
		[
			'title' => 'Elephant Memory',
			'fact' => 'Elephants can remember locations, individuals, and events for decades. They\'ve been observed visiting the bones of deceased family members years after death, gently touching them with their trunks.',
			'url' => 'https://en.wikipedia.org/wiki/Elephant_cognition',
		],
		[
			'title' => 'The Deep Sea Gigantism',
			'fact' => 'Many deep-sea creatures grow to enormous sizes compared to their shallow-water relatives. Giant squid, giant isopods, and Japanese spider crabs are examples of this mysterious phenomenon.',
			'url' => 'https://en.wikipedia.org/wiki/Deep-sea_gigantism',
		],
		[
			'title' => 'Human-Made Mass',
			'fact' => 'In 2020, the total mass of human-made objects exceeded the mass of all living things on Earth for the first time. Buildings, roads, and machines now outweigh all animals and plants combined.',
			'url' => 'https://en.wikipedia.org/wiki/Anthropogenic_mass',
		],
	];

	/**
	 * Render the rabbit hole widget
	 *
	 * @param Parser $parser
	 * @param mixed ...$args
	 * @return array
	 */
	public static function render( Parser $parser, ...$args ): array {
		$parser->getOutput()->addModules( [ 'ext.fundraisingWidgets.js' ] );
		$parser->getOutput()->addModuleStyles( [ 'ext.fundraisingWidgets' ] );

		$params = self::parseArgs( $args );

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
	 * @param array $args
	 * @return array
	 */
	private static function parseArgs( array $args ): array {
		$params = [
			'theme' => 'light',
			'donate-after' => '3',
			'button-text' => '',
			'button-link' => '',
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
