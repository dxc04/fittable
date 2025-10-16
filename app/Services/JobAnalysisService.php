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
Analyze this candidate's resume against the job requirements and provide a comprehensive, honest assessment. Return ONLY a valid JSON object with this exact structure:

{
    "overallMatch": 85,
    "skillBreakdown": {
        "technical": 90,
        "soft": 80,
        "domain": 75
    },
    "summary": "You possess strong technical skills in software development and system design. Your experience aligns well with the requirements of this role. You would likely be a strong candidate for this position.",
    "strengths": [
        "13+ years of development experience - you're not a junior developer",
        "Remote work veteran - you know how to work independently and communicate well",
        "Mentoring experience - you can help others, not just code",
        "Product thinking - you've built features users rely on daily",
        "Fast learner - you've picked up new technologies throughout your career"
    ],
    "gaps": [
        "Limited Vue3 production experience - \"I've used Vue2 and I'm ready to dive into Vue3\"",
        "No TypeScript depth - \"I've used it but haven't made it my primary language yet\"",
        "Not frontend-exclusive - \"I'm pivoting from fullstack to frontend-focused work\"",
        "No data visualization experience - \"I haven't built complex charts but I'm interested in learning\""
    ],
    "applicationStrategy": {
        "resumeOptimization": [
            "Highlight your 13+ years of experience prominently in your summary",
            "Create a 'Remote Work Experience' section showcasing your distributed team successes",
            "Add metrics to your mentoring experience (e.g., 'Mentored 8 junior developers over 3 years')",
            "Lead with projects that demonstrate product thinking and user impact"
        ],
        "coverLetterFocus": [
            "Open with your strongest match: extensive development experience and proven remote work success",
            "Address the Vue3/TypeScript gaps head-on with your Vue2 background and learning track record",
            "Close with genuine enthusiasm for the product and specific reasons you're excited about this role"
        ],
        "howToAddressGaps": [
            "For Vue3: Mention you're already building a side project with Vue3 to get hands-on experience",
            "For TypeScript: Highlight that you've used it and are committed to deepening your knowledge",
            "For frontend focus: Frame your fullstack background as an advantage for understanding system architecture"
        ]
    },
    "interviewPreparation": {
        "likelyQuestions": [
            "Tell me about your experience working in remote teams and how you stay productive",
            "Can you walk me through a complex feature you built and the impact it had?",
            "How do you approach learning new technologies quickly?",
            "Describe a time when you mentored a junior developer through a challenging problem"
        ],
        "topicsToStudy": [
            "Vue 3 Composition API and its differences from Vue 2 Options API",
            "TypeScript advanced patterns (generics, utility types, type inference)",
            "Modern frontend testing strategies (unit, integration, e2e)"
        ],
        "storiesToPrepare": [
            "Situation: Legacy system migration. Task: Lead the refactor. Action: Your approach. Result: Quantifiable impact",
            "Situation: Remote team coordination challenge. Task: Keep project on track. Action: Your solution. Result: Team success",
            "Situation: Learning a new technology under pressure. Task: Deliver quickly. Action: Your process. Result: Successful delivery"
        ]
    },
    "personalizedRecommendation": {
        "shouldApply": "Apply now - you're a strong candidate with the core skills they need, and your learning track record will cover any gaps quickly.",
        "biggestAdvantage": "Your combination of deep technical experience and proven remote work success makes you a low-risk hire who can contribute from day one.",
        "nextStep": "Today: Start a small Vue3 project using TypeScript to demonstrate your commitment to closing the skill gaps, then apply with confidence."
    }
}

Guidelines for assessment:
- overallMatch: Overall percentage match (0-100) based on how well the candidate's qualifications align with job requirements
- skillBreakdown.technical: Score for technical/hard skills (programming, tools, technologies) - 0-100
- skillBreakdown.soft: Score for soft skills (communication, teamwork, problem-solving) - 0-100
- skillBreakdown.domain: Score for domain knowledge and industry-specific expertise - 0-100
- summary: 2-3 sentences in second person ("You...") explaining the candidate's overall fit and potential

STRENGTHS FORMAT (3-6 items):
Write each strength as: "[Specific achievement/experience] - [why it matters for this job]"
Examples:
- "15 years of backend development - you're a senior engineer, not a junior"
- "Led teams of 5+ developers - you can mentor and guide others"
- "Built systems handling millions of users - you understand scale"
- "Strong communication skills - you've worked with remote teams successfully"
Focus on making the candidate feel confident and understood. Highlight years of experience, proven abilities, and transferable skills.

GAPS FORMAT (2-4 items):
Write each gap as: "[What's missing] - \"[Suggested talking point in first person]\""
Examples:
- "Limited React experience - \"I've used Vue extensively and I'm ready to learn React\""
- "No AWS certification - \"I have hands-on cloud experience but haven't pursued certification yet\""
- "Junior in leadership - \"I've mentored individuals but haven't managed a full team yet\""
Be honest about gaps but provide a confident, reasonable way to address them in interviews.

APPLICATION STRATEGY:
- resumeOptimization: 3-4 specific, actionable tips on what to emphasize or reword on their resume
- coverLetterFocus: 2-3 key points they should highlight in their cover letter
- howToAddressGaps: 2-3 specific strategies to explain or compensate for missing requirements

INTERVIEW PREPARATION:
- likelyQuestions: 3-4 interview questions they should prepare for based on their background and the job requirements
- topicsToStudy: 2-3 technical or domain topics they should review before the interview
- storiesToPrepare: 2-3 STAR method (Situation, Task, Action, Result) examples they should have ready

PERSONALIZED RECOMMENDATION:
- shouldApply: Clear, honest advice on whether they should apply now or wait and develop skills first (1-2 sentences)
- biggestAdvantage: What makes them uniquely valuable for this role (1 sentence)
- nextStep: One specific, actionable step they should take today (1 sentence)

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
                'applicationStrategy' => [
                    'resumeOptimization' => $data['applicationStrategy']['resumeOptimization'] ?? [],
                    'coverLetterFocus' => $data['applicationStrategy']['coverLetterFocus'] ?? [],
                    'howToAddressGaps' => $data['applicationStrategy']['howToAddressGaps'] ?? [],
                ],
                'interviewPreparation' => [
                    'likelyQuestions' => $data['interviewPreparation']['likelyQuestions'] ?? [],
                    'topicsToStudy' => $data['interviewPreparation']['topicsToStudy'] ?? [],
                    'storiesToPrepare' => $data['interviewPreparation']['storiesToPrepare'] ?? [],
                ],
                'personalizedRecommendation' => [
                    'shouldApply' => $data['personalizedRecommendation']['shouldApply'] ?? '',
                    'biggestAdvantage' => $data['personalizedRecommendation']['biggestAdvantage'] ?? '',
                    'nextStep' => $data['personalizedRecommendation']['nextStep'] ?? '',
                ],
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
