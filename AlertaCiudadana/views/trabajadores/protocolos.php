<?php
require_once __DIR__ . '../../../templates/layout_agente.php';
require_once __DIR__ . '../../../controllers/agente_controller.php';

// Obtener ID de denuncia de la URL
$denunciaId = $_GET['id'] ?? 0;

// Instanciar controlador
$protocoloController = new AgenteController();

// Manejar el marcado de paso completado si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['marcar_completado'])) {
        $resultado = $protocoloController->marcarPasoCompletado();
    } elseif (isset($_FILES['evidencias'])) {
        $resultado = $protocoloController->guardarEvidencia();
    }

    // Mostrar mensaje de éxito/error
    if (isset($resultado['error'])) {
        echo '<div class="alert alert-danger">' . $resultado['error'] . '</div>';
    } elseif (isset($resultado['success'])) {
        echo '<div class="alert alert-success">' . $resultado['success'] . '</div>';
    }

    // Redirigir para evitar reenvío del formulario
    header("Refresh: 2; URL=protocolo_denuncia.php?id=" . $denunciaId);
    exit;
}

// Obtener datos para mostrar el protocolo
$datos = $protocoloController->mostrarProtocoloDenuncia($denunciaId);

// Manejar errores
if (isset($datos['error'])) {
    echo '<div class="alert alert-danger">' . $datos['error'] . '</div>';
    exit;
}

