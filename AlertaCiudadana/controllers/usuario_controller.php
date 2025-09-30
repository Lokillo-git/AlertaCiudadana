<?php require_once __DIR__ . '../../models/usuario_model.php';

class UsuarioController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    // Método para registrar un nuevo usuario
    public function registrar()
    {
        // Verificar si el formulario ha sido enviado
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtener los datos del formulario
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $genero = $_POST['genero'] ?? 'otro'; // Valor por defecto
            $direccion = $_POST['direccion'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $latitud = $_POST['latitud'] ?? 0;
            $longitud = $_POST['longitud'] ?? 0;

            // Validar los campos (esto puede mejorarse con más validaciones)
            if (empty($nombre) || empty($email) || empty($password) || empty($direccion) || empty($telefono)) {
                echo "Todos los campos son obligatorios.";
                return;
            }

            // Subir la foto de perfil (si se envía una)
            $fotoPerfil = null;
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                $fotoPerfil = file_get_contents($_FILES['foto_perfil']['tmp_name']);
            }

            // Registrar el usuario usando el modelo
            $resultado = $this->usuarioModel->registrarUsuario(
                $nombre,
                $email,
                $password,
                $genero,
                $direccion,
                $telefono,
                $fotoPerfil,
                $latitud,
                $longitud
            );

            // Verificar si el registro fue exitoso
            if ($resultado) {
                echo "Usuario registrado con éxito.";
                // Redirigir a otra página si es necesario
                header("Location: router.php?page=login");
            } else {
                echo "Hubo un error al registrar el usuario.";
            }
        } else {
            echo "Por favor, complete el formulario.";
        }
    }

    //iniciar sesión
    public function iniciarSesion()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                echo "El correo electrónico y la contraseña son obligatorios.";
                return;
            }

            $usuario = $this->usuarioModel->iniciarSesion($email, $password);

            if ($usuario) {
                session_start();
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];

                // Guardar la foto de perfil en ambos formatos
                if (!empty($usuario['foto_perfil'])) {
                    $_SESSION['foto_perfil'] = $usuario['foto_perfil']; // Formato BLOB
                    $_SESSION['usuario_foto_base64'] = 'data:image/jpeg;base64,' . base64_encode($usuario['foto_perfil']); // Formato base64
                }

                header("Location: router.php?page=home");
                exit;
            } else {
                echo "Correo electrónico o contraseña incorrectos.";
            }
        }
    }


    /**
     * Obtiene los datos completos del usuario para precargar el formulario de edición
     * 
     * @param int $idUsuario ID del usuario a obtener
     * @return array Datos del usuario formateados para el formulario
     * @throws Exception Si el usuario no existe o hay error en la consulta
     */
    public function obtenerDatosUsuario($idUsuario)
    {
        try {
            // Validar ID
            if (!is_numeric($idUsuario)) {
                throw new Exception("ID de usuario no válido");
            }

            // Obtener datos del modelo
            $usuario = $this->usuarioModel->obtenerUsuarioCompleto($idUsuario);

            if (!$usuario) {
                throw new Exception("Usuario no encontrado");
            }

            // Formatear datos para el formulario
            return [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'] ?? '',
                'email' => $usuario['email'] ?? '',
                'genero' => $usuario['genero'] ?? 'otro',
                'direccion' => $usuario['direccion'] ?? '',
                'telefono' => $usuario['telefono'] ?? '',
                'foto_perfil' => $usuario['foto_perfil'] ?? null,
                'latitud' => $usuario['latitud'] ?? 0,
                'longitud' => $usuario['longitud'] ?? 0
            ];
        } catch (Exception $e) {
            error_log("Error en obtenerDatosUsuario: " . $e->getMessage());
            throw $e; // Relanzar para manejo superior
        }
    }

    /**
     * Actualiza los datos de un usuario existente
     * 
     * @param int $idUsuario ID del usuario a actualizar
     * @param array $datos Datos del formulario
     * @return bool True si la actualización fue exitosa
     * @throws Exception Si hay errores de validación o en la actualización
     */
    public function actualizarUsuario($idUsuario, $datos)
    {
        try {
            // Validaciones básicas
            if (!is_numeric($idUsuario)) {
                throw new Exception("ID de usuario no válido");
            }

            $requiredFields = ['nombre', 'email', 'direccion', 'telefono'];
            foreach ($requiredFields as $field) {
                if (empty($datos[$field])) {
                    throw new Exception("El campo $field es requerido");
                }
            }

            if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("El formato del email no es válido");
            }

            // Verificar si el email ya existe (excepto para este usuario)
            if ($this->usuarioModel->existeEmail($datos['email'], $idUsuario)) {
                throw new Exception("El email ya está registrado por otro usuario");
            }

            // Procesar foto si se envió
            $fotoPerfil = null;
            if (!empty($datos['foto_perfil_base64'])) {
                $fotoPerfil = $datos['foto_perfil_base64'];
            } elseif (!empty($_FILES['foto_perfil']['tmp_name'])) {
                $fotoPerfil = base64_encode(file_get_contents($_FILES['foto_perfil']['tmp_name']));
            }

            // Convertir coordenadas a float
            $latitud = !empty($datos['latitud']) ? (float)$datos['latitud'] : null;
            $longitud = !empty($datos['longitud']) ? (float)$datos['longitud'] : null;

            // Llamar al modelo para actualizar
            $resultado = $this->usuarioModel->actualizarUsuario(
                $idUsuario,
                trim($datos['nombre']),
                trim($datos['email']),
                $datos['genero'] ?? 'otro',
                trim($datos['direccion']),
                trim($datos['telefono']),
                $fotoPerfil,
                $latitud,
                $longitud
            );

            if (!$resultado) {
                throw new Exception("Error al actualizar los datos del usuario");
            }

            // Actualizar datos en sesión si es el mismo usuario
            if (session_status() === PHP_SESSION_ACTIVE && $_SESSION['usuario_id'] == $idUsuario) {
                $_SESSION['usuario_nombre'] = trim($datos['nombre']);
                $_SESSION['usuario_email'] = trim($datos['email']);

                if ($fotoPerfil) {
                    $_SESSION['foto_perfil'] = $fotoPerfil;
                }
            }

            return true;
        } catch (Exception $e) {
            error_log("Error en actualizarUsuario: " . $e->getMessage());
            throw $e; // Relanzar para manejo superior
        }
    }

    // Función para obtener las denuncias del usuario logueado
    public function obtenerDenunciasParaUsuario()
    {
        // Verificamos que el usuario esté logueado
        if (!isset($_SESSION['usuario_id'])) {
            return ['error' => 'Acceso no autorizado'];
        }

        $usuarioId = $_SESSION['usuario_id'];
        $denuncias = $this->usuarioModel->obtenerDenunciasPorUsuario($usuarioId);

        // if (empty($denuncias)) {
        //     return ['error' => 'No hay denuncias registradas para este usuario.'];
        // }

        return $denuncias;
    }

    // Función para obtener los detalles de la denuncia
    public function obtenerDetalles($denunciaId)
    {
        // Verificamos que el usuario esté logueado
        if (!isset($_SESSION['usuario_id'])) {
            return ['error' => 'Acceso no autorizado'];
        }

        // Obtener los detalles de la denuncia
        $detalles = $this->usuarioModel->obtenerDetallesDenuncia($denunciaId);

        if (isset($detalles['error'])) {
            return $detalles;
        }

        return $detalles;
    }
}
