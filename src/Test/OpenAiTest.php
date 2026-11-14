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

namespace Base3\OpenAiConversation\Test;

use Base3\CoreFoundation\Configuration\Api\IConfiguration;
use Base3\CoreFoundation\Base\AbstractOutput;

class OpenAiTest extends AbstractOutput {

	private $configuration;

	public function __construct(IConfiguration $configuration) {
		$this->configuration = $configuration;
	}

	public function getOutput(string $out = 'html', bool $final = false): string {

		return 'Disabled';

		$conversation = new \Base3\OpenAiConversation\Conversation\SimpleChatGptConversation($this->configuration);

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
