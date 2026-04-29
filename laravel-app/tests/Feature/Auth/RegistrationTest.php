<?php

use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('Staf', 'web');
});

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    // Registration auto-generates username and assigns Staf role
    // Staf role redirects to user dashboard
    $response->assertRedirect(route('dashboard', absolute: false));
});
