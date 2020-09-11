<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

/**
 * Class StatusSeeder
 * @package Database\Seeders
 */
class StatusSeeder extends Seeder
{
    /**
     * @var string[]
     */
    private $statuses = [
        'Draft',
        'Open',
        'In progress',
        'Complete',
        'Reopen',
        'Close'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->statuses as $status) {
            $statusModel = new Status();
            $statusModel->status_name = $status;
            $statusModel->save();
        }
    }
}
