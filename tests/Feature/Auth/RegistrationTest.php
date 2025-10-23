<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('job.analyze', absolute: false));
});

test('new users get job_seeker role by default', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'jobseeker@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'jobseeker@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasRole('job_seeker'))->toBeTrue();
});
