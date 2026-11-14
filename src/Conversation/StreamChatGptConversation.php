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

class StreamChatGptConversation extends AbstractOpenAiConversation {

    public function __construct(IConfiguration $configuration) {
        parent::__construct($configuration);
    }

    public function chat(array $messages, array $context = []): string {
        $context['stream'] = true;

        $url = 'https://api.openai.com/v1/chat/completions';
        $model = $context['model'] ?? $this->model;

        $data = array_merge($this->options, $context, [
            'model' => $model,
            'messages' => $messages,
            'stream' => true,
            'max_tokens' => 4096
        ]);

        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . 'Bearer ' . $this->apiKey,
        ];

        $output = '';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_WRITEFUNCTION => function ($ch, $chunk) use (&$output) {
                $lines = explode("\n", $chunk);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (str_starts_with($line, 'data: ')) {
                        $json = substr($line, 6);
                        if ($json === '[DONE]') {
                            return strlen($chunk);
                        }

                        $data = json_decode($json, true);
                        if (isset($data['choices'][0]['delta']['content'])) {
                            $output .= $data['choices'][0]['delta']['content'];
                        }
                    }
                }
                return strlen($chunk);
            }
        ]);

        curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('OpenAI API stream failed: ' . $error);
        }

        curl_close($ch);
        return trim($output);
    }

    public function raw(array $messages, array $context = []) {
        throw new \RuntimeException("Streaming does not support 'raw' responses.");
    }
}

