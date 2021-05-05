@foreach($subcategories as $subcategory)
            @if (isset($parent) && $subcategory->id == $usergroup->id)
                <option value="{{$subcategory->id}}" selected="selected">
                    <li>- {{$parent}} </li>
                </option>
            @else
                <option value="{{$subcategory->id}}">
                <li>- {{$subcategory->title}}</li>
                </option>
            @endif
                @if(count($subcategory->subcategory))
                    @include('partials.subCategoryListGroup',['subcategories' => $subcategory->subcategory])
                @endif 
@endforeach