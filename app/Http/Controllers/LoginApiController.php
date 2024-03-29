<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Redirect;
use PDOException;
use Exception;

class LoginApiController extends Controller
{

    public function login(Request $request)
    {
        //Validacion 
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        //retornar errores
        if ($validator->fails()) {
            return response()->json([
                'mensaje' => $validator->errors(),
                'success' => false
            ], 401);
        }

        //Validar credenciales
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'mensaje' => 'Credenciales invalidas',
                'success' => false
            ], 401);
        }

        //Regresar si el usuario no es admin    
        if (!$user->hasRole('admin')) {
            return response()->json([
                'mensaje' => 'Acceso denegado',
                'success' => false
            ], 401);
        }

        return response()->json([
            'mensaje' => 'logeado',
            'user' => $user,
            'token' => $user->createToken('token')->plainTextToken,
            'success' => true
        ], 200);
    }

    public function Logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'mensaje' => 'Log out',
            'success' => true
        ]);
    }

    public function verificarCodigo(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'codigo' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'success' => false
            ], 401);
        }

        //Buscar al usuario
        $user = $request->user();

        //Verificar si el codigo que se mando por correo y se introdujo a la app es correcto
        if (password_verify($request->codigo, $user->codigo)) {
            // Generar Codigo Movil, que introducira en la app web
            // Generar numero aleatorio, convertirlo a string y hashear
            $random = sprintf("%04d", rand(0, 9999));
            $codigoMovil = strval($random); //convertir a string
            $codigo_hash = password_hash($codigoMovil, PASSWORD_DEFAULT);
            //Guardarlo en BD 
            $user->codigoMovil = $codigo_hash;
            $user->save();
            return response()->json([
                'mensaje' => 'Codigo verificado',
                'codigoMovil' => $codigoMovil,
                'success' => true
            ], 200);
        }

        return response()->json([
            'mensaje' => 'Codigo invalido',
            'success' => false
        ], 401);
    }
}
