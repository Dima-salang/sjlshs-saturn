<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    //

    /**
     * Display a listing of sections.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Section>
     */
    public function index(): \Illuminate\Database\Eloquent\Collection
    {
        return Section::all();
    }

    /**
     * Display the specified section.
     */
    public function show(int $id): Section
    {
        return Section::findOrFail($id);
    }

    /**
     * Update the specified section.
     */
    public function update(Request $request, int $id): Section
    {
        $validated = $request->validate([
            'section_name' => 'required|string|max:255',
            'grade_level' => 'required|string|max:255',
            'adviser_id' => 'required|exists:teachers,id',
        ]);

        $section = Section::findOrFail($id);
        $section->update($validated);

        return $section;
    }

    /**
     * Remove the specified section.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $section = Section::findOrFail($id);
        $section->delete();

        return response()->json([
            'message' => 'Section deleted successfully',
        ], 200);
    }
}
