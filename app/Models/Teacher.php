<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $user_id
 * @property string $full_name
 * @property string $section_advisory
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Teacher extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'teachers';

    protected $fillable = [
        'user_id',
        'full_name',
        'section_advisory',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'workos_id');
    }
}
