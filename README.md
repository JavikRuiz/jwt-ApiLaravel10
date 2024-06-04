# DESCRIPCION DEL PROYECTO

- Este proyecto se realizó usando el patrón de diseño MVC en este caso para Api, se creo los siguientes archivos

### migración task
- para dar la estructura de la tabla tareas en la base de datos

### Modelo Task
- para la interacción con la tabla tareas

### controlador Task
- para el manejo de la lógica, realizar la CRUD y los filtros respectivos

# COMANDOS COMUNES
- php artisan make:model Task -rm
- php artisan make:controller Api/TaskController --api

## AUTENTICACION JWT

### con la librería Tymon

- basada en la siguiente documentación:
- https://onlinewebtutorblog.com/laravel-10-restful-apis-with-jwt-authentication/