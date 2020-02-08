<!DOCTYPE html>
<html>
<head>
      <title>Add room of operation</title>
</head>
<body>
    <h1>Hello, {{ $user->name }}</h1>
    <p>
        Your operation({{$operation->info}}) has been booked.<br>
        Date: {{$operation->date}}<br>
        Operation room: {{$room->name}} {{$room->number}}
    </p>
</body>
</html>