// Extraer datos
$denuncia = $datos['denuncia'];
$pasos = $datos['pasos'];
$estadosPasos = $datos['estadosPasos'];
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

    .paso-actions {
        margin-top: 1rem;
        display: flex;
        gap: 1rem;
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

    .btn-outline {
        background-color: transparent;
        border: 1px solid var(--secondary-color);
        color: var(--secondary-color);
    }

    .btn-outline:hover {
        background-color: #f0f7fc;
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

    /* Estilos para el modal de evidencia */
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
        max-width: 700px;
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

    /* Responsive */
    @media (max-width: 768px) {
        .paso-item {
            flex-direction: column;
        }

        .paso-number {
            margin-right: 0;
            margin-bottom: 1rem;
        }

        .modal-content {
            width: 95%;
            margin: 2% auto;
        }
    }
</style>

<div class="main-content">
    <div class="worker-content">
        <div class="content-header">
            <h1 class="page-title">Seguimiento de Protocolo</h1>
            <button class="btn btn-outline" onclick="history.back()">
                <i class="fas fa-arrow-left"></i> Volver a denuncias
            </button>
        </div>

        <!-- Encabezado de la denuncia -->
        <div class="denuncia-header">
            <h2 class="denuncia-title"><?= htmlspecialchars($denuncia['titulo']) ?></h2>
            <div class="denuncia-meta">
                <div class="meta-item">
                    <i class="fas fa-tag"></i>
                    <span>Categoría: <?= htmlspecialchars($denuncia['nombre_categoria']) ?></span>
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
                    <span class="denuncia-status status-en_proceso">En proceso</span>
                </div>
            </div>
            <p class="denuncia-descripcion"><?= nl2br(htmlspecialchars($denuncia['descripcion'])) ?></p>
        </div>

        <div class="protocolo-container">
            <h3 class="protocolo-title">Protocolo de Actuación - <?= htmlspecialchars($denuncia['nombre_categoria']) ?></h3>

            <ul class="pasos-list">
                <?php foreach ($pasos as $index => $paso): ?>
                    <?php
                    $pasoRegistrado = isset($estadosPasos[$paso['id_paso']]);
                    $completado = $pasoRegistrado && $estadosPasos[$paso['id_paso']]['completado'];

                    // El primer paso siempre está en proceso, los siguientes dependen del anterior
                    $enProceso = ($index === 0 && !$completado) ||
                        ($index > 0 && isset($estadosPasos[$pasos[$index - 1]['id_paso']]) &&
                            $estadosPasos[$pasos[$index - 1]['id_paso']]['completado'] &&
                            !$completado);
                    ?>
                    <li class="paso-item <?= $completado ? 'completado' : ($enProceso ? 'en-proceso' : '') ?>">
                        <div class="paso-number"></div>
                        <div class="paso-content">
                            <p class="paso-descripcion"><?= htmlspecialchars($paso['descripcion_paso']) ?></p>

                            <?php if ($completado): ?>
                                <div class="completado-badge">
                                    <i class="fas fa-check-circle"></i>
                                    Completado el <?= date('d/m/Y', strtotime($estadosPasos[$paso['id_paso']]['fecha_completado'])) ?> a las <?= date('H:i', strtotime($estadosPasos[$paso['id_paso']]['fecha_completado'])) ?>
                                </div>
                            <?php elseif ($enProceso): ?>
                                <div class="paso-actions">
                                    <form method="POST" action="router.php?page=marcar_paso_completado">
                                        <input type="hidden" name="denuncia_id" value="<?= $denunciaId ?>">
                                        <input type="hidden" name="paso_id" value="<?= $paso['id_paso'] ?>">
                                        <button type="submit" name="marcar_completado" class="btn btn-success btn-completar-paso">
                                            <i class="fas fa-check"></i> Marcar como completado
                                        </button>
                                    </form>
                                    <button class="btn btn-primary btn-agregar-evidencia" data-paso-id="<?= $paso['id_paso'] ?>">
                                        <i class="fas fa-camera"></i> Añadir evidencia
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="paso-actions">
                                    <button class="btn btn-primary" disabled>
                                        <i class="fas fa-lock"></i> Paso bloqueado
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div style="text-align: center; margin-top: 2rem;">
                <button class="btn btn-primary btn-finalizar-protocolo" <?= count($estadosPasos) < count($pasos) ? 'disabled' : '' ?>>
                    <i class="fas fa-flag-checkered"></i> Finalizar Protocolo
                </button>
                <p style="font-size: 0.9rem; color: #666; margin-top: 0.5rem;">
                    Todos los pasos deben estar completados para finalizar el protocolo
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal para añadir evidencia -->
<div id="evidenciaModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 class="modal-title">Añadir Evidencia</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="evidenciaForm" action="router.php?page=guardar_evidencia" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="denuncia_id" id="modalDenunciaId" value="<?= $denunciaId ?>">
                <input type="hidden" name="paso_id" id="modalPasoId">

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="4"
                        placeholder="Describa en detalle la evidencia que está adjuntando (mínimo 20 caracteres)"
                        style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 6px; resize: vertical;"></textarea>
                    <small class="text-muted">Descripción detallada del avance o resultado de este paso</small>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Adjuntar archivos (Máx. 3) <span id="file-counter">0/3</span>
                    </label>
                    <div style="border: 2px dashed #ddd; border-radius: 8px; padding: 1.5rem; text-align: center; background: #f9f9f9;"
                        id="drop-zone">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #3498db; margin-bottom: 0.5rem;"></i>
                        <p style="margin-bottom: 0.5rem;">Arrastra y suelta archivos aquí o</p>
                        <input type="file" name="evidencias[]" id="fileInput"
                            accept="image/*,video/*" multiple
                            style="display: none;" max="3">
                        <button type="button" class="btn btn-outline" onclick="document.getElementById('fileInput').click()">
                            <i class="fas fa-folder-open"></i> Seleccionar archivos
                        </button>
                        <p class="text-muted" style="margin-top: 0.5rem; font-size: 0.8rem;">
                            Formatos permitidos: JPG, PNG, GIF, MP4 (Máx. 5MB cada uno)
                        </p>
                    </div>
                    <div id="file-preview" style="margin-top: 1rem;"></div>
                </div>

                <div style="text-align: right; margin-top: 1.5rem; border-top: 1px solid #eee; padding-top: 1rem;">
                    <button type="button" class="btn btn-outline cerrar-modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitEvidencia" disabled>
                        <i class="fas fa-save"></i> Guardar Evidencia
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Incluir jQuery y SweetAlert2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Seleccionar todos los botones para agregar evidencia
        const btnAgregarEvidencias = document.querySelectorAll('.btn-agregar-evidencia');

        // Obtener elementos del modal
        const evidenciaModal = document.getElementById('evidenciaModal');
        const closeModalBtn = document.querySelector('.close-modal');
        const cerrarModalBtn = document.querySelector('.cerrar-modal');
        const fileInput = document.getElementById('fileInput');
        const dropZone = document.getElementById('drop-zone');
        const filePreview = document.getElementById('file-preview');
        const fileCounter = document.getElementById('file-counter');
        const submitEvidencia = document.getElementById('submitEvidencia');
        const descripcionTextarea = document.querySelector('#evidenciaForm textarea[name="descripcion"]');

        // Variables para el control de archivos
        let files = [];
        const maxFiles = 3;
        const maxSize = 5 * 1024 * 1024; // 5MB en bytes

        // Función para abrir el modal
        btnAgregarEvidencias.forEach(btn => {
            btn.addEventListener('click', function() {
                const pasoId = this.getAttribute('data-paso-id');

                // Establecer el ID del paso en el modal
                document.getElementById('modalPasoId').value = pasoId;

                // Mostrar el modal
                evidenciaModal.style.display = 'block';

                // Resetear el formulario al abrir
                resetFileForm();
            });
        });

        // Función para cerrar el modal
        function closeModal() {
            evidenciaModal.style.display = 'none';
            resetFileForm();
        }

        // Event listeners para cerrar el modal
        closeModalBtn.addEventListener('click', closeModal);
        cerrarModalBtn.addEventListener('click', closeModal);

        // Cerrar modal al hacer clic fuera del contenido
        window.addEventListener('click', function(event) {
            if (event.target === evidenciaModal) {
                closeModal();
            }
        });

        // Función para resetear el formulario de archivos
        function resetFileForm() {
            files = [];
            fileInput.value = '';
            filePreview.innerHTML = '';
            fileCounter.textContent = '0/3';
            descripcionTextarea.value = '';
            updateSubmitButton();
        }

        // Función para actualizar el estado del botón de enviar
        function updateSubmitButton() {
            const isDescriptionValid = descripcionTextarea.value.trim().length >= 20;
            const hasFiles = files.length > 0;
            submitEvidencia.disabled = !(isDescriptionValid && hasFiles);
        }

        // Evento para arrastrar y soltar archivos
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#3498db';
            dropZone.style.backgroundColor = '#f0f8ff';
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = '#ddd';
            dropZone.style.backgroundColor = '#f9f9f9';
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#ddd';
            dropZone.style.backgroundColor = '#f9f9f9';

            if (e.dataTransfer.files.length) {
                handleFiles(e.dataTransfer.files);
            }
        });

        // Evento para seleccionar archivos
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                handleFiles(fileInput.files);
            }
        });

        // Función para manejar los archivos seleccionados
        function handleFiles(newFiles) {
            // Verificar si excede el máximo de archivos
            const remainingSlots = maxFiles - files.length;

            if (newFiles.length > remainingSlots) {
                alert(`Solo puedes subir un máximo de ${maxFiles} archivos. Ya tienes ${files.length} seleccionados.`);
                return;
            }

            // Procesar cada archivo
            for (let i = 0; i < newFiles.length && files.length < maxFiles; i++) {
                const file = newFiles[i];

                // Validar tipo de archivo
                if (!file.type.match(/image\/.*|video\/.*/)) {
                    alert(`El archivo ${file.name} no es una imagen o video válido.`);
                    continue;
                }

                // Validar tamaño
                if (file.size > maxSize) {
                    alert(`El archivo ${file.name} excede el tamaño máximo de 5MB.`);
                    continue;
                }

                files.push(file);
                renderFilePreview(file);
            }

            fileCounter.textContent = `${files.length}/${maxFiles}`;
            updateSubmitButton();
        }

        // Función para mostrar la previsualización de los archivos
        function renderFilePreview(file) {
            const fileElement = document.createElement('div');
            fileElement.className = 'file-preview-item';
            fileElement.style.display = 'flex';
            fileElement.style.alignItems = 'center';
            fileElement.style.marginBottom = '0.5rem';
            fileElement.style.padding = '0.5rem';
            fileElement.style.backgroundColor = '#f5f5f5';
            fileElement.style.borderRadius = '4px';

            const icon = document.createElement('i');
            icon.className = file.type.startsWith('image/') ? 'fas fa-image' : 'fas fa-video';
            icon.style.marginRight = '0.5rem';
            icon.style.color = '#666';

            const fileName = document.createElement('span');
            fileName.textContent = file.name;
            fileName.style.flexGrow = '1';
            fileName.style.overflow = 'hidden';
            fileName.style.textOverflow = 'ellipsis';
            fileName.style.whiteSpace = 'nowrap';

            const fileSize = document.createElement('span');
            fileSize.textContent = formatFileSize(file.size);
            fileSize.style.marginRight = '0.5rem';
            fileSize.style.color = '#666';
            fileSize.style.fontSize = '0.8rem';

            const removeBtn = document.createElement('button');
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.style.background = 'none';
            removeBtn.style.border = 'none';
            removeBtn.style.cursor = 'pointer';
            removeBtn.style.color = '#ff4444';

            removeBtn.addEventListener('click', () => {
                files = files.filter(f => f !== file);
                filePreview.removeChild(fileElement);
                fileCounter.textContent = `${files.length}/${maxFiles}`;
                updateSubmitButton();
            });

            fileElement.appendChild(icon);
            fileElement.appendChild(fileName);
            fileElement.appendChild(fileSize);
            fileElement.appendChild(removeBtn);

            filePreview.appendChild(fileElement);
        }

        // Función para formatear el tamaño del archivo
        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' bytes';
            else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            else return (bytes / 1048576).toFixed(1) + ' MB';
        }

        // Validar el textarea de descripción
        descripcionTextarea.addEventListener('input', updateSubmitButton);
    });

    document.getElementById('evidenciaForm').addEventListener('submit', function(e) {
        // Validaciones antes de enviar
        if (files.length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un archivo');
            return;
        }

        if (descripcionTextarea.value.trim().length < 20) {
            e.preventDefault();
            alert('La descripción debe tener al menos 20 caracteres');
            return;
        }

        // Mostrar estado de carga
        submitEvidencia.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        submitEvidencia.disabled = true;

        // El formulario se enviará normalmente con action y method
    });

    // Función para convertir FileList a array
    function handleFiles(newFiles) {
        const remainingSlots = maxFiles - files.length;

        if (newFiles.length > remainingSlots) {
            alert(`Solo puedes subir un máximo de ${maxFiles} archivos. Ya tienes ${files.length} seleccionados.`);
            return;
        }

        // Convertir FileList a array y agregar
        Array.from(newFiles).forEach(file => {
            if (files.length >= maxFiles) return;

            // Validar tipo de archivo
            if (!file.type.match(/image\/.*|video\/.*/)) {
                alert(`El archivo ${file.name} no es una imagen o video válido.`);
                return;
            }

            // Validar tamaño
            if (file.size > maxSize) {
                alert(`El archivo ${file.name} excede el tamaño máximo de 5MB.`);
                return;
            }

            files.push(file);
            renderFilePreview(file);
        });

        fileCounter.textContent = `${files.length}/${maxFiles}`;
        updateSubmitButton();
    }
</script>