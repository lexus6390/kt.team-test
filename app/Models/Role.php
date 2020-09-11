<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * @property $id int
 * @property $role_name string
 * @property $created_at string
 * @property $updated_at string
 * @package App\Models
 */
class Role extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'role';

    /**
     * @var string[]
     */
    protected $fillable = ['role_name'];
}
