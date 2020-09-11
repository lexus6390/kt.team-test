<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Status
 * @property $id int
 * @property $status_name string
 * @property $created_at string
 * @property $updated_at string
 * @package App\Models
 */
class Status extends Model
{
    use HasFactory;

    const STATUS_NEW_TASK = 1;

    /**
     * @var string
     */
    protected $table = 'status';

    /**
     * @var string[]
     */
    protected $fillable = ['status_name'];
}
