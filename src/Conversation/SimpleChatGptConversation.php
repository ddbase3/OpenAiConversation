<?php

namespace OpenAiConversation\Conversation;

use Base3\Configuration\Api\IConfiguration;

class SimpleChatGptConversation extends AbstractOpenAiConversation {

	public function __construct(IConfiguration $configuration) {
		parent::__construct($configuration);
	}

	public function chat(array $messages, array $context = []): string {
		$response = $this->sendRequest($messages, $context);
		return $response['choices'][0]['message']['content'] ?? '';
	}

	public function raw(array $messages, array $context = []) {
		return $this->sendRequest($messages, $context);
	}
}

