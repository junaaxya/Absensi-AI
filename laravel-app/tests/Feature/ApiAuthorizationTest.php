<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('Staf', 'web');
    Role::findOrCreate('Direktur', 'web');
});

test('guest cannot update admin settings api', function () {
    $response = $this->putJson('/api/admin/settings', [
        'latitude' => -6.2,
        'longitude' => 106.8,
        'radius' => 100,
    ]);

    $response->assertUnauthorized();
});

test('user without permission cannot update admin settings api', function () {
    $user = User::factory()->create();
    $user->assignRole('Staf');

    $response = $this->actingAs($user)->putJson('/api/admin/settings', [
        'latitude' => -6.2,
        'longitude' => 106.8,
        'radius' => 100,
    ]);

    $response->assertForbidden();
});

test('guest cannot register face via api', function () {
    $response = $this->postJson('/api/face/register', [
        'user_id' => 1,
        'photos' => [],
    ]);

    $response->assertUnauthorized();
});

test('guest cannot submit attendance via api', function () {
    $response = $this->postJson('/api/attendance/auto', [
        'photo' => UploadedFile::fake()->create('selfie.jpg', 100, 'image/jpeg'),
        'type' => 'masuk',
        'latitude' => -6.2,
        'longitude' => 106.8,
    ]);

    $response->assertUnauthorized();
});

test('attendance api is rate limited', function () {
    $user = User::factory()->create();
    $user->assignRole('Staf');

    for ($i = 0; $i < 7; $i++) {
        $response = $this->actingAs($user)->postJson('/api/attendance/auto', [
            'photo' => UploadedFile::fake()->create('selfie.jpg', 100, 'image/jpeg'),
            'type' => 'masuk',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ]);
    }

    $response->assertStatus(429);
});
