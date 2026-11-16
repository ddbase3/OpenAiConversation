# OpenAiConversation privacy and processing notes

## Data flow and recipients

The existing client sends caller-provided messages, optional images and model parameters from the BASE3 server over HTTPS to the OpenAI Chat Completions API (`https://api.openai.com/v1/chat/completions`). The caller, not this transport, selects which source fields or image content it submits. `ManageDancephotography` applies a metadata allowlist before calling the client. Other callers must independently minimize their own data.

OpenAI receives the content and associated request information. OpenAI's actual retention, processing locations, subprocessors, training/data-use terms, and any international transfer depend on the API account, contract and deployment configuration. These cannot be established solely from this repository. Review the active contractual terms and applicable privacy assessment before using personal photographs.

## Credentials, storage and logging

An API key is loaded server-side from the existing BASE3 `IConfiguration` group `openai`, key `apikey`. It is sent only as an HTTPS Authorization header, never in a browser response. This plugin does not itself persist prompts, images or completions to a separate database or file store. Its exceptions suppress raw provider error bodies. Callers may separately display or store generated content and must document their own storage and deletion behavior. API-provider and infrastructure logs and backups require deployment-specific examination.

The plugin does not implement its own retention schedule or erasure endpoint; data subject requests involving OpenAI or the consuming service must be handled by the responsible deployment and provider contracts. No claim of full GDPR compliance or EU-only processing is made here.

## Security and user authorization

TLS verification is performed by PHP cURL's standard HTTPS handling. The plugin has no standalone user interface, actor identity, authorization decision, or credential rotation interface. An application invoking it must authenticate and authorize its own operation at the existing host authority. Timeouts reduce hanging requests but do not guarantee network or model availability.

## Accessibility

This plugin provides an API client only. Accessibility of UI status, error messages, dialogs, keyboard operation and review of generated text remains the responsibility of the consuming UI.
