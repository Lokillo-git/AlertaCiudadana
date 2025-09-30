<?php
ob_start(); // Iniciar el buffer de salida
require_once __DIR__ . '../../../templates/layout_agente.php';
require_once __DIR__ . '../../../controllers/agente_controller.php';

// Instanciamos el controlador
$agenteController = new AgenteController();

// Manejar activación de protocolo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activar_protocolo'])) {
    $denunciaId = $_POST['denuncia_id'] ?? 0;
    $categoriaId = $_POST['categoria_id'] ?? 0;

    $agenteController->iniciarProtocoloDenuncia($denunciaId, $categoriaId);

    // Redirigir para evitar reenvío del formulario
    header("Location: router.php?page=listar_denuncias");
    exit();
}

// Obtenemos las denuncias para el dashboard
$denuncias = $agenteController->obtenerDenunciasParaDashboard();

// Verificamos si hay un error
if (isset($denuncias['error'])) {
    echo '<div class="alert alert-danger">' . $denuncias['error'] . '</div>';
    exit;
}
?>

<style>
    /* Estilos específicos para la página de denuncias */
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

    /* Estilos para las tarjetas de denuncias */
    .denuncias-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .denuncia-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        background-color: #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .denuncia-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .denuncia-header {
        padding: 1rem;
        border-bottom: 1px solid #e0e0e0;
        background-color: #f9f9f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .denuncia-title {
        font-size: 1.2rem;
        color: var(--primary-color);
        font-weight: 600;
        margin: 0;
    }

    .denuncia-status {
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
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

    .denuncia-body {
        padding: 1rem;
    }

    .denuncia-descripcion {
        color: #555;
        margin-bottom: 1rem;
        font-size: 0.95rem;
    }

    .denuncia-meta {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        color: #777;
        margin-bottom: 0.5rem;
    }

    .denuncia-ubicacion {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--secondary-color);
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .denuncia-footer {
        padding: 1rem;
        border-top: 1px solid #e0e0e0;
        display: flex;
        justify-content: flex-end;
        gap: 0.8rem;
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background-color: var(--secondary-color);
        color: white;
    }

    .btn-primary:hover {
        background-color: #2980b9;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    /* Galería de evidencias */
    .evidencias-container {
        margin-top: 1rem;
    }

    .evidencias-title {
        font-size: 1rem;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }

    .evidencias-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
    }

    .evidencia-item {
        height: 100px;
        overflow: hidden;
        border-radius: 4px;
    }

    .evidencia-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .evidencia-img:hover {
        transform: scale(1.05);
    }

    /* Modal para ver detalles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 2rem;
        border-radius: 8px;
        width: 80%;
        max-width: 800px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .modal-title {
        font-size: 1.5rem;
        color: var(--primary-color);
    }

    .close-modal {
        font-size: 1.8rem;
        cursor: pointer;
        color: #777;
    }

    .modal.active {
        display: block;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .denuncias-grid {
            grid-template-columns: 1fr;
        }

        .evidencias-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .modal-content {
            width: 95%;
            margin: 2% auto;
        }
    }


    .mapa-container {
        margin-top: 1.5rem;
    }

    .mapa-title {
        font-size: 1rem;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }

    #map {
        height: 300px;
        width: 100%;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        z-index: 0;
        /* Importante para que funcione correctamente Leaflet */
    }

    /* Estilos para el control de búsqueda de Leaflet */
    .leaflet-control-geocoder {
        border-radius: 4px;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.4);
        background: white;
    }

    .leaflet-control-geocoder a {
        border-bottom: none;
        display: block;
    }
</style>

