<?php

namespace App\Services;

use Anthropic\Client;
use Anthropic\Messages\Base64ImageSource;
use Anthropic\Messages\ImageBlockParam;
use Anthropic\Messages\JSONOutputFormat;
use Anthropic\Messages\OutputConfig;
use Anthropic\Messages\TextBlockParam;
use App\Models\BugCategory;
use App\Models\BugPriority;
use App\Models\BugReport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BugTriageService
{
    public function __construct(public Client $client) {}

    /**
     * Whether an Anthropic API key has been configured. The rest of the app
     * must keep working with AI triage entirely absent when this is false.
     */
    public function isConfigured(): bool
    {
        return filled(config('services.anthropic.api_key'));
    }

    /**
     * Classify a bug report's category and priority using Claude, restricted to
     * whichever bug_categories/bug_priorities rows currently exist. Leaves the
     * report untriaged (nulls) on any failure rather than throwing, so the
     * queued job that calls this is not retried against a permanent classification error.
     */
    public function triage(BugReport $bugReport): void
    {
        if (! $this->isConfigured()) {
            Log::info('AI bug triage skipped: ANTHROPIC_API_KEY is not configured.', [
                'bug_report_id' => $bugReport->id,
            ]);

            return;
        }

        $categories = BugCategory::pluck('name', 'id');
        $priorities = BugPriority::orderBy('level')->pluck('name', 'id');

        if ($categories->isEmpty() || $priorities->isEmpty()) {
            Log::warning('AI bug triage skipped: no bug categories/priorities configured.', [
                'bug_report_id' => $bugReport->id,
            ]);

            return;
        }

        try {
            $message = $this->client->messages->create(
                model: 'claude-opus-4-8',
                maxTokens: 1024,
                messages: [[
                    'role' => 'user',
                    'content' => $this->buildContent($bugReport, $categories, $priorities),
                ]],
                outputConfig: OutputConfig::with(
                    format: JSONOutputFormat::with(schema: $this->buildSchema($categories, $priorities)),
                ),
            );

            $result = json_decode($message->content[0]->text, true);

            if (! is_array($result)
                || ! $categories->has($result['category_id'] ?? null)
                || ! $priorities->has($result['priority_id'] ?? null)) {
                throw new \RuntimeException('Model response did not resolve to a valid category/priority pair.');
            }

            $bugReport->update([
                'bug_category_id' => $result['category_id'],
                'bug_priority_id' => $result['priority_id'],
                'ai_triage_confidence' => max(0, min(100, (int) $result['confidence'])),
                'ai_triage_reasoning' => $result['reasoning'],
                'ai_triaged_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::warning('AI bug triage failed.', [
                'bug_report_id' => $bugReport->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param  Collection<int, string>  $categories
     * @param  Collection<int, string>  $priorities
     * @return array<string, mixed>
     */
    private function buildSchema(Collection $categories, Collection $priorities): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'category_id' => [
                    'type' => 'integer',
                    'enum' => $categories->keys()->values()->all(),
                    'description' => 'The id of the bug category that best matches this report.',
                ],
                'priority_id' => [
                    'type' => 'integer',
                    'enum' => $priorities->keys()->values()->all(),
                    'description' => "The id of the bug priority that best matches this report's severity.",
                ],
                'confidence' => [
                    'type' => 'integer',
                    'description' => 'Confidence in this classification, from 0 to 100.',
                ],
                'reasoning' => [
                    'type' => 'string',
                    'description' => 'One or two sentences explaining the classification, for a human reviewer.',
                ],
            ],
            'required' => ['category_id', 'priority_id', 'confidence', 'reasoning'],
            'additionalProperties' => false,
        ];
    }

    /**
     * @param  Collection<int, string>  $categories
     * @param  Collection<int, string>  $priorities
     * @return array<int, TextBlockParam|ImageBlockParam>
     */
    private function buildContent(BugReport $bugReport, Collection $categories, Collection $priorities): array
    {
        $categoryList = $categories->map(fn ($name, $id) => "{$id}: {$name}")->implode("\n");
        $priorityList = $priorities->map(fn ($name, $id) => "{$id}: {$name}")->implode("\n");

        $prompt = <<<PROMPT
        Classify this bug report by choosing the single best-fitting category and priority from the lists below. Only use the ids provided — do not invent new ones.

        Title: {$bugReport->title}

        Description:
        {$bugReport->description}

        Available categories (id: name):
        {$categoryList}

        Available priorities (id: name):
        {$priorityList}
        PROMPT;

        $content = [TextBlockParam::with(text: $prompt)];

        foreach ($bugReport->images->take(3) as $image) {
            $data = Storage::disk('public')->get($image->image_path);

            if ($data === null) {
                continue;
            }

            $content[] = ImageBlockParam::with(
                source: Base64ImageSource::with(
                    data: base64_encode($data),
                    mediaType: $image->mime_type,
                ),
            );
        }

        return $content;
    }
}
