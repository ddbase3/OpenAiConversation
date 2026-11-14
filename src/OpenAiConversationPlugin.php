<?php declare(strict_types=1);

/***********************************************************************
 * This file is part of OpenAiConversation for BASE3 Framework.
 *
 * OpenAiConversation provides conversation implementations.
 * It supplies OpenAI-oriented chat and image conversation APIs.
 * These implementations reuse BASE3 configuration contracts.
 *
 * Developed by Daniel Dahme
 * Licensed under GPL-3.0
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * https://base3.de
 ***********************************************************************/


namespace Base3\OpenAiConversation;

use Base3\CoreFoundation\Api\IPlugin;
use Base3\CoreFoundation\Api\IContainer;
use Base3\CoreFoundation\Api\ICheck;

class OpenAiConversationPlugin implements IPlugin, ICheck {

	public function __construct(protected readonly IContainer $container) {}

	// Implementation of IBase

	public static function getName(): string {
		$fullClass = static::class;
		$parts = explode('\\', $fullClass);
		return strtolower(end($parts));
	}

	// Implementation of IPlugin

	public function init() {
		$this->container->set(self::getName(), $this, IContainer::SHARED);
	}

        // Implementation of ICheck

        public function checkDependencies(): array {
                return [];
        }
}
