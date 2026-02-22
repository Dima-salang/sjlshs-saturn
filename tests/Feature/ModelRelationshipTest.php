<?php

use App\Models\Attendance;
use App\Models\Section;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('tests teacher relationships', function () {
    $user = User::factory()->create();
    $teacher = Teacher::factory()->create(['user_id' => $user->workos_id]);
    $section = Section::factory()->create(['adviser_id' => $teacher->id]);
    $teacher->update(['section_advisory' => $section->section_name]);

    expect($teacher->user->workos_id)->toBe($user->workos_id);
    expect($section->adviser->id)->toBe($teacher->id);
});


it('tests section relationships', function () {
    $teacher = Teacher::factory()->create();
    $section = Section::factory()->create(['adviser_id' => $teacher->id]);
    Student::factory()->count(3)->create(['section_id' => $section->section_id]);

    expect($section->adviser->id)->toBe($teacher->id);
    expect($section->students)->toHaveCount(3);
    expect($section->students->first()->section_id)->toBe($section->section_id);
});

it('tests student relationships', function () {
    $section = Section::factory()->create();
    $teacher = Teacher::factory()->create();
    $student = Student::factory()->create([
        'section_id' => $section->section_id,
        'adviser_id' => $teacher->id,
    ]);

    expect($student->section->section_id)->toBe($section->section_id);
    expect($student->adviser->id)->toBe($teacher->id);
});

it('tests attendance relationships', function () {
    $student = Student::factory()->create();
    $section = Section::factory()->create();
    $attendance = Attendance::factory()->create([
        'lrn' => $student->lrn,
        'section_id' => $section->section_id,
    ]);

    expect($attendance->student->lrn)->toBe($student->lrn);
    expect($attendance->section->section_id)->toBe($section->section_id);
});
