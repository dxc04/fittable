<?php

use App\Models\User;
use App\Services\DocumentTextExtractor;
use App\Services\JobAnalysisService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guests can access the recruiter page', function () {
    $response = $this->get(route('recruiter.index'));
    $response->assertStatus(200);
});

test('authenticated users can access the recruiter page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('recruiter.index'));
    $response->assertStatus(200);
});

test('recruiter page sets registration_role session', function () {
    $this->get(route('recruiter.index'));

    expect(session('registration_role'))->toBe('recruiter');
});

test('user registered from recruiter page gets recruiter role', function () {
    // Set registration_role session to recruiter
    session(['registration_role' => 'recruiter']);

    $response = $this->post(route('register'), [
        'name' => 'Recruiter User',
        'email' => 'recruiter@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'recruiter',
    ]);

    $response->assertRedirect();

    $user = User::where('email', 'recruiter@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasRole('recruiter'))->toBeTrue();
});

test('guest can submit match - redirected to register with pending data', function () {
    $jobDescription = 'We are looking for a Senior Software Engineer with 5+ years of experience in PHP and Laravel. Must have strong problem-solving skills and experience with Vue.js.';
    $candidateResume = 'Experienced Software Engineer with 6 years of PHP and Laravel development. Skilled in Vue.js, TypeScript, and building scalable web applications.';

    $response = $this->post(route('recruiter.match'), [
        'jobDescription' => $jobDescription,
        'candidateResume' => $candidateResume,
    ]);

    $response->assertRedirect(route('register'));
    expect(session('pending_recruiter_match'))->not->toBeNull();
    expect(session('pending_recruiter_match')['jobDescription'])->toBe($jobDescription);
    expect(session('pending_recruiter_match')['candidateResume'])->toBe($candidateResume);
});

test('authenticated user can calculate match with job description and resume text', function () {
    $user = User::factory()->create();
    $user->assignRole('recruiter');
    $this->actingAs($user);

    $jobDescription = 'We are looking for a Senior Software Engineer with 5+ years of experience in PHP and Laravel. Must have strong problem-solving skills and experience with Vue.js.';
    $candidateResume = 'Experienced Software Engineer with 6 years of PHP and Laravel development. Skilled in Vue.js, TypeScript, and building scalable web applications.';

    $this->mock(JobAnalysisService::class, function ($mock) {
        $mock->shouldReceive('analyzeJobPosting')
            ->zeroOrMoreTimes()
            ->andReturn([
                'jobTitle' => 'Senior Software Engineer',
                'company' => 'Tech Company',
                'companyBackground' => 'A leading tech company',
                'location' => 'Remote',
                'jobType' => 'Full-time',
                'summary' => 'Looking for a Senior Software Engineer',
                'requiredSkills' => ['PHP', 'Laravel', 'Vue.js'],
                'niceToHaveSkills' => ['TypeScript'],
                'responsibilities' => ['Build scalable applications'],
                'requirements' => ['5+ years experience'],
                'benefits' => ['Competitive salary'],
                'salaryRange' => '$100k-$150k',
                'hiringProcess' => 'Standard interview process',
                'warnings' => [],
            ]);

        $mock->shouldReceive('assessCandidateForRecruiter')
            ->once()
            ->andReturn([
                'overallMatch' => 85,
                'skillBreakdown' => [
                    'technical' => 90,
                    'soft' => 80,
                    'domain' => 85,
                ],
                'summary' => 'This candidate possesses strong technical skills.',
                'strengths' => ['6 years PHP experience', 'Vue.js skills'],
                'gaps' => ['Limited TypeScript depth'],
                'applicationStrategy' => [
                    'resumeOptimization' => ['The candidate should highlight Laravel projects'],
                    'coverLetterFocus' => ['The candidate should emphasize problem-solving'],
                    'howToAddressGaps' => ['The candidate could mention TypeScript learning'],
                ],
                'interviewPreparation' => [
                    'likelyQuestions' => ['Ask about their Laravel experience'],
                    'topicsToStudy' => ['Assess Laravel best practices knowledge'],
                    'storiesToPrepare' => ['Ask them to describe a complex project'],
                ],
                'personalizedRecommendation' => [
                    'shouldApply' => 'This candidate should be interviewed',
                    'biggestAdvantage' => 'Their extensive Laravel experience',
                    'nextStep' => 'Schedule an initial screening call',
                ],
            ]);
    });

    $response = $this->post(route('recruiter.match'), [
        'jobDescription' => $jobDescription,
        'candidateResume' => $candidateResume,
    ]);

    $response->assertSuccessful();
});

test('authenticated user can calculate match with job description and uploaded resume file', function () {
    $user = User::factory()->create();
    $user->assignRole('recruiter');
    $this->actingAs($user);

    Storage::fake('local');

    $jobDescription = 'We are looking for a Senior Software Engineer with 5+ years of experience in PHP and Laravel.';
    $candidateResumeText = 'Experienced Software Engineer with 6 years of PHP and Laravel development.';
    $file = UploadedFile::fake()->create('resume.txt', 1, 'text/plain');

    $this->mock(DocumentTextExtractor::class, function ($mock) use ($candidateResumeText) {
        $mock->shouldReceive('extractText')
            ->once()
            ->andReturn($candidateResumeText);
    });

    $this->mock(JobAnalysisService::class, function ($mock) {
        $mock->shouldReceive('analyzeJobPosting')
            ->zeroOrMoreTimes()
            ->andReturn([
                'jobTitle' => 'Senior Software Engineer',
                'company' => 'Tech Company',
                'companyBackground' => 'A leading tech company',
                'location' => 'Remote',
                'jobType' => 'Full-time',
                'summary' => 'Looking for a Senior Software Engineer',
                'requiredSkills' => ['PHP', 'Laravel'],
                'niceToHaveSkills' => [],
                'responsibilities' => ['Build scalable applications'],
                'requirements' => ['5+ years experience'],
                'benefits' => ['Competitive salary'],
                'salaryRange' => '$100k-$150k',
                'hiringProcess' => 'Standard interview process',
                'warnings' => [],
            ]);

        $mock->shouldReceive('assessCandidateForRecruiter')
            ->once()
            ->andReturn([
                'overallMatch' => 85,
                'skillBreakdown' => [
                    'technical' => 90,
                    'soft' => 80,
                    'domain' => 85,
                ],
                'summary' => 'This candidate has strong relevant experience.',
                'strengths' => ['6 years PHP experience'],
                'gaps' => ['Limited TypeScript depth'],
            ]);
    });

    $response = $this->post(route('recruiter.match'), [
        'jobDescription' => $jobDescription,
        'resumeFile' => $file,
    ]);

    $response->assertSuccessful();
});

test('validates job description is required', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('recruiter.match'), [
        'candidateResume' => 'Some resume text',
    ]);

    $response->assertSessionHasErrors('jobDescription');
});

