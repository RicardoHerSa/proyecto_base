@foreach($subcategories as $subcategory)
    <ul>
        <li> 
            <label>
                @if(in_array($subcategory->id,$arrayGroupUser))
                    {{ Form::checkbox('groups[]', $subcategory->id, true) }}
                @else   
                    {{ Form::checkbox('groups[]', $subcategory->id, false) }}
                @endif
                {{ $subcategory->title }}
                <em>( {{ $subcategory->id ?: 'Sin descripción' }} )</em>
            </label>
        @if(count($subcategory->subcategory))
            @include('partials.subCategoryList',['subcategories' => $subcategory->subcategory])
        @endif 
        </li>
    </ul> 
@endforeach