<?php

namespace App\Models\Menu;

use Illuminate\Database\Eloquent\Model;
use App\Models\Viewlevel\Viewlevel;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;

class Menu extends Model {

    protected $table = 'jess_menu';
    protected $primaryKey = 'id';
    protected $fillable = ['menutype', 'title', 'link', 'parent_id', 'published', 'alias','icono','orden'];

    public function getChildren($data, $line) {
        $children = [];
        foreach ($data as $line1) {
            if ($line['id'] == $line1['parent_id']) {
                $children = array_merge($children, [ array_merge($line1, ['submenu' => $this->getChildren($data, $line1)])]);
            }
        }
        return $children;
    }

    public function optionsMenu() {

        if (Auth::check()){
            $infoUser = auth()->user();
            $user = $infoUser->username;  
        }else{
            $user = "";
        }
        $consultaMenu =  User::select('jess_menu.*')
                ->join('jess_user_usergroup_map', 'jess_user_usergroup_map.user_id', '=', 'jess_users.id')
                ->join('jess_usergroups', 'jess_user_usergroup_map.usergroup_id', '=', 'jess_usergroups.id')
                ->join('jess_usergroup_viewlevel', 'jess_usergroup_viewlevel.usergroup_id', '=', 'jess_usergroups.id')
                ->join('jess_viewlevels', 'jess_usergroup_viewlevel.viewlevel_id', '=', 'jess_viewlevels.id')
                ->join('jess_menu_viewlevel', 'jess_menu_viewlevel.viewlevel_id', '=', 'jess_viewlevels.id')
                ->join('jess_menu', 'jess_menu_viewlevel.menu_id', '=', 'jess_menu.id')
                ->where('jess_users.username', '=', $user)
                ->where('jess_menu.published', '=', 1)
                ->orderby('parent_id')
                ->orderby('orden')
                ->orderby('title')
                ->orderby('menutype')
                ->distinct()
                ->get()
                ->toArray();
        return $consultaMenu;
    }

    public static function menus() {
        $menus = new Menu();
        $data = $menus->optionsMenu();
        $menuAll = [];
        foreach ($data as $line) {
            $item = [ array_merge($line, ['submenu' => $menus->getChildren($data, $line)])];
            $menuAll = array_merge($menuAll, $item);
        }
        return $menus->menuAll = $menuAll;
    }

    public function Viewlevel() {
        return $this->belongsToMany(Viewlevel::class, 'jess_menu_viewlevel');
    }
    
    public function nameParent($id){
        $query = \DB::table('jess_menu')
                        ->select("title")            
                        ->where("id",$id)
                        ->get();
        return $query[0]->title ;
    }

}
