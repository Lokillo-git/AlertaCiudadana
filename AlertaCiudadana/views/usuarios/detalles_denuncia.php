<?php
require_once __DIR__ . '../../../templates/header.php';
require_once __DIR__ . '../../../controllers/usuario_controller.php';

// Obtener ID de denuncia de la URL
$denunciaId = $_GET['id'] ?? 0;

// Instanciar controlador
$denunciaController = new UsuarioController();
$detalles = $denunciaController->obtenerDetalles($denunciaId);

// Manejar errores
if (isset($detalles['error'])) {
    echo '<div class="alert alert-danger">' . $detalles['error'] . '</div>';
    exit;
}

$denuncia = $detalles['denuncia'];
$pasos = $detalles['pasos'];
?>

<style>
    /* Estilos base del worker-content */
    .worker-content {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        margin-top: 1rem;
    }

    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .page-title {
        font-size: 1.8rem;
        color: var(--primary-color);
        font-weight: 600;
    }

    /* Estilos para el encabezado de la denuncia */
    .denuncia-header {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
    }

    .denuncia-title {
        font-size: 1.5rem;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .denuncia-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 1rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
    }

    .meta-item i {
        color: var(--secondary-color);
    }

    .denuncia-status {
        display: inline-block;
        padding: 0.3rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .status-pendiente {
        background-color: #f8d7da;
        color: #721c24;
    }

    .status-en_proceso {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-resuelto {
        background-color: #d4edda;
        color: #155724;
    }

    /* Estilos para el protocolo */
    .protocolo-container {
        margin-top: 2rem;
    }

    .protocolo-title {
        font-size: 1.3rem;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--secondary-color);
    }

    .pasos-list {
        list-style: none;
        padding: 0;
        counter-reset: paso-counter;
    }

    .paso-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        padding: 1.5rem;
        border-radius: 8px;
        background-color: #f9f9f9;
        border-left: 4px solid var(--secondary-color);
        transition: all 0.3s ease;
    }

    .paso-item.completado {
        background-color: #e8f5e9;
        border-left-color: #2e7d32;
    }

    .paso-item.en-proceso {
        background-color: #fff8e1;
        border-left-color: #ff8f00;
    }

    .paso-number {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        background-color: var(--secondary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1.5rem;
        font-weight: bold;
        position: relative;
        counter-increment: paso-counter;
    }

    .paso-item.completado .paso-number {
        background-color: #2e7d32;
    }

    .paso-item.en-proceso .paso-number {
        background-color: #ff8f00;
    }

    .paso-number::before {
        content: counter(paso-counter);
    }

    .paso-content {
        flex-grow: 1;
    }

    .paso-descripcion {
        margin-bottom: 0.5rem;
        color: #333;
    }

    .completado-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background-color: #e8f5e9;
        color: #2e7d32;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    /* Estilos para las evidencias */
    .evidencias-container {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px dashed #ddd;
    }

    .evidencias-title {
        font-size: 1rem;
        color: #555;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .evidencia-item {
        margin-bottom: 1.5rem;
    }

    .evidencia-descripcion {
        background-color: #f0f7ff;
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1rem;
        border-left: 3px solid var(--secondary-color);
    }

    .evidencia-archivos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }

    .evidencia-archivo {
        border: 1px solid #ddd;
        border-radius: 6px;
        overflow: hidden;
    }

    .evidencia-img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .evidencia-video {
        width: 100%;
    }

    .evidencia-fecha {
        font-size: 0.8rem;
        color: #666;
        padding: 0.5rem;
        text-align: right;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .paso-item {
            flex-direction: column;
        }

        .paso-number {
            margin-right: 0;
            margin-bottom: 1rem;
        }

        .evidencia-archivos {
            grid-template-columns: 1fr;
        }
    }

    .evidencias-container {
        margin-top: 1rem;
    }

    .evidencias-title {
        font-size: 1.25rem;
        font-weight: bold;
    }

    .evidencias-row {
        display: flex;
        flex-wrap: wrap;
        /* Permite que las imágenes se acomoden en múltiples líneas si es necesario */
        gap: 1rem;
        /* Espaciado entre las imágenes */
    }

    .evidencia-item {
        width: calc(33.33% - 1rem);
        /* 3 imágenes por fila con un pequeño margen entre ellas */
        box-sizing: border-box;
        margin-bottom: 1rem;
    }

    .evidencia-descripcion {
        font-size: 0.9rem;
        color: #555;
        margin-bottom: 0.5rem;
    }

    .evidencia-archivo {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .evidencia-img {
        max-width: 100%;
        /* Asegura que la imagen no se estire más allá de su contenedor */
        height: auto;
        /* Mantiene la relación de aspecto */
        border-radius: 5px;
        /* Opcional: para bordes redondeados */
        object-fit: cover;
        /* Mantiene la calidad de la imagen al ajustarse al contenedor */
    }

    .evidencia-fecha {
        font-size: 0.8rem;
        color: #888;
        margin-top: 0.5rem;
    }
</style>

<div class="main-content">
    <div class="worker-content">
        <div class="content-header">
            <h1 class="page-title">Seguimiento de Mi Denuncia</h1>
            <a href="router.php?page=listar_denuncias_usuarios" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Volver a mis denuncias
            </a>
        </div>

        <!-- Encabezado de la denuncia -->
        <div class="denuncia-header">
            <h2 class="denuncia-title"><?= htmlspecialchars($denuncia['titulo']) ?></h2>
            <div class="denuncia-meta">
                <div class="meta-item">
                    <i class="fas fa-tag"></i>
                    <span>Categoría: <?= htmlspecialchars($denuncia['categoria_nombre']) ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Ubicación: <?= htmlspecialchars($denuncia['ubicacion']) ?></span>
                </div>
                <div class="meta-item">
                    <i class="far fa-calendar-alt"></i>
                    <span>Fecha: <?= date('d/m/Y', strtotime($denuncia['fecha_creacion'])) ?></span>
                </div>
                <div class="meta-item">
                    <span class="denuncia-status status-<?= str_replace('_', '-', $denuncia['estado']) ?>">
                        <?= ucfirst(str_replace('_', ' ', $denuncia['estado'])) ?>
                    </span>
                </div>
            </div>
            <p class="denuncia-descripcion"><?= nl2br(htmlspecialchars($denuncia['descripcion'])) ?></p>
        </div>

        <div class="protocolo-container">
            <h3 class="protocolo-title">Progreso del Protocolo - <?= htmlspecialchars($denuncia['categoria_nombre']) ?></h3>

            <ul class="pasos-list">
                <?php foreach ($pasos as $index => $paso): ?>
                    <?php
                    $completado = $paso['completado'];
                    $enProceso = (!$completado && $index === 0) || (!$completado && isset($pasos[$index - 1]['completado']) && $pasos[$index - 1]['completado']);
                    ?>

                    <li class="paso-item <?= $completado ? 'completado' : ($enProceso ? 'en-proceso' : '') ?>">
                        <div class="paso-number"></div>
                        <div class="paso-content">
                            <p class="paso-descripcion"><?= htmlspecialchars($paso['descripcion_paso']) ?></p>

                            <?php if ($completado): ?>
                                <div class="completado-badge">
                                    <i class="fas fa-check-circle"></i>
                                    Completado el <?= date('d/m/Y', strtotime($paso['fecha_completado'])) ?>
                                </div>

                                <?php if (!empty($paso['evidencias'])): ?>
                                    <div class="evidencias-container">
                                        <h4 class="evidencias-title">Evidencias del trabajador:</h4>
                                        <div class="evidencias-row">
                                            <?php foreach ($paso['evidencias'] as $evidencia): ?>
                                                <div class="evidencia-item">
                                                    <!-- Mostrar la descripción de la evidencia -->
                                                    <?php if (!empty($evidencia['Descripcion'])): ?>
                                                        <div class="evidencia-descripcion">
                                                            <?= nl2br(htmlspecialchars($evidencia['Descripcion'])) ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="evidencia-archivo">
                                                        <?php
                                                        $imgData = base64_encode($evidencia['archivo_path']);
                                                        $mimeType = 'image/jpeg'; // O deberías detectar el tipo real
                                                        ?>
                                                        <!-- Imagen de la evidencia con un tamaño ajustado -->
                                                        <img src="data:<?= $mimeType ?>;base64,<?= $imgData ?>" alt="Evidencia" class="evidencia-img">
                                                        <div class="evidencia-fecha">
                                                            Subido el <?= date('d/m/Y H:i', strtotime($evidencia['fecha_subida'])) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            <?php elseif ($enProceso): ?>
                                <div class="completado-badge">
                                    <i class="fas fa-hourglass-half"></i>
                                    Paso en progreso
                                </div>
                            <?php else: ?>
                                <div class="completado-badge">
                                    <i class="fas fa-lock"></i>
                                    Paso pendiente
                                </div>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '../../../templates/footer.php'; ?>