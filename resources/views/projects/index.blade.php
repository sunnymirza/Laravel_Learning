<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Index</title>
</head>
<body>
    <h1>BirdBoard</h1>
    <ul>
        @foreach($projects as $project)
            <li>{{  $project->title }}</li>
        @endforeach
    </ul>

</body>
</html>