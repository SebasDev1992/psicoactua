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

        header('Location: /');
        exit;
    }
}
