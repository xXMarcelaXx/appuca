<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use PDOException;
use Throwable;

class Handler extends ExceptionHandler
{
    
    
    
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>, \Psr\Log\<LogLevel::*> 
     */
    protected $levels = [
        //
    ];

    public function re(Exception $e)
    {
        parent::report($e);
    }

    public function render($request, Throwable $e)
    {
        if($e instanceof PDOException || $e instanceof QueryException){
            Log::channel('slackerror')->error('Handler (appuca) ERROR EN BASE DE DATOS',[$e->getMessage()]);
            return response()->view('error', [], 500);
        }
        if($e instanceof \Illuminate\Session\TokenMismatchException){
            Log::channel('slackerror')->error('Handler (appuca) ERROR EN BASE DE DATOS CON LA SESSION',[$e->getMessage()]);
            return redirect()->back()->withErrors(['errors' => 'Error interno del servidor. Por favor, inténtelo de nuevo más tarde.']);
        }

        return parent::render($request, $e);

    }
    
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
