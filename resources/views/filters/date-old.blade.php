<input type="date" name="{{$filter->getSelectField()}}[start]"
       value="{{request($filter->getSelectField())['start'] ?? ""}}">

<input type="date" name="{{$filter->getSelectField()}}[end]"
       value="{{request($filter->getSelectField())['end'] ?? ""}}">