<!DOCTYPE html>
<html lang="en">
<head>
      <title>Add to operation</title>
</head>
<body>
    <h1>Hello, {{ $user->name }}</h1>
    <p>
        You have been added to operation({{$operation->info}}).<br>
        Date: {{$operation->date}}<br>
        Operation room: {{$room->name}} {{$room->number}}
    </p>
</body>
</html>