<?php require_once __DIR__ . '../../models/denuncia_model.php';

class denuncia_controller
{
    private $denunciaModel;

    public function __construct()
    {
        $this->denunciaModel = new DenunciaModel();
    }

    public function registrarDenuncia()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar campos obligatorios
            $required = ['categoria_id', 'titulo', 'descripcion', 'ubicacion'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    $_SESSION['error'] = 'Todos los campos obligatorios deben estar llenos.';
                    header('Location: router.php?page=denuncias');
                    exit;
                }
            }

            // Obtener datos del formulario
            $data = [
                'usuario_id' => $_SESSION['usuario_id'],
                'categoria_id' => $_POST['categoria_id'],
                'titulo' => $_POST['titulo'],
                'descripcion' => $_POST['descripcion'],
                'ubicacion' => $_POST['ubicacion'],
                'latitud' => $_POST['latitud'] ?? null,
                'longitud' => $_POST['longitud'] ?? null
            ];

            $modelo = new DenunciaModel();
            $denuncia_id = $modelo->guardarDenuncia(
                $data['usuario_id'],
                $data['categoria_id'],
                $data['titulo'],
                $data['descripcion'],
                $data['ubicacion'],
                $data['latitud'],
                $data['longitud']
            );

            if ($denuncia_id) {
                // Procesar evidencias si existen
                if (!empty($_FILES['evidencias']['name'][0])) {
                    $allowedTypes = [
                        'image/jpeg' => 'foto',
                        'image/png' => 'foto',
                        'image/gif' => 'foto',
                        'video/mp4' => 'video',
                        'video/quicktime' => 'video'
                    ];

                    foreach ($_FILES['evidencias']['tmp_name'] as $index => $tmp_name) {
                        if ($_FILES['evidencias']['error'][$index] === UPLOAD_ERR_OK) {
                            $fileType = $_FILES['evidencias']['type'][$index];

                            if (array_key_exists($fileType, $allowedTypes)) {
                                $tipo = $allowedTypes[$fileType];
                                $contenido = file_get_contents($tmp_name);

                                if ($contenido !== false) {
                                    $modelo->guardarEvidencia($denuncia_id, $tipo, $contenido);
                                }
                            }
                        }
                    }
                }

                $_SESSION['success'] = 'Denuncia registrada correctamente.';
            } else {
                $_SESSION['error'] = 'Ocurrió un error al registrar la denuncia.';
            }

            header('Location: router.php?page=listar_denuncias_usuarios');
            exit;
        }
    }
}
