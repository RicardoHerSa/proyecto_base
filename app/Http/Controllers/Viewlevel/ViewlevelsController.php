<?php

namespace App\Http\Controllers\Viewlevel;

use App\Http\Controllers\Controller;
use App\Models\Viewlevel\Viewlevel;
use App\Models\Usergroup\Usergroup;
use App\Models\Relations\RelationsGroupViewlevel;
use Illuminate\Http\Request;

class ViewlevelsController extends Controller
{
    public function index(Request $request)
    {
        $viewlevels = Viewlevel::all();
        $id =  Viewlevel::pluck('id');
        $consulta = RelationsGroupViewlevel::whereIn('viewlevel_id',$id)->get();

        return view('viewlevels.index', compact('viewlevels','consulta'));
    }

    public function create()
    {
        $arrayGroupUser = [];
        $groups = Usergroup::where('parent_id',0)->get();
        return view('viewlevels.create', compact('groups', 'arrayGroupUser'));
    }

    public function store(Request $request)
    {

        $view = Viewlevel::create($request->all());
        //crear relacion viewlevels-groups
        $view->usergroups()->sync($request->get('groups'));

        return redirect('viewlevels')->with('success', 'Nivel de acceso creado con éxito!');

    }

    public function show($id)
    {
        $viewlevel = Viewlevel::findOrFail($id);
        $consulta = RelationsGroupViewlevel::where('viewlevel_id',$viewlevel->id)->get();
        return view('viewlevels.show', compact('viewlevel', 'consulta'));
    }

    public function edit($id)
    {
        $viewlevel = Viewlevel::findOrFail($id);
        $arrayGroupUser= [];
        $groupUser = RelationsGroupViewlevel::select("usergroup_id")
                ->where("viewlevel_id",$id)->get()->toArray();
        foreach($groupUser as $userGroup){
            $arrayGroupUser[] = $userGroup['usergroup_id'];
        }
        $groups = Usergroup::where('parent_id',0)->get();

        return view('viewlevels.edit', compact('viewlevel', 'groups', 'arrayGroupUser'));
    }

    public function update(Request $request, $id)
    {
        $requestData = $request->all();
        $viewlevel = Viewlevel::findOrFail($id);
        $viewlevel->update($requestData);

        $viewlevel->usergroups()->sync($request->get('groups'));

        return redirect('viewlevels')->with('success', 'Nivel de acceso editado con éxito!');
    }

    public function destroy($id)
    {
        Viewlevel::destroy($id);

        return redirect('viewlevels')->with('danger', 'Nivel de acceso eliminado!');
    }

}
