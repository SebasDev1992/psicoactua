# Contexto técnico de PsicoActúa

> Auditoría estática del estado de trabajo actual, realizada el 17 de agosto de 2026. Este documento se basa únicamente en los archivos presentes en el repositorio/directorio de trabajo. No se ejecutaron consultas contra MySQL, por lo que los nombres de tablas y columnas se documentan solo cuando el código los usa explícitamente; no se afirman esquemas, restricciones ni datos que no estén definidos en los archivos.

## Información general

- **Nombre:** `psicoactua` (campo `name` de `package.json`); la interfaz se presenta como **PsicoActúa** y, en algunas vistas, **Psicoagenda**.
- **Descripción observable:** plataforma de atención psicológica. Las vistas describen citas, historial y servicios de consultorio, pero esas capacidades no tienen implementación funcional aún.
- **Arquitectura:** aplicación PHP monolítica de front controller con MVC manual. Apache reescribe las rutas no físicas a `public/index.php`; este inicia sesión, crea `Router`, carga `routes/web.php` y despacha a un método de controlador.
- **MVC real:**
  - Los controladores están en `app/controllers/` y se cargan con `require_once` explícitos. No usan namespaces, Composer ni autoloading, y no extienden `core/controller.php` (el archivo está vacío).
  - `User` extiende la base `Model`, que entrega una instancia PDO. `Patient` es una clase independiente y no sigue esa base.
  - Las vistas están divididas entre `app/views/` y `resources/views/`. Solo la portada usa el layout compartido de `resources/views/layouts/app.php`; login, registro y dashboards son documentos HTML completos separados.
  - El middleware no se registra en `Router`; cada controlador protegido lo invoca manualmente.
- **Tecnologías y versiones detectables:**
  - PHP con PDO y sintaxis que requiere al menos PHP 8.0 (propiedades tipadas y tipos unión). No hay archivo que fije una versión exacta de PHP.
  - MySQL mediante el DSN `mysql:` de PDO; el charset configurado es `utf8mb4`. No hay versión de MySQL declarada.
  - Node.js/npm para compilar CSS; no se fija versión de Node.
  - Tailwind CSS `3.4.17`, PostCSS `8.5.15` y Autoprefixer `10.5.0` instalados según `package-lock.json`/`npm.cmd ls`.
  - Apache `mod_rewrite` es necesario para el enrutamiento bonito, según `public/.htaccess`.

## Estructura del proyecto

Árbol de archivos propios relevantes (se omite el contenido de `node_modules/`):

```text
psicoactua/
├── app/
│   ├── controllers/
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── PatientController.php
│   │   └── PsychologistController.php
│   ├── middleware/
│   │   ├── AuthMiddleware.php
│   │   └── RoleMiddleware.php
│   ├── models/
│   │   ├── Patient.php                 # no está versionado actualmente
│   │   └── User.php
│   └── views/
│       ├── admin/index.php
│       ├── auth/login.php
│       ├── auth/register.php
│       ├── patient/index.php
│       └── psychologist/index.php
├── config/
│   ├── app.php                         # vacío
│   ├── database.example.php
│   └── database.php                    # local, ignorado por Git
├── core/
│   ├── app.php                         # vacío
│   ├── auth.php                        # vacío
│   ├── controller.php                  # vacío
│   ├── database.php
│   ├── model.php
│   ├── router.php
│   └── view.php                        # vacío
├── database/
│   ├── seeders/                        # vacío
│   └── sql/                            # vacío
├── public/
│   ├── .htaccess
│   ├── index.php
│   └── assets/css/app.css              # CSS Tailwind generado; ignorado por Git
├── resources/
│   ├── css/app.css
│   └── views/
│       ├── components/{footer,navbar}.php
│       ├── home/index.php
│       └── layouts/app.php
├── routes/web.php
├── .gitignore
├── package.json / package-lock.json
├── postcss.config.js
├── README.md
├── tailwind.config.js
└── notas.text
```

No hay archivos de migraciones, DDL, seeds, pruebas automatizadas, Composer, JavaScript de aplicación ni archivos de rutas adicionales.

## Base de datos

### Configuración y conexión

