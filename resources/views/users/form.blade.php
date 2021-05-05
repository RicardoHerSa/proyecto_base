<hr>
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info"  role="tab" aria-controls="info" aria-selected="true">Detalles de la cuenta</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="grupos-tab" data-toggle="tab" href="#grupos" role="tab" aria-controls="grupos" aria-selected="false">Grupos de usuarios asignados</a>
    </li>
</ul> 
<br>
@php
$style = $visibility ?? '';
@endphp
<div class="tab-content" id="myTabContent">
    <div class="tab-pane active" id="info" role="tabpanel" aria-labelledby="info-tab">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                    <label for="name" class="col-sm-4 control-label">{{ 'Nombre *' }}</label>
                    <div class="col-sm-12">
                        <input class="form-control" name="name" type="text" id="name" value="{{ isset($user->name) ? $user->name : ''}}" required>
                        {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="form-group {{ $errors->has('username') ? 'has-error' : ''}}">
                    <label for="username" class="col-sm-4 control-label">{{ 'Usuario *' }}</label>
                    <div class="col-sm-12">
                        <input class="form-control" name="username" type="text" id="username" value="{{ isset($user->username) ? $user->username : ''}}" required>
                        {!! $errors->first('username', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="form-group {{ $errors->has('password') ? 'has-error' : ''}}" style="{{ isset($style) ? $style : '' }}">
                    <label for="password" class="col-sm-4 control-label">{{ 'Contraseña *' }}</label>
                    <div class="col-sm-12">
                        <input class="form-control" name="password" type="password" id="password" value="{{ isset($user->password) ? '' : ''}}">
                        {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
                    </div>   
                </div>
                <div class="form-group {{ $errors->has('password_antiguo') ? 'has-error' : ''}}">
                    <div class="col-sm-12">
                        <input class="form-control" name="password_antiguo" type="hidden" id="password_antiguo" value="{{ isset($user->password) ? $user->password : '' }}">
                    </div>   
                </div>
                <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                    <label for="email" class="col-sm-4 control-label">{{ 'Correo electrónico *' }}</label>
                    <div class="col-sm-12">
                        <input class="form-control" name="email" type="email" id="email" value="{{ isset($user->email) ? $user->email : ''}}" required>
                        {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="form-group {{ $errors->has('block') ? 'has-error' : ''}}">
                    <label for="block" class="col-sm-4 control-label">{{ 'Estado' }}</label>
                    <div class="col-sm-12">
                        <select class="form-control" name="block" id="block" value="{{ isset($user->block) ? $user->block : '' }}">
                            @if(isset($user->block) && $user->block == "0")
                                <option value="0">Activo</option>
                                <option value="1">Inactivo</option>
                            @else
                                <option value="0">Activo</option>
                                <option value="1" selected="selected">Inactivo</option>
                            @endif
                        </select>
                        {!! $errors->first('block', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group {{ $errors->has('profile_orgcountry') ? 'has-error' : ''}}">
                    <label for="profile_orgcountry" class="col-sm-4 control-label">{{ 'Id Organizacion *' }}</label>
                    <div class="col-sm-12">
                        <input class="form-control" name="profile_orgcountry" type="text" id="profile_orgcountry" value="{{ isset($user->profile_orgcountry) ? $user->profile_orgcountry : ''}}" required>
                        {!! $errors->first('profile_orgcountry', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="form-group {{ $errors->has('profile_externalid') ? 'has-error' : ''}}">
                    <label for="profile_externalid" class="col-sm-4 control-label">{{ 'Id Empleado *' }}</label>
                    <div class="col-sm-12">
                        <input class="form-control" name="profile_externalid" type="text" id="profile_externalid" value="{{ isset($user->profile_externalid) ? $user->profile_externalid : ''}}" required>
                        {!! $errors->first('profile_externalid', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="form-group {{ $errors->has('profile_ordinal') ? 'has-error' : ''}}">
                    <label for="profile_ordinal" class="col-sm-4 control-label">{{ 'Ordinal Empleado *' }}</label>
                    <div class="col-sm-12">
                        <input class="form-control" name="profile_ordinal" type="number" id="profile_ordinal" value="{{ isset($user->profile_ordinal) ? $user->profile_ordinal : ''}}" required>
                        {!! $errors->first('profile_ordinal', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="form-group {{ $errors->has('gestor_externo') ? 'has-error' : ''}}">
                    <label for="gestor_externo" class="col-sm-4 control-label">{{ '¿Es gestor externo?' }}</label>
                    <div class="col-sm-12">
                        <select class="form-control" name="gestor_externo" id="gestor_externo" value="{{ isset($user->gestor_externo) ? $user->gestor_externo : ''}}">
                            <option value="0">No</option>
                            <option value="1">Si</option>
                        </select>
                        {!! $errors->first('parent_id', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <div class="tab-pane" id="grupos" role="tabpanel" aria-labelledby="grupos-tab">
        <h3>Grupos de usuario asignados</h3>
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
                        <em>( {{ $group->id ?: 'Sin descripción' }} )</em>
                    </label>
                        @if(count($group->subcategory))
                            @include('partials.subCategoryList',['subcategories' => $group->subcategory, 'arrayGroupUser'=>$arrayGroupUser])
                        @endif 
                    
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
<hr>
<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Editar' : 'Agregar' }}">
</div>
