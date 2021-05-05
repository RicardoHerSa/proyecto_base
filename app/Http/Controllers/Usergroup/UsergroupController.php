<?php

namespace App\Http\Controllers\Usergroup;

use App\Http\Controllers\Controller;
use App\Models\Usergroup\Usergroup;
use Illuminate\Http\Request;

class UsergroupController extends Controller
{

    public function index(Request $request)
    {
        $keyword = $request->get('search'); 
        $paginate = 30;
        
        if (!empty($keyword)) { 
            $usergroup = Usergroup::where('title', 'LIKE', "%$keyword%")
                ->latest()->paginate($paginate); 
        } else { 
            $usergroup = Usergroup::orderBy("parent_id","asc")->paginate($paginate); 
        }
        return view('usergroup.index', compact('usergroup'));
    }

    public function create(Request $request)
    {
        $parent = null;
        $group = Usergroup::where('parent_id',0)->get();
        return view('usergroup.create', compact('group', 'parent'));
    }

    public function recursiveGroups($id,$idDo){
        $groupos[$id] = Usergroup::where('parent_id', $id)->get();
        foreach($groupos[$id] as $moreCHild){
            if(count($moreCHild->noHijos) > 0){
                return $this->recursiveGroups($moreCHild->id,$id);
            }else{
                return $groupos[$id];
            }
        }
    }   
    
    public function store(Request $request)
    {
        Usergroup::where('id',$request['parent_id'])->get();  
        Usergroup::create([
            'title' => $request['title'],
            'parent_id' => $request['parent_id'],
            'companycessid' => $request['companycessid'],
        ]);

        return redirect('usergroup')->with('success', 'Grupo agregado con éxito!');
    }

    public function show($id)
    {
        $usergroup = Usergroup::findOrFail($id);
        return view('usergroup.show', compact('usergroup'));
    }

    public function edit(Request $request, $id)
    {
        $usergroup = Usergroup::findOrFail($id);
        //dd($usergroup);
        if($usergroup->parent_id == 0){
            $usergroup->parent_id = $usergroup->id;
        }
        $consulta = Usergroup::select('title')
                    ->where('id', $usergroup->parent_id)
                    ->get();
        $parent = $consulta[0]->title;
        $group = Usergroup::where('parent_id',0)->get();
        return view('usergroup.edit', compact('usergroup', 'group','parent'));
    }

    public function update(Request $request, $id)
    {
        
        $requestData = $request->all();
        $usergroup = Usergroup::findOrFail($id);
        $usergroup->update($requestData);
        return redirect('usergroup')->with('success', 'Grupo editado con éxito!');
    }

    public function destroy($id)
    {
        Usergroup::destroy($id);

        return redirect('usergroup')->with('danger', 'Grupo eliminado!');
    }

    public function selectBox()
    {
        return view('usergroup.form');
    }

}
