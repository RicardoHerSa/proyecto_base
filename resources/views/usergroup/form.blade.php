<div class="form-group {{ $errors->has('title') ? 'has-error' : ''}}">
    <label for="title" class="control-label">{{ 'Titulo grupo' }}</label>
    <input class="form-control" name="title" type="text" id="title" value="{{ isset($usergroup->title) ? $usergroup->title : ''}}" required>
    {!! $errors->first('title', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group {{ $errors->has('parent_id') ? 'has-error' : ''}}">
    <label for="parent_id" class="control-label">{{ 'Grupo principal' }}</label>
    <select class="form-control" name="parent_id" id="parent_id">
            <option></option>
            <option value="0">-Padre principal</option>
            @foreach($group as $groups)
            @if (isset($parent) && $usergroup->id == $groups->id)
                <option value="{{$groups->id}}" selected="selected">
                <li>- {{$parent}}</li>
                </option>
                
            @else
                <option value="{{$groups->id}}">
                <li>-{{$groups->title}}</li>
                </option>
            @endif
            
            @if(count($groups->subcategory))
            @include('partials.subCategoryListGroup',['subcategories' => $groups->subcategory, 'padre' => $parent])
            @endif

            @endforeach
    </select>
    {!! $errors->first('parent_id', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group {{ $errors->has('companycessid') ? 'has-error' : ''}}">
    <label for="companycessid" class="control-label">{{ 'Empresa Id' }}</label>
    <input class="form-control" name="companycessid" type="text" id="companycessid" value="{{ isset($usergroup->companycessid) ? $usergroup->companycessid : ''}}" required>
    {!! $errors->first('companycessid', '<p class="help-block">:message</p>') !!}
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

</script>