<div class="main-content">
    <div class="worker-content">
        <div class="content-header">
            <h1 class="page-title">Mis Denuncias Asignadas</h1>
        </div>

        <div class="denuncias-grid">
            <?php if (empty($denuncias)): ?>
                <div class="alert alert-info" style="grid-column: 1 / -1;">
                    No tienes denuncias asignadas actualmente.
                </div>
            <?php else: ?>
                <?php foreach ($denuncias as $denuncia): ?>
                    <?php if ($denuncia['estado'] === 'pendiente'): ?>
                        <div class="denuncia-card">
                            <div class="denuncia-header">
                                <h3 class="denuncia-title"><?php echo htmlspecialchars($denuncia['titulo']); ?></h3>
                                <span class="denuncia-status status-<?php echo str_replace('_', '-', $denuncia['estado']); ?>">
                                    <?php
                                    $estado = str_replace('_', ' ', $denuncia['estado']);
                                    echo ucfirst($estado);
                                    ?>
                                </span>
                            </div>

                            <div class="denuncia-body">
                                <p class="denuncia-descripcion"><?php echo nl2br(htmlspecialchars($denuncia['descripcion'])); ?></p>

                                <div class="denuncia-meta">
                                    <span><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($denuncia['fecha_creacion'])); ?></span>
                                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($denuncia['nombre_categoria']); ?></span>
                                </div>

                                <div class="denuncia-ubicacion">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($denuncia['ubicacion'] ?? 'Ubicación no especificada'); ?></span>
                                </div>

                                <?php if (!empty($denuncia['evidencias'])): ?>
                                    <div class="evidencias-container">
                                        <h4 class="evidencias-title">Evidencias:</h4>
                                        <div class="evidencias-grid">
                                            <?php foreach ($denuncia['evidencias'] as $evidencia): ?>
                                                <div class="evidencia-item">
                                                    <?php if ($evidencia['tipo'] === 'foto'): ?>
                                                        <img src="data:<?php echo $evidencia['mime']; ?>;base64,<?php echo $evidencia['data']; ?>"
                                                            alt="Evidencia imagen"
                                                            class="evidencia-img">
                                                    <?php else: ?>
                                                        <video class="evidencia-video" controls>
                                                            <source src="data:<?php echo $evidencia['mime']; ?>;base64,<?php echo $evidencia['data']; ?>"
                                                                type="<?php echo $evidencia['mime']; ?>">
                                                            Tu navegador no soporta video.
                                                        </video>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="denuncia-footer">
                                <button class="btn btn-primary btn-ver-detalles" data-denuncia-id="<?php echo $denuncia['id']; ?>">
                                    <i class="fas fa-eye"></i> Ver Detalles
                                </button>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="activar_protocolo" value="1">
                                    <input type="hidden" name="denuncia_id" value="<?= $denuncia['id'] ?>">
                                    <input type="hidden" name="categoria_id" value="<?= $denuncia['categoria_id'] ?>">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-play-circle"></i> Activar Protocolo
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>



<!-- Modal para ver detalles -->
<div id="detallesDenunciaModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Detalles de la Denuncia</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div id="modal-denuncia-content">

        </div>
    </div>
</div>


