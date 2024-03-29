<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>CODIGO DE VERIFICACION</h1>
    <h3>{{$codigo}}</H3><br>
    @role('admin')
    <p>1- Inicia sesion en la app movil</p>
    <p>2- El codigo de verificacion ingresalo en el input de la app</p>
    <p>2- Si es correcto, te devolvera un codigo para introducir en la aplicacion web</p>
    @endrole
</body>

</html>