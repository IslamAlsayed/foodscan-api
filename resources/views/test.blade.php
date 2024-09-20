<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>

    <a href="/uniland/proprieties2?name=islam&crm_unit_code_id=PRO7126&area_id=2&project_id=1&developere_id=1">test</a>

    <br />
    <br />
    <br />

    @php
    $baseUrl = url()->current();
    echo $baseUrl;
    // $queryString = http_build_query($queryParameters);
    // $fullUrl = $queryString ? "{$baseUrl}?{$queryString}" : $baseUrl;
    @endphp

    {{-- <a href="{{ url('/uniland/proprieties2') . '?name=islam' . $fullUrl }}">test2</a> --}}


    {{-- <form action="{{route('project')}}" method="GET">
        <input type="hidden" name="crm_unit_code_id" value="PRO7126">
        <input type="hidden" name="area_id" value="2">
        <input type="hidden" name="project_id" value="1">
        <input type="hidden" name="developere_id" value="1">
        <button type="submit">cearsh</button>
    </form> --}}
</body>

</html>