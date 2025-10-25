<?php

use App\Models\Assessment;
use App\Models\JobPosting;
use App\Models\User;

test('user can close their own job posting', function () {
    $user = User::factory()->create();
    $user->assignRole('job_seeker');
    $jobPosting = JobPosting::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post(route('job-postings.close', $jobPosting));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Job posting has been closed.');

    $jobPosting->refresh();
    expect($jobPosting->closed_at)->not->toBeNull();
});

test('closing job posting also closes related assessments', function () {
    $user = User::factory()->create();
    $user->assignRole('job_seeker');
    $jobPosting = JobPosting::factory()->create(['user_id' => $user->id]);

    // Create assessments for this job posting
    $assessment1 = Assessment::factory()->create(['job_posting_id' => $jobPosting->id]);
    $assessment2 = Assessment::factory()->create(['job_posting_id' => $jobPosting->id]);

    $this->actingAs($user)->post(route('job-postings.close', $jobPosting));

    $assessment1->refresh();
    $assessment2->refresh();

    expect($assessment1->closed_at)->not->toBeNull();
    expect($assessment2->closed_at)->not->toBeNull();
});

test('user cannot close another users job posting', function () {
    $user = User::factory()->create();
    $user->assignRole('job_seeker');
    $otherUser = User::factory()->create();
    $otherUser->assignRole('job_seeker');
    $jobPosting = JobPosting::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->post(route('job-postings.close', $jobPosting));

    $response->assertForbidden();

    $jobPosting->refresh();
    expect($jobPosting->closed_at)->toBeNull();
});

test('user can reopen their closed job posting', function () {
    $user = User::factory()->create();
    $user->assignRole('job_seeker');
    $jobPosting = JobPosting::factory()->closed()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post(route('job-postings.reopen', $jobPosting));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Job posting has been reopened.');

    $jobPosting->refresh();
    expect($jobPosting->closed_at)->toBeNull();
});

test('reopening job posting does not reopen assessments', function () {
    $user = User::factory()->create();
    $user->assignRole('job_seeker');
    $jobPosting = JobPosting::factory()->closed()->create(['user_id' => $user->id]);

    // Create closed assessments for this job posting
    $assessment = Assessment::factory()->closed()->create(['job_posting_id' => $jobPosting->id]);

    $this->actingAs($user)->post(route('job-postings.reopen', $jobPosting));

    $assessment->refresh();
    $jobPosting->refresh();

    expect($jobPosting->closed_at)->toBeNull();
    expect($assessment->closed_at)->not->toBeNull(); // Assessment should still be closed
});

test('unauthenticated user cannot close job posting', function () {
    $jobPosting = JobPosting::factory()->create();

    $response = $this->post(route('job-postings.close', $jobPosting));

    $response->assertRedirect(route('login'));
});

test('unauthenticated user cannot reopen job posting', function () {
    $jobPosting = JobPosting::factory()->closed()->create();

    $response = $this->post(route('job-postings.reopen', $jobPosting));

    $response->assertRedirect(route('login'));
});
