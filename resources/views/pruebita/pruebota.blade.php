<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <title>Document</title>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="text-white">Iniciar sesión</h1>
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="{{ route('guardar') }}">
                            @csrf
                            <div class="form-group">
                                <label>Correo electrónico</label>
                                <input class="form-control" id="exampleInputEmail1" name="email" value="{{ old('email') }}">
                                <small class="form-text text-danger">
                                    @if ($errors->has('email'))
                                    {{ $errors->first('email') }}
                                    @endif
                                </small>
                                @if ($errors->any())
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                                @endif
                            </div>
                            <div class="form-group mt-2">
                                <label>Contraseña</label>
                                <input class="form-control" id="exampleInputPassword1" name="password" type="password">
                                <small class="form-text text-danger">
                                    @if ($errors->has('password'))
                                    {{ $errors->first('password') }}
                                    @endif
                                </small>
                            </div>
                            <button type="submit" class="btn btn-dark mt-3">Iniciar sesión</button>
                            <a class="btn btn-danger mt-3" href="/">Cancelar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>