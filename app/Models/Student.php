<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $lrn
 * @property string $first_name
 * @property string $last_name
 * @property string|null $middle_name
 * @property int $section_id
 * @property string $gender
 * @property string $grade_level
 * @property int|null $adviser_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Student extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'students';

    protected $primaryKey = 'lrn';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'lrn',
        'first_name',
        'last_name',
        'middle_name',
        'section_id',
        'gender',
        'grade_level',
        'adviser_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Section, $this>
     */
    public function section(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Teacher, $this>
     */
    public function adviser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'adviser_id', 'id');
    }
}
