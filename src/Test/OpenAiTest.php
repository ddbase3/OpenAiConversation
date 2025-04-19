<?php

namespace OpenAiConversation\Test;

use Base3\Configuration\Api\IConfiguration;
use Base3\Core\AbstractOutput;

class OpenAiTest extends AbstractOutput {

	private $configuration;

	public function __construct(IConfiguration $configuration) {
		$this->configuration = $configuration;
	}

	public function getOutput($out = "html") {

		return 'Disabled';

		$conversation = new \OpenAiConversation\Conversation\SimpleChatGptConversation($this->configuration);

		$messages = [
			[ 'role' => 'user', 'content' => 'Why is Peru such an exciting travel destination?']
		];

		$response = $conversation->chat($messages);

		$out = '<h1>Open AI Test</h1>';
		$out .= '<p><strong>Prompt:</strong></p>';
		$out .= '<p>' . $messages[0]['content'] . '</p>';
		$out .= '<p><strong>Response:</strong></p>';
		$out .= '<p>' . $response . '</p>';

		return $out;
	}

}

