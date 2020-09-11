<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class TaskFactory
 * @package Database\Factories
 */
class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'     => rand(1,10),
            'title'       => $this->faker->name,
            'description' => $this->faker->text,
            'estimate'    => rand(30,120),
            'spent'       => rand(30,120),
            'status_id'   => rand(1,6)
        ];
    }
}
