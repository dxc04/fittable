<?php

use App\Models\User;

test('guests are redirected to the login page from dashboard', function () {
    $response = $this->get(route('job.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the job analysis page (dashboard replacement)', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('job.index'));
    $response->assertStatus(200);
});

test('dashboard route redirects to job analysis page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/dashboard');
    $response->assertRedirect(route('job.index'));
});
