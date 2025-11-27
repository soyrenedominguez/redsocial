# Red Social

Una aplicación web de red social desarrollada con PHP, MySQL y JavaScript. Este proyecto permite a los usuarios registrarse, iniciar sesión, crear publicaciones, comentar y gestionar sus perfiles.

## Características Principales

- **Autenticación**: Sistema de registro (Sign Up) e inicio de sesión (Login).
- **Publicaciones**: Los usuarios pueden crear nuevas publicaciones y ver las de otros usuarios.
- **Comentarios**: Posibilidad de agregar comentarios a las publicaciones.
- **Perfiles**: Visualización y edición de información de perfil de usuario.
- **API REST**: Backend estructurado con endpoints PHP para manejar las solicitudes.

## Estructura del Proyecto

- `backend/`: Contiene la lógica del servidor y la conexión a la base de datos.
  - `api/`: Endpoints para las operaciones (login, register, posts, etc.).
  - `config/`: Configuración de la base de datos.
  - `models/`: Modelos de datos (User, Post, Comment).
- `assets/`: Archivos estáticos (JS, CSS, imágenes).
- `bd.sql`: Script SQL para la creación de la base de datos.
- `*.html`: Vistas de la aplicación (Login, Registro, Inicio, Perfil).

## Requisitos

- Servidor Web (Apache/Nginx)
- PHP 7.4 o superior
- MySQL

## Instalación

1. Clona este repositorio.
2. Importa el archivo `bd.sql` en tu gestor de base de datos MySQL.
3. Configura las credenciales de la base de datos en `backend/config/Database.php` si es necesario.
4. Ejecuta el proyecto desde tu servidor local (ej. XAMPP) accediendo a `index.html` o `login.html`.

