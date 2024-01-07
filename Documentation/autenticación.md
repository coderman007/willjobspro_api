# Documentación API de AuthController

## Registrar Usuario

Registra un nuevo usuario con los detalles proporcionados.

### Endpoint

`POST /api/register`

### Solicitud

```json
{
  "name": "Juan Pérez",
  "email": "juanito.perez@example.com",
  "password": "contraseñaSecreta"
}
Respuesta
json
Copy code
{
  "message": "Usuario creado correctamente!",
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juanito.perez@example.com",
    "created_at": "2023-01-01T12:00:00Z",
    "updated_at": "2023-01-01T12:00:00Z"
  },
  "token": "plainTextToken"
}
Iniciar Sesión
Autentica a un usuario existente con el email y contraseña proporcionados.

Endpoint
POST /api/login

Solicitud
json
Copy code
{
  "email": "juanito.perez@example.com",
  "password": "contraseñaSecreta"
}
Respuesta
json
Copy code
{
  "message": "Usuario logueado correctamente!",
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juanito.perez@example.com",
    "created_at": "2023-01-01T12:00:00Z",
    "updated_at": "2023-01-01T12:00:00Z"
  },
  "token": "plainTextToken"
}
Obtener Perfil de Usuario
Recupera la información del perfil del usuario autenticado, incluyendo sus roles.

Endpoint
GET /api/profile

Cabeceras
Authorization: Bearer plainTextToken
Respuesta
json
Copy code
{
  "message": "Perfil del usuario obtenido correctamente",
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juanito.perez@example.com",
    "created_at": "2023-01-01T12:00:00Z",
    "updated_at": "2023-01-01T12:00:00Z"
  },
  "roles": ["candidate"]
}
Cerrar Sesión
Cierra la sesión del usuario autenticado revocando sus tokens de acceso.

Endpoint
GET /api/logout

Cabeceras
Authorization: Bearer plainTextToken
Respuesta
json
Copy code
{
  "message": "Sesión cerrada correctamente"
}
Nota: Incluye la cabecera Authorization con el token Bearer en las cabeceras de cada solicitud autenticada.