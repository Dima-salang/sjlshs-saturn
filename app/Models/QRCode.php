<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * QRCode model
 *
 * @property string $data
 * @property string $path
 */
class QRCode extends Model
{
    use HasFactory;

    protected $table = 'q_r_codes';

    protected $fillable = [
        'data',
        'path',
    ];
}
