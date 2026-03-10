<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attendance\StoreAttendanceRequest;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttendanceController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Display a listing of attendance records.
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $dateRange = $request->input('date_range', [
            now()->startOfDay()->toDateTimeString(),
            now()->endOfDay()->toDateTimeString(),
        ]);
        $section = $request->input('section_id');

        $records = $this->attendanceService->getAttendanceRecords((array) $dateRange, $section);

        if (is_string($records)) {
            return response()->json(['message' => $records], 400);
        }

        return AttendanceResource::collection($records);
    }

    /**
     * Store a newly created attendance record.
     */
    public function store(StoreAttendanceRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $isLate = $validated['is_late'] ?? false;
        $result = $this->attendanceService->addAttendanceRecord($validated, (bool) $isLate);

        if ($result === 'Attendance added successfully') {
            return response()->json(['message' => $result], 201);
        }

        return response()->json(['message' => $result], 400);
    }

    /**
     * Display the specified attendance record.
     */
    public function show(int $id): AttendanceResource|JsonResponse
    {
        try {
            $attendance = Attendance::findOrFail($id);

            return new AttendanceResource($attendance);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Attendance not found'], 404);
        }
    }

    /**
     * Update the specified attendance record.
     */
    public function update(UpdateAttendanceRequest $request, int $id): AttendanceResource|JsonResponse
    {
        $result = $this->attendanceService->updateAttendanceRecord($id, $request->validated());

        if ($result instanceof Attendance) {
            return new AttendanceResource($result);
        }

        return response()->json(['message' => $result], 400);
    }

    /**
     * Remove the specified attendance record from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->attendanceService->deleteAttendanceRecord($id);

        if ($result === 'Attendance deleted successfully') {
            return response()->json(['message' => $result]);
        }

        return response()->json(['message' => $result], 404);
    }
}
