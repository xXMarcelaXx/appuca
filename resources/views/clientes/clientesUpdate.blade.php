@extends('inicio')
@section("container")

<div class="row mt-3">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header bg-dark text-white">Editar curso</div>
            <div class="card-body">
                <form method="POST" action="{{url('putCliente',[$clientes])}}">
                    @method('PUT')
                    @csrf
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                        <input type="text" name="nombre" value="{{$clientes->nombre}}" class="form-control" placeholder="nombre" aria-label="Username" aria-describedby="basic-addon1">
                        @error('nombre')
                            <br><small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                        <input type="number" name="edad" value="{{$clientes->edad}}" class="form-control" placeholder="nombre" aria-label="Username" aria-describedby="basic-addon1">
                        @error('edad')
                            <br><small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                        <input type="number" name="telefono" value="{{$clientes->telefono}}" class="form-control" placeholder="nombre" aria-label="Username" aria-describedby="basic-addon1">
                        @error('telefono')
                            <br><small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                        <input type="email" name="correo" value="{{$clientes->correo}}" class="form-control" placeholder="nombre" aria-label="Username" aria-describedby="basic-addon1">
                        @error('correo')
                            <br><small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                    <select name="curso_id" class="form-select" required>
                        <option value="{{$clientes->curso}}">Curso</option>
                        @foreach ($cursos as $row )
                        <option value="{{$row->id}}">{{$row->nombre}}</option>
                        @endforeach
                    </select>
                    @error('curso_id')
                        <small class="text-danger">{{$message}}</small>
                    @enderror
                </div>
                    <div class="d-grid col-6 mx-auto">
                        <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection