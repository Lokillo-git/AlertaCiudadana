<?php require_once __DIR__ . '../../models/protocolo_model.php';

class ProtocoloController
{
    private $protocoloModel;

    public function __construct()
    {
        $this->protocoloModel = new ProtocoloModel();
    }

    // Método para registrar un nuevo paso de protocolo
    public function registrarPasoProtocolo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_categoria = $_POST['id_categoria'] ?? null;
            $descripcion_paso = trim($_POST['descripcion_paso'] ?? '');
            $orden = $_POST['orden'] ?? null;

            // Validaciones básicas
            if (empty($id_categoria) || empty($descripcion_paso) || empty($orden)) {
                $_SESSION['error'] = "Todos los campos son obligatorios.";
                header("Location: router_admin.php?page=nuevo_protocolo");
                exit;
            }

            $exito = $this->protocoloModel->registrarPasoProtocolo($id_categoria, $descripcion_paso, $orden);

            if ($exito) {
                $_SESSION['success'] = "Paso del protocolo registrado correctamente.";
            } else {
                $_SESSION['error'] = "Error al registrar el paso del protocolo.";
            }

            header("Location: router_admin.php?page=listar_protocolos");
            exit;
        }
    }

    public function listarProtocolos()
    {
        return $this->protocoloModel->obtenerCategoriasConPasos();
    }
}
