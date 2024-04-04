@extends('inicio')
@section("container")

@if ($message = Session::get('msg'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>{{$message}}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@role('admin|coordinador')
<div class="row mt-3">
    <div class="col-12 col-lg-8 offset-0 offset-lg-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Añadir
        </button>
    </div>
</div>
@endrole


<div class="row mt-3">
    <div class="col-12 col-lg-8 offset-0 offset-lg-2">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-primary">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NOMBRE</th>
                        <th>EDAD</th>
                        <th>TELEFONO</th>
                        <th>CORREO</th>
                        <th>CURSO</th>
                        @role('admin|coordinador')
                        <th>ELIMINAR</th>
                        <th>ACTUALIZAR</th>
                        @endrole
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @foreach ($clientes as $row)
                    <tr>
                        <td>{{$row->id}}</td>
                        <td>{{$row->nombre}}</td>
                        <td>{{$row->edad}}</td>
                        <td>{{$row->telefono}}</td>
                        <td>{{$row->correo}}</td>
                        <td>{{$row->curso}}</td>
                        @role('admin|coordinador')
                        <td>
                            <form method="POST" action="{{url('deleteCliente',$row->id)}}">
                                @method("delete")
                                @csrf
                                <button class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                        <td>
                            <a href="{{url('cliente',$row->id)}}" class="btn btn-warning data-bs-toggle=">Actualizar</a>
                        </td>
                        @endrole
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>



<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Añadir clientes</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{url('postClientes')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                        <input type="text" name="nombre" class="form-control" placeholder="nombre" aria-label="Username" aria-describedby="basic-addon1" required>
                        @error('nombre')
                        <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                        <input type="number" name="edad" class="form-control" placeholder="edad" aria-label="Username" aria-describedby="basic-addon1" required>
                        @error('edad')
                        <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                        <input type="number" name="telefono" class="form-control" placeholder="telefono" aria-label="Username" aria-describedby="basic-addon1" required>
                        @error('telefono')
                        <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                        <input type="email" name="correo" class="form-control" placeholder="correo" aria-label="Username" aria-describedby="basic-addon1" required>
                        @error('correo')
                        <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                        <select name="curso_id" class="form-select" required>
                            <option value="">Curso</option>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


@endsection