test('validates job description minimum length', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('recruiter.match'), [
        'jobDescription' => 'Too short',
        'candidateResume' => 'Some resume text here with enough characters to pass validation rules',
    ]);

    $response->assertSessionHasErrors('jobDescription');
});

test('validates job description maximum length', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('recruiter.match'), [
        'jobDescription' => str_repeat('a', 10001),
        'candidateResume' => 'Some resume text here with enough characters to pass validation rules',
    ]);

    $response->assertSessionHasErrors('jobDescription');
});

test('validates candidate resume or file is required', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('recruiter.match'), [
        'jobDescription' => 'We are looking for a Senior Software Engineer with 5+ years of experience in PHP and Laravel. Must have strong problem-solving skills.',
    ]);

    $response->assertSessionHasErrors(['candidateResume', 'resumeFile']);
});

test('validates candidate resume minimum length', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('recruiter.match'), [
        'jobDescription' => 'We are looking for a Senior Software Engineer with 5+ years of experience in PHP and Laravel. Must have strong problem-solving skills.',
        'candidateResume' => 'Short',
    ]);

    $response->assertSessionHasErrors('candidateResume');
});

test('validates resume file type', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Storage::fake('local');

    $file = UploadedFile::fake()->create('resume.exe', 1);

    $response = $this->post(route('recruiter.match'), [
        'jobDescription' => 'We are looking for a Senior Software Engineer with 5+ years of experience in PHP and Laravel.',
        'resumeFile' => $file,
    ]);

    $response->assertSessionHasErrors('resumeFile');
});

test('validates resume file size', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Storage::fake('local');

    $file = UploadedFile::fake()->create('resume.pdf', 6000); // 6MB, exceeds 5MB limit

    $response = $this->post(route('recruiter.match'), [
        'jobDescription' => 'We are looking for a Senior Software Engineer with 5+ years of experience in PHP and Laravel.',
        'resumeFile' => $file,
    ]);

    $response->assertSessionHasErrors('resumeFile');
});

