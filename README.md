# DESCRIPCION DEL PROYECTO

- Este proyecto se realizó usando el patrón de diseño MVC en este caso para Api, se creo los siguientes archivos

### migración task
- para dar la estructura de la tabla tareas en la base de datos

### Modelo Task
- para la interacción con la tabla tareas

### controlador Task
- para el manejo de la lógica, realizar la CRUD y los filtros respectivos

## COMANDOS COMUNES
- php artisan make:model Task -rm
- php artisan make:controller Api/TaskController --api

# AUTENTICACION JWT

## LIBRERIA TYMON


- **instalar libreria tymon:**
```bash
composer require tymon/jwt-auth
```

- **Agreagar linea en app/config/app.php " Providers "**
```bash
'providers' => ServiceProvider::defaultProviders()->merge([
    //...
    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
])->toArray(),
```

- **Agreagar linea en app/config/app.php " Aliases "**
```bash
'aliases' => Facade::defaultAliases()->merge([
   //...
   'Jwt' => Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
   'JWTFactory' => Tymon\JWTAuth\Facades\JWTFactory::class,
   'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class,
])->toArray(),
```

- **Publique el archivo jwt.php (configuración de jwt). Ejecute este comando en la terminal:**
```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

- **Generar clave secreta jwt**
```bash
php artisan jwt:secret
```

## MODELO "USER"

- **Ejecutar migraciones**
```bash
php artisan migrate
```

- **Actualice User.php (archivo de clase de modelo de usuario). Abra el archivo User.php desde la carpeta /app/Models**

```bash
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
**use Tymon\JWTAuth\Contracts\JWTSubject;**

class User extends Authenticatable **implements JWTSubject**
{
    use HasApiTokens, HasFactory, Notifiable;

    // +++++++++ Resto del codigo ++++++++++


    // AGREGAR:

    public function getJWTIdentifier()
    {
      return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
      return [];
    }
}
```

## GUARDS

- **Abra el archivo auth.php desde la carpeta /config. Busque " guards ". Agregue estas líneas de código:**
```bash
'guards' => [
    //...
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

## CONTROLADOR

- **Crear controlador**
```bash
php artisan make:controller Auth/AuthController
```

- **Crear metodos y validaciones, ejemplo:**
```bash
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed"
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }
}
```

## PROTEGER RUTAS

- **Crear Rutas**
```bash
use App\Http\Controllers\Api\CiudadController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::post("register", [AuthController::class, "register"]);
Route::post("login", [AuthController::class, "login"]);

Route::group([
    "middleware" => ["jwt.routes"]
], function(){
    Route::resource("users", UserController::class);
    Route::get("logout", [AuthController::class, "logout"]);
    Route::resource("tasks",TaskController::class);
    Route::get('filter-task/{status_name}', [TaskController::class, "status_task"]);
    Route::get('task_user/{user_id}', [TaskController::class, "task_user"]);
});
```

- **Crear Middleware "JwtMiddleware"**
```bash
php artisan make:middleware JwtMiddleware
```
- **Ajustar metodo Handle, Ejemplo:**
```bash
public function handle(Request $request, Closure $next): Response
{
    try {
            JWTAuth::parseToken()->authenticate();
    } catch (Exception $e) {
        if ($e instanceof TokenInvalidException) {
            return response()->json(['status'=>'invalid token']);
        }
        if ($e instanceof TokenExpiredException) {
            return response()->json(['status'=>'expired token']);
        }

        return response()->json(['status'=>'token not found']);
    }
    return $next($request);
}
```

## KERNEL - app/http/kernel.php

- **Agrear linea  en middlewareAliases**
```bash
protected $middlewareAliases = [
    //...
    'jwt.routes' => \App\Http\Middleware\JwtMiddleware::class,
];
```