- **Base de datos configurada:** `bd_psico_actua` en `config/database.php`.
- **Archivo de conexión efectivo:** `config/database.php`. Está presente localmente pero es ignorado por Git mediante `/config/database.php`; el archivo versionado de referencia es `config/database.example.php`.
- **Credenciales:** el archivo local contiene host `localhost`, puerto `3306`, usuario `root`, contraseña en texto plano y charset `utf8mb4`. Por seguridad, la contraseña literal no se reproduce aquí. El archivo de ejemplo deja la contraseña vacía.
- **Conexión estándar:** `core/database.php`, clase `Database`. Su constructor carga la configuración y construye:

```php
$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
    $config['host'], $config['port'], $config['database'], $config['charset']
);
$this->connection = new PDO($dsn, $config['username'], $config['password'], [...]);
```

  Configura `PDO::ERRMODE_EXCEPTION`, `PDO::FETCH_ASSOC` y `PDO::ATTR_EMULATE_PREPARES => false`. Si falla la conexión, termina la respuesta con el mensaje de la excepción.
- **Uso estándar por modelos:** `core/model.php` instancia `Database` en el constructor y guarda el PDO en `protected PDO $database`. `User` usa esa vía.
- **No hay conexión singleton:** cada instancia de un modelo que extiende `Model` crea un nuevo `Database` y, por tanto, un nuevo PDO.

### Tablas, columnas y relaciones que el código usa

El directorio `database/sql/` no contiene archivos y no hay migraciones ni DDL. Por tanto, no es posible verificar las tablas físicas, tipos, índices, PK o FK declaradas en MySQL. La siguiente tabla es el inventario exacto de identificadores SQL presentes:

| Tabla usada por código | Columnas mencionadas | Clave/relación observable | Evidencia |
|---|---|---|---|
| `users` | `id_user`, `id_rol`, `nom`, `ape`, `email`, `password`, `tel`, `estado` | `id_user` se usa como identificador del usuario. El código no declara su PK ni una FK real. | `app/models/User.php` |
| `pacientes` | `id_pac`, `id_user`, `fecha_nac`, `genero`, `direccion` | Se hace `INNER JOIN pacientes p ON p.id_user = u.id_user`. Esto demuestra la relación usada por la consulta, no que exista una restricción FK en la base. | `app/models/Patient.php` |

`id_rol` es usado en `users`, pero no hay una consulta ni un archivo de esquema que nombre una tabla de roles. No debe asumirse su nombre, PK ni relación declarada.

### Consultas SQL existentes

`User::findByEmail()` ejecuta una consulta preparada a `users` por `email`, seleccionando exactamente las ocho columnas de la tabla anterior y limitando el resultado a una fila:

```sql
SELECT id_user, id_rol, nom, ape, email, password, tel, estado
FROM users
WHERE email = :email
LIMIT 1
```

`User::create()` inserta un usuario:

```sql
INSERT INTO users (id_rol, nom, ape, email, password, tel, estado)
VALUES (:role_id, :name, :last_name, :email, :password, :phone, :status)
```

`Patient::findByUserId()` prepara un `SELECT` con el `INNER JOIN` documentado arriba y filtra `u.id_user = :id_user`, también con `LIMIT 1`.

No existen en el código consultas `UPDATE`, `DELETE`, DDL, pagos, citas, disponibilidad, historias clínicas ni reportes.

### Roles con valores importantes

Los valores no proceden de una tabla consultada; están codificados en `AuthController::redirectByRole()` y los controladores:

| ID | Nombre usado por el código/interfaz | Uso actual |
|---:|---|---|
| `1` | administrador | Redirección a `/administrador`; `AdminController` pide este valor. |
| `2` | psicólogo | Redirección a `/psicologo`; `PsychologistController` pide este valor. |
| `3` | paciente | Se asigna en el registro y redirige a `/paciente`; `PatientController` pide este valor. |

## Autenticación

### Sesión

`public/index.php` llama a `session_start()` cuando no hay una sesión activa. La clave de sesión creada tras registro o login es `$_SESSION['user']`:

```php
[
    'id' => /* id de users.id_user */,
    'role_id' => /* id de users.id_rol o 3 */,
    'name' => /* nom o nombre ingresado */,
    'last_name' => /* ape o apellido ingresado */,
    'email' => /* email */,
]
```