test('handles AI service errors gracefully', function () {
    $user = User::factory()->create();
    $user->assignRole('recruiter');
    $this->actingAs($user);

    $jobDescription = 'We are looking for a Senior Software Engineer with 5+ years of experience in PHP and Laravel.';
    $candidateResume = 'Experienced Software Engineer with 6 years of PHP and Laravel development.';

    $this->mock(JobAnalysisService::class, function ($mock) {
        $mock->shouldReceive('analyzeJobPosting')
            ->zeroOrMoreTimes()
            ->andReturn([
                'jobTitle' => 'Senior Software Engineer',
                'company' => 'Tech Company',
                'companyBackground' => 'A leading tech company',
                'location' => 'Remote',
                'jobType' => 'Full-time',
                'summary' => 'Looking for a Senior Software Engineer',
                'requiredSkills' => ['PHP', 'Laravel'],
                'niceToHaveSkills' => [],
                'responsibilities' => ['Build scalable applications'],
                'requirements' => ['5+ years experience'],
                'benefits' => ['Competitive salary'],
                'salaryRange' => '$100k-$150k',
                'hiringProcess' => 'Standard interview process',
                'warnings' => [],
            ]);

        $mock->shouldReceive('assessCandidateForRecruiter')
            ->once()
            ->andThrow(new \Exception('AI service error'));
    });

    $response = $this->post(route('recruiter.match'), [
        'jobDescription' => $jobDescription,
        'candidateResume' => $candidateResume,
    ]);

    $response->assertSessionHasErrors('jobDescription');
});

test('processPendingMatch saves evaluation to database after registration', function () {
    $jobDescription = 'We are looking for a Senior Software Engineer with 5+ years of experience in PHP and Laravel. Must have strong problem-solving skills and experience with Vue.js.';
    $candidateResume = 'Experienced Software Engineer with 6 years of PHP and Laravel development. Skilled in Vue.js, TypeScript, and building scalable web applications.';

    $this->mock(JobAnalysisService::class, function ($mock) {
        $mock->shouldReceive('analyzeJobPosting')
            ->once()
            ->andReturn([
                'jobTitle' => 'Senior Software Engineer',
                'company' => 'Tech Company',
                'companyBackground' => 'A leading tech company',
                'location' => 'Remote',
                'jobType' => 'Full-time',
                'summary' => 'Looking for a Senior Software Engineer',
                'requiredSkills' => ['PHP', 'Laravel'],
                'niceToHaveSkills' => ['Vue.js'],
                'responsibilities' => ['Build scalable applications'],
                'requirements' => ['5+ years experience'],
                'benefits' => ['Competitive salary'],
                'salaryRange' => '$100k-$150k',
                'hiringProcess' => 'Standard interview process',
                'warnings' => [],
            ]);

        $mock->shouldReceive('assessCandidateForRecruiter')
            ->once()
            ->andReturn([
                'overallMatch' => 85,
                'summary' => 'Strong candidate with relevant experience',
                'strengths' => ['PHP expertise', 'Laravel knowledge'],
                'gaps' => [],
                'skillBreakdown' => ['PHP' => 90, 'Laravel' => 85],
                'applicationStrategy' => ['Highlight PHP experience'],
                'interviewPreparation' => ['Prepare for technical questions'],
                'personalizedRecommendation' => ['Good fit for the role'],
            ]);
    });

    // Store pending match data in session
    session([
        'pending_recruiter_match' => [
            'jobDescription' => $jobDescription,
            'candidateResume' => $candidateResume,
        ],
    ]);

    // Create and authenticate a recruiter user
    $user = User::factory()->create();
    $user->assignRole('recruiter');
    $this->actingAs($user);

    // Process the pending match
    $response = $this->get(route('recruiter.match.process'));

    // Assert the response is successful
    $response->assertStatus(200);

    // Assert job posting was created
    $this->assertDatabaseHas('job_postings', [
        'user_id' => $user->id,
        'job_title' => 'Senior Software Engineer',
        'company' => 'Tech Company',
    ]);

    // Assert resume was created
    $this->assertDatabaseHas('resumes', [
        'resume_text' => $candidateResume,
    ]);

    // Assert assessment was created
    $this->assertDatabaseHas('assessments', [
        'overall_match' => 85,
    ]);

    // Assert recruiter_assessments pivot entry was created
    $this->assertDatabaseHas('recruiter_assessments', [
        'user_id' => $user->id,
    ]);

    // Assert pending match was cleared from session
    expect(session('pending_recruiter_match'))->toBeNull();
});
