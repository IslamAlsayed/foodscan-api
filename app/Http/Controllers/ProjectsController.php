<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectsController extends Controller
{
    public function test4()
    {
        // $name = $request->query('name');
        // $crmUnitCodeId = $request->query('crm_unit_code_id');
        // $areaId = $request->query('area_id');
        // $projectId = $request->query('project_id');

        $projects = DB::table('projects')
            ->leftJoin('units', 'projects.id', '=', 'units.project_id')
            ->select('projects.*', 'units.*')
            ->get()
            ->groupBy('id');

        // dd($projects);

        // $projects = Project::Where('name', $name)->WhereHas('units', function ($query) use ($crmUnitCodeId, $areaId, $projectId) {
        //     $query->Where('crm_unit_code_id', $crmUnitCodeId)
        //         ->orWhere('area_id', $areaId)
        //         ->orWhere('project_id', $projectId);
        // })
        //     ->with('units')
        //     ->get();

        dd($projects);
    }

    // running this
    public function test3()
    {
        $projectColumns = DB::getSchemaBuilder()->getColumnListing('projects');
        $unitColumns = DB::getSchemaBuilder()->getColumnListing('units');

        $unitColumns = array_map(function ($column) {
            return 'units.' . $column . ' as unit_' . $column;
        }, $unitColumns);

        $selectColumns = array_merge(
            array_map(function ($column) {
                return 'projects.' . $column;
            }, $projectColumns),
            $unitColumns
        );

        // $selectColumns = array_merge(
        //     array_map(fn($column) => 'projects.' . $column, $projectColumns),
        //     $unitColumns
        // );

        $projects = DB::table('projects')
            ->leftJoin('units', 'projects.id', '=', 'units.project_id')
            ->select($selectColumns)
            ->get()
            ->groupBy('id')
            ->map(function ($projectUnits) {
                $project = $projectUnits->first();

                $units = $projectUnits->map(function ($unit) {
                    $unitData = [];
                    foreach ($unit as $key => $value) {
                        if (str_starts_with($key, 'unit_')) $unitData[substr($key, 5)] = $value;
                    }

                    return $unitData;
                })->filter(function ($unit) {
                    return $unit['id'] !== null;
                });

                $projectData = get_object_vars($project);

                foreach ($projectData as $key => $value) {
                    if (str_starts_with($key, 'unit_')) unset($projectData[$key]);
                }

                $projectData['units'] = $units->toArray();

                return $projectData;
            });

        // dd($projects->toArray());
        return view('test3', compact('projects'));
    }

    public function test6()
    {
        $projects = DB::table('projects')
            ->leftJoin('units', 'projects.id', '=', 'units.project_id')
            ->select(
                'projects.*',
                'units.id as unit_id',
                'units.type as unit_type',
                'units.crm_unit_code_id as unit_crm_unit_code_id',
                'units.featured as unit_featured',
                'units.area_id as unit_area_id'
            )
            ->get()
            ->groupBy('id')
            ->map(function ($projectUnits) {
                $project = $projectUnits->first();
                $units = $projectUnits->map(fn($unit) => [
                    'id' => $unit->unit_id,
                    'type' => $unit->unit_type,
                    'crm_unit_code_id' => $unit->unit_crm_unit_code_id,
                    'featured' => $unit->unit_featured,
                    'area_id' => $unit->unit_area_id
                ])->filter(fn($unit) => $unit['id'] !== null);
                $projectData = get_object_vars($project);

                unset(
                    $projectData['unit_id'],
                    $projectData['unit_type'],
                    $projectData['unit_crm_unit_code_id'],
                    $projectData['unit_featured'],
                    $projectData['unit_area_id']
                );

                $projectData['units'] = $units->toArray();

                return $projectData;
            });

        dd($projects->toArray());
    }

    public function test5()
    {
        $projects = DB::table('projects')
            ->leftJoin('units', 'projects.id', '=', 'units.project_id')
            ->select(
                'projects.id as project_id',
                'projects.name as project_name',
                'units.id as unit_id',
                'units.type as unit_type',
                'units.crm_unit_code_id as unit_crm_unit_code_id',
                'units.featured as unit_featured',
                'units.area_id as unit_area_id',
                'units.project_id as unit_project_id'
            )
            ->get()
            ->groupBy('project_id');

        $formattedProjects = $projects->map(function ($projectUnits) {
            $project = $projectUnits->first();
            $units = $projectUnits->map(function ($unit) {
                return [
                    'id' => $unit->unit_id,
                    'type' => $unit->unit_type,
                    'crm_unit_code_id' => $unit->unit_crm_unit_code_id,
                    'featured' => $unit->unit_featured,
                    'area_id' => $unit->unit_area_id,
                    'project_id' => $unit->unit_project_id,
                ];
            });

            return [
                'id' => $project->project_id,
                'name' => $project->project_name,
                'units' => $units->toArray()
            ];
        });

        dd($formattedProjects->toArray());
    }

    // $projectsWithUnits = Project::where('')
    // ->leftJoin('units as unit', 'project.id', '=', 'unit.project_id')
    // ->select('project.id', 'unit.*')
    // ->groupBy('project.id', 'unit.id')
    // ->get();
    // =============================

    // $units = Unit::where('project_id', $project)->where(function ($query) {
    //             $query->where('developer_id', $requestQuery['developer_id']);

    //         if (isset($requestQuery['finishing_types'])) {
    //             $query->where('finishing_type', $requestQuery['finishing_types']);
    //         }
    //     })->get();
    // =============================    

    public function test0(Request $request)
    {
        $requestQuery = $request->query();
        $unitsQuery = Unit::where('project_id', 1);

        if (isset($requestQuery['developer_id'])) {
            $unitsQuery->orWhere('developer_id', $requestQuery['developer_id']);
        }

        if (isset($requestQuery['finishing_types'])) {
            $unitsQuery->orWhere('finishing_type', $requestQuery['finishing_types']);
        }

        $units = $unitsQuery->get();

        dd($units);
    }
    // =============================
    // $requestQuery = $request->query();
    // $units = Unit::where('project_id', $project)
    //     ->where(function ($query) use ($requestQuery) {
    //         if (isset($requestQuery['developer_id'])&&
    //         \Schema::hasColumn('units', 'crm_unit_code_id')) {
    //             $query->where('crm_unit_code_id', $requestQuery['developer_id']);
    //         }

    //         if (isset($requestQuery['area_id']) && \Schema::hasColumn('units', 'area_id')) {
    //             $query->orWhere('area_id', $requestQuery['area_id']);
    //         }
    //     })
    //     ->get();
    // =============================

    // public function test3(Request $request)
    // {
    //     $name = $request->query('name');
    //     $crmUnitCodeId = $request->query('crm_unit_code_id');
    //     $areaId = $request->query('area_id');
    //     $projectId = $request->query('project_id');

    //     dd($request->all());

    //     $query = Project::whereHas('units', function ($query) use ($crmUnitCodeId, $areaId, $projectId) {
    //         $query->when($crmUnitCodeId, function ($query, $crmUnitCodeId) {
    //             $query->where('crm_unit_code_id', $crmUnitCodeId);
    //         })
    //             ->when($areaId, function ($query, $areaId) {
    //                 $query->where('area_id', $areaId);
    //             })
    //             ->when($projectId, function ($query, $projectId) {
    //                 $query->where('project_id', $projectId);
    //             });
    //     })
    //         ->with('units');

    //     dd($query->toSql(), $query->getBindings());


    // $projects = Project::whereHas('units', function ($query) use ($crmUnitCodeId, $areaId, $projectId) {
    //     $query->when($crmUnitCodeId, function ($query, $crmUnitCodeId) {
    //         $query->where('crm_unit_code_id', $crmUnitCodeId);
    //     })
    //         ->when($areaId, function ($query, $areaId) {
    //             $query->where('area_id', $areaId);
    //         })

    //         ->when($projectId, function ($query, $projectId) {
    //             $query->where('project_id', $projectId);
    //         });
    // })
    //     ->with('units')
    //     ->get();


    // dd($projects->toArray());
    // }


    // public function search1(Request $request)
    // {
    //     $crm_unit_code_id = $request->query('crm_unit_code_id');
    //     $area_id = $request->query('area_id');
    //     $project_id = $request->query('project_id');

    //     // $projects = Project::Where('name', $name)->WhereHas('units', function ($query) use ($crm_unit_code_id, $area_id, $project_id) {
    //         $query->Where('crm_unit_code_id', $crm_unit_code_id)
    //             ->orWhere('area_id', $area_id)
    //             ->orWhere('project_id', $project_id);
    //     })
    //         ->with('units')
    //         ->get();

    //     return $projects;
    // }
}

    // return view('test2')->with(['name' => $name]);
    // public function search(Request $request)
    // {
    //     $crm_unit_code_id = $request->query('crm_unit_code_id');
    //     $area_id = $request->query('area_id');
    //     $project_id = $request->query('project_id');

    //     $projectsQuery = Project::query();

    //     if ($crm_unit_code_id || $area_id || $project_id) {
    //         $projectsQuery->whereHas('units', function ($query) use (
    //             $crm_unit_code_id,
    //             $area_id,
    //             $project_id
    //         ) {
    //             if ($crm_unit_code_id) $query->where('crm_unit_code_id', $crm_unit_code_id);

    //             if ($area_id) $query->where('area_id', $area_id);

    //             if ($project_id) $query->where('project_id', $project_id);
    //         });
    //     }

    //     $projects = $projectsQuery->with('units')->get();

    //     return response()->json($projects);
    // }



    
            // $projects = Project::WhereHas('units', function ($query) {
            //     $query->Where('crm_unit_code_id', 'PRO7126')
            //         ->orWhere('area_id', 2)
            //         ->orWhere('project_id', 1);
            // })
            //     ->with('units')
            //     ->get();