<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('admins can access active-teacher protected routes', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
        'is_active' => true,
    ]);

    Sanctum::actingAs($admin);

    $this->getJson('/api/teachers')->assertSuccessful();
    $this->getJson('/api/sections')->assertSuccessful();
    $this->getJson('/api/students')->assertSuccessful();
});

test('non-admin active teachers can access routes', function () {
    $teacher = User::factory()->create([
        'is_admin' => false,
        'is_active' => true,
    ]);

    Sanctum::actingAs($teacher);

    $this->getJson('/api/teachers')->assertSuccessful();
});

test('inactive users are forbidden from accessing active routes', function () {
    $user = User::factory()->create([
        'is_admin' => false,
        'is_active' => false,
    ]);

    Sanctum::actingAs($user);

    $this->getJson('/api/teachers')->assertForbidden();
});

test('unauthenticated users are unauthorized', function () {
    $this->getJson('/api/teachers')->assertStatus(401);
});

test('inactive admins are still allowed access', function () {
    // According to EnsureTeacherIsActive.php: "Admins always pass through."
    $admin = User::factory()->create([
        'is_admin' => true,
        'is_active' => false,
    ]);

    Sanctum::actingAs($admin);

    $this->getJson('/api/teachers')->assertSuccessful();
});
