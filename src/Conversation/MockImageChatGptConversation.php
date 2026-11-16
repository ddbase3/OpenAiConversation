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

namespace Base3\OpenAiConversation\Conversation;

use Base3\CoreFoundation\Configuration\Api\IConfiguration;

class MockImageChatGptConversation extends ImageChatGptConversation {

	public function __construct(IConfiguration $configuration) {
		parent::__construct($configuration);
	}

	protected function sendRequest(array $messages, array $context = []): array {
		return [
			'id' => 'chatcmpl-mock-image',
			'object' => 'chat.completion',
			'created' => time(),
			'model' => $this->model,
			'choices' => [[
				'index' => 0,
				'message' => [
					'role' => 'assistant',
					'content' => "## Ballet photography in warm light\n\nA dancer holds an elegant pose in a softly lit studio.\n### ALT TEXT START ###\nA ballet dancer poses in warm studio light."
				],
				'finish_reason' => 'stop'
			]]
		];
	}
}
