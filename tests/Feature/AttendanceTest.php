<?php

use App\Models\Attendance;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;

// ──────────────────────────────────────────────
// Helpers
// ──────────────────────────────────────────────

function actingAsTeacher(): User
{
    $user = User::factory()->create(['is_active' => true, 'is_admin' => false]);

    return $user;
}

function actingAsAdmin(): User
{
    return User::factory()->create(['is_active' => true, 'is_admin' => true]);
}

// ──────────────────────────────────────────────
// Authentication guard
// ──────────────────────────────────────────────

describe('Attendance authentication', function () {
    it('rejects unauthenticated requests to index', function () {
        $this->getJson('/api/attendance')->assertUnauthorized();
    });

    it('rejects unauthenticated requests to store', function () {
        $this->postJson('/api/attendance', [])->assertUnauthorized();
    });

    it('rejects unauthenticated requests to show', function () {
        $this->getJson('/api/attendance/1')->assertUnauthorized();
    });

    it('rejects unauthenticated requests to update', function () {
        $this->putJson('/api/attendance/1', [])->assertUnauthorized();
    });

    it('rejects unauthenticated requests to destroy', function () {
        $this->deleteJson('/api/attendance/1')->assertUnauthorized();
    });
});

// ──────────────────────────────────────────────
// Store (POST /api/attendance)
// ──────────────────────────────────────────────

describe('POST /api/attendance', function () {
    it('records attendance for a valid student lrn', function () {
        $user = actingAsTeacher();
        $student = Student::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/attendance', ['lrn' => $student->lrn])
            ->assertCreated()
            ->assertJson(['message' => 'Attendance added successfully']);

        $this->assertDatabaseHas('attendance', [
            'lrn' => $student->lrn,
            'is_absent' => false,
            'is_late' => false,
        ]);
    });

    it('records attendance as late when is_late is true', function () {
        $user = actingAsTeacher();
        $student = Student::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/attendance', ['lrn' => $student->lrn, 'is_late' => true])
            ->assertCreated();

        $this->assertDatabaseHas('attendance', [
            'lrn' => $student->lrn,
            'is_late' => true,
        ]);
    });

    it('copies student name and section from the student record', function () {
        $user = actingAsTeacher();
        $student = Student::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/attendance', ['lrn' => $student->lrn])
            ->assertCreated();

        $this->assertDatabaseHas('attendance', [
            'lrn' => $student->lrn,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'section_id' => $student->section_id,
            'grade_level' => $student->grade_level,
        ]);
    });

    it('prevents duplicate attendance for the same student on the same day', function () {
        $user = actingAsTeacher();
        $student = Student::factory()->create();

        // First scan — should succeed
        $this->actingAs($user)
            ->postJson('/api/attendance', ['lrn' => $student->lrn])
            ->assertCreated();

        // Second scan on the same day — should be rejected
        $this->actingAs($user)
            ->postJson('/api/attendance', ['lrn' => $student->lrn])
            ->assertBadRequest()
            ->assertJson(['message' => 'Student is already present today']);

        $this->assertDatabaseCount('attendance', 1);
    });

    it('returns 400 when student lrn does not exist', function () {
        $user = actingAsTeacher();

        $this->actingAs($user)
            ->postJson('/api/attendance', ['lrn' => '000000000000'])
            ->assertUnprocessable();
    });

    it('returns 422 when lrn is missing', function () {
        $user = actingAsTeacher();

        $this->actingAs($user)
            ->postJson('/api/attendance', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['lrn']);
    });
});

// ──────────────────────────────────────────────
// Index (GET /api/attendance)
// ──────────────────────────────────────────────