<!-- Incluir jQuery y SweetAlert2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    $(document).ready(function() {
        // Variable para almacenar el mapa y el marcador
        let map;
        let marker;

        // Función para inicializar el mapa
        function initMap(lat, lng) {
            // Destruir el mapa anterior si existe
            if (map) {
                map.remove();
            }

            // Crear nuevo mapa
            map = L.map('map').setView([lat, lng], 15);

            // Añadir capa de tiles (OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Añadir marcador
            marker = L.marker([lat, lng]).addTo(map)
                .bindPopup('Ubicación de la denuncia')
                .openPopup();
        }

        // Manejar clic en el botón "Ver Detalles"
        $('.btn-ver-detalles').on('click', function() {
            const denunciaId = $(this).data('denuncia-id');

            // Buscar la denuncia correspondiente en los datos ya cargados
            const denunciaData = <?php echo json_encode($denuncias); ?>.find(d => d.id == denunciaId);

            if (!denunciaData) {
                Swal.fire('Error', 'No se encontraron los detalles de la denuncia', 'error');
                return;
            }

            // Construir el contenido del modal
            let modalContent = `
        <div class="denuncia-header">
            <h3 class="denuncia-title">${escapeHtml(denunciaData.titulo)}</h3>
            <span class="denuncia-status status-${denunciaData.estado.replace('_', '-')}">
                ${denunciaData.estado.replace('_', ' ').charAt(0).toUpperCase() + denunciaData.estado.replace('_', ' ').slice(1)}
            </span>
        </div>

        <div class="denuncia-body">
            <p class="denuncia-descripcion">${escapeHtml(denunciaData.descripcion)}</p>

            <div class="denuncia-meta">
                <span><i class="far fa-calendar-alt"></i> ${formatDate(denunciaData.fecha_creacion)}</span>
                <span><i class="fas fa-tag"></i> ${escapeHtml(denunciaData.nombre_categoria)}</span>
            </div>

            <div class="denuncia-ubicacion">
                <i class="fas fa-map-marker-alt"></i>
                <span>${escapeHtml(denunciaData.ubicacion || 'Ubicación no especificada')}</span>
            </div>`;

            // Mostrar mapa si hay coordenadas
            if (denunciaData.latitud && denunciaData.longitud) {
                modalContent += `
            <div class="mapa-container" style="margin-top: 1rem;">
                <h4 class="mapa-title">Ubicación exacta:</h4>
                <div id="map" style="height: 300px; width: 100%; border-radius: 8px; margin-top: 0.5rem;"></div>
            </div>`;
            }

            // Mostrar evidencias
            if (denunciaData.evidencias && denunciaData.evidencias.length > 0) {
                modalContent += `
            <div class="evidencias-container">
                <h4 class="evidencias-title">Evidencias:</h4>
                <div class="evidencias-grid">
                    ${denunciaData.evidencias.map(evidencia => `
                        <div class="evidencia-item">
                            ${evidencia.tipo === 'foto' ? 
                                `<img src="data:${evidencia.mime};base64,${evidencia.data}" 
                                      alt="Evidencia imagen" 
                                      class="evidencia-img"
                                      style="max-width: 100%; height: auto;">` : 
                                `<video class="evidencia-video" controls style="max-width: 100%;">
                                    <source src="data:${evidencia.mime};base64,${evidencia.data}" 
                                            type="${evidencia.mime}">
                                    Tu navegador no soporta video.
                                </video>`}
                        </div>
                    `).join('')}
                </div>
            </div>`;
            }

            modalContent += `</div>`;

            // Insertar el contenido en el modal
            $('#modal-denuncia-content').html(modalContent);

            // Mostrar el modal
            $('#detallesDenunciaModal').addClass('active');

            // Inicializar el mapa si hay coordenadas
            if (denunciaData.latitud && denunciaData.longitud) {
                // Usamos setTimeout para asegurarnos de que el div del mapa ya existe en el DOM
                setTimeout(() => {
                    initMap(parseFloat(denunciaData.latitud), parseFloat(denunciaData.longitud));
                }, 100);
            }
        });

        // Cerrar el modal al hacer clic en la X
        $('.close-modal').on('click', function() {
            $('#detallesDenunciaModal').removeClass('active');
            // Limpiar el mapa cuando se cierra el modal
            if (map) {
                map.remove();
                map = null;
            }
        });

        // Cerrar el modal al hacer clic fuera del contenido
        $(window).on('click', function(e) {
            if ($(e.target).hasClass('modal')) {
                $('#detallesDenunciaModal').removeClass('active');
                // Limpiar el mapa cuando se cierra el modal
                if (map) {
                    map.remove();
                    map = null;
                }
            }
        });

        // Funciones auxiliares
        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe.toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
    });

    // Confirmación antes de enviar el formulario
    $(document).on('submit', 'form[method="POST"]', function(e) {
        // Solo interceptar formularios de activar protocolo
        if ($(this).find('input[name="activar_protocolo"]').length) {
            e.preventDefault();

            Swal.fire({
                title: '¿Activar protocolo?',
                text: '¿Estás seguro de que deseas activar el protocolo para esta denuncia?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, activar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Si confirma, enviar el formulario
                    e.target.submit();
                }
            });
        }
    });
</script>

<?php ob_end_flush(); // Enviar el contenido del buffer al navegador ?>