También se emplean claves flash manuales: `login_errors`, `old_email`, `register_errors` y `register_old`. Las vistas de login y registro las leen y las eliminan con `unset()` al renderizar.

### Registro: `POST /registro`

`AuthController::register()` lee `name`, `last_name`, `email`, `phone`, `password`, `password_confirmation` y `privacy_policy` de `$_POST`; aplica `trim()` a los campos de texto y normaliza el email con `strtolower()`.

Validaciones de servidor existentes:

- nombres, apellidos y teléfono no vacíos;
- email válido mediante `filter_var(..., FILTER_VALIDATE_EMAIL)`;
- contraseña de mínimo 8 caracteres;
- confirmación idéntica;
- `privacy_policy` exactamente igual a `'1'`;
- ausencia previa del email, comprobada con `User::emailExists()`.

Ante errores, conserva los cuatro campos no sensibles en `register_old`, guarda los textos de error en sesión, redirige a `/registro` y termina. Si pasa la validación:

1. inserta solo un registro `users` con `id_rol = 3` y `estado = 1`;
2. usa `password_hash($password, PASSWORD_DEFAULT)`;
3. regenera el identificador de sesión con `session_regenerate_id(true)`;
4. crea `$_SESSION['user']` con las claves indicadas;
5. llama a `redirectByRole(3)`, que envía a `/paciente`.

No hay creación de un registro en `pacientes`, ni validación de unicidad mediante una restricción de base de datos documentada, ni token CSRF.

### Login: `POST /login`

`AuthController::login()` valida que haya email y contraseña, y valida el formato del email. Luego busca con `User::findByEmail()`, compara el hash con `password_verify($password, $user['password'])`, y exige `(int) $user['estado'] === 1`.

Si alguna comprobación falla, establece `login_errors`, preserva `old_email` cuando corresponde y redirige a `/login`. Si es válida, regenera el ID de sesión, crea `$_SESSION['user']` con las mismas claves `id` y `role_id`, y redirige por `id_rol` obtenido de la fila SQL.

El checkbox `remember` existe solo en la vista; el controlador no lo lee ni crea una cookie persistente. El enlace `/recuperar-contrasena` se muestra, pero no hay ruta registrada para él.

### Logout: `POST /logout`

`AuthController::logout()` vacía `$_SESSION`, vence la cookie de sesión si se usan cookies, llama `session_destroy()`, redirige a `/login` y termina. No exige autenticación previa y no usa token CSRF.

### Middleware y redirecciones

- `AuthMiddleware::handle()` permite continuar solo si existe `$_SESSION['user']`. Si no existe, añade el error “Debes iniciar sesión…” a `login_errors`, redirige a `/login` y termina.
- `RoleMiddleware::handle(array $allowedRoles)` también comprueba la sesión y, después, compara un valor de sesión con los roles permitidos. En el estado actual lee `$_SESSION['user']['id_rol']`, aunque el controlador guarda `role_id`; esto se detalla como inconsistencia crítica más abajo.
- La tabla de redirección privada es `1 => /administrador`, `2 => /psicologo`, `3 => /paciente`, con `/` como alternativa para cualquier ID no mapeado.

## Roles y capacidades actuales

La intención de cada rol solo se ve en las tarjetas del dashboard; no existen rutas ni operaciones detrás de esas tarjetas (`href="#"`). Todos los controles de rol se ejecutan dentro del método `index()` correspondiente, no en una capa de routing.

| Rol | Ruta protegida | Contenido visible previsto | Lo que no está implementado |
|---|---|---|---|
| Administrador (1) | `/administrador` | Tarjetas: gestionar usuarios/psicólogos, citas/pagos/reportes, historias clínicas y disponibilidad. | Todas esas acciones, enlaces y modelos. Además, el middleware actual bloquea la sesión creada por login. |
| Psicólogo (2) | `/psicologo` | Tarjetas: agenda, pacientes, historias clínicas y disponibilidad. | Todas las acciones, enlaces, persistencia y rutas. Además, el middleware actual bloquea la sesión creada por login. |
| Paciente (3) | `/paciente` | Tarjetas: agendar cita, citas programadas, historial y perfil. | Todas las acciones y rutas; no se crea `pacientes` en el registro. La ruta también contiene un intento de consulta de paciente que no puede conectarse con la implementación presente. |

