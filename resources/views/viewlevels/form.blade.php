<a 
    href="{{ url('/viewlevels') }}" 
    title="Back" class="btn btn-warning form-inline mr-2 my-lg-0 ">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> Atrás
</a>
<input class="btn btn-primary form-inline  mr-1 my-lg-0" type="submit" value="{{ $formMode === 'edit' ? 'Editar' : 'Agregar' }}">
<hr>
<div class="form-group {{ $errors->has('title') ? 'has-error' : ''}}">
    <label for="title" class="control-label">{{ 'Título del nivel de acceso *' }}</label>
    <input class="form-control" name="title" type="text" id="title" value="{{ isset($viewlevel->title) ? $viewlevel->title : ''}}" required >
    {!! $errors->first('title', '<p class="help-block">:message</p>') !!}
</div>
<div>

</div>
<hr>

<h4>Grupos que tienen acceso</h4>
<br>
<div class="form-group">
    <ul class="list-unstyled">
        @foreach($groups as $group) 
            <li>
                <label>
                   @if(in_array($group->id,$arrayGroupUser))
                        {{ Form::checkbox('groups[]', $group->id, true) }}
                    @else   
                        {{ Form::checkbox('groups[]', $group->id, false) }}
                    @endif
                    {{ $group->title }}
                    <em>( {{ $group->lft ?: 'Sin descripción' }} )</em>
                </label>
                    @if(count($group->subcategory))
                       @include('partials.subCategoryList',['subcategories' => $group->subcategory])
                    @endif 
                
            </li>
        @endforeach
    </ul>
</div>
<hr>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Editar' : 'Agregar' }}">
</div>
