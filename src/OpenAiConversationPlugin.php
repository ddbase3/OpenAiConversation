<?php declare(strict_types=1);

namespace OpenAiConversation;

use Base3\Api\IPlugin;
use Base3\Api\IContainer;
use Base3\Api\ICheck;

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
