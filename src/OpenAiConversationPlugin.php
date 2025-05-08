<?php declare(strict_types=1);

namespace OpenAiConversation;

use Base3\Api\IPlugin;
use Base3\Api\IContainer;
use Base3\Api\ICheck;

class OpenAiConversationPlugin implements IPlugin, ICheck {

	protected $container;

	public function __construct(IContainer $container) {
		$this->container = $container;
	}

	// Implementation of IBase

	public function getName(): string {
		return strtolower($this->getClassName());
	}

	// Implementation of IPlugin

	public function init() {
		$this->container->set($this->getName(), $this, IContainer::SHARED);
	}

        // Implementation of ICheck

        public function checkDependencies(): array {
                return [];
        }

        // Private methods

        private function getClassName(): string {
                return (new \ReflectionClass($this))->getShortName();
        }
}