## Routing

`Router` solo admite registros exactos `GET` y `POST`. Usa `parse_url($uri, PHP_URL_PATH)`, por lo que ignora la query string. Si no encuentra la combinación método/ruta, devuelve HTTP 404 con HTML fijo. No soporta parámetros, grupos, nombres de rutas ni middleware registrado en el router.

| Método | URL | Controlador y método | Middleware aplicado realmente |
|---|---|---|---|
| GET | `/` | `HomeController::index` | `AuthMiddleware::handle()` dentro del método. |
| GET | `/login` | `AuthController::showLogin` | Ninguno. |
| POST | `/login` | `AuthController::login` | Ninguno. |
| POST | `/logout` | `AuthController::logout` | Ninguno. |
| GET | `/registro` | `AuthController::showRegister` | Ninguno. |
| POST | `/registro` | `AuthController::register` | Ninguno. |
| GET | `/paciente` | `PatientController::index` | `AuthMiddleware`, luego `RoleMiddleware::handle([3])`. |
| GET | `/psicologo` | `PsychologistController::index` | `AuthMiddleware`, luego `RoleMiddleware::handle([2])`. |
| GET | `/administrador` | `AdminController::index` | `AuthMiddleware`, luego `RoleMiddleware::handle([1])`. |

No existe ruta GET para `/logout`, recuperación de contraseña, perfiles, citas ni las tarjetas de dashboards.

## Controllers

| Controlador | Ubicación | Métodos / responsabilidad | Dependencias y rutas |
|---|---|---|---|
| `HomeController` | `app/controllers/HomeController.php` | `index()`: exige sesión, captura `resources/views/home/index.php` en buffer y lo inyecta en el layout. | `AuthMiddleware`; GET `/`. |
| `AuthController` | `app/controllers/AuthController.php` | `showLogin()`, `showRegister()`, `register()`, `login()`, `logout()` y privado `redirectByRole()`. Gestiona formularios, autenticación, sesión y redirección. | `User`; GET/POST `/login`, GET/POST `/registro`, POST `/logout`. |
| `PatientController` | `app/controllers/PatientController.php` | `index()`: intenta proteger por rol, obtener usuario/paciente y cargar vista. La variable `$patient` no se consume en la vista. | `Patient`, `AuthMiddleware`, `RoleMiddleware`; GET `/paciente`. |
| `PsychologistController` | `app/controllers/PsychologistController.php` | `index()`: protege por rol y carga el dashboard. | Ambos middleware; GET `/psicologo`. |
| `AdminController` | `app/controllers/AdminController.php` | `index()`: protege por rol y carga el dashboard. | Ambos middleware; GET `/administrador`. |

## Models

| Modelo | Ubicación | Métodos y consultas | Tablas/relaciones |
|---|---|---|---|
| `User extends Model` | `app/models/User.php` | `findByEmail(string): ?array` prepara el SELECT por email; `emailExists(string): bool` reutiliza la búsqueda; `create(array): int` inserta y devuelve `lastInsertId()`. | `users`; no carga relaciones. Usa PDO heredado de `Model`. |
| `Patient` | `app/models/Patient.php` | `static findByUserId(int): ?array` intenta preparar un SELECT de usuario con paciente y devolver una fila asociativa. | `users` y `pacientes`, unidos por `p.id_user = u.id_user`. No extiende `Model` y su conexión no coincide con `core/database.php`. |

No hay modelos para roles, psicólogos, citas, disponibilidad, historias clínicas, pagos, reportes ni administración de usuarios.

## Views / frontend

### Vistas y formularios

| Vista | Ubicación | Formularios/acciones y campos |
|---|---|---|
| Portada | `resources/views/home/index.php` mediante layout | Si hay sesión, muestra nombre y un formulario `POST /logout`; no tiene enlace de login. |
| Login | `app/views/auth/login.php` | Formulario `POST /login`: `email`, `password` y `remember`. Muestra errores de sesión. Enlace a `/registro` y enlace no enrutable a `/recuperar-contrasena`. |
| Registro | `app/views/auth/register.php` | Formulario `POST /registro`: `name`, `last_name`, `email`, `phone`, `password`, `password_confirmation`, `privacy_policy`. Muestra errores y valores antiguos no sensibles. |
| Paciente | `app/views/patient/index.php` | Formulario `POST /logout`; cuatro tarjetas sin navegación real. |
| Psicólogo | `app/views/psychologist/index.php` | Formulario `POST /logout`; cuatro tarjetas sin navegación real. |
| Administrador | `app/views/admin/index.php` | Formulario `POST /logout`; cuatro tarjetas sin navegación real. |

