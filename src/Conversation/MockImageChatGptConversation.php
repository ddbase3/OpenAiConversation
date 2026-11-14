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

protected function sendRequest(array $messages, array $context = []) {
    // Simulate two different completions (choices)
    $mockChoice1 = <<<EOT
**Photo Description (Markdown):**

### Serenity in Motion – Ballet Photography with Isabelle

Under the golden hues of the Paris Opera House, **Isabelle Moreau** strikes a stunning *arabesque*, her **black leotard** accentuating her sculpted form against the historic stone backdrop. The late afternoon light highlights the grace in her posture, creating a timeless portrait of elegance and poise. This image captures the spirit of **ballet photography** where power and softness exist in perfect harmony.

**Alt Text (Plain Text):**

Ballet dancer Isabelle Moreau poses in an arabesque at the Paris Opera House during golden hour. She wears a black leotard and has bare legs, standing with grace in soft warm light. The scene is peaceful and artistically composed, emphasizing classical beauty and balance.
EOT;

    $mockChoice2 = <<<EOT
**Photo Description (Markdown):**

### Timeless Grace – A Ballet Moment with Isabelle

Framed by the regal architecture of the **Paris Opera House**, Isabelle floats mid-arabesque, her posture defined and breathtaking. Wearing a minimalistic black leotard and bare legs, her silhouette contrasts beautifully with the warm golden sunlight. This shot elevates **ballet photography** to fine art — each detail evoking strength, elegance, and Parisian charm.

**Alt Text (Plain Text):**

Isabelle Moreau performs a classical ballet pose in front of the Paris Opera House, captured during sunset. She's dressed in a black leotard, legs bare, illuminated by golden natural light. The setting is rich in texture and elegance, reflecting the artistry and strength of ballet.
EOT;

    return [
        'id' => 'chatcmpl-mock-id-2',
        'object' => 'chat.completion',
        'created' => time(),
        'model' => $this->model,
        'choices' => [
            [
                'index' => 0,
                'message' => [
                    'role' => 'assistant',
                    'content' => $mockChoice1
                ],
                'finish_reason' => 'stop'
            ],
            [
                'index' => 1,
                'message' => [
                    'role' => 'assistant',
                    'content' => $mockChoice2
                ],
                'finish_reason' => 'stop'
            ]
        ],
        'usage' => [
            'prompt_tokens' => 120,
            'completion_tokens' => 380,
            'total_tokens' => 500
        ]
    ];
}

}

