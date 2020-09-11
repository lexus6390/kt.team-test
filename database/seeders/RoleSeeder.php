<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Class RoleSeeder
 * @package Database\Seeders
 */
class RoleSeeder extends Seeder
{
    /**
     * @var string[]
     */
    private $roles = [
        'Admin',
        'Developer',
        'DevOps',
        'Tester',
        'HR'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->roles as $role) {
            $roleModel = new Role();
            $roleModel->role_name = $role;
            $roleModel->save();
        }
    }
}