### Layout, componentes, CSS y responsive

- `resources/views/layouts/app.php` incluye `components/navbar.php`, el contenido capturado y `components/footer.php`. Solo `HomeController` lo utiliza.
- El navbar contiene cuatro enlaces `#` (Inicio, Servicios, Psicólogos y Contacto); el footer contiene texto institucional. No se incluyen en auth ni dashboards.
- El CSS fuente es `resources/css/app.css` con las tres directivas `@tailwind base`, `@tailwind components` y `@tailwind utilities`.
- `npm run dev` observa y compila a `public/assets/css/app.css`; `npm run build` compila la versión minificada a la misma ruta. El CSS resultante existe localmente y está ignorado por Git.
- `tailwind.config.js` analiza vistas de `resources`, `app`, controladores, `public` y `resources/js`. No existe actualmente `resources/js/` ni JavaScript de aplicación.
- Diseño responsive observable: login/registro usan una grilla de dos columnas a partir de `lg` y ocultan el panel informativo en tamaños menores; registro pasa campos a dos columnas desde `md`; dashboards usan `md:grid-cols-2` y `lg:grid-cols-4`.

## Middleware

| Middleware | Ubicación | Comportamiento exacto |
|---|---|---|
| `AuthMiddleware` | `app/middleware/AuthMiddleware.php` | Si falta `$_SESSION['user']`, fija un error de login, redirige con `header('Location: /login')` y `exit`; de lo contrario no hace nada. |
| `RoleMiddleware` | `app/middleware/RoleMiddleware.php` | Si falta sesión, redirige a login y termina. Convierte a entero `$_SESSION['user']['id_rol']` y lo busca estrictamente en `$allowedRoles`; si falla, responde 403 y termina con `Acceso no autorizado.` |

## Flujo actual de usuario

El flujo codificado pretende funcionar así, pero se interrumpe al llegar a los dashboards por las inconsistencias documentadas:

1. **Registro:** el visitante abre `GET /registro`, envía los datos a `POST /registro`; el controlador valida y crea un usuario activo con rol 3.
2. **Login:** el visitante abre `GET /login`, envía email/contraseña a `POST /login`; el controlador busca en `users`, valida hash y estado 1.
3. **Creación de sesión:** tanto registro como login regeneran el ID y crean `$_SESSION['user']` con `id`, `role_id`, `name`, `last_name`, `email`.
4. **Redirección por rol:** el mapeo usa 1/2/3 hacia administrador/psicólogo/paciente.
5. **Acceso a dashboard:** el controlador llama primero a `AuthMiddleware`, que sí detecta la sesión. Inmediatamente `RoleMiddleware` busca `id_rol`, clave que la sesión no contiene; el entero resultante es 0 y devuelve 403. En paciente no se alcanza la consulta `Patient` por ese motivo. Si se superara esa barrera, el controlador leería otro campo inexistente (`id_user`) y `Patient` intentaría llamar a una conexión no definida.
6. **Logout:** cualquiera puede enviar `POST /logout`; el controlador borra y destruye la sesión y vuelve a `/login`.

## Estado actual de cada módulo

