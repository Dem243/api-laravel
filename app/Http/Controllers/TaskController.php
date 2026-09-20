<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        return response()->json($project->tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request, Project $project)
    {

        $task = new Task($request->all());

        $project->tasks()->save($task);

        return $this->successResponse($task, 'Taches créé avec succés');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Task $task)
    {
        if ($task->project_id != $project->id) {
            return $this->errorResponse("Cette tache n'appartient pas à ce projet");
        };
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Project $project, Task $task)
    {
        if ($task->project_id != $project->id) {
            return $this->errorResponse("Cette tache n'appartient pas à ce projet");
        };

        $task->update($request->validated());

        return $this->successResponse($task, 'Taches modifié avec succés');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Task $task)
    {
        if ($task->project_id != $project->id) {
            return $this->errorResponse("Cette tache n'appartient pas à ce projet");
        };
        $task->delete();
        return response()->json("Tache supprimé avec succès!");
    }
}
