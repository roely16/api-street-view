@component('mail::message')
# Registro de Nuevo Usuario

Se ha generado una solicitud para la creación de un nuevo usuario con los siguientes datos:

@component('mail::table')
| Campo       | Descripción         | 
| ------------- |:-------------:| 
| Nombre      |  {{ $usuario->nombre }}     | 
| Dependencia      | {{ $usuario->dependencia }} |
| Email      | {{ $usuario->email }} |
| Teléfono      | {{ $usuario->telefono }} |
@endcomponent

{!!$confirm!!}

@endcomponent