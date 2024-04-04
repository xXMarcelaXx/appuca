@extends('inicio')
@section("container")

<div class="row mt-3">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header bg-dark text-white">Editar curso</div>
            <div class="card-body">
                <form method="POST" action="{{url('putCursos',[$cursos])}}">
                    @method('PUT')
                    @csrf
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                        <input type="text" name="nombre" value="{{$cursos->nombre}}" class="form-control" placeholder="nombre" aria-label="Username" aria-describedby="basic-addon1">
                        @error('nombre')
                            <br><small class="text-danger">{{$message}}</small>
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