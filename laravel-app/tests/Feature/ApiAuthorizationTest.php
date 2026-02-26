<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;

test('guest cannot update admin settings api', function () {
    $response = $this->putJson('/api/admin/settings', [
        'latitude' => -6.2,
        'longitude' => 106.8,
        'radius' => 100,
    ]);

    $response->assertUnauthorized();
});

test('non admin cannot update admin settings api', function () {
    $user = User::factory()->create([
        'role' => 'karyawan',
    ]);

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

test('non admin cannot register face for another user', function () {
    $actor = User::factory()->create([
        'role' => 'karyawan',
    ]);

    $target = User::factory()->create([
        'role' => 'karyawan',
    ]);

    $response = $this->actingAs($actor)->post('/api/face/register', [
        'user_id' => $target->id,
        'photos' => [UploadedFile::fake()->image('face.jpg')],
    ]);

    $response->assertForbidden();
});
