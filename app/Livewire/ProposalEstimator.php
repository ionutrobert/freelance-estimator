<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProposalEstimator extends Component
{
    public $brief = '';                // Client job description
    public $hourly_rate = 45;          // Your default rate (editable)
    public $currency = 'USD';          // Default currency
    public $results = null;            // AI output
    public $error = null;
    public $savedEstimateId = null;    // Track the last saved estimate ID

    // Settings
    public $provider = 'groq';         // Default provider
    public $apiKey = '';               // Custom API Key
    public $showSettings = false;      // Toggle settings visibility

    public function mount()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->hourly_rate = $user->hourly_rate;
            $this->currency = $user->currency ?? 'USD';
        }
        
        // Load settings from session if available (overrides profile for this session)
        $this->provider = session('estimator_provider', 'groq');
        $this->apiKey = session('estimator_api_key', '');
        if (session()->has('estimator_currency')) $this->currency = session('estimator_currency');
        if (session()->has('estimator_hourly_rate')) $this->hourly_rate = session('estimator_hourly_rate');
    }

    // ... updatedProvider, updatedApiKey, toggleSettings remain the same ...

    public function estimate()
    {
        Log::info('estimate() method STARTED');

        $this->validate(['brief' => 'required|string|min:20']);
        $this->results = null;
        $this->error = null;
        $this->savedEstimateId = null;

        $systemPrompt = <<<PROMPT
You are an expert freelance PHP/Laravel developer with 10+ years experience.
Analyze this client brief and provide a realistic breakdown.
User hourly rate: {$this->hourly_rate} {$this->currency}.
Focus on mid-level full-stack work (Laravel, PHP, WordPress, basic JS/CSS).

Respond **only** in valid JSON format with this exact structure:
{
  "tasks": [
    {"name": "Task Name", "hours": 2.5, "description": "Details..."}
  ],
  "total_hours": 10,
  "total_price_low": 400,
  "total_price_high": 500,
  "notes": "Assumptions or warnings"
}
PROMPT;

        $userPrompt = "Brief: " . $this->brief . "\n\nProvide the JSON estimation.";

        try {
            // 1. Determine API Key
            $currentKey = $this->apiKey; // Session/Input key first
            
            if (empty($currentKey) && Auth::check()) {
                // Check DB for logged in user
                $dbKey = Auth::user()->apiKeys()->where('provider', $this->provider)->first();
                if ($dbKey) {
                    $currentKey = $dbKey->api_key;
                }
            }

            if (empty($currentKey)) {
                // Fallback to .env keys
                $envKey = match($this->provider) {
                    'groq' => config('services.groq.key') ?? env('GROQ_API_KEY'),
                    'xai' => config('services.grok.key') ?? env('GROK_API_KEY'),
                    'google' => config('services.google.key') ?? env('GOOGLE_API_KEY'), // Add to services/env if needed
                    'openrouter' => config('services.openrouter.key') ?? env('OPENROUTER_API_KEY'),
                    default => null,
                };
                $currentKey = $envKey;
            }
            
            if (empty($currentKey)) {
                throw new \Exception("API Key missing for {$this->provider}. Please enter it in Settings.");
            }

            // 2. Configure Request
            $url = '';
            $model = '';
            $headers = [];
            $body = [
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.7,
            ];

            switch ($this->provider) {
                case 'groq':
                    $url = 'https://api.groq.com/openai/v1/chat/completions';
                    $model = 'llama-3.3-70b-versatile';
                    $body['response_format'] = ['type' => 'json_object'];
                    break;
                case 'xai':
                    $url = 'https://api.x.ai/v1/chat/completions';
                    $model = 'grok-2';
                    $body['response_format'] = ['type' => 'json_object'];
                    break;
                case 'google':
                    // Google Gemini uses a different API structure usually, but let's try OpenAI compatibility if available or standard REST
                    // For simplicity in this edit, assuming OpenAI-compatible endpoint or adapting to Gemini API
                    // Actually, Gemini via AI Studio often uses `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=...`
                    // Implementing "Chat" style for Gemini REST:
                    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$currentKey}";
                    $model = 'gemini-1.5-flash';
                    $body = [
                        'contents' => [
                            ['parts' => [['text' => $systemPrompt . "\n\n" . $userPrompt]]]
                        ],
                        'generationConfig' => ['responseMimeType' => 'application/json']
                    ];
                    // Google uses Key in URL, not header usually, but we'll handle calls differently below
                    break;
                case 'openrouter':
                    $url = 'https://openrouter.ai/api/v1/chat/completions';
                    $model = 'google/gemini-2.0-flash-lite-preview-02-05:free'; // Example free model
                    $headers['HTTP-Referer'] = url('/');
                    break;
                default:
                    throw new \Exception("Unknown provider.");
            }

            if ($this->provider !== 'google') {
                $body['model'] = $model;
                $request = Http::withToken($currentKey)->withHeaders($headers);
            } else {
                $request = Http::withHeaders($headers); // Key is in URL for Google
            }
            
            // 3. Make Request
            $response = $request->withoutVerifying()->post($url, $body);

            if ($response->failed()) {
                throw new \Exception("AI Provider Error ({$this->provider}): " . $response->body());
            }

            // 4. Parse Response
            $jsonContent = '';
            if ($this->provider === 'google') {
                $jsonContent = $response->json('candidates.0.content.parts.0.text');
            } else {
                $jsonContent = $response->json('choices.0.message.content');
            }
            
            $jsonContent = preg_replace('/^```json\s*|\s*```$/', '', $jsonContent);
            $data = json_decode($jsonContent, true);

            // Fallback parsing ... (same as before)
            if (json_last_error() !== JSON_ERROR_NONE) {
                if (preg_match('/\{.*\}/s', $jsonContent, $matches)) {
                    $data = json_decode($matches[0], true);
                }
            }

            if (!is_array($data) || !isset($data['tasks'])) {
                 throw new \Exception('Invalid JSON structure received from AI.');
            }

            $this->results = $data;

            // Ensure we have a meaningful price spread (buffer)
            $low = $this->results['total_price_low'];
            $high = $this->results['total_price_high'];

            // If spread is less than 5%, or values are identical, create a buffer
            if ($high <= ($low * 1.05)) {
                $this->results['total_price_low'] = round($low * 0.95);
                $this->results['total_price_high'] = round($low * 1.15);
            }

            // 5. Save History (if logged in)
            if (Auth::check()) {
                $estimate = Auth::user()->estimates()->create([
                    'brief' => $this->brief,
                    'result' => $this->results, // Save the data inclusive of our buffer
                    'provider' => $this->provider,
                    'model' => $model,
                    'total_hours' => $this->results['total_hours'] ?? 0,
                    'price_low' => $this->results['total_price_low'] ?? 0,
                    'price_high' => $this->results['total_price_high'] ?? 0,
                    'currency' => $this->currency,
                    'hourly_rate' => $this->hourly_rate,
                ]);
                $this->savedEstimateId = $estimate->id;
            }

        } catch (\Exception $e) {
            Log::error('AI Error: ' . $e->getMessage());
            $this->error = $e->getMessage();
        }
    }

    public function toggleSettings()
    {
        $this->showSettings = !$this->showSettings;
    }

    public function updatedProvider($value)
    {
        session(['estimator_provider' => $value]);
    }

    public function updatedApiKey($value)
    {
        session(['estimator_api_key' => $value]);
    }

    public function render()
    {
        return view('livewire.proposal-estimator');
    }
}