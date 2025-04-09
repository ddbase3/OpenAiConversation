<?php

namespace App\AI;

class OpenAIChatService implements ChatAIInterface
{
    private string $apiKey;
    private string $model;
    private array $defaultOptions;

    public function __construct(string $apiKey, string $model = 'gpt-4-1106-vision-preview', array $defaultOptions = [])
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->defaultOptions = array_merge([
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ], $defaultOptions);
    }

    public function chat(array $messages, array $context = []): string
    {
        $response = $this->sendRequest($messages, $context);
        return $response['choices'][0]['message']['content'] ?? '';
    }

    public function raw(array $messages, array $context = []): mixed
    {
        return $this->sendRequest($messages, $context);
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function configure(array $options): void
    {
        $this->defaultOptions = array_merge($this->defaultOptions, $options);
    }

    public function extractToolCall(mixed $response): ?array
    {
        $message = $response['choices'][0]['message'] ?? null;
        if (!$message || !isset($message['content'])) {
            return null;
        }

        // Erwartet, dass die KI ein Steuerobjekt mitsendet
        if (isset($message['metadata']['target']) && in_array($message['metadata']['target'], ['crm', 'api'])) {
            return [
                'target' => $message['metadata']['target'],
                'payload' => $message['metadata']['payload'] ?? null,
            ];
        }

        // Alternativ: Versuche, JSON mit expliziter Anweisung zu parsen
        if (preg_match('/<action>(.*?)<\/action>/s', $message['content'], $matches)) {
            $json = json_decode($matches[1], true);
            if (json_last_error() === JSON_ERROR_NONE && isset($json['target'])) {
                return $json;
            }
        }

        return null;
    }

    public function isFinalResponse(mixed $response): bool
    {
        $message = $response['choices'][0]['message'] ?? null;
        if (!$message) {
            return false;
        }

        // Prüfe explizites Flag für finale Antwort
        if (isset($message['metadata']['final']) && $message['metadata']['final'] === true) {
            return true;
        }

        // Fallback: nach expliziter Markierung im Text suchen
        return stripos($message['content'], '<final>true</final>') !== false;
    }

    private function sendRequest(array $messages, array $context = []): array
    {
        $payload = array_merge($this->defaultOptions, $context, [
            'model' => $this->model,
            'messages' => $messages,
        ]);

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \RuntimeException('OpenAI API request failed: ' . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}

