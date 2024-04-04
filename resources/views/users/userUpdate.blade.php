@extends('inicio')
@section("container")

<div class="row mt-3">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header bg-dark text-white">Editar usuario</div>
            <div class="card-body">
                <form method="POST" action="{{url('putUser',[$user])}}">
                    @method('PUT')
                    @csrf
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                        <input type="text" name="name" value="{{$user->name}}" class="form-control" placeholder="nombre" aria-label="Username" aria-describedby="basic-addon1">
                        @error('name')
                            <br><small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                        <input type="email" name="email" value="{{$user->email}}" class="form-control" placeholder="email" aria-label="Username" aria-describedby="basic-addon1">
                        @error('email')
                            <br><small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                        <input type="number" name="telefono" value="{{$user->telefono}}" class="form-control" placeholder="telefono" aria-label="Username" aria-describedby="basic-addon1">
                        @error('telefono')
                            <br><small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                    <select name="rol" class="form-select" required>
                        <option value="{{$user->rol}}">Rol</option>
                        @foreach ($roles as $row )
                        <option value="{{$row->name}}">{{$row->name}}</option>
                        @endforeach
                    </select>
                    @error('rol')
                        <small class="text-danger">{{$message}}</small>
                    @enderror
                </div>
                    <div class="d-grid col-6 mx-auto">
                        <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                    </div>
                    <div class="d-grid col-6 mx-auto">
                        <a class="btn btn-danger" href="/users">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection