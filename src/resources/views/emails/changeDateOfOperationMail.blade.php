<!DOCTYPE html>
<html>
<head>
      <title>Change date of operation</title>
</head>
<body>
    <h1>Hello, {{ $user->name }}</h1>
    <p>
        Date of operation({{$operation->info}}) has been changed from {{$oldDate}} to {{$operation->date}}.<br>
        Operation room: {{$room->name}} {{$room->number}}
    </p>
</body>
</html>