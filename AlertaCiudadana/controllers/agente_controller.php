<?php require_once __DIR__ . '../../models/agente_model.php';

class AgenteController
{
    private $agenteModel;

    public function __construct()
    {
        $this->agenteModel = new AgenteModel();
    }

    public function obtenerDenunciasParaDashboard()
    {
        // Verificamos que el agente esté logueado
        if (!isset($_SESSION['agente_id'])) {
            return ['error' => 'Acceso no autorizado'];
        }

        $agenteId = $_SESSION['agente_id'];
        $denuncias = $this->agenteModel->obtenerDenunciasPorCategoriaAgente($agenteId);

        return $denuncias;
    }

    // Función para obtener denuncias asignadas al agente
    public function obtenerDenunciasAsignadas()
    {
        // Verificamos que el agente esté logueado
        if (!isset($_SESSION['agente_id'])) {
            return ['error' => 'Acceso no autorizado'];
        }

        $agenteId = $_SESSION['agente_id'];
        $denuncias = $this->agenteModel->obtenerDenunciasAsignadas($agenteId);

        if (empty($denuncias)) {
            return ['error' => 'No hay denuncias asignadas para este agente.'];
        }

        return $denuncias;
    }

    public function iniciarProtocoloDenuncia($denunciaId)
    {
        // Verificar que el agente está logueado
        if (!isset($_SESSION['agente_id'])) {
            return ['error' => 'Acceso no autorizado'];
        }

        $agenteId = $_SESSION['agente_id'];

        // Obtener categoría del agente
        $agente = $this->agenteModel->obtenerAgentePorId($agenteId);
        if (!$agente || !isset($agente['categoria_id'])) {
            return ['error' => 'Agente no tiene categoría asignada'];
        }

        $categoriaId = $agente['categoria_id'];

        // Verificar que la denuncia es de su categoría y está pendiente
        if (!$this->agenteModel->verificarDenunciaPendiente($denunciaId, $categoriaId)) {
            return ['error' => 'La denuncia no está disponible o no pertenece a tu categoría'];
        }

        // Asignar agente a la denuncia
        if (!$this->agenteModel->asignarAgenteADenuncia($denunciaId, $agenteId)) {
            return ['error' => 'Error al asignarse a la denuncia'];
        }

        // Obtener pasos del protocolo
        $pasos = $this->agenteModel->obtenerPasosPorCategoria($categoriaId);
        if (empty($pasos)) {
            return ['error' => 'No hay pasos definidos para este tipo de denuncia'];
        }

        // Registrar seguimiento inicial
        if (!$this->agenteModel->registrarSeguimientoInicial($denunciaId, $pasos)) {
            return ['error' => 'Error al registrar el seguimiento'];
        }

        return ['success' => 'Protocolo iniciado correctamente'];
    }

    public function mostrarProtocoloDenuncia($denunciaId)
    {
        // Verificar sesión
        if (!isset($_SESSION['agente_id'])) {
            return ['error' => 'Acceso no autorizado'];
        }

        $agenteId = $_SESSION['agente_id'];

        // Obtener datos de la denuncia
        $denuncia = $this->agenteModel->obtenerDenunciaConProtocolo($denunciaId, $agenteId);

        if (!$denuncia) {
            return ['error' => 'Denuncia no encontrada o no tienes permisos'];
        }

        // Obtener pasos del protocolo
        $pasos = $this->agenteModel->obtenerPasosProtocolo($denuncia['categoria_id']);

        if (!$pasos) {
            return ['error' => 'No se encontraron pasos para este protocolo'];
        }

        // Obtener estados de los pasos
        $estadosPasos = $this->agenteModel->obtenerEstadoPasos($denunciaId);

        return [
            'denuncia' => $denuncia,
            'pasos' => $pasos,
            'estadosPasos' => $estadosPasos
        ];
    }

    public function marcarPasoCompletado()
    {
        // Verificar sesión
        if (!isset($_SESSION['agente_id'])) {
            return ['error' => 'Acceso no autorizado'];
        }

        // Verificar que se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['marcar_completado'])) {
            return ['error' => 'Método no permitido'];
        }

        $agenteId = $_SESSION['agente_id'];
        $denunciaId = $_POST['denuncia_id'] ?? 0;
        $pasoId = $_POST['paso_id'] ?? 0;

