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

class SimpleChatGptConversation extends AbstractOpenAiConversation {

	public function chat(array $messages, array $context = []): string {
		$response = $this->sendRequest($messages, $context);
		$content = $response['choices'][0]['message']['content'] ?? null;
		if (!is_string($content) || trim($content) === '') {
			throw new \RuntimeException('OpenAI returned no generated text.');
		}
		return $content;
	}

	public function raw(array $messages, array $context = []) {
		return $this->sendRequest($messages, $context);
	}
}