describe('GET /api/attendance', function () {
    it('returns todays attendance records for an admin', function () {
        $admin = actingAsAdmin();
        Attendance::factory()->count(3)->create();

        $response = $this->actingAs($admin)
            ->getJson('/api/attendance')
            ->assertSuccessful();

        expect($response->json('data'))->toHaveCount(3);
    });

    it('returns only the sections attendance records for a teacher', function () {
        $user = actingAsTeacher();
        $teacher = $user->teacher;
        $section = Section::factory()->create(['adviser_id' => $teacher->id]);
        $teacher->update(['section_id' => $section->section_id]);

        // Attendance for the teacher's section
        Attendance::factory()->count(2)->create(['section_id' => $section->section_id]);
        // Attendance for a different section — should NOT appear
        Attendance::factory()->count(5)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/attendance')
            ->assertSuccessful();

        expect($response->json('data'))->toHaveCount(2);
    });

    it('filters attendance by section_id when admin passes section filter', function () {
        $admin = actingAsAdmin();
        $section = Section::factory()->create();

        Attendance::factory()->count(2)->create(['section_id' => $section->section_id]);
        Attendance::factory()->count(4)->create(); // other sections

        $response = $this->actingAs($admin)
            ->getJson("/api/attendance?section_id={$section->section_id}")
            ->assertSuccessful();

        expect($response->json('data'))->toHaveCount(2);
    });

    it('returns an empty collection for a teacher without a section assigned', function () {
        $user = actingAsTeacher();
        // teacher has no section_id by default from factory
        Attendance::factory()->count(3)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/attendance')
            ->assertSuccessful();

        expect($response->json('data'))->toBeEmpty();
    });
});

// ──────────────────────────────────────────────
// Show (GET /api/attendance/{id})
// ──────────────────────────────────────────────

describe('GET /api/attendance/{id}', function () {
    it('returns a single attendance record', function () {
        $user = actingAsAdmin();
        $attendance = Attendance::factory()->create();

        $this->actingAs($user)
            ->getJson("/api/attendance/{$attendance->id}")
            ->assertSuccessful()
            ->assertJsonPath('data.id', $attendance->id);
    });

    it('returns 404 for a non-existent attendance record', function () {
        $user = actingAsAdmin();

        $this->actingAs($user)
            ->getJson('/api/attendance/99999')
            ->assertNotFound();
    });
});

// ──────────────────────────────────────────────
// Update (PUT /api/attendance/{id})
// ──────────────────────────────────────────────

describe('PUT /api/attendance/{id}', function () {
    it('updates the is_late flag on an attendance record', function () {
        $user = actingAsAdmin();
        $attendance = Attendance::factory()->create(['is_late' => false]);

        $this->actingAs($user)
            ->putJson("/api/attendance/{$attendance->id}", ['is_late' => true])
            ->assertSuccessful()
            ->assertJsonPath('data.is_late', true);

        $this->assertDatabaseHas('attendance', ['id' => $attendance->id, 'is_late' => true]);
    });

    it('updates the is_absent flag on an attendance record', function () {
        $user = actingAsAdmin();
        $attendance = Attendance::factory()->create(['is_absent' => false]);

        $this->actingAs($user)
            ->putJson("/api/attendance/{$attendance->id}", ['is_absent' => true])
            ->assertSuccessful()
            ->assertJsonPath('data.is_absent', true);
    });

    it('returns 422 when grade_level exceeds 2 characters', function () {
        $user = actingAsAdmin();
        $attendance = Attendance::factory()->create();

        $this->actingAs($user)
            ->putJson("/api/attendance/{$attendance->id}", ['grade_level' => 'Grade 11'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['grade_level']);
    });

    it('returns 422 when section_id does not exist', function () {
        $user = actingAsAdmin();
        $attendance = Attendance::factory()->create();

        $this->actingAs($user)
            ->putJson("/api/attendance/{$attendance->id}", ['section_id' => 99999])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['section_id']);
    });
});

// ──────────────────────────────────────────────
// Destroy (DELETE /api/attendance/{id})
// ──────────────────────────────────────────────

describe('DELETE /api/attendance/{id}', function () {
    it('deletes an attendance record', function () {
        $user = actingAsAdmin();
        $attendance = Attendance::factory()->create();

        $this->actingAs($user)
            ->deleteJson("/api/attendance/{$attendance->id}")
            ->assertSuccessful()
            ->assertJson(['message' => 'Attendance deleted successfully']);

        $this->assertDatabaseMissing('attendance', ['id' => $attendance->id]);
    });

    it('returns 404 when deleting a non-existent record', function () {
        $user = actingAsAdmin();

        $this->actingAs($user)
            ->deleteJson('/api/attendance/99999')
            ->assertNotFound();
    });
});
