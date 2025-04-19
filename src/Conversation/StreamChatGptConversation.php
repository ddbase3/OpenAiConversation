<?php

namespace OpenAiConversation\Conversation;

use Base3\Configuration\Api\IConfiguration;

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
            'stream' => true
        ]);

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Wurde auf true geändert
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $chunk) use (&$output) {
            $lines = explode("\n", $chunk);
            foreach ($lines as $line) {
                if ($this->my_str_starts_with($line, 'data: ')) {
                    $json = trim(substr($line, 6));
                    if ($json === '[DONE]') continue;

                    $data = json_decode($json, true);
                    if (isset($data['choices'][0]['delta']['content'])) {
                        $content = $data['choices'][0]['delta']['content'];
                        $output .= $content; // Zum Sammeln der Ausgabe in der Variablen
                    }
                }
            }
            return strlen($chunk);
        });

        // Den Curl-Request ausführen und sicherstellen, dass keine Fehler auftreten
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \RuntimeException('OpenAI API stream failed: ' . curl_error($ch));
        }

        curl_close($ch);

        // Sicherstellen, dass das JSON ordnungsgemäß abgeschlossen wird
        return rtrim($output);  // Entfernt unnötige Leerzeichen und Zeilenumbrüche am Ende
    }

    public function raw(array $messages, array $context = []) {
        throw new \RuntimeException("Streaming does not support 'raw' responses.");
    }

    // Private methods
    function my_str_starts_with(string $haystack, string $needle): bool
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}

