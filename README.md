# OpenAiConversation

This plugin provides the existing BASE3 `IConversation` implementations for OpenAI Chat Completions. It is the current implementation used by `ManageDancephotography` (image plus text input) and `ManageMisc` (streamed text generation). This update does not add a second OpenAI integration or require MissionBay.

## Configuration

The existing BASE3 `IConfiguration` group remains unchanged:

```ini
[openai]
apikey = "<server-side API key>"
```

Never place the API key into an HTML template or browser request.

The default for the standard, image, and streaming conversations is `gpt-6-luna`, which supports text and image input through `v1/chat/completions`. The current request options are `reasoning_effort = none` and `max_completion_tokens = 4096`. Callers may use `configure()` to select another compatible model and options, but must supply parameters appropriate to that model. The previous `gpt-4` and `gpt-4-turbo` defaults are retired.

## Error behavior and limitations

- Requests have a 10-second connection timeout and a 120-second total timeout.
- Non-200 HTTP responses raise a `RuntimeException` containing only HTTP status and a sanitized provider error code. Provider error bodies, image data, prompts and credentials are not included in the exception.
- Invalid JSON, missing completions and empty generated output cause an explicit exception.
- Streaming responses are parsed across network chunk boundaries; HTTP failures and empty output are errors instead of silent success.
- PHP cURL must be installed, a usable OpenAI API key must be configured, and the chosen model must be enabled for that key.
- Only a live request against an authorized OpenAI account verifies upstream availability, account access and real model behavior.

See `PRIVACY.md` for processing and data transfer details.
