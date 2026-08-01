<?php

require_once __DIR__ . '/../models/User.php';

/**
 * Controlador encargado de la autenticación de usuarios.
 */
class AuthController
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLogin(): void
    {
        require_once __DIR__ . '/../views/auth/login.php';
    }
    /**
     * Muestra el formulario de registro de pacientes.
     */
    public function showRegister(): void
    {
        require_once __DIR__ . '/../views/auth/register.php';
    }
    /**
     * Procesa el registro de un nuevo paciente.
     */
    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';
        $privacyPolicy = $_POST['privacy_policy'] ?? null;

        $errors = [];

        if ($name === '') {
            $errors[] = 'Los nombres son obligatorios.';
        }

        if ($lastName === '') {
            $errors[] = 'Los apellidos son obligatorios.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo electrónico no es válido.';
        }

        if ($phone === '') {
            $errors[] = 'El número de teléfono es obligatorio.';
        }

        if (strlen($password) < 8) {
            $errors[] = 'La contraseña debe tener mínimo 8 caracteres.';
        }

        if ($password !== $passwordConfirmation) {
            $errors[] = 'Las contraseñas no coinciden.';
        }

        if ($privacyPolicy !== '1') {
            $errors[] = 'Debes aceptar la política de privacidad.';
        }

        $userModel = new User();

        if ($email !== '' && $userModel->emailExists($email)) {
            $errors[] = 'El correo electrónico ya está registrado.';
        }

        if ($errors !== []) {
            $_SESSION['register_errors'] = $errors;

            $_SESSION['register_old'] = [
                'name' => $name,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
            ];

            header('Location: /registro');
            exit;
        }

        $userId = $userModel->create([
            'role_id' => 3,
            'name' => $name,
            'last_name' => $lastName,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'phone' => $phone,
            'status' => 1,
        ]);

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $userId,
            'role_id' => 3,
            'name' => $name,
            'last_name' => $lastName,
            'email' => $email,
        ];

        $this->redirectByRole(3);
    }

    /**
     * Procesa el inicio de sesión.
     */
    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];

        if ($email === '') {
            $errors[] = 'El correo electrónico es obligatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo electrónico no es válido.';
        }

        if ($password === '') {
            $errors[] = 'La contraseña es obligatoria.';
        }

        if ($errors !== []) {
            $_SESSION['login_errors'] = $errors;
            $_SESSION['old_email'] = $email;

            header('Location: /login');
            exit;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (
            $user === null
            || !password_verify($password, $user['password'])
        ) {
            $_SESSION['login_errors'] = [
                'El correo o la contraseña son incorrectos.',
            ];

            $_SESSION['old_email'] = $email;

            header('Location: /login');
            exit;
        }

        if ((int) $user['estado'] !== 1) {
            $_SESSION['login_errors'] = [
                'El usuario se encuentra inactivo.',
            ];

            header('Location: /login');
            exit;
        }

        /**
         * Regeneramos el identificador para evitar
         * ataques de fijación de sesión.
         */
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => (int) $user['id_user'],
            'role_id' => (int) $user['id_rol'],
            'name' => $user['nom'],
            'last_name' => $user['ape'],
            'email' => $user['email'],
        ];

        $this->redirectByRole((int) $user['id_rol']);
    }
    /**
     * Cierra la sesión del usuario y lo redirige al login.
     */
    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $cookieParameters = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $cookieParameters['path'],
                $cookieParameters['domain'],
                $cookieParameters['secure'],
                $cookieParameters['httponly']
            );
        }

        session_destroy();

        header('Location: /login');
        exit;
    }
    /**
     * Redirige al usuario al panel correspondiente según su rol.
     */
    private function redirectByRole(int $roleId): void
    {
        $routes = [
            1 => '/administrador',
            2 => '/psicologo',
            3 => '/paciente',
        ];

        header('Location: ' . ($routes[$roleId] ?? '/'));
        exit;
    }
}