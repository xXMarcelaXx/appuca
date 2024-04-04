<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Redirect;
use PDOException;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CursoController extends Controller
{
    public function verCursos()
    {
        try {

            $user = Auth::user();
            return view('cursos.cursos')->with(['cursos' => Curso::all(), 'user' => $user]);

        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::channel('slackerror')->error('CursoController@verCursos (appuca) Error consulta SQL', [$e->getMessage()]);
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            Log::channel('slackerror')->error('CursoController@verCursos (appuca) Error PDO', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            Log::channel('slackerror')->error('CursoController@verCursos (appuca) Excepción no controlada', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }
    }

    public function post(Request $request)
    {
        try {

            $request->validate([
                'nombre' => 'required'
            ]);

            $curso = new Curso();
            $curso->nombre = $request->input('nombre');
            $curso->save();
            return redirect('/verCursos')->with(['msg' => "operacion realizada"]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::channel('slackerror')->error('CursoController@post (appuca) Error consulta SQL', [$e->getMessage()]);
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            Log::channel('slackerror')->error('CursoController@post (appuca) Error PDO', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            Log::channel('slackerror')->error('CursoController@post (appuca) Excepción no controlada', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }
    }

    public function show($id)
    {
        $cursos = Curso::find($id);
        $user = Auth::user();
        return view('cursos.cursosUpdate', compact('cursos'))->with(['user' => $user]);
    }

    public function put(Request $request, $id)
    {
        $validacion = $request->validate([
            'nombre' => 'required',
        ]);

        if (!$validacion) {
            return redirect('/putCursos');
        } else {
            $curso = Curso::find($id);
            $curso->nombre = $request->input('nombre');
            $curso->save();
            return redirect('/verCursos');
        }
    }

    public function delete($id)
    {
        $curso = Curso::find($id);
        $curso->delete();

        return redirect('/verCursos')->with(['msg' => "operacion realizada"]);
    }

    /*CLIENTE CURSO */

    public function show2($id)
    {
        $clientes = DB::table('cursos')
            ->join('clientes', 'cursos.id', '=', 'clientes.curso_id')
            ->select('clientes.*', 'cursos.nombre as curso', 'cursos.id as id_curso')
            ->where('cursos.id', '=', $id)
            ->get();
        $curso = DB::table('cursos')
            ->select('cursos.*')
            ->where('cursos.id', '=', $id)
            ->get();
        $user = Auth::user();
        return view('cursos.verClientes', ['clientes' => $clientes, 'curso' => $curso, 'user' => $user]);
    }

    public function deleteClienteCurso($id)
    {
        $cliente = Cliente::find($id);
        $curso = DB::table('cursos')
            ->join('clientes', 'cursos.id', '=', 'clientes.curso_id')
            ->select('cursos.id')
            ->where('clientes.id', '=', $id)
            ->get();
        $cliente->delete();

        foreach ($curso as $cursoo) {
            $id_curso = $cursoo->id;
        }


        return redirect()->route('ver', [$id_curso])->with(['msg' => "operacion realizada"]);
    }

    public function postClienteCurso(Request $request)
    {
        $request->validate([
            'nombre'  => 'required',
            'edad'    => 'required|integer',
            'telefono' => 'required|integer',
            'correo'  => 'required|email|unique:clientes',
            'curso_id' => 'required'
        ]);

        $cliente = new Cliente();
        $cliente->nombre = $request->input('nombre');
        $cliente->edad = $request->input('edad');
        $cliente->telefono = $request->input('telefono');
        $cliente->correo = $request->input('correo');
        $cliente->curso_id = $request->input('curso_id');
        $cliente->save();

        $id = $request->input('curso_id');


        return redirect()->route('ver', [$id])->with(['msg' => "operacion realizada"]);
    }
}
