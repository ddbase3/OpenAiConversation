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

use RuntimeException;

class StreamChatGptConversation extends AbstractOpenAiConversation {

	public function chat(array $messages, array $context = []): string {
		$payload = $this->buildRequest($messages, $context);
		$payload['stream'] = true;
		$ch = curl_init('https://api.openai.com/v1/chat/completions');
		if ($ch === false) {
			throw new RuntimeException('Could not initialize OpenAI stream.');
		}

		$output = '';
		$buffer = '';
		$errorBody = '';
		curl_setopt_array($ch, [
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
				'Authorization: Bearer ' . $this->apiKey
			],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT => 120,
			CURLOPT_WRITEFUNCTION => function ($handle, string $chunk) use (&$output, &$buffer, &$errorBody): int {
				if ((int) curl_getinfo($handle, CURLINFO_HTTP_CODE) !== 200) {
					$errorBody .= substr($chunk, 0, max(0, 4096 - strlen($errorBody)));
					return strlen($chunk);
				}

				$buffer = str_replace("\r\n", "\n", $buffer . $chunk);
				while (($position = strpos($buffer, "\n\n")) !== false) {
					$event = substr($buffer, 0, $position);
					$buffer = substr($buffer, $position + 2);
					foreach (explode("\n", $event) as $line) {
						if (!str_starts_with($line, 'data: ')) continue;
						$json = substr($line, 6);
						if ($json === '[DONE]') continue;
						$data = json_decode($json, true);
						if (is_array($data)) {
							$content = $data['choices'][0]['delta']['content'] ?? null;
							if (is_string($content)) $output .= $content;
						}
					}
				}
				return strlen($chunk);
			}
		]);

		$result = curl_exec($ch);
		$error = $result === false ? curl_error($ch) : '';
		$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($result === false) {
			throw new RuntimeException('OpenAI stream failed: ' . $error);
		}
		if ($httpCode !== 200) {
			throw new RuntimeException($this->formatApiError($httpCode, $errorBody));
		}
		if (trim($output) === '') {
			throw new RuntimeException('OpenAI stream returned no generated text.');
		}
		return trim($output);
	}

	public function raw(array $messages, array $context = []) {
		throw new RuntimeException('Streaming does not support raw responses.');
	}
}
