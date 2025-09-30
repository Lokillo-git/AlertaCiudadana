<?php require_once __DIR__ . '../../models/admin_model.php';

class admin_controller
{
    private $adminModel;

    public function __construct()
    {
        $this->adminModel  = new AdministradorModel();
    }

    public function registrarAdministrador()
    {
        // Verificar si se enviaron los datos esperados
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $fotoPerfil = null;

            // Asegurar que el email tenga el dominio
            $dominio = '@ciudad.alert.bo';
            if (!str_ends_with($email, $dominio)) {
                $email .= $dominio;
            }

            // Validaciones mínimas
            if (empty($nombre) || empty($email) || empty($password)) {
                header("Location: router_admin.php?page=nuevo_administrador");
                exit;
            }

            // Hashear la contraseña
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Si se subió una imagen, obtener su contenido binario
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                $fotoPerfil = file_get_contents($_FILES['foto_perfil']['tmp_name']);
            }

            // Registrar el administrador
            $registrado = $this->adminModel->registrarAdministrador($nombre, $email, $passwordHash, $fotoPerfil);

            if ($registrado) {
                header("Location: router_admin.php?page=listar_administradores");
            } else {
                header("Location: router_admin.php?page=nuevo_administrador");
            }
            exit;
        }
    }

        // Iniciar sesión del administrador o del trabajador
    public function iniciarSesion()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $rol = $_POST['user_role'] ?? '';

            // Asegurar dominio para ambos roles
            $dominio = '@ciudad.alert.bo';
            if (!str_ends_with($email, $dominio)) {
                $email .= $dominio;
            }

            if ($rol === 'admin') {
                // Login como ADMINISTRADOR
                $admin = $this->adminModel->obtenerPorEmail($email);

                // 🔹 Comparar en texto plano
                if ($admin && $password === $admin['password']) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_nombre'] = $admin['nombre'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['usuario_foto_base64'] = base64_encode($admin['foto_perfil'] ?? '');
                    header("Location: router_admin.php?page=home");
                    exit;
                } else {
                    $_SESSION['error_login'] = "Correo o contraseña incorrectos para administrador.";
                    header("Location: router_admin.php?page=login_admin");
                    exit;
                }
            } elseif ($rol === 'worker') {
                // Login como TRABAJADOR
                $agente = $this->adminModel->obtenerAgentePorEmail($email);

                // 🔹 Comparar en texto plano
                if ($agente && $password === $agente['password']) {
                    $_SESSION['agente_id'] = $agente['id'];
                    $_SESSION['agente_nombre'] = $agente['nombre'];
                    $_SESSION['agente_email'] = $agente['email'];
                    $_SESSION['usuario_foto_base64'] = base64_encode($agente['foto_perfil'] ?? '');
                    header("Location: router.php?page=trabajador_dashboard");
                    exit;
                } else {
                    $_SESSION['error_login'] = "Correo o contraseña incorrectos para trabajador.";
                    header("Location: router_admin.php?page=login_admin");
                    exit;
                }
            } else {
                $_SESSION['error_login'] = "Rol inválido. Seleccione 'Administrador' o 'Trabajador'.";
                header("Location: router_admin.php?page=login_admin");
                exit;
            }
        }
    }


    //listar todos los administradores
    public function listarAdministradores()
    {
        return $this->adminModel->obtenerTodos();
    }

    //Obtener un administrador por su ID
    public function obtenerAdministradorPorId($id)
    {
        return $this->adminModel->obtenerPorId($id);
    }

    //Actualizar un administrador por su ID
    public function actualizarAdministrador()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';

            // Verificar si tiene el dominio
            $dominio = '@ciudad.alert.bo';
            if (!str_ends_with($email, $dominio)) {
                $email .= $dominio;
            }

            // Leer foto si se subió una nueva
            $fotoPerfil = null;
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                $fotoPerfil = file_get_contents($_FILES['foto_perfil']['tmp_name']);
            }

            $actualizado = $this->adminModel->actualizarAdministrador($id, $nombre, $email, $fotoPerfil);

            if ($actualizado) {
                header("Location: router_admin.php?page=listar_administradores");
            } else {
                $_SESSION['error_edicion'] = "Error al actualizar el administrador.";
                header("Location: router_admin.php?page=editar_administrador&id=$id");
            }
            exit;
        }
    }

    //Eliminar un administrador por su ID
    public function eliminarAdministrador()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $modelo = new AdministradorModel();
            $resultado = $modelo->eliminarAdministrador($id);
            echo json_encode(['success' => $resultado]);
        }
    }

    //registrar un nuevo agente
    public function registrarAgente()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $categoria_id = !empty($_POST['categoria_id']) ? $_POST['categoria_id'] : null;

            // Asegurar que el email tenga el dominio
            $dominio = '@ciudad.alert.bo';
            if (!str_ends_with($email, $dominio)) {
                $email .= $dominio;
            }

            // Foto de perfil (longblob)
            $foto_perfil = null;
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                $foto_perfil = file_get_contents($_FILES['foto_perfil']['tmp_name']);
            }

            // Datos faciales (por ahora null)
            $datos_faciales = null;

            // Hashear la contraseña
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $modelo = new AdministradorModel(); // Asegúrate de que estás llamando al modelo correcto
            $exito = $modelo->registrarAgente($nombre, $email, $passwordHash, $telefono, $foto_perfil, $datos_faciales, $categoria_id);

            if ($exito) {
                $_SESSION['success'] = "Trabajador registrado correctamente.";
                header("Location: router_admin.php?page=listar_trabajadores");
            } else {
                $_SESSION['error'] = "Error al registrar el trabajador.";
                header("Location: router_admin.php?page=nuevo_agente");
            }

            exit;
        }
    }

    // Mostrar la lista de agentes gubernamentales
    public function listarAgentes()
    {
        $modelo = new AdministradorModel();
        return $modelo->obtenerAgentes();
    }

    public function obtenerAgentePorId($id)
    {
        $modelo = new AdministradorModel();
        return $modelo->obtenerAgentePorId($id);
    }

    public function actualizarAgente()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $categoria_id = !empty($_POST['categoria_id']) ? $_POST['categoria_id'] : null;

            // Verificar si tiene el dominio
            $dominio = '@ciudad.alert.bo';
            if (!str_ends_with($email, $dominio)) {
                $email .= $dominio;
            }

            // Foto de perfil (si se reemplazó)
            $foto_perfil = null;
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                $foto_perfil = file_get_contents($_FILES['foto_perfil']['tmp_name']);
            }

            $modelo = new AdministradorModel();
            $exito = $modelo->actualizarAgente($id, $nombre, $email, $telefono, $categoria_id, $foto_perfil);

            if ($exito) {
                $_SESSION['success'] = "Agente actualizado correctamente.";
            } else {
                $_SESSION['error'] = "Error al actualizar el agente.";
            }

            header("Location: router_admin.php?page=listar_agentes");
            exit;
        }
    }

    // Eliminar un agente por su ID
    public function eliminarAgente()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $modelo = new AdministradorModel();
            $resultado = $modelo->eliminarAgente($id);
            echo json_encode(['success' => $resultado]);
        }
    }

    // Registrar nueva categoría
    public function registrarCategoria()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $encargado_id = !empty($_POST['encargado_id']) ? $_POST['encargado_id'] : null;
            $categoria_id = !empty($_POST['categoria_id']) ? $_POST['categoria_id'] : null;

            $modelo = new AdministradorModel();

            if ($categoria_id) {
                // EDITAR
                $exito = $modelo->actualizarCategoria($categoria_id, $nombre, $encargado_id);
            } else {
                // CREAR
                $exito = $modelo->registrarCategoria($nombre, $encargado_id);
            }

            if ($exito) {
                $_SESSION['success'] = $categoria_id ? "Categoría actualizada correctamente." : "Categoría registrada correctamente.";
            } else {
                $_SESSION['error'] = "Error al guardar la categoría.";
            }

            header("Location: router_admin.php?page=listar_categorias");
            exit;
        }
    }

    // Controlador para obtener las categorías
    public function obtenerCategorias()
    {
        $modelo = new AdministradorModel();
        return $modelo->obtenerCategorias();
    }

    // Obtener datos de una categoría y sus agentes para editar
    public function obtenerDatosCategoriaAjax()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $categoria_id = $_GET['id'];

            $modelo = new AdministradorModel();
            $datos = $modelo->obtenerDatosCategoriaConAgentes($categoria_id);

            if ($datos) {
                // Devolver como JSON
                header('Content-Type: application/json');
                echo json_encode($datos);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Categoría no encontrada']);
            }
            exit;
        }
    }

    public function obtenerTodasLasCategorias()
    {
        $modelo = new AdministradorModel();
        return $modelo->obtenerCategorias();
    }

    // Obtener todas las denuncias pendientes
    public function ListarTotalDenuncias()
    {
        // Obtener las denuncias resueltas
        $denuncias = $this->adminModel->obtenerTodasDenuncias();
        return $denuncias;
    }

    // Obtener todas las denuncias pendientes
    public function ListarDenunciasReueltas()
    {
        // Obtener las denuncias resueltas
        $denuncias = $this->adminModel->obtenerDenunciasResueltas();
        return $denuncias;
    }
}