<?php
require_once __DIR__ . '../../../templates/layout_agente.php';
require_once __DIR__ . '../../../controllers/reportes_agente_controller.php';

// Obtener el ID del agente desde la sesión (asumiendo que está almacenado allí)
$agente_id = $_SESSION['agente_id'] ?? 0;

// Crear instancia del controlador y obtener los datos del dashboard
$reporteController = new ReporteController();
$datosDashboard = $reporteController->obtenerDatosDashboard($agente_id);

// Extraer información del agente para mostrarla en el dashboard
$infoAgente = $datosDashboard['info_agente'] ?? [];
$categoriaAgente = $infoAgente['categoria'] ?? 'Sin categoría';
?>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<style>
    /* Estilos adicionales para el dashboard */
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        margin-bottom: 20px;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #2c3e50;
        color: white;
        border-radius: 10px 10px 0 0 !important;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-body {
        padding: 1.5rem;
    }

    .stat-card {
        text-align: center;
        padding: 20px;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 10px 0;
    }

    .stat-label {
        font-size: 1rem;
        color: #7f8c8d;
    }

    .badge-status {
        padding: 8px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .badge-pendiente {
        background-color: #f39c12;
        color: white;
    }

    .badge-en_proceso {
        background-color: #3498db;
        color: white;
    }

    .badge-resuelto {
        background-color: #2ecc71;
        color: white;
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .recent-activity {
        list-style: none;
        padding: 0;
    }

    .recent-activity li {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
    }

    .recent-activity li:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e3f2fd;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: #3498db;
        font-size: 1.2rem;
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        margin-bottom: 3px;
    }

    .activity-time {
        font-size: 0.8rem;
        color: #7f8c8d;
    }

    .btn-export {
        background-color: #e74c3c;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .btn-export:hover {
        background-color: #c0392b;
        color: white;
    }

    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .progress {
        height: 10px;
        border-radius: 5px;
    }

    .progress-bar {
        background-color: #3498db;
    }

    .category-badge {
        background-color: #9b59b6;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
</style>

<!-- Contenido principal -->
<div class="main-content">
    <div class="content-header">
        <h1 class="page-title">Dashboard de Denuncias</h1>
        <button id="exportDashboard" class="btn-export">
            <i class="fas fa-file-pdf"></i> Exportar a PDF
        </button>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <span><i class="fas fa-filter me-2"></i>Filtros</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="fechaInicio" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fechaInicio">
                        </div>
                        <div class="col-md-3">
                            <label for="fechaFin" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="fechaFin">
                        </div>
                        <div class="col-md-3">
                            <label for="estadoDenuncia" class="form-label">Estado</label>
                            <select class="form-select" id="estadoDenuncia">
                                <option value="todos">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="en_proceso">En Proceso</option>
                                <option value="resuelto">Resuelto</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary w-100" id="aplicarFiltros">
                                <i class="fas fa-search me-2"></i>Aplicar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-number" id="totalDenuncias"><?php echo $datosDashboard['estadisticas']['total'] ?? 0; ?></div>
                <div class="stat-label">Total Denuncias</div>
                <div class="mt-2">
                    <span class="badge rounded-pill bg-primary"><?php echo $categoriaAgente; ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-number" id="denunciasPendientes"><?php echo $datosDashboard['estadisticas']['pendientes'] ?? 0; ?></div>
                <div class="stat-label">Pendientes</div>
                <div class="progress mt-2">
                    <div class="progress-bar" role="progressbar"
                        style="width: <?php echo ($datosDashboard['estadisticas']['total'] > 0 ?
                                            round(($datosDashboard['estadisticas']['pendientes'] / $datosDashboard['estadisticas']['total']) * 100) :
                                            0); ?>%">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-number" id="denunciasProceso"><?php echo $datosDashboard['estadisticas']['en_proceso'] ?? 0; ?></div>
                <div class="stat-label">En Proceso</div>
                <div class="progress mt-2">
                    <div class="progress-bar" role="progressbar"
                        style="width: <?php echo ($datosDashboard['estadisticas']['total'] > 0 ?
                                            round(($datosDashboard['estadisticas']['en_proceso'] / $datosDashboard['estadisticas']['total']) * 100) :
                                            0); ?>%">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-number" id="denunciasResueltas"><?php echo $datosDashboard['estadisticas']['resueltas'] ?? 0; ?></div>
                <div class="stat-label">Resueltas</div>
                <div class="progress mt-2">
                    <div class="progress-bar" role="progressbar"
                        style="width: <?php echo ($datosDashboard['estadisticas']['total'] > 0 ?
                                            round(($datosDashboard['estadisticas']['resueltas'] / $datosDashboard['estadisticas']['total']) * 100) :
                                            0); ?>%">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y tablas -->
    <div class="row">
        <!-- Gráfico de denuncias por estado -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <span><i class="fas fa-chart-pie me-2"></i>Distribución por Estado</span>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="estadoChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de tendencia temporal -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <span><i class="fas fa-chart-line me-2"></i>Tendencia Mensual</span>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="tendenciaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Denuncias recientes -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <span><i class="fas fa-list me-2"></i>Denuncias Recientes</span>
                    <a href="router.php?page=listar_denuncias" class="btn btn-sm btn-outline-light">Ver Todas</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Progreso</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($datosDashboard['denuncias_recientes'] as $denuncia):
                                    $estadoClass = 'badge-' . str_replace('_', '', $denuncia['estado']);
                                    $estadoTexto = ucfirst(str_replace('_', ' ', $denuncia['estado']));
                                ?>
                                    <tr>
                                        <td>#<?php echo $denuncia['id']; ?></td>
                                        <td><?php echo htmlspecialchars($denuncia['titulo']); ?></td>
                                        <td><?php echo $denuncia['fecha']; ?></td>
                                        <td><span class="badge-status <?php echo $estadoClass; ?>"><?php echo $estadoTexto; ?></span></td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar" style="width: <?php echo $denuncia['porcentaje'] ?? 0; ?>%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="router.php?page=ver_denuncia&id=<?php echo $denuncia['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actividad reciente -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <span><i class="fas fa-bell me-2"></i>Actividad Reciente</span>
                </div>
                <div class="card-body">
                    <ul class="recent-activity">
                        <?php foreach ($datosDashboard['actividad_reciente'] as $actividad):
                            $iconos = [
                                'paso_completado' => 'fa-check-circle',
                                'nueva_denuncia' => 'fa-exclamation'
                            ];
                            $icono = $iconos[$actividad['tipo']] ?? 'fa-comment';

                            $fecha = new DateTime($actividad['fecha']);
                            $tiempo = $fecha->format('d/m/Y H:i');
                        ?>
                            <li>
                                <div class="activity-icon">
                                    <i class="fas <?php echo $icono; ?>"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title"><?php echo htmlspecialchars($actividad['descripcion']); ?></div>
                                    <div class="activity-desc"><?php echo htmlspecialchars($actividad['denuncia_titulo']); ?></div>
                                    <div class="activity-time"><?php echo $tiempo; ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Función para inicializar los gráficos con datos reales
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener datos PHP en formato JSON para JavaScript
        const graficoEstadoData = <?php echo json_encode($datosDashboard['grafico_estado'] ?? []); ?>;
        const graficoTendenciaData = <?php echo json_encode($datosDashboard['grafico_tendencia'] ?? []); ?>;

        // Gráfico de distribución por estado
        const estadoCtx = document.getElementById('estadoChart').getContext('2d');
        const estadoChart = new Chart(estadoCtx, {
            type: 'doughnut',
            data: {
                labels: graficoEstadoData.labels || ['Pendientes', 'En Proceso', 'Resueltas'],
                datasets: [{
                    data: graficoEstadoData.data || [0, 0, 0],
                    backgroundColor: graficoEstadoData.colors || [
                        '#f39c12',
                        '#3498db',
                        '#2ecc71'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfico de tendencia temporal
        const tendenciaCtx = document.getElementById('tendenciaChart').getContext('2d');
        const tendenciaChart = new Chart(tendenciaCtx, {
            type: 'line',
            data: {
                labels: graficoTendenciaData.labels || [],
                datasets: [{
                    label: 'Denuncias',
                    data: graficoTendenciaData.data || [],
                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Función para exportar a PDF
        document.getElementById('exportDashboard').addEventListener('click', function() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('p', 'pt', 'a4');

            // Título del reporte
            doc.setFontSize(18);
            doc.text('Reporte de Denuncias - Dashboard', 40, 40);

            // Fecha de generación
            doc.setFontSize(12);
            doc.text(`Generado el: ${new Date().toLocaleDateString()}`, 40, 60);

            // Categoría del trabajador
            doc.text(`Categoría: <?php echo $categoriaAgente; ?>`, 40, 80);

            // Estadísticas
            doc.setFontSize(14);
            doc.text('Estadísticas Principales', 40, 120);
            doc.text(`Total denuncias: <?php echo $datosDashboard['estadisticas']['total'] ?? 0; ?>`, 40, 140);
            doc.text(`Pendientes: <?php echo $datosDashboard['estadisticas']['pendientes'] ?? 0; ?>`, 40, 160);
            doc.text(`En proceso: <?php echo $datosDashboard['estadisticas']['en_proceso'] ?? 0; ?>`, 40, 180);
            doc.text(`Resueltas: <?php echo $datosDashboard['estadisticas']['resueltas'] ?? 0; ?>`, 40, 200);

            // Guardar el PDF
            doc.save(`reporte-denuncias-${new Date().toISOString().split('T')[0]}.pdf`);

            // Mostrar notificación
            Swal.fire({
                icon: 'success',
                title: 'Reporte generado',
                text: 'El PDF se ha descargado correctamente',
                timer: 2000,
                showConfirmButton: false
            });
        });

        // Eliminar la simulación de carga de datos ya que ahora usamos datos reales
    });

    // Manejar el evento de aplicar filtros
    document.getElementById('aplicarFiltros').addEventListener('click', function() {
        const fechaInicio = document.getElementById('fechaInicio').value;
        const fechaFin = document.getElementById('fechaFin').value;
        const estado = document.getElementById('estadoDenuncia').value;

        // Mostrar carga
        Swal.fire({
            title: 'Aplicando filtros',
            html: 'Por favor espere...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Enviar datos al servidor via AJAX
        fetch('router.php?page=filtrar_denuncias', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin,
                    estado: estado
                })
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();

                if (data.success) {
                    // Actualizar la tabla de denuncias recientes
                    const tbody = document.querySelector('.table tbody');
                    tbody.innerHTML = '';

                    data.denuncias.forEach(denuncia => {
                        const estadoClass = 'badge-' + denuncia.estado.replace('_', '');
                        const estadoTexto = denuncia.estado.charAt(0).toUpperCase() + denuncia.estado.slice(1).replace('_', ' ');

                        const row = document.createElement('tr');
                        row.innerHTML = `
                        <td>#${denuncia.id}</td>
                        <td>${denuncia.titulo}</td>
                        <td>${denuncia.fecha}</td>
                        <td><span class="badge-status ${estadoClass}">${estadoTexto}</span></td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar" style="width: ${denuncia.porcentaje || 0}%"></div>
                            </div>
                        </td>
                        <td>
                            <a href="router.php?page=ver_denuncia&id=${denuncia.id}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    `;
                        tbody.appendChild(row);
                    });

                    // Mostrar notificación de éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Filtros aplicados',
                        text: 'Las denuncias se han filtrado correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(data.message || 'Error al aplicar filtros');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            });
    });
</script>