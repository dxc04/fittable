<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;

test('authenticated user can store resume with text', function () {
    $user = User::factory()->create();
    $user->assignRole('job_seeker');

    $resumeText = 'This is my resume text with over 50 characters to meet the validation requirement.';

    $response = $this->actingAs($user)->post(route('resumes.store'), [
        'resumeText' => $resumeText,
    ]);

    $response->assertRedirect(route('resumes.index'));
    $response->assertSessionHas('success', 'Resume saved successfully!');

    $this->assertDatabaseHas('resumes', [
        'user_id' => $user->id,
        'resume_text' => $resumeText,
    ]);
});

test('resume file must be valid format', function () {
    $user = User::factory()->create();
    $user->assignRole('job_seeker');

    $file = UploadedFile::fake()->create('resume.exe', 1, 'application/x-msdownload');

    $response = $this->actingAs($user)->post(route('resumes.store'), [
        'resumeFile' => $file,
    ]);

    $response->assertSessionHasErrors(['resumeFile']);
});

test('resume requires either text or file', function () {
    $user = User::factory()->create();
    $user->assignRole('job_seeker');

    $response = $this->actingAs($user)->post(route('resumes.store'), []);

    $response->assertSessionHasErrors(['resumeText']);
});

test('resume text must be at least 50 characters', function () {
    $user = User::factory()->create();
    $user->assignRole('job_seeker');

    $response = $this->actingAs($user)->post(route('resumes.store'), [
        'resumeText' => 'Too short',
    ]);

    $response->assertSessionHasErrors(['resumeText']);
});

test('duplicate resume is rejected', function () {
    $user = User::factory()->create();
    $user->assignRole('job_seeker');

    $resumeText = 'This is my resume text with over 50 characters to meet the validation requirement.';

    // Create first resume
    $this->actingAs($user)->post(route('resumes.store'), [
        'resumeText' => $resumeText,
    ]);

    // Try to create duplicate
    $response = $this->actingAs($user)->post(route('resumes.store'), [
        'resumeText' => $resumeText,
    ]);

    $response->assertSessionHasErrors(['resumeText']);
});

test('unauthenticated user cannot store resume', function () {
    $response = $this->post(route('resumes.store'), [
        'resumeText' => 'This is my resume text with over 50 characters to meet the validation requirement.',
    ]);

    $response->assertRedirect(route('login'));
});
