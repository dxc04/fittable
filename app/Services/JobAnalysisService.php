<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;

class JobAnalysisService
{
    public function analyzeJobAd(string $jobAdText): array
    {
        $prompt = $this->buildAnalysisPrompt($jobAdText);

        $result = Gemini::geminiPro()->generateContent($prompt);

        return $this->parseGeminiResponse($result->text());
    }

    protected function buildAnalysisPrompt(string $jobAdText): string
    {
        return <<<PROMPT
Analyze the following job advertisement and extract structured information. Return ONLY a valid JSON object with this exact structure:

{
    "jobTitle": "extracted job title",
    "company": "company name or 'Not specified'",
    "location": "location or 'Not specified'",
    "jobType": "Full-time/Part-time/Contract/etc or 'Not specified'",
    "summary": "2-3 sentence summary of the role",
    "skills": [
        {"name": "skill name", "level": 1-100, "category": "technical/soft/domain"}
    ],
    "responsibilities": [
        {"title": "responsibility title", "importance": 1-100, "description": "brief description"}
    ],
    "requirements": [
        {"title": "requirement title", "type": "must-have/nice-to-have", "priority": 1-100}
    ],
    "benefits": ["benefit 1", "benefit 2"],
    "salaryRange": "salary range or 'Not specified'"
}

Job Advertisement:
{$jobAdText}

Return ONLY the JSON object, no additional text or explanation.
PROMPT;
    }

    protected function parseGeminiResponse(string $response): array
    {
        // Remove markdown code blocks if present
        $response = preg_replace('/```json\s*|\s*```/', '', $response);
        $response = trim($response);

        try {
            $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

            // Ensure all required fields exist with defaults
            return [
                'jobTitle' => $data['jobTitle'] ?? 'Job Position',
                'company' => $data['company'] ?? 'Not specified',
                'location' => $data['location'] ?? 'Not specified',
                'jobType' => $data['jobType'] ?? 'Not specified',
                'summary' => $data['summary'] ?? '',
                'skills' => $data['skills'] ?? [],
                'responsibilities' => $data['responsibilities'] ?? [],
                'requirements' => $data['requirements'] ?? [],
                'benefits' => $data['benefits'] ?? [],
                'salaryRange' => $data['salaryRange'] ?? 'Not specified',
            ];
        } catch (\JsonException $e) {
            throw new \RuntimeException('Failed to parse AI response: '.$e->getMessage());
        }
    }
}
