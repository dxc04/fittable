<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;

class JobAnalysisService
{
    public function analyzeJobAd(string $jobAdText, ?string $jobTitle = null, ?string $company = null): array
    {
        $prompt = $this->buildAnalysisPrompt($jobAdText);

        $result = Gemini::generativeModel(model: 'gemini-2.0-flash-exp')
            ->generateContent($prompt);

        $analysis = $this->parseGeminiResponse($result->text());

        // Override with user-provided values if available
        if ($jobTitle) {
            $analysis['jobTitle'] = $jobTitle;
        }

        if ($company) {
            $analysis['company'] = $company;
        }

        return $analysis;
    }

    public function assessResume(string $resumeText, string $jobAdText): array
    {
        $prompt = $this->buildAssessmentPrompt($resumeText, $jobAdText);

        $result = Gemini::generativeModel(model: 'gemini-2.0-flash-exp')
            ->generateContent($prompt);

        return $this->parseAssessmentResponse($result->text());
    }

    protected function buildAssessmentPrompt(string $resumeText, string $jobAdText): string
    {
        return <<<PROMPT
Analyze this candidate's resume against the job requirements and provide a comprehensive assessment. Return ONLY a valid JSON object with this exact structure:

{
    "overallMatch": 85,
    "skillBreakdown": {
        "technical": 90,
        "soft": 80,
        "domain": 75
    },
    "summary": "You possess strong technical skills in software development and system design. Your experience aligns well with the requirements of this role. You would likely be a strong candidate for this position.",
    "strengths": [
        "You excel in technical skills, particularly in software development and system design",
        "Your communication skills are strong, enabling you to effectively convey complex ideas and collaborate with team members",
        "You demonstrate excellent problem-solving abilities through your project work and technical experience",
        "Your teamwork experience shows you can work effectively in collaborative environments"
    ],
    "gaps": [
        "Project management and leadership experience",
        "Cloud computing skills (AWS, Azure)",
        "Specific programming languages or frameworks mentioned in the job description"
    ],
    "recommendation": "hire"
}

Guidelines for assessment:
- overallMatch: Overall percentage match (0-100) based on how well the candidate's qualifications align with job requirements
- skillBreakdown.technical: Score for technical/hard skills (programming, tools, technologies) - 0-100
- skillBreakdown.soft: Score for soft skills (communication, teamwork, problem-solving) - 0-100
- skillBreakdown.domain: Score for domain knowledge and industry-specific expertise - 0-100
- summary: 2-3 sentences in second person ("You...") explaining the candidate's overall fit and potential
- strengths: Array of 3-5 specific, detailed strengths written in second person. Focus on concrete skills and experiences that align with the job
- gaps: Array of 2-4 specific skill gaps or areas for development. Be constructive and specific about what's missing
- recommendation: Must be one of: "hire" (80%+ match), "interview" (60-79% match), or "reject" (<60% match)

Job Requirements:
{$jobAdText}

Candidate Resume:
{$resumeText}

Return ONLY the JSON object, no additional text or markdown formatting.
PROMPT;
    }

    protected function parseAssessmentResponse(string $response): array
    {
        $response = preg_replace('/```json\\s*|\\s*```/', '', $response);
        $response = trim($response);

        try {
            $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

            return [
                'overallMatch' => $data['overallMatch'] ?? 0,
                'skillBreakdown' => [
                    'technical' => $data['skillBreakdown']['technical'] ?? 0,
                    'soft' => $data['skillBreakdown']['soft'] ?? 0,
                    'domain' => $data['skillBreakdown']['domain'] ?? 0,
                ],
                'summary' => $data['summary'] ?? '',
                'strengths' => $data['strengths'] ?? [],
                'gaps' => $data['gaps'] ?? [],
                'recommendation' => $data['recommendation'] ?? 'interview',
            ];
        } catch (\JsonException $e) {
            throw new \RuntimeException('Failed to parse AI assessment: '.$e->getMessage());
        }
    }

    protected function buildAnalysisPrompt(string $jobAdText): string
    {
        return <<<PROMPT
Analyze the following job advertisement and extract structured information. Return ONLY a valid JSON object with this exact structure:

{
    "jobTitle": "extracted job title",
    "company": "company name or 'Not specified'",
    "companyBackground": "Brief 1-2 sentence summary of the company, its industry, and what they do. Use 'Not specified' if no information is available.",
    "location": "location or 'Not specified'",
    "jobType": "Full-time/Part-time/Contract/etc or 'Not specified'",
    "summary": "2-3 sentence summary of the role",
    "requiredSkills": ["skill name 1", "skill name 2", "skill name 3"],
    "niceToHaveSkills": ["optional skill 1", "optional skill 2"],
    "responsibilities": [
        {"title": "responsibility title", "importance": 1-100, "description": "brief description"}
    ],
    "requirements": [
        {"title": "requirement title", "type": "must-have/nice-to-have", "priority": 1-100}
    ],
    "benefits": ["benefit 1", "benefit 2"],
    "salaryRange": "salary range or 'Not specified'",
    "hiringProcess": "Brief description of the hiring process/steps if mentioned, or 'Not specified' if not available"
}

Guidelines:
- requiredSkills: Extract all explicitly required/must-have skills as simple strings (e.g., "Java", "Python", "Communication")
- niceToHaveSkills: Extract optional/preferred/nice-to-have skills as simple strings
- companyBackground: Summarize what the company does, their industry, size, or mission if mentioned
- hiringProcess: Include any information about interview rounds, coding tests, timeline, etc.

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
                'companyBackground' => $data['companyBackground'] ?? 'Not specified',
                'location' => $data['location'] ?? 'Not specified',
                'jobType' => $data['jobType'] ?? 'Not specified',
                'summary' => $data['summary'] ?? '',
                'requiredSkills' => $data['requiredSkills'] ?? [],
                'niceToHaveSkills' => $data['niceToHaveSkills'] ?? [],
                'responsibilities' => $data['responsibilities'] ?? [],
                'requirements' => $data['requirements'] ?? [],
                'benefits' => $data['benefits'] ?? [],
                'salaryRange' => $data['salaryRange'] ?? 'Not specified',
                'hiringProcess' => $data['hiringProcess'] ?? 'Not specified',
            ];
        } catch (\JsonException $e) {
            throw new \RuntimeException('Failed to parse AI response: '.$e->getMessage());
        }
    }
}
