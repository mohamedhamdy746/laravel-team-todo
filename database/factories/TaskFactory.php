<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $creatorId = User::query()->inRandomOrder()->value('id') ?? User::factory();
        $assigneeId = User::query()->inRandomOrder()->value('id') ?? $creatorId;

        $subtasksCount = fake()->numberBetween(0, 4);
        $subtasks = [];

        for ($i = 0; $i < $subtasksCount; $i++) {
            $subtasks[] = [
                'id' => $i + 1,
                'title' => fake()->sentence(3),
                'completed' => fake()->boolean(35),
            ];
        }

        return [
            'user_id' => $creatorId,
            'creator_id' => $creatorId,
            'assignee_id' => $assigneeId,
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'completed' => fake()->boolean(35),
            'due_date' => fake()->optional()?->dateTimeBetween('-1 month', '+3 months')?->format('Y-m-d'),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => fake()->randomElement(['to-do', 'in_progress', 'done']),
            'board_column' => fake()->randomElement(['To Do', 'In Progress', 'Review', 'Done']),
            'assigned_to' => User::query()->whereKey($assigneeId)->value('name') ?? fake()->name(),
            'color' => fake()->hexColor(),
            'tags' => fake()->randomElements(['backend', 'frontend', 'api', 'bug', 'urgent', 'research', 'test'], fake()->numberBetween(0, 3)),
            'labels' => fake()->randomElements(['sprint-1', 'sprint-2', 'release', 'hotfix', 'feature'], fake()->numberBetween(0, 2)),
            'subtasks' => $subtasks,
        ];
    }
}
