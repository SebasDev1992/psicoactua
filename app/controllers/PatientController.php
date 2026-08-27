<?php
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
/**
 * Controlador del panel del paciente.
 */
class PatientController
{
    /**
     * Muestra el panel principal del paciente.
     */
    public function index(): void
    {
        // Ambas comprobaciones impiden mostrar este panel a visitantes u otros roles.
        AuthMiddleware::handle();
        RoleMiddleware::handle([3]);

        // Usa el identificador de sesión para obtener el registro relacionado del paciente.
        $userId = (int) $_SESSION['user']['id'];

        // El modelo consulta los datos del paciente usando la conexión heredada.
        $patientModel = new Patient();

        $patient = $patientModel->findByUserId($userId);

        require_once __DIR__ . '/../views/patient/index.php';
    }
    public function profile(): void
    {
        // El perfil comparte las mismas restricciones que el panel del paciente.
        AuthMiddleware::handle();
        RoleMiddleware::handle([3]);

        $userId = (int) $_SESSION['user']['id'];

        // El modelo consulta los datos del paciente usando la conexión heredada.
        $patientModel = new Patient();

        $patient = $patientModel->findByUserId($userId);

        // Evita cargar una vista de perfil sin datos asociados al usuario actual.
        if ($patient === null) {
            http_response_code(404);
            exit('No se encontró la información del paciente.');
        }

        require_once __DIR__ . '/../views/patient/profile.php';
    }
    /**
     * Procesa la actualización de los datos personales del paciente.
     */
    public function updateProfile(): void
    {
        // Verifica que el usuario esté autenticado y tenga rol de paciente.
        AuthMiddleware::handle();
        RoleMiddleware::handle([3]);

        // Obtiene el usuario actual desde la sesión.
        $userId = (int) $_SESSION['user']['id'];

        // Recibe y normaliza los datos enviados por el formulario.
        $fechaNacimiento = trim($_POST['fecha_nac'] ?? '');
        $genero = trim($_POST['genero'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');

        // Convierte los campos vacíos en NULL.
        $fechaNacimiento = $fechaNacimiento !== '' ? $fechaNacimiento : null;
        $genero = $genero !== '' ? $genero : null;
        $direccion = $direccion !== '' ? $direccion : null;

        // Actualiza la información del paciente.
        $patientModel = new Patient();

        $patientModel->update(
            $userId,
            $fechaNacimiento,
            $genero,
            $direccion
        );

        // Guarda el mensaje para mostrarlo en el dashboard.
        $_SESSION['success'] = 'Cambios guardados correctamente.';

        // Regresa al dashboard del paciente.
        header('Location: /paciente');
        exit;
    }
}
