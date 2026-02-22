<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $section_id
 * @property string $section_name
 * @property string $grade_level
 * @property int|null $adviser_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Section extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'sections';

    protected $primaryKey = 'section_id';

    protected $fillable = [
        'section_name',
        'grade_level',
        'adviser_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Student, $this>
     */
    public function students(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Student::class, 'section_id', 'section_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Teacher, $this>
     */
    public function adviser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'adviser_id', 'id');
    }
}
