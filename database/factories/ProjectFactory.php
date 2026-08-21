<?php

namespace Database\Factories;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d');
        $due = date('Y-m-d', strtotime($start . ' +30 days'));

        return [
            'client_name' => $this->faker->company(),
            'project_name' => $this->faker->bs(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(ProjectStatus::cases()),
            'priority' => $this->faker->randomElement(ProjectPriority::cases()),
            'start_date' => $start,
            'due_date' => $due,
        ];
    }
}
