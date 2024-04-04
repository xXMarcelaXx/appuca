<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Cliente;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Redirect;
use PDOException;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class ClienteController extends Controller
{

    public function clientes()
    {
        try {
            $clientes = DB::table('cursos')
                ->join('clientes', 'cursos.id', '=', 'clientes.curso_id')
                ->select('clientes.*', 'cursos.nombre as curso', 'cursos.id as id_curso')
                ->get();
            $cursos = Curso::all();
            $user = Auth::user();
            return view('clientes.clientes', ['clientes' => $clientes, 'cursos' => $cursos, 'user' => $user]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::channel('slackerror')->error('ClientesController@clientes (appuca) Error consulta SQL', [$e->getMessage()]);
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            Log::channel('slackerror')->error('ClientesController@clientes (appuca) Error PDO', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            Log::channel('slackerror')->error('ClientesController@clientes (appuca) Excepción no controlada', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }
    }

    public function post(Request $request)
    {
        try {
            $validacion = Validator::make($request->all(), [
                'nombre'  => 'required',
                'edad'    => 'required|integer',
                'telefono' => 'required|integer',
                'correo'  => 'required|email|unique:clientes',
                'curso_id' => 'required'
            ]);
            if ($validacion->fails()) {
                return redirect('clientes')
                    ->withErrors($validacion)
                    ->withInput();
            }

            $cliente = new Cliente();
            $cliente->nombre = $request->input('nombre');
            $cliente->edad = $request->input('edad');
            $cliente->telefono = $request->input('telefono');
            $cliente->correo = $request->input('correo');
            $cliente->curso_id = $request->input('curso_id');
            $cliente->save();


            return redirect('/clientes')->with(['msg' => "operacion realizada"]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::channel('slackerror')->error('ClientesController@post (appuca) Error consulta SQL', [$e->getMessage()]);
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            Log::channel('slackerror')->error('ClientesController@post (appuca) Error PDO', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            Log::channel('slackerror')->error('ClientesController@post (appuca) Excepción no controlada', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }
    }


    public function delete($id)
    {
        try {
            $cliente = Cliente::find($id);
            if ($cliente === null) {
                return abort(403);
            }
            $cliente->delete();

            return redirect('/clientes')->with(['msg' => "operacion realizada"]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::channel('slackerror')->error('ClientesController@delete (appuca) Error consulta SQL', [$e->getMessage()]);
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            Log::channel('slackerror')->error('ClientesController@delete (appuca) Error PDO', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            Log::channel('slackerror')->error('ClientesController@delete (appuca) Excepción no controlada', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }
    }

    public function show($id)
    {
        try {
            $clientes = Cliente::find($id);
            if ($clientes === null) {
                return abort(403);
            }
            $curso = Curso::all();
            if ($curso === null) {
                return abort(403);
            }
            $user = Auth::user();

            return view('clientes.clientesUpdate', ['clientes' => $clientes, 'cursos' => $curso, 'user' => $user]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::channel('slackerror')->error('ClientesController@show (appuca) Error consulta SQL', [$e->getMessage()]);
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            Log::channel('slackerror')->error('ClientesController@show (appuca) Error PDO', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            Log::channel('slackerror')->error('ClientesController@show (appuca) Excepción no controlada', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }
    }

    public function put(Request $request, $id)
    {
        try {
            $validacion = Validator::make($request->all(), [
                'nombre'  => 'required',
                'edad'    => 'required|integer',
                'telefono' => 'required|integer',
                'correo'  => 'required|email',
                'curso_id' => 'required'
            ]);
            if ($validacion->fails()) {
                return redirect('putCliente')
                    ->withErrors($validacion)
                    ->withInput();
            }


            $cliente = Cliente::find($id);
            $cliente->nombre = $request->input('nombre');
            $cliente->edad = $request->input('edad');
            $cliente->telefono = $request->input('telefono');
            $cliente->correo = $request->input('correo');
            $cliente->curso_id = $request->input('curso_id');
            $cliente->save();


            return redirect('/clientes')->with(['msg' => "operacion realizada"]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::channel('slackerror')->error('ClientesController@put (appuca) Error consulta SQL', [$e->getMessage()]);
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            Log::channel('slackerror')->error('ClientesController@put (appuca) Error PDO', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            Log::channel('slackerror')->error('ClientesController@put (appuca) Excepción no controlada', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }
    }
}
