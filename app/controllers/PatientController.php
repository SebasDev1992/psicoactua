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
}
