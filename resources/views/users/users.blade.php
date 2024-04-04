@extends('inicio')
@section("container")

@if ($message = Session::get('msg'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>{{$message}}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif



<div class="row mt-3">
    <div class="col-12 col-lg-8 offset-0 offset-lg-2">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-primary">
                <thead><tr><th>#</th><th>NOMBRE</th><th>CORREO</th><th>TELEFONO</th><th>ROL</th><th>ELIMINAR</th><th>ACTUALIZAR</th></tr></thead>
                <tbody class="table-group-divider">
                    @foreach ($users as $row)
                        <tr>
                            <td>{{$row->id}}</td>
                            <td>{{$row->name}}</td>
                            <td>{{$row->email}}</td>
                            <td>{{$row->telefono}}</td>
                            <td>{{$row->rol}}</td>
                            <td>
                                <form method="POST" action="{{url('deleteUser',$row->id)}}">
                                    @method("delete")
                                    @csrf
                                    <button class="btn btn-danger">Eliminar</button>
                                </form>
                            </td>
                            <td>
                                <a href="{{url('user',$row->id)}}"class="btn btn-warning data-bs-toggle=">Actualizar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>



@endsection