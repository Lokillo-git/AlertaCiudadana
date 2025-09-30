<?php
require_once __DIR__ . '../../../templates/header.php';
require_once __DIR__ . '../../../models/denuncia_model.php';

$categorias = new DenunciaModel();
$categorias = $categorias->obtenerCategorias();
?>

<style>
    /* Estilos generales */
    .report-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .page-title {
        font-size: 1.8rem;
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-control:disabled {
        background-color: #f5f5f5;
        color: #666;
    }

    .form-control:focus {
        border-color: var(--secondary-color);
        outline: none;
    }

    .select-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        background-color: white;
        cursor: pointer;
    }

    /* Estilos para la línea de tiempo */
    .timeline {
        position: relative;
        padding: 1rem 0;
        margin: 2rem 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: var(--secondary-color);
        transform: translateX(-50%);
    }

    .timeline-step {
        position: relative;
        padding: 1rem;
        margin-bottom: 1.5rem;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        width: 45%;
    }

    .timeline-step:nth-child(odd) {
        left: 0;
    }

    .timeline-step:nth-child(even) {
        left: 55%;
    }

    .timeline-step.active {
        border-left: 4px solid var(--secondary-color);
    }

    .timeline-step.completed {
        border-left: 4px solid #2ecc71;
    }

    .timeline-step .step-title {
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .timeline-step .step-description {
        color: #666;
        font-size: 0.9rem;
    }

    /* Estilos para el mapa */
    #map {
        height: 400px;
        width: 100%;
        border-radius: 8px;
        border: 1px solid #ddd;
        margin-bottom: 1.5rem;
    }

    .map-coordinates {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .map-coordinates input {
        flex: 1;
    }

    /* Estilos para subir archivos */
    .file-upload {
        border: 2px dashed #ddd;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .file-upload:hover {
        border-color: var(--secondary-color);
    }

    .file-upload input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .file-upload-icon {
        font-size: 2.5rem;
        color: var(--secondary-color);
        margin-bottom: 1rem;
    }

    .file-upload-text {
        font-size: 1.1rem;
        color: #666;
    }

    .file-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 1rem;
    }

    .file-preview-item {
        width: 100px;
        height: 100px;
        border-radius: 6px;
        overflow: hidden;
        position: relative;
        border: 1px solid #ddd;
    }

    .file-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .file-preview-item .remove-file {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: rgba(255, 0, 0, 0.7);
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    /* Botón de enviar */
    .btn-submit {
        background-color: var(--secondary-color);
        color: white;
        padding: 0.8rem 1.5rem;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: block;
        width: 100%;
        max-width: 200px;
        margin: 2rem auto 0;
    }

    .btn-submit:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .report-container {
            padding: 1rem;
        }

        .timeline::before {
            left: 20px;
        }

        .timeline-step {
            width: calc(100% - 40px);
            left: 40px !important;
        }

        .timeline-step::before {
            left: -30px;
        }

        .map-coordinates {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>

<div class="report-container">
    <h1 class="page-title">Reportar Problema o Denuncia</h1>

    <form id="denunciaForm" method="POST" action="router.php?page=guardar_denuncia" enctype="multipart/form-data">
        <!-- Información del usuario -->
        <div class="form-group">
            <label for="usuarioNombre">Nombre del denunciante</label>
            <input type="text" id="usuarioNombre" class="form-control" disabled value="<?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="usuarioEmail">Email del denunciante</label>
            <input type="email" id="usuarioEmail" class="form-control" disabled value="<?php echo htmlspecialchars($_SESSION['usuario_email'] ?? ''); ?>">
        </div>

        <!-- Selección de categoría -->
        <div class="form-group">
            <label for="categoriaDenuncia">Categoría del problema</label>
            <select id="categoriaDenuncia" name="categoria_id" class="select-control" required>
                <option value="">-- Seleccione una categoría --</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo $categoria['id']; ?>"><?php echo htmlspecialchars($categoria['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Línea de tiempo del proceso -->
        <div class="timeline">
            <div class="timeline-step active">
                <div class="step-title">1. Reporte inicial</div>
                <div class="step-description">Usted está aquí</div>
            </div>
            <div class="timeline-step">
                <div class="step-title">2. Revisión por el equipo</div>
                <div class="step-description">Su denuncia será asignada a un agente</div>
            </div>
            <div class="timeline-step">
                <div class="step-title">3. En proceso</div>
                <div class="step-description">El agente estará trabajando en la solución</div>
            </div>
            <div class="timeline-step">
                <div class="step-title">4. Resuelto</div>
                <div class="step-description">Se le notificará cuando se complete</div>
            </div>
        </div>

        <!-- Título y descripción -->
        <div class="form-group">
            <label for="tituloDenuncia">Título de la denuncia</label>
            <input type="text" id="tituloDenuncia" name="titulo" class="form-control" required placeholder="Ej: Basura acumulada en la esquina">
        </div>

        <div class="form-group">
            <label for="descripcionDenuncia">Descripción detallada</label>
            <textarea id="descripcionDenuncia" name="descripcion" class="form-control" rows="5" required placeholder="Describa el problema con el mayor detalle posible..."></textarea>
        </div>

        <!-- Mapa para ubicación -->
        <div class="form-group">
            <label>Ubicación del problema</label>
            <div id="map"></div>
            <div class="map-coordinates">
                <input type="text" id="ubicacion" name="ubicacion" class="form-control" placeholder="Dirección aproximada" required>
                <input type="text" id="latitud" name="latitud" class="form-control" placeholder="Latitud" readonly>
                <input type="text" id="longitud" name="longitud" class="form-control" placeholder="Longitud" readonly>
            </div>
        </div>

        <!-- Subida de evidencias -->
        <div class="form-group">
            <label>Evidencias (fotos/videos)</label>
            <div class="file-upload">
                <div class="file-upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div class="file-upload-text">Arrastra archivos aquí o haz clic para seleccionar</div>
                <input type="file" id="evidencias" name="evidencias[]" multiple accept="image/*,video/*" max="5">
            </div>
            <small class="text-muted">Máximo 5 archivos (fotos o videos)</small>
            <div class="file-preview" id="filePreview"></div>
        </div>

        <!-- Botón de enviar -->
        <button type="submit" class="btn-submit">
            <i class="fas fa-paper-plane"></i> Enviar Denuncia
        </button>
    </form>
</div>

<!-- Incluir Leaflet para el mapa -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<!-- Incluir jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Inicializar el mapa
        const map = L.map('map').setView([-17.7833, -63.1825], 13); // Coordenadas iniciales (ejemplo: Santa Cruz, Bolivia)

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let marker = null;

        // Manejar clic en el mapa
        map.on('click', function(e) {
            const {
                lat,
                lng
            } = e.latlng;

            // Eliminar marcador anterior si existe
            if (marker) {
                map.removeLayer(marker);
            }

            // Agregar nuevo marcador
            marker = L.marker([lat, lng]).addTo(map)
                .bindPopup('Ubicación del problema')
                .openPopup();

            // Actualizar coordenadas en el formulario
            $('#latitud').val(lat);
            $('#longitud').val(lng);

            // Obtener dirección aproximada (usando Nominatim)
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    const address = data.display_name || 'Ubicación seleccionada';
                    $('#ubicacion').val(address);
                })
                .catch(error => {
                    console.error('Error al obtener dirección:', error);
                    $('#ubicacion').val('Ubicación seleccionada');
                });
        });

        // Manejar la previsualización de archivos
        $('#evidencias').on('change', function() {
            const files = this.files;
            const filePreview = $('#filePreview');
            filePreview.empty();

            if (files.length > 5) {
                alert('Solo puedes subir un máximo de 5 archivos');
                $(this).val('');
                return;
            }

            // Array para guardar las promesas de lectura de archivos
            const readPromises = [];

            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();

                    // Creamos una promesa para cada lectura de archivo
                    const promise = new Promise((resolve) => {
                        reader.onload = function(e) {
                            resolve({
                                type: 'image',
                                result: e.target.result,
                                index: i
                            });
                        };
                        reader.readAsDataURL(file);
                    });
                    readPromises.push(promise);
                } else if (file.type.startsWith('video/')) {
                    // Para videos, agregamos directamente al array
                    readPromises.push(Promise.resolve({
                        type: 'video',
                        index: i
                    }));
                }
            }

            // Cuando todas las lecturas hayan terminado
            Promise.all(readPromises).then(results => {
                results.forEach(item => {
                    if (item.type === 'image') {
                        filePreview.append(`
                    <div class="file-preview-item">
                        <img src="${item.result}" alt="Preview">
                        <button type="button" class="remove-file" data-index="${item.index}">&times;</button>
                    </div>
                `);
                    } else {
                        filePreview.append(`
                    <div class="file-preview-item">
                        <div style="display:flex;align-items:center;justify-content:center;height:100%;background:#f0f0f0;">
                            <i class="fas fa-video" style="font-size:2rem;color:#666;"></i>
                        </div>
                        <button type="button" class="remove-file" data-index="${item.index}">&times;</button>
                    </div>
                `);
                    }
                });
            });
        });

        // Manejar eliminación de archivos de la previsualización
        $(document).on('click', '.remove-file', function() {
            const index = $(this).data('index');
            const files = $('#evidencias')[0].files;
            const newFiles = Array.from(files).filter((_, i) => i !== index);

            // Crear una nueva DataTransfer para actualizar los archivos
            const dataTransfer = new DataTransfer();
            newFiles.forEach(file => dataTransfer.items.add(file));

            $('#evidencias')[0].files = dataTransfer.files;
            $('#evidencias').trigger('change'); // Volver a generar las previsualizaciones
        });

        // Validar el formulario antes de enviar
        $('#denunciaForm').on('submit', function(e) {
            if (!$('#latitud').val() || !$('#longitud').val()) {
                e.preventDefault();
                alert('Por favor, seleccione una ubicación en el mapa');
                return;
            }

            if ($('#evidencias')[0].files.length === 0) {
                if (!confirm('¿Está seguro de enviar la denuncia sin evidencia fotográfica o de video?')) {
                    e.preventDefault();
                    return;
                }
            }
        });
    });
</script>

<?php require_once __DIR__ . '../../../templates/footer.php'; ?>