        // Validar que el agente tiene permiso sobre esta denuncia
        $denuncia = $this->agenteModel->obtenerDenunciaConProtocolo($denunciaId, $agenteId);
        if (!$denuncia) {
            return ['error' => 'Denuncia no encontrada o no tienes permisos'];
        }

        // Validar que el paso pertenece a la categoría de la denuncia
        $pasos = $this->agenteModel->obtenerPasosProtocolo($denuncia['categoria_id']);
        $pasoValido = false;
        foreach ($pasos as $paso) {
            if ($paso['id_paso'] == $pasoId) {
                $pasoValido = true;
                break;
            }
        }

        if (!$pasoValido) {
            return ['error' => 'Paso no válido para esta denuncia'];
        }

        // Marcar el paso como completado
        if ($this->agenteModel->marcarPasoComoCompletado($denunciaId, $pasoId)) {
            return ['success' => 'Paso marcado como completado correctamente'];
        } else {
            return ['error' => 'Error al marcar el paso como completado'];
        }
    }

    public function guardarEvidencia()
    {
        // Verificar sesión
        if (!isset($_SESSION['agente_id'])) {
            return ['error' => 'Acceso no autorizado'];
        }

        $agenteId = $_SESSION['agente_id'];
        $denunciaId = $_POST['denuncia_id'] ?? 0;
        $pasoId = $_POST['paso_id'] ?? 0;
        $descripcion = $_POST['descripcion'] ?? '';

        // Validaciones
        if (empty($denunciaId)) {
            return ['error' => 'ID de denuncia no proporcionado'];
        }
        if (empty($pasoId)) {
            return ['error' => 'ID de paso no proporcionado'];
        }
        if (strlen($descripcion) < 20) {
            return ['error' => 'La descripción debe tener al menos 20 caracteres'];
        }
        if (empty($_FILES['evidencias']['name'][0])) {
            return ['error' => 'Debe subir al menos un archivo de evidencia'];
        }

        // Validar cantidad de archivos
        if (count($_FILES['evidencias']['name']) > 3) {
            return ['error' => 'Solo puede subir un máximo de 3 archivos'];
        }

        // Validar permisos del agente
        $denuncia = $this->agenteModel->obtenerDenunciaConProtocolo($denunciaId, $agenteId);
        if (!$denuncia) {
            return ['error' => 'Denuncia no encontrada o no tienes permisos'];
        }

        // Validar que el paso pertenece a la categoría
        $pasos = $this->agenteModel->obtenerPasosProtocolo($denuncia['categoria_id']);
        $pasoValido = false;
        foreach ($pasos as $paso) {
            if ($paso['id_paso'] == $pasoId) {
                $pasoValido = true;
                break;
            }
        }

        if (!$pasoValido) {
            return ['error' => 'Paso no válido para esta denuncia'];
        }

        // Procesar archivos
        $archivosValidos = [];
        for ($i = 0; $i < count($_FILES['evidencias']['name']); $i++) {
            // Validar tipo
            $tipo = $_FILES['evidencias']['type'][$i];
            if (!in_array($tipo, ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'])) {
                return ['error' => 'Formato no permitido: ' . $_FILES['evidencias']['name'][$i]];
            }

            // Validar tamaño
            if ($_FILES['evidencias']['size'][$i] > 5 * 1024 * 1024) {
                return ['error' => 'Archivo demasiado grande: ' . $_FILES['evidencias']['name'][$i]];
            }

            // Validar errores de subida
            if ($_FILES['evidencias']['error'][$i] !== UPLOAD_ERR_OK) {
                return ['error' => 'Error al subir el archivo: ' . $_FILES['evidencias']['name'][$i]];
            }

            $archivosValidos[] = [
                'tmp_name' => $_FILES['evidencias']['tmp_name'][$i],
                'name' => $_FILES['evidencias']['name'][$i]
            ];
        }

        // Guardar en la base de datos
        if ($this->agenteModel->guardarEvidenciaSeguimiento(
            ['denuncia_id' => $denunciaId, 'paso_id' => $pasoId],
            $descripcion,
            $archivosValidos
        )) {
            return ['success' => 'Evidencia guardada y paso marcado como completado'];
        } else {
            return ['error' => 'Error al guardar la evidencia'];
        }
    }
}
