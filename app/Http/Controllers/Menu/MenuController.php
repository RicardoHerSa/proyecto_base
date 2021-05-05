<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Models\Menu\Menu;
use App\Models\Viewlevel\Viewlevel;
use App\Models\Relations\RelationMenuViewlevel;
use Illuminate\Http\Request;

class MenuController extends Controller {

    public function index(Request $request) {
        $menu = Menu::get();
        return view('menu.index', compact('menu'));
    }

    public function create() {
        $menutype = Menu::select('menutype')
            ->distinct()
            ->get();
        $infoLevel = [];
        $level = Viewlevel::get();
        $infoMenu = Menu::get();
        return view('menu.create', compact('level', 'infoMenu', 'infoLevel', 'menutype'));
    }

    public function store(Request $request) {

        $title = $request['title'];
        $alias = str_replace(" ", "-", $title);

        //crear menu
        $menu = Menu::create([
                'menutype' => $request['menutype'],
                'title' => $request['title'],
                'alias' => $alias,
                'link' => $request['link'],
                'parent_id' => $request['parent_id'],
                'published' => $request['published'],
                'icono' => $request['icono'],
                'orden' => $request['orden'],
        ]);

        //crear relacion menu-viewlevel
        $menu->Viewlevel()->sync($request->get('level'));
        return redirect('menu')->with('success', 'Menú creado con éxito!');
    }

    public function show($id) {
        $menu = Menu::findOrFail($id);

        return view('menu.show', compact('menu'));
    }

    public function edit($id) {

        $menutype = Menu::select('menutype')
            ->distinct()
            ->get();        
        $menu = Menu::findOrFail($id);
        $infoMenu = Menu::get();
        $level = Viewlevel::get();
        $consulta = RelationMenuViewlevel::select("viewlevel_id")
                ->where("menu_id", $id)->get();

        $array = $consulta->toArray();
        if($array == null || $array == 1){
            $infoLevel = $array[0] = 1;
        }
        if($array != null && $array[0] != 1){
            $infoLevel = $consulta[0]->viewlevel_id;
        }
        
        return view('menu.edit', compact('menu', 'infoMenu', 'level', 'infoLevel', 'menutype'));
    }

    public function update(Request $request, $id) {

        $menu = Menu::findOrFail($id);       
        $title = $menu['title'];
        $alias = str_replace(" ", "-", $title);
   
        $menu->update([
            'menutype' => $request['menutype'],
            'title' => $request['title'],
            'alias' => $alias,
            'link' => $request['link'],
            'parent_id' => $request['parent_id'],
            'published' => $request['published'],
            'icono' => $request['icono'],
            'orden' => $request['orden'],
            ]);
            
        //actualizar relacion menu-viewlevel
        $menu->Viewlevel()->sync($request->get('level'));

        return redirect('menu')->with('success', 'Menú actualizado!');
    }

    public function destroy($id) {
        Menu::destroy($id);

        return redirect('menu')->with('danger', 'Menu eliminado!');
    }
 
    public function getMenuChange(Request $request){
        $menu = Menu::findOrFail($request->menu_id);
        $menu->published = $request->published;
        $menu->save();
        return response()->json(['message' => 'Estado del Menú actualizado.']);
    }

}
