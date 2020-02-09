<!DOCTYPE html>
<html lang="en">

<style>
.button {
  background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
}

a:link, a:visited {
  background-color: #f44336;
  color: white;
  padding: 14px 25px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
}

a:hover, a:active {
  background-color: red;
}

.button2 {background-color: #008CBA;} /* Blue */
.button3 {background-color: #f44336;} /* Red */ 
</style>
<head>
      <title>Appointment reserved!</title>
</head>
<body style="background-color:#36c7a5">
    <div style="background-color:#36c7a5">
    <h1 style="color:white">Appointment confirmation</h1>
    <h1 style="color:white">Clinic {{$appointment->clinic->name}}</h1>
    <small style="color:white">{{$clinic->address}}<small>
    </div>
    <h5>Hello, {{ $user->name }},</h5>
    <p>Your appointment request has been approved, please confirm.</p>
    <table aria-describedby="mydesc">
      <tr>
      <th id='1'></th>
      <th id='2'></th>
    </tr>
        <tr>
        <td>date:</td>
        <td>{{$appointment->date}}</td>
        </tr>
        <tr>
        <td>doctor:</td>
        <td>{{$doctor->name}} {{$doctor->last_name}}</td>
        </tr>
        <tr>
        <td>room:</td>
        <td>{{$operationsRoom->number}}</td>
        </tr>
        <tr>
        <td>price:</td>
        <td>{{$appointment->price}}</td>
        </tr>
    </table>
    <a style ="background-color: #008CBA" href="http://localhost:8000/api/confirmations/confirm/<?=$id?>">Confirm</a>
    <a href="http://localhost:8000/api/confirmations/decline/<?=$id?>">Decline</a>


</body>
</html>