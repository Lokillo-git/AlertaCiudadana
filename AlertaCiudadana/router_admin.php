<?php
session_start();

require_once 'controllers/admin_controller.php';
$controller = new admin_controller();

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'login_admin':
        include 'views/administradores/login.php';
        break;

    case 'home':
        include 'views/administradores/dashboard.php';
        break;

    case 'listar_trabajadores':
        include 'views/administradores/listar_agentes.php';
        break;

    case 'listar_administradores':
        include 'views/administradores/listar_admins.php';
        break;

    case 'listar_protocolos':
        include 'views/administradores/listar_protocolos.php';
        break;

    case 'listar_categorias':
        include 'views/administradores/listar_categorias.php';
        break;

    case 'denuncias_totales':
        include 'views/administradores/denuncias_totales.php';
        break;
    case 'denuncias_resueltas':
        include 'views/administradores/denuncias_resueltas.php';
        break;

    //SECCION DE ADMINISTRADORES
    case 'nuevo_administrador':
        // Modo creación
        $modo = 'crear';
        $admin = null;
        include 'views/administradores/crear_admin.php';
        break;

    case 'guardar_administrador':
        $controller->registrarAdministrador();
        break;

    case 'editar_administrador':
        if (isset($_GET['id'])) {
            $modo = 'editar';
            $admin = $controller->obtenerAdministradorPorId($_GET['id']);
            if ($admin) {
                include 'views/administradores/crear_admin.php';
            } else {
                echo "<div style='text-align:center; padding: 2rem;'>Administrador no encontrado.</div>";
            }
        } else {
            echo "<div style='text-align:center; padding: 2rem;'>ID no especificado.</div>";
        }
        break;

    case 'actualizar_administrador':
        if (isset($_GET['id'])) {
            $controller->actualizarAdministrador();
        } else {
            echo "<div style='text-align:center; padding: 2rem;'>ID no especificado para actualización.</div>";
        }
        break;

    case 'eliminar_admin':
        require_once 'controllers/admin_controller.php';
        $controller = new admin_controller();
        $controller->eliminarAdministrador();
        break;

    //SECCION DE AGENTES Y TRABAJADORES
    case 'nuevo_agente':
        $modo = 'crear';
        $agente = null;
        $modelo = new AdministradorModel();
        $categorias = $modelo->ObtenerTodasCategorias(); // Obtener categorías desde la base de datos
        include 'views/administradores/crear_agente.php';
        break;

    case 'guardar_agente':
        $controller->registrarAgente();
        break;

    case 'editar_agente':
        if (isset($_GET['id'])) {
            $modo = 'editar';
            $controller = new admin_controller();
            $agente = $controller->obtenerAgentePorId($_GET['id']);
            $modelo = new AdministradorModel();
            $categorias = $modelo->ObtenerTodasCategorias();
            if ($agente) {
                include 'views/administradores/crear_agente.php';
            } else {
                echo "<div style='text-align:center; padding: 2rem;'>Agente no encontrado.</div>";
            }
        } else {
            echo "<div style='text-align:center; padding: 2rem;'>ID no especificado.</div>";
        }
        break;

    case 'actualizar_agente':
        if (isset($_GET['id'])) {
            $controller->actualizarAgente();
        } else {
            echo "<div style='text-align:center; padding: 2rem;'>ID no especificado para actualización.</div>";
        }
        break;

    case 'listar_agentes':
        include 'views/administradores/listar_agentes.php';
        break;

    case 'eliminar_agente':
        require_once 'controllers/admin_controller.php';
        $controller = new admin_controller();
        $controller->eliminarAgente();
        break;

    //SECCION DE CATEGORIAS
    case 'listar_categorias':
        include 'views/administradores/listar_categorias.php';
        break;

    case 'guardar_categoria':
        require_once 'controllers/admin_controller.php';
        $controller = new admin_controller();
        $controller->registrarCategoria();
        break;

    case 'datos_categoria':
        $controller->obtenerDatosCategoriaAjax();
        break;

    //SECCION DE PROTOCOLOS
    case 'guardar_protocolo':
        require_once 'controllers/protocolo_controller.php';
        $controller = new ProtocoloController();
        $controller->registrarPasoProtocolo();
        break;

    default:
        echo "<div style='text-align:center; padding: 2rem;'>Página no encontrada</div>";
        break;
}
