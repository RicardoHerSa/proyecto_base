<div class="form-group {{ $errors->has('menutype') ? 'has-error' : ''}}">
    <label for="menutype" class="control-label">{{ 'Tipo menú' }}</label>
    <select class="form-control" name="menutype" id="menutype" value="{{ isset($menu->menutype) ? $menu->menutype : ''}}">
        <option disabled selected>Seleccione un tipo de menú</option>    
        @foreach($menutype as $item)   
            @if(isset($menu->menutype) && $menu->menutype == $item->menutype )
                    <option value="{{$item->menutype}}" selected="selected">{{$item->menutype}}</option>
                @else
                    <option value="{{$item->menutype}}">{{$item->menutype}}</option>
                @endif
        @endforeach
    </select>
    {!! $errors->first('parent_id', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group {{ $errors->has('title') ? 'has-error' : ''}}">
    <label for="title" class="control-label">{{ 'Nombre menú' }}</label>
    <input class="form-control" name="title" placeholder="Crear usuarios" type="text" id="title" value="{{ isset($menu->title) ? $menu->title : ''}}" required>
    {!! $errors->first('title', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group {{ $errors->has('link') ? 'has-error' : ''}}">
    <label for="link" class="control-label">{{ 'URL menú' }}</label>
    <input class="form-control" name="link" placeholder="users/create" type="text" id="link" value="{{ isset($menu->link) ? $menu->link : ''}}" required>
    {!! $errors->first('link', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('parent_id') ? 'has-error' : ''}}">
    <label for="parent_id" class="control-label">{{ 'Padre' }}</label>
    <select class="form-control" name="parent_id" id="parent_id" value="{{ isset($menu->title) ? $menu->title : ''}}">
        <ul class="list-unstyled">
            <option value="0">Menú principal</option>
            @foreach($infoMenu as $menus)
                @if(isset($menu->parent_id) && $menu->parent_id == $menus->id)
                    <option value="{{$menus->id}}" selected="selected">
                        <li>{{$menus->title}}</li>
                    </option>
                @else
                    <option value="{{$menus->id}}">
                        <li>{{$menus->title}}</li>
                    </option>
                @endif
            @endforeach
        </ul>
    </select>
    {!! $errors->first('parent_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('level') ? 'has-error' : ''}}">
    <label for="level" class="control-label">{{ 'Nivel de acceso' }}</label>
    <select class="form-control" name="level" id="level">
        <ul class="list-unstyled">
            @foreach($level as $levels)
                
                @if($infoLevel == $levels->id )
                    <option value="{{$levels->id}}" selected="selected">
                        <li> {{ $levels->title }}</li>
                    </option>
                @else
                    <option value="{{$levels->id}}">
                        <li>{{$levels->title}}</li>
                    </option>
                @endif
            @endforeach
        </ul>
    </select>
    {!! $errors->first('level', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('published') ? 'has-error' : ''}}">
    <label for="parent_id" class="control-label">{{ 'Estado' }}</label>
    <select class="form-control" name="published" id="published" value="{{ isset($menu->title) ? $menu->title : ''}}">
        @if(isset($menu->published) && $menu->published == "1" )
            <option value="1">Activo</option>
            <option value="0">Inactivo</option>
        @else
            <option value="1">Activo</option>
            <option value="0" selected="selected">Inactivo</option>
        @endif
    </select>
</div> 

<div class="form-group {{ $errors->has('orden') ? 'has-error' : ''}}">
    <label for="orden" class="control-label">{{ 'Orden' }}</label>
    <input class="form-control" name="orden" placeholder="#" type="number" id="orden" value="{{ isset($menu->orden) ? $menu->orden : ''}}"  >
    {!! $errors->first('orden', '<p class="help-block">:message</p>') !!}
</div>
  
<div class="form-group {{ $errors->has('icono') ? 'has-error' : ''}}">
    <label for="icono" class="control-label">{{ 'Icono' }}</label>
    <input class="form-control" name="icono" placeholder="html  https://fontawesome.com/v4.7.0/icons/ " type="text" id="icono" value="{{ isset($menu->icono) ? $menu->icono : ''}}"  >
    {!! $errors->first('icono', '<p class="help-block">:message</p>') !!}
</div>

<hr>
<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Editar' : 'Agregar' }}">
</div>
<script>

    $("#parent_id").select2({
        placeholder: "Seleccione un grupo",
        allowClear: true,
    });
    $("#level").select2({
        placeholder: "Seleccione un nivel de acceso",
        allowClear: true,
    });





</script>