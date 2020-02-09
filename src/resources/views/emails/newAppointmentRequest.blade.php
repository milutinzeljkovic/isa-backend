<!DOCTYPE html>
<html>
<head>
      <title>Appointment request</title>
</head>
<body>
    <h1>Hello, {{ $user->name }}</h1>
    <p>
        You have a new appointment request. See the info down below<br>
        Patient: {{$patient->name }} {{$patient->last_name}}<br>
        Date: {{$appointment->date}}<br>
        Price: {{$appointment->price}}<br>
        Additional discounts and the duration of the appointment will be decided by you <br>
    </p>
</body>
</html>