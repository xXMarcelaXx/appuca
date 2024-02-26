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

class LoginController extends Controller
{

    public function registro()
    {
        return view('registro');
    }

    public function registrarUsuario(Request $request)
    {
        try {
            // Verificar el reCAPTCHA
            $recaptchaResponse =  $request->input('g-recaptcha-response');
            $recaptchaSecret = '6Leeyl4pAAAAAD_1KhmEwnITeh4-jD-lFMuv-ts0';

            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $recaptchaResponse,
            ]);

            $recaptchaData = $response->json();

            if (!$recaptchaData['success']) {
                // El reCAPTCHA no se validó correctamente
                $message = 'Error en validacion de recaptcha formulario de registro';
                Log::warning($message);
                Log::channel('slackwarning')->warning(
                    'LoginController@registrarUsuario (appuca) Error validacion reCAPTCHA formulario de registro',
                    [$request->all()]
                );
                return response()->json([
                    "Status" => 403,
                    "msg" => "Acceso no autorizado",
                ], 403);
            }

            //Validaciones
            $validacion = Validator::make($request->all(), [
                'name' => ['required', 'string', 'regex:/^[A-Za-z\s]{4,50}$/'],
                'email' => 'required|email|unique:users',
                'password' => ['required', 'string', 'min:10', 'max:16', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{10,16}$/',],
                'telefono' => 'required|integer'
            ]);

            if ($validacion->fails()) {
                return redirect('registro')
                    ->withInput()
                    ->withErrors($validacion);
            }

            //Agregar usuario admin o visitante
            $tablaVacia = User::count() === 0;

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->telefono = $request->telefono;
            $user->status = true;
            if ($tablaVacia) {
                $user->assignRole('admin');
            } else {
                $user->assignRole('visitante');
            }
            $user->password = Hash::make($request->password);

            if ($user->save()) {
                if ($user->hasRole('admin')) {
                    Log::channel('slackwarning')->warning('Se registro un nuevo usuario administrador', [$user]);
                    return view('login');
                }
                Log::channel('slackinfo')->info('Se registro un nuevo usuario', [$user]);
                return view('login');
            }
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            Log::channel('slackerror')->error('LoginController@registrarUsuario (appuca) Error consulta SQL',[$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            Log::channel('slackerror')->error('LoginController@registrarUsuario (appuca) Error PDO',[$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            Log::channel('slackerror')->error('LoginController@registrarUsuario (appuca) Excepcion no controlada',[$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }
    }

    public function iniciarSesion()
    {
        return view('login');
    }
    public function login(Request $request)
    {
        try {
            // Verificar el reCAPTCHA
            $recaptchaResponse =  $request->input('g-recaptcha-response');
            $recaptchaSecret = '6Leeyl4pAAAAAD_1KhmEwnITeh4-jD-lFMuv-ts0';

            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $recaptchaResponse,
            ]);

            $recaptchaData = $response->json();

            if (!$recaptchaData['success']) {
                // El reCAPTCHA no se validó correctamente
                $message = 'Error en validacion de recaptcha formulario de registro';
                Log::warning($message);
                Log::channel('slackwarning')->warning(
                    'LoginController@login (appuca) Error validacion reCAPTCHA formulario de login',
                    [$request->all()]
                );
                return response()->json([
                    "Status" => 403,
                    "msg" => "Acceso no autorizado",
                ], 403);
            }

            $validacion = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validacion->fails()) {
                return redirect('iniciarSesion')
                    ->withInput()
                    ->withErrors($validacion);
            }


            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = User::where("email", "=", $request->email)->first();
                if ($user->hasRole('admin')) {

                    $url = URL::temporarySignedRoute('mandarSMS', now()->addMinutes(10), [
                        'id' => $user->id
                    ]);
                    Log::channel('slackinfo')->info('LoginController@login (appuca) Se genero ruta firmada para logeo de usuario admin',[$user]);

                    return view('mandarSMS')->with('url', $url);
                }
                $message = 'Se logeo un usuario visitante con id: ' . $user->id;
                Log::info($message);
                Log::channel('slackinfo')->info('LoginController@login (appuca) Se logeo usuario visitante',[$user]);
                $request->session()->regenerate();
                return redirect()->route('index', ['id' => $user->id]);
            } else {
                return redirect('iniciarSesion')->withErrors(['errors' => 'Las credenciales proporcionadas son incorrectas.']);
            }
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            Log::channel('slackerror')->error('LoginController@login (appuca) Error de consulta SQL',[$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            Log::channel('slackerror')->error('LoginController@login (appuca) Error PDO',[$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            Log::channel('slackerror')->error('LoginController@login (appuca) Excepcion no controlada',[$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }
    }

    public function mandarSMS($id, Request $request)
    {
        try {

            if (!$request->hasValidSignature()) {
                $message = 'Alguien intenta acceder al modulo mandarSMS sin ruta frimada valida';
                Log::warning($message);
                Log::channel('slackwarning')->warning('LoginController@mandarSMS (appuca) Alguien intenta acceder al modulo mandarSMS sin ruta frimada valida',[$id ,$request->all()]);
                abort(401);
            }
            $user = User::find($id);
            if ($user === null) {
                return redirect('iniciarSesion')->withErrors(['errors' => 'No tienes acceso a esa página. Inicia sesión para continuar.']);
            }
            $response = Http::withBasicAuth('5140e05f', 'pa0wgQ6Z8lEi4jBk')->post(
                "https://api.nexmo.com/v2/verify",
                [
                    'brand' => 'Verifica Usuario',
                    'workflow' => [[
                        'channel' => "sms",
                        'to' => '528714389101',
                    ]],
                    'locale' => 'es-mx',
                    "channel_timeout" => 300,
                    "code_length" => 4
                ]
            );
            if ($response->successful()) {
                Log::channel('slackinfo')->info('LoginController@mandarSMS (appuca) se mando correctamente el codigo SMS',[$id,$request->all()]);
                return view('validarSMS')->with('request_id', $response->object('request')->request_id)->with('id', $user->id);
            }
            Log::channel('slackerror')->error('LoginController@mandarSMS (appuca) no se mando correctamente el codigo SMS',[$id,$request->all()]);
            return abort(401);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            Log::channel('slackerror')->error('LoginController@mandarSMS (appuca) Error consulta SQL',[$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            Log::channel('slackerror')->error('LoginController@mandarSMS (appuca) Error PDO',[$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            Log::channel('slackerror')->error('LoginController@mandarSMS (appuca) Excepcion no controlada',[$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }
    }
    public function validarCodigo(Request $request)
    {
        try {
            $validacion = Validator::make($request->all(), [
                'code' => 'required',
            ]);
            if ($validacion->fails()) {
                return redirect('validarSMS')
                    ->withErrors($validacion)
                    ->withInput();
            }
            $user = User::find($request->id);
            $request_id = $request->request_id;
            $response = Http::withBasicAuth('5140e05f', 'pa0wgQ6Z8lEi4jBk')->post(
                "https://api.nexmo.com/v2/verify/$request_id",
                [
                    "code" => $request->code
                ]
            );
            if ($response->ok()) {
                $message = 'Inicio sesion un usuario administrador con id: ' . $user->id;
                Log::info($message);
                Log::channel('slackinfo')->info('LoginController@validarCodigo (appuca) inicio sesion un usuario admin',[$user]);  
                $request->session()->regenerate();
                return redirect()->route('index', ['id' => $user->id]);
            }
            Log::channel('slackerror')->error('LoginController@validarCodigo (appuca) ocurrio un problema al validar codigoSMS con usuario admin',[$user]);
            return response()->json("Ocurrio algo", 400);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::channel('slackerror')->error('LoginController@validarCodigo (appuca) Error consulta SQL',[$e->getMessage()]);
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            Log::channel('slackerror')->error('LoginController@validarCodigo (appuca) Error PDO',[$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            Log::channel('slackerror')->error('LoginController@validarCodigo (appuca) Excepción no controlada',[$e->getMessage()]);
            return Redirect::back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }
    }
    public function index($id)
    {
        $user = User::find($id);
        if ($user === null) {
            Log::channel('slackwarning')->warning('LoginCOntroller@index (appuca) alguien intenta acceder a la vista de otro usuario desde la ruta',[$id]);
            return Redirect::back();
        }
        Log::channel('slackinfo')->info('LoginController@index (appuca) Usuario entro a la app',[$user]);
        return view('index')->with('user', $user);
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        return redirect('iniciarSesion');
    }


    public function prueba(Request $request)
    {
        Log::channel('slackerror')->error('appuca', [$request->all()]);

        Log::channel('slackinfo')->info('appuca', [$request->all()]);

        Log::channel('slackwarning')->warning('appuca', [$request->all()]);

        return response()->json([
            "Status" => 200,
            "msg" => "Se ha registrado un nuevo usuario",
        ], 200);
    }
}
