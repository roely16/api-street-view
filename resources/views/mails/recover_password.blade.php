@component('mail::message')
# Recuperación de Contraseña

Estimado usuario.  Se ha recibido una petición para la recuperación de la contraseña de la cuenta registrada con el correo {{ $email }}.

@component('mail::panel')
Contraseña: **{{ $password }}**
@endcomponent

@endcomponent