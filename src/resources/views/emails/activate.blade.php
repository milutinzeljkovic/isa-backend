<!DOCTYPE html>
<html>
<head>
      <title>Activate Mail</title>
</head>
<body>
    <h1>Welcome, {{ $user->name }}</h1>
    <p>
        To activate account click on <a href="http://localhost:8000/api/auth/activate/<?=$encryptedId?>" >Link</a> !
    </p>
</body>
</html>