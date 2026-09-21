<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
        //$projects= Project::paginate(3);

        // En fonction de l'utilisateur
        $projects = Project::where('user_id', Auth::user()->id)->get();

        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $request->validated($request->all());

        $image = $request->image;

        if ($image != null && !$image->getError()) {
            $image = $request->image->store('asset','public');
        }

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'rate' => $request->rate,
            'image'=>$image,
            'user_id' => Auth::user()->id
        ]);

        return $this->successResponse($project, 'Projet créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        /* if (!Gate::allows('access', $project)) {
            return $this->unauthorizedResponse("Vous êtes pas autorisé à cette ressource ");
        } */
        if (Auth::user()->cannot('view', $project)) {
            return $this->unauthorizedResponse("Vous êtes pas autorisé à cette ressource ");
        };

        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        if (!Gate::allows('access', $project)) {
            return $this->unauthorizedResponse("Vous êtes pas autorisé à cette ressource ");
        }
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
        if (!Gate::allows('access', $project)) {
            return $this->unauthorizedResponse("Vous êtes pas autorisé à cette ressource ");
        }
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
