<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Supports: claude, openai, deepseek
    |
    | For Claude: Set AI_API_KEY to your Anthropic API key
    | For OpenAI: Set AI_API_KEY to your OpenAI API key
    | For DeepSeek: Set AI_API_KEY to your DeepSeek API key
    | 
    | API keys are read from .env: AI_PROVIDER, AI_API_KEY, AI_MODEL
    |
    */

    'provider' => env('AI_PROVIDER', 'claude'),

    'api_key' => env('AI_API_KEY', ''),

    'model' => env('AI_MODEL', 'claude-sonnet-4-20250514'),

    'max_tokens' => 300,
];