| Módulo | Estado | Evidencia y alcance real |
|---|---|---|
| Autenticación | EN DESARROLLO | Controlador, hash, sesión y middleware existen; el acceso posterior por rol está bloqueado por inconsistencia de claves. |
| Registro | EN DESARROLLO | Inserta en `users` con validaciones; no crea perfil en `pacientes`. |
| Login | EN DESARROLLO | Consulta usuario, verifica contraseña y estado; sus redirecciones llegan a middleware inconsistente. |
| Logout | IMPLEMENTADO | Destruye sesión y cookie de sesión, y redirige. |
| Roles | EN DESARROLLO | IDs/rutas/controladores definidos, pero la clave de sesión comprobada no coincide con la almacenada. |
| Dashboard paciente | EN DESARROLLO | Ruta y vista existen; se bloquea por roles y la lógica de paciente tiene otra incompatibilidad. |
| Dashboard psicólogo | EN DESARROLLO | Ruta y vista existen; se bloquea por roles; tarjetas sin acciones. |
| Dashboard administrador | EN DESARROLLO | Ruta y vista existen; se bloquea por roles; tarjetas sin acciones. |
| Perfil paciente | NO IMPLEMENTADO | Solo tarjeta `href="#"`; no hay ruta, formulario, actualización ni vista específica. |
| Citas | NO IMPLEMENTADO | Solo texto de interfaz; sin tablas/modelos/rutas/consultas. |
| Disponibilidad de psicólogos | NO IMPLEMENTADO | Solo texto de interfaz; sin implementación. |
| Historias clínicas | NO IMPLEMENTADO | Solo texto de interfaz; sin implementación. |
| Pagos | NO IMPLEMENTADO | Solo texto de interfaz; sin implementación. |
| Reportes | NO IMPLEMENTADO | Solo texto de interfaz; sin implementación. |

## Git

- **Rama actual:** `feature/authentication`, en `b0af668`, que coincide con `origin/feature/authentication`.
- **Ramas locales:** `main`, `develop`, `feature/authentication`. También se detectan sus tres remotos `origin/*`.
- **Relación verificable:** `main` está en `bcff5e2` (“chore: estructura inicial MVC y configuracion de Tailwind”); `develop` está un commit después, en `5809c0c` (“feat: actualizar enrutador inicial”); `feature/authentication` contiene cuatro commits posteriores a `develop` y no hay merge commit que indique integración.
- **Commits recientes relevantes de la feature:**
  1. `9f9bd37 feat: implementar estructura inicial de autenticacion`
  2. `d5f469c feat: conectar inicio de sesion con base de datos`
  3. `6c780cc feat: implementar login registro y cierre de sesion`
  4. `b0af668 feat: implementar paneles y control de acceso por roles`
- **Árbol de trabajo al auditar:** modificado `app/controllers/PatientController.php`, modificado `app/middleware/RoleMiddleware.php`, y sin seguimiento `app/models/Patient.php`. Este documento describe esos archivos tal como están ahora, no solo lo confirmado en Git.
- `config/database.php` no está versionado porque `.gitignore` lo excluye; `config/database.example.php` sí está en Git. La feature elimina `config/database.php` del historial respecto a `develop`, mientras que el archivo local sigue presente para ejecución.

## Problemas o inconsistencias detectadas

| Archivo / ubicación | Problema observado | Posible impacto |
|---|---|---|
| `app/middleware/RoleMiddleware.php`, lectura de sesión en `handle()` | Lee `$_SESSION['user']['id_rol']`; `AuthController` solo guarda `role_id`. | Tras un login o registro válido, los dashboards devuelven 403 porque el valor ausente se convierte a 0. |
| `app/controllers/PatientController.php`, `index()` | Lee `$_SESSION['user']['id_user']`; la sesión usa `id`. | Si se llega a esa línea, busca usuario 0 en lugar del autenticado. |
| `app/models/Patient.php`, inicio y `findByUserId()` | Incluye `config/database.php`, que devuelve un array, pero llama a `Database::getConnection()`. La clase `Database` real está en `core/database.php` y solo expone un método de instancia `connection()`. | La ruta de paciente terminaría con clase `Database` no encontrada o llamada a método inexistente, según el contexto de carga. |
| `app/controllers/AuthController.php` frente a `app/models/Patient.php` | El registro crea solo `users`; no crea una fila `pacientes`, aunque `Patient` exige un `INNER JOIN` a esa tabla. | Aun con conexión corregida, un usuario recién registrado no obtendría perfil de paciente. |
| `app/controllers/PatientController.php` y `app/views/patient/index.php` | `$patient` se calcula (si llegara a hacerlo) pero nunca se usa en la vista. | La consulta no aporta contenido visible. |
| `app/views/psychologist/index.php`, `<title>` | El título dice “Panel del administrador” mientras el encabezado dice psicólogo. | Metadato/título del navegador incorrecto. |
| `app/views/auth/login.php`, comentario inicial | Indica que el formulario contiene solo interfaz y que se conectará después, pero sí está conectado a `AuthController::login()`. | Documentación interna desactualizada. |
| `app/views/auth/login.php`, enlace de recuperación | Apunta a `/recuperar-contrasena`, ruta inexistente. | La navegación llega a 404. |
| Dashboards y `resources/views/components/navbar.php` | Las tarjetas y enlaces del navbar usan `href="#"`. | No hay navegación a módulos anunciados. |
| `HomeController::index()` | La portada `/` exige autenticación, aunque su vista contiene una condición para el caso de sesión ausente. | El contenido para visitantes no autenticados es inalcanzable por esta ruta. |
| `config/database.php` / `core/database.php` | Hay contraseña local en texto plano y, ante error PDO, se imprime `PDOException` al cliente. | Riesgo de exposición de secretos y detalles de infraestructura. |
| Formularios POST y controladores | No hay validación CSRF; `remember` no se implementa; no se observan limitación de intentos, recuperación de contraseña ni rotación de credenciales. | Riesgos de seguridad y funciones de interfaz que no tienen efecto. |
| `database/sql/` y `database/seeders/` | Directorios presentes pero vacíos; no hay esquema versionado. | No puede reconstruirse ni verificarse el esquema de la base desde el repositorio. |
| Controladores/modelos base en `core/` | `app.php`, `auth.php`, `controller.php` y `view.php` están vacíos; los controladores no usan una base común. | La arquitectura es parcialmente esbozada y la responsabilidad está dispersa. |

