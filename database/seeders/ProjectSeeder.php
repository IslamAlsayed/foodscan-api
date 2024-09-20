<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::create([
            'name' => 'name1',
            'featured' => 'yes',
            'developer_id' => 2,
            'type' => json_encode(['residential']),
            'area_id' => 1,
        ]);
        Project::create([
            'name' => 'name2',
            'featured' => 'yes',
            'developer_id' => 3,
            'type' => json_encode(['residential']),
            'area_id' => 2,
        ]);
        Project::create([
            'name' => 'name3',
            'featured' => 'yes',
            'developer_id' => 5,
            'type' => json_encode(['residential']),
            'area_id' => 3,
        ]);
        Project::create([
            'name' => 'name4',
            'featured' => 'yes',
            'developer_id' => 3,
            'type' => json_encode(['residential']),
            'area_id' => 4,
        ]);
        Project::create([
            'name' => 'name5',
            'featured' => 'yes',
            'developer_id' => 4,
            'type' => json_encode(['residential']),
            'area_id' => 5,
        ]);
        Project::create([
            'name' => 'name6',
            'featured' => 'yes',
            'developer_id' => 6,
            'type' => json_encode(['residential']),
            'area_id' => 6,
        ]);
        Project::create([
            'name' => 'name7',
            'featured' => 'yes',
            'developer_id' => 2,
            'type' => json_encode(['residential']),
            'area_id' => 7,
        ]);

        Unit::create([
            'crm_unit_code_id' => 'PRO7126',
            'type' => 'Resale',
            'featured' => 'no',
            'area_id' => 1,
            'project_id' => 1,
        ]);
        Unit::create([
            'crm_unit_code_id' => 'PRO7126',
            'type' => 'Resale',
            'featured' => 'yes',
            'area_id' => 1,
            'project_id' => 2,
        ]);
        Unit::create([
            'crm_unit_code_id' => 'PRO7126',
            'type' => 'Resale',
            'featured' => 'no',
            'area_id' => 1,
            'project_id' => 3,
        ]);
        Unit::create([
            'crm_unit_code_id' => 'PRO7126',
            'type' => 'Resale',
            'featured' => 'yes',
            'area_id' => 1,
            'project_id' => 4,
        ]);
        Unit::create([
            'crm_unit_code_id' => 'PRO7126',
            'type' => 'Resale',
            'featured' => 'no',
            'area_id' => 1,
            'project_id' => 5,
        ]);
        Unit::create([
            'crm_unit_code_id' => 'PRO7126',
            'type' => 'Resale',
            'featured' => 'yes',
            'area_id' => 1,
            'project_id' => 6,
        ]);
        Unit::create([
            'crm_unit_code_id' => 'PRO7126',
            'type' => 'Resale',
            'featured' => 'no',
            'area_id' => 1,
            'project_id' => 7,
        ]);
    }
}