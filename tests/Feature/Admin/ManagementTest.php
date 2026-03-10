<?php

use App\Models\Section;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
    Sanctum::actingAs($this->admin);
});

test('admin can fetch all teachers', function () {
    // Creating users automatically creates teachers via UserObserver
    User::factory()->count(3)->create();

    $this->getJson('/api/teachers')
        ->assertSuccessful()
        ->assertJsonStructure(['data']);
});

test('admin can update teacher and its user status', function () {
    $user = User::factory()->create(['is_active' => false]);
    $teacher = $user->teacher;

    $this->putJson("/api/teachers/{$teacher->id}", [
        'full_name' => 'Updated Name',
        'is_active' => true,
        'is_admin' => false,
        'is_nonFaculty' => false,
    ])->assertSuccessful();

    expect($teacher->refresh()->full_name)->toBe('Updated Name');
    expect($user->refresh()->is_active)->toBeTrue();
    expect($user->name)->toBe('Updated Name');
});

test('admin can delete a teacher and associated user', function () {
    $user = User::factory()->create();
    $teacher = $user->teacher;

    $this->deleteJson("/api/teachers/{$teacher->id}")->assertSuccessful();

    $this->assertDatabaseMissing('teachers', ['id' => $teacher->id]);
    $this->assertDatabaseMissing('users', ['workos_id' => $user->workos_id]);
});

test('admin can manage students', function () {
    $section = Section::factory()->create();

    // Create student
    $studentData = [
        'lrn' => '123456789012',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'gender' => 'Male',
        'grade_level' => '11',
        'section_id' => $section->section_id,
    ];

    $this->postJson('/api/students', $studentData)->assertCreated();

    // Update student
    $this->putJson('/api/students/123456789012', array_merge($studentData, ['first_name' => 'Jane']))
        ->assertSuccessful();

    $this->assertDatabaseHas('students', ['lrn' => '123456789012', 'first_name' => 'Jane']);

    // Delete student
    $this->deleteJson('/api/students/123456789012')->assertSuccessful();
    $this->assertDatabaseMissing('students', ['lrn' => '123456789012']);
});

test('admin can bulk store students', function () {
    $section = Section::factory()->create();
    $data = [
        'students' => [
            [
                'lrn' => '000000000001',
                'first_name' => 'S1',
                'last_name' => 'L1',
                'gender' => 'Male',
                'grade_level' => '12',
                'section_id' => $section->section_id,
            ],
            [
                'lrn' => '000000000002',
                'first_name' => 'S2',
                'last_name' => 'L2',
                'gender' => 'Female',
                'grade_level' => '12',
                'section_id' => $section->section_id,
            ],
        ],
    ];

    $this->postJson('/api/students/bulk', $data)->assertStatus(201);
    $this->assertDatabaseHas('students', ['lrn' => '000000000001']);
    $this->assertDatabaseHas('students', ['lrn' => '000000000002']);
});

test('admin can manage sections', function () {
    // Creating user automatically creates teacher via UserObserver
    $user = User::factory()->create();
    $teacher = $user->teacher;

    // Create section
    $sectionData = [
        'section_name' => 'STEM A',
        'grade_level' => '12',
        'adviser_id' => $teacher->id,
    ];

    $response = $this->postJson('/api/sections', $sectionData)->assertCreated();
    $sectionId = $response->json('section_id');

    // Update section
    $this->putJson("/api/sections/{$sectionId}", array_merge($sectionData, ['section_name' => 'STEM B']))
        ->assertSuccessful();

    $this->assertDatabaseHas('sections', ['section_id' => $sectionId, 'section_name' => 'STEM B']);

    // Delete section
    $this->deleteJson("/api/sections/{$sectionId}")->assertSuccessful();
    $this->assertDatabaseMissing('sections', ['section_id' => $sectionId]);
});
