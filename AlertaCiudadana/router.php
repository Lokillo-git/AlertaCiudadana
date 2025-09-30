<?php
session_start();
require_once __DIR__ . '../../AlertaCiudadana/controllers/agente_controller.php';
require_once __DIR__ . '../../AlertaCiudadana/controllers/usuario_controller.php';

$page = $_GET['page'] ?? 'home'; // Si no se pasa, se asume 'home'

switch ($page) {
    case 'home':
        include 'index.php';
        break;
    case 'reportar':
        include 'views/usuarios/realizar_denuncia.php';
        break;
    case 'denuncias':
        include 'views/usuarios/denuncias_recientes.php';
        break;
    case 'acerca_de':
        include 'views/usuarios/acerca_de.php';
        break;
    case 'contacto':
        include 'views/usuarios/contacto.php';
        break;
    case 'login':
        include 'views/usuarios/login.php';
        break;
    case 'sign_up':
        include 'views/usuarios/signunp.php';
        break;
    case 'editar_perfil':
        include 'views/usuarios/editar_perfil.php';
        break;
    case 'listar_denuncias_usuarios':
        include 'views/usuarios/listar_denuncias.php';
        break;
    case 'detalles_denuncia':
        // Obtener el ID de la denuncia desde la URL
        $denunciaId = $_GET['id'] ?? 0;

        // Instanciar el controlador de la denuncia
        $denunciaController = new UsuarioController();

        // Obtener los detalles de la denuncia
        $detalles = $denunciaController->obtenerDetalles($denunciaId);

        // Verificar si hubo un error
        if (isset($detalles['error'])) {
            echo '<div class="alert alert-danger">' . $detalles['error'] . '</div>';
            exit;
        }

        // Extraer los detalles de la denuncia y los pasos
        $denuncia = $detalles['denuncia'];
        $pasos = $detalles['pasos'];

        // Incluir la vista de detalles de la denuncia
        include 'views/usuarios/detalles_denuncia.php';
        break;

    case 'guardar_denuncia':
        require_once 'controllers/denuncia_controller.php';
        $controller = new denuncia_controller();
        $controller->registrarDenuncia();
        break;

    case 'trabajador_dashboard':
        include 'views/trabajadores/dashboard.php';
        break;
    case 'listar_denuncias':
        include 'views/trabajadores/denuncias.php';
        break;
    case 'trabajador_protocolos':
        include 'views/trabajadores/protocolos.php';
        break;
    case 'mis_denuncias':
        include 'views/trabajadores/mis_denuncias.php';
        break;

    case 'iniciar_protocolo':
        $controller = new AgenteController();
        $denunciaId = $_POST['denuncia_id'] ?? 0;
        $resultado = $controller->iniciarProtocoloDenuncia($denunciaId);
        echo json_encode($resultado);
        break;

    // Para mostrar el protocolo
    case 'seguimiento_protocolo':
        require_once __DIR__ . '../../AlertaCiudadana/controllers/agente_controller.php';
        $controller = new AgenteController();
        $denunciaId = $_GET['id'] ?? 0;
        $datos = $controller->mostrarProtocoloDenuncia($denunciaId);

        if (isset($datos['error'])) {
            echo $datos['error'];
        } else {
            require_once __DIR__ . '../../AlertaCiudadana/views/trabajadores/protocolos.php';
        }
        break;

    // Nuevo caso para marcar pasos como completados
    case 'marcar_paso_completado':
        require_once __DIR__ . '../../AlertaCiudadana/controllers/agente_controller.php';
        $controller = new AgenteController();
        $resultado = $controller->marcarPasoCompletado();

        if (isset($resultado['error'])) {
            $_SESSION['error_message'] = $resultado['error'];
        } elseif (isset($resultado['success'])) {
            $_SESSION['success_message'] = $resultado['success'];
        }

        // Redirigir de vuelta al protocolo
        $denunciaId = $_POST['denuncia_id'] ?? 0;
        header("Location: router.php?page=seguimiento_protocolo&id=" . $denunciaId);
        exit;
        break;

    // Nuevo caso para guardar evidencias
    case 'guardar_evidencia':
        require_once __DIR__ . '../../AlertaCiudadana/controllers/agente_controller.php';
        $controller = new AgenteController();
        $resultado = $controller->guardarEvidencia();

        if (isset($resultado['error'])) {
            $_SESSION['error_message'] = $resultado['error'];
        } elseif (isset($resultado['success'])) {
            $_SESSION['success_message'] = $resultado['success'];
        }

        $denunciaId = $_POST['denuncia_id'] ?? 0;
        header("Location: router.php?page=seguimiento_protocolo&id=" . $denunciaId);
        exit;
        break;

    case 'reportes':
        include 'views/trabajadores/reportes.php';
        break;

    default:
        echo "<div style='text-align:center; padding: 2rem;'>Página no encontrada</div>";
        break;
}
