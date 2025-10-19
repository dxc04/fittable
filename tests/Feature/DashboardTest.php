<?php

use App\Models\User;

test('guests can access the job analysis page', function () {
    $response = $this->get(route('job.index'));
    $response->assertStatus(200);
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
