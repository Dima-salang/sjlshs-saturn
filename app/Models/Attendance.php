<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $lrn
 * @property string $first_name
 * @property string $last_name
 * @property string|null $middle_name
 * @property string $grade_level
 * @property int $section_id
 * @property bool $is_absent
 * @property bool $is_late
 * @property \Illuminate\Support\Carbon|null $scan_timestamp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Attendance extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'lrn',
        'first_name',
        'last_name',
        'middle_name',
        'grade_level',
        'section_id',
        'is_absent',
        'is_late',
        'scan_timestamp',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_absent' => 'boolean',
            'is_late' => 'boolean',
            'scan_timestamp' => 'datetime',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Student, $this>
     */
    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class, 'lrn', 'lrn');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Section, $this>
     */
    public function section(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }
}
