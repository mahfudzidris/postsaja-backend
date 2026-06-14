<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GitHubIssueReporter
{
    protected string $repo;
    protected string $token;
    protected bool $enabled;

    public function __construct()
    {
        $this->repo = config('services.github.repo', 'mahfudzidris/postsaja-backend');
        $this->token = config('services.github.token');
        $this->enabled = !empty($this->token) && app()->isProduction();
    }

    /**
     * Report an exception as a GitHub issue
     */
    public function report(\Throwable $e, array $context = []): ?string
    {
        if (!$this->enabled) {
            return null;
        }

        try {
            $title = $this->buildTitle($e);
            $body = $this->buildBody($e, $context);

            // Check if similar issue already exists (last 50 issues)
            $existing = $this->findExistingIssue($title);
            if ($existing) {
                return $existing['html_url'];
            }

            $response = Http::withToken($this->token)
                ->post("https://api.github.com/repos/{$this->repo}/issues", [
                    'title' => $title,
                    'body' => $body,
                    'labels' => ['bug', 'auto-reported'],
                ]);

            if ($response->successful()) {
                $url = $response->json('html_url');
                Log::info('GitHub issue created', ['url' => $url]);
                return $url;
            }

            Log::warning('GitHub issue creation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

        } catch (\Throwable $e2) {
            Log::warning('GitHub issue reporter failed', [
                'error' => $e2->getMessage(),
            ]);
        }

        return null;
    }

    protected function buildTitle(\Throwable $e): string
    {
        $class = class_basename($e);
        $message = mb_substr($e->getMessage(), 0, 100);

        return "[Auto] {$class}: {$message}";
    }

    protected function buildBody(\Throwable $e, array $context): string
    {
        $trace = collect($e->getTrace())
            ->take(15)
            ->map(fn($t) => ($t['file'] ?? '?') . ':' . ($t['line'] ?? '?'))
            ->join("\n");

        return "## 🐛 Auto-Reported Error\n\n"
            . "**Time:** " . now()->toIso8601String() . "\n"
            . "**Environment:** " . app()->environment() . "\n\n"
            . "### Error\n"
            . "**Class:** `" . get_class($e) . "`\n"
            . "**Message:** {$e->getMessage()}\n"
            . "**File:** `{$e->getFile()}`\n"
            . "**Line:** {$e->getLine()}\n\n"
            . "### Stack Trace (top 15)\n```\n{$trace}\n```\n\n"
            . ($context ? "### Context\n```json\n" . json_encode($context, JSON_PRETTY_PRINT) . "\n```\n" : '')
            . "---\n_Generated automatically by PostSaja Error Reporter_";
    }

    protected function findExistingIssue(string $title): ?array
    {
        try {
            $response = Http::withToken($this->token)
                ->get("https://api.github.com/repos/{$this->repo}/issues", [
                    'state' => 'open',
                    'per_page' => 50,
                    'sort' => 'created',
                    'direction' => 'desc',
                ]);

            if (!$response->successful()) {
                return null;
            }

            foreach ($response->json() as $issue) {
                // Match by similar title prefix (first 80 chars)
                $similarity = similar_text($title, $issue['title'], $percent);
                if ($percent > 70) {
                    return $issue;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('GitHub issue dedup check failed', [
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}