La sintaxis de los 30 archivos PHP actuales fue verificada con `php -l` y no reportó errores de sintaxis. Eso no resuelve los errores de ejecución anteriores, que dependen de rutas de código, sesión y conexión.

## Próximo desarrollo recomendado

Orden sugerido, basado en las dependencias reales detectadas:

1. Definir y versionar, sin exponer secretos, el esquema/migraciones de las dos tablas ya referenciadas (`users` y `pacientes`) y sus restricciones reales antes de introducir tablas nuevas.
2. Establecer un contrato único y comprobado para la estructura de `$_SESSION['user']`, y alinear los consumidores actuales de autenticación, roles y paciente con ese contrato.
3. Consolidar el acceso de `Patient` sobre la conexión PDO existente y decidir/documentar en qué flujo se crea el perfil `pacientes` para los registros de rol 3.
4. Probar de extremo a extremo registro, login, autorización de cada rol, dashboard y logout antes de abrir módulos nuevos.
5. Implementar primero perfil de paciente y la base de psicólogos/disponibilidad necesaria para que las citas tengan entidades y reglas consistentes.
6. Construir citas y su gestión por roles; después historias clínicas, pagos y reportes, en ese orden de dependencia funcional.
7. Sustituir los enlaces placeholder por rutas, controladores, validación y pruebas conforme cada módulo exista; atender las medidas de seguridad indicadas antes de exponer formularios sensibles.

## REGLAS PARA CONTINUAR EL DESARROLLO

- No inventar nombres de tablas, columnas, claves, rutas, clases o relaciones; confirmar primero en el esquema versionado o el código existente.
- Reutilizar `core/database.php`/`Model` como conexión actual o documentar una migración deliberada; no crear una segunda conexión paralela sin necesidad.
- Mantener MVC manual actual mientras no se planifique una migración arquitectónica completa: rutas, controladores, modelos y vistas deben permanecer separados.
- Mantener los nombres actuales que el código ya usa (`users`, `pacientes`, `id_user`, `id_rol`, etc.) hasta que un cambio de esquema coordinado los migre.
- No asumir que una relación inferida por un `JOIN` es una FK de base de datos; comprobarla en SQL/migraciones.
- Probar cada funcionalidad de forma integrada antes de continuar con otra, especialmente sesión, autorización y acceso PDO.
- No modificar `main` directamente; mantener el flujo `feature -> develop -> main` que refleja la estructura de ramas observada.
- Mantener `config/database.php` como configuración local ignorada y evitar registrar secretos; actualizar el ejemplo cuando cambie el contrato de configuración.
- Registrar middleware de forma coherente y usar exactamente el mismo contrato de sesión en autenticación, roles, controladores y vistas.
- No presentar tarjetas o enlaces como funcionales hasta que existan sus rutas y operaciones reales.
