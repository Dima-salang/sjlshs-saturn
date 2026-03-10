<?php

use App\Models\QRCode;
use App\Models\Student;
use App\Models\User;
use App\Services\QRService;

// ──────────────────────────────────────────────
// Authentication guard
// ──────────────────────────────────────────────

describe('QR Code authentication', function () {
    it('rejects unauthenticated requests to generate a QR code', function () {
        $this->postJson('/api/qrcodes')->assertUnauthorized();
    });
});

// ──────────────────────────────────────────────
// POST /api/qrcodes
// ──────────────────────────────────────────────

describe('POST /api/qrcodes', function () {
    beforeEach(function () {
        // Prevent actual filesystem writes during HTTP-layer tests.
        // Use make() so no DB insert is attempted before RefreshDatabase runs.
        $this->mock(QRService::class, function ($mock) {
            $mock->shouldReceive('generateQRCode')
                ->andReturn(QRCode::factory()->make(['id' => 1]));
        });
    });

    it('returns 201 with the generated QR code data', function () {
        $user = User::factory()->create(['is_active' => true]);
        $student = Student::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/qrcodes', [
                'lrn' => $student->lrn,
                'last_name' => $student->last_name,
                'section' => 'STEM-A',
            ])
            ->assertCreated()
            ->assertJsonStructure([
                'message',
                'data' => ['path'],
            ])
            ->assertJson(['message' => 'QR Code generated successfully.']);
    });

    it('returns 422 when lrn is missing', function () {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->postJson('/api/qrcodes', [
                'last_name' => 'Dela Cruz',
                'section' => 'STEM-A',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['lrn']);
    });

    it('returns 422 when last_name is missing', function () {
        $user = User::factory()->create(['is_active' => true]);
        $student = Student::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/qrcodes', [
                'lrn' => $student->lrn,
                'section' => 'STEM-A',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['last_name']);
    });

    it('returns 422 when section is missing', function () {
        $user = User::factory()->create(['is_active' => true]);
        $student = Student::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/qrcodes', [
                'lrn' => $student->lrn,
                'last_name' => $student->last_name,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['section']);
    });

    it('returns 422 when the lrn does not belong to any student', function () {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->postJson('/api/qrcodes', [
                'lrn' => '000000000000',
                'last_name' => 'Ghost',
                'section' => 'STEM-A',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['lrn']);
    });
});

// ──────────────────────────────────────────────
// QRService unit tests (writes real files)
// ──────────────────────────────────────────────

describe('QRService', function () {
    it('creates a QRCode database record when generating', function () {
        $student = Student::factory()->create();

        $data = [
            'lrn' => $student->lrn,
            'last_name' => $student->last_name,
            'section' => 'STEM-A',
        ];

        $qrCode = app(QRService::class)->generateQRCode($data);

        expect($qrCode)->toBeInstanceOf(QRCode::class);
        expect($qrCode->id)->not->toBeNull();

        $this->assertDatabaseHas('_q_r_codes', ['id' => $qrCode->id]);

        // Cleanup
        @unlink($qrCode->path);
        @rmdir(dirname($qrCode->path));
    });

    it('saves the QR code image to the correct storage path', function () {
        $student = Student::factory()->create();

        $data = [
            'lrn' => $student->lrn,
            'last_name' => $student->last_name,
            'section' => 'STEM-A',
        ];

        $qrCode = app(QRService::class)->generateQRCode($data);

        expect(file_exists($qrCode->path))->toBeTrue();

        // Cleanup
        @unlink($qrCode->path);
        @rmdir(dirname($qrCode->path));
    });

    it('stores the payload as JSON in the data column', function () {
        $student = Student::factory()->create();

        $data = [
            'lrn' => $student->lrn,
            'last_name' => $student->last_name,
            'section' => 'STEM-B',
        ];

        $qrCode = app(QRService::class)->generateQRCode($data);

        expect($qrCode->data)->toBe(json_encode($data));

        // Cleanup
        @unlink($qrCode->path);
        @rmdir(dirname($qrCode->path));
    });

    it('organizes QR codes into per-section subdirectories', function () {
        $student = Student::factory()->create();
        $section = 'ABM-C';

        $data = [
            'lrn' => $student->lrn,
            'last_name' => $student->last_name,
            'section' => $section,
        ];

        $qrCode = app(QRService::class)->generateQRCode($data);

        expect($qrCode->path)->toContain($section);

        // Cleanup
        @unlink($qrCode->path);
        @rmdir(dirname($qrCode->path));
    });

    it('names the QR code file after the lrn and last name', function () {
        $student = Student::factory()->create();

        $data = [
            'lrn' => $student->lrn,
            'last_name' => $student->last_name,
            'section' => 'STEM-A',
        ];

        $qrCode = app(QRService::class)->generateQRCode($data);

        expect($qrCode->path)->toContain("{$student->lrn} - {$student->last_name}.png");

        // Cleanup
        @unlink($qrCode->path);
        @rmdir(dirname($qrCode->path));
    });
});
