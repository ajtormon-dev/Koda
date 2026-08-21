<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $json = File::get(base_path('test_data.json'));
        $records = json_decode($json, true);

        foreach ($records as $record) {
            Project::updateOrCreate(
                ['id' => $record['id']],
                [
                    'client_name' => $record['clientName'],
                    'project_name' => $record['projectName'],
                    'description' => $record['description'],
                    'status' => $record['status'],
                    'priority' => $record['priority'],
                    'start_date' => $record['startDate'],
                    'due_date' => $record['dueDate'],
                ],
            );
        }
    }
}
