<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <h1>test3</h1>

    @foreach ($projects as $project)
    <h2>{{ $project->name }}</h2>

    @foreach ($project->units as $unit)
    <p>Unit Name: {{ $unit->name }}</p>
    @endforeach
    @endforeach

</body>

</html>