<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Redirect;
use PDOException;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function users()
    {
        try {
            
            $users = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as rol')
            //->where('roles.name', '!=', 'admin')
            ->get();
            $user = Auth::user();
            return view('users.users', ['users' => $users,'user' => $user]);

        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::channel('slackerror')->error('UserController@users (appuca) Error consulta SQL', [$e->getMessage()]);
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            Log::channel('slackerror')->error('UserController@users (appuca) Error PDO', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            Log::channel('slackerror')->error('UserController@users (appuca) Excepción no controlada', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }
    }

    public function delete($id)
    {
        try {

            $user = User::find($id);
            if ($user === null) {
                return abort(403);
            }
            $user->delete();

            return redirect('/users')->with(['msg' => "operacion realizada"]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::channel('slackerror')->error('UserController@delete (appuca) Error consulta SQL', [$e->getMessage()]);
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            Log::channel('slackerror')->error('UserController@delete (appuca) Error PDO', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            Log::channel('slackerror')->error('UserController@delete (appuca) Excepción no controlada', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }
    }

    public function show($id)
    {
        try {
            $user = User::find($id);
            if ($user === null) {
                return abort(403);
            }
            $roles = DB::table('roles')
            //->where('roles.name', '!=', 'admin')
            ->get();
            if ($roles === null) {
                return abort(403);
            }

            return view('users.userUpdate', ['user' => $user, 'roles' => $roles]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::channel('slackerror')->error('UserController@show (appuca) Error consulta SQL', [$e->getMessage()]);
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            Log::channel('slackerror')->error('UserController@show (appuca) Error PDO', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            Log::channel('slackerror')->error('UserController@show (appuca) Excepción no controlada', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }
    }

    public function put(Request $request, $id)
    {
        try {

            $user = User::find($id);

            if ($user->email == $request->input('email')) {
                $validacion = Validator::make($request->all(), [
                    'name' => ['required', 'string', 'regex:/^[A-Za-z\s]{4,50}$/'],
                    'telefono' => 'required|integer',
                    'rol' => 'required'
                ]);
                if ($validacion->fails()) {
                    return Redirect::back()
                        ->withErrors($validacion)
                        ->withInput();
                }
            } else {
                $validacion = Validator::make($request->all(), [
                    'name' => ['required', 'string', 'regex:/^[A-Za-z\s]{4,50}$/'],
                    'email' => 'required|email|unique:users',
                    'telefono' => 'required|integer',
                    'rol' => 'required'
                ]);
                if ($validacion->fails()) {
                    return Redirect::back()
                        ->withErrors($validacion)
                        ->withInput();
                }
            }

            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->telefono = $request->input('telefono');
            $user->syncRoles([$request->input('rol')]);

            $user->save();


            return redirect('/users')->with(['msg' => "operacion realizada"]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::channel('slackerror')->error('UserController@put (appuca) Error consulta SQL', [$e->getMessage()]);
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            Log::channel('slackerror')->error('UserController@put (appuca) Error PDO', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            Log::channel('slackerror')->error('UserController@put (appuca) Excepción no controlada', [$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }
    }}
