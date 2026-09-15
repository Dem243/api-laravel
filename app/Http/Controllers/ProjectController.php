<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        //-----------------------------------Filtre----------------------------------------------
        //Récupérer tous les projet supérieur au 11-septembre
        // $project= Project::whereDate('start_date','>','2026-09-11')->get();

        //Récupérer tous les projet inferieur au 13-septembre
        // $project= Project::whereDate('start_date','<','2026-09-13')->get();
        //Récupérer tous les projet egale au 13-septembre
        //$project = Project::whereDate('start_date', '=', '2026-09-13')->get();
        //Récupérer tous les projet à l'interval de deux dates précises
        /* $project = Project::whereBetween('start_date', ['2026-09-11','2026-09-13'])->orWhereBetween('end_date',['2026-09-11','2026-09-13'])->get();
 */

        //----------------------------------Trie-----------------------------------------------
        // Le projet les plus récent DESC, ASC Le moins récent 
        // $project=Project::orderBy('rate','DESC')->orderBy('start_date', 'DESC')->get();

       // $project = Project::orderBy('rate', 'DESC')->orderBy('name', 'ASC')->get();
        
       // Pagination
       $projects= Project::paginate(3);
       return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $request->validated($request->all());

        $project = Project::create($request->all());
        return $this->successResponse($project, 'Projet créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        return $this->successResponse(
            $project,
            'Projet modifié avec succès'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['success' => true, 'message' => 'Projet supprimé avec succès']);
    }
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        if (trim($keyword) == '') {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez entrer un mot-clé pour effectuer la recherche'
            ]);
        }

        $projects = Project::where('name', 'like', "%$keyword%")->get();

        if ($projects->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "Aucune correspondance trouvée pour votre recherche : $keyword"
            ]);
        }

        return response()->json($projects);
    }
}
