<?php
require_once __DIR__ . '../../../templates/layout_admin.php';
require_once __DIR__ . '../../../controllers/reporte_admin_controller.php';
$reporteAdminController = new ReporteAdminController();

// Obtener los datos del dashboard
$datosDashboard = $reporteAdminController->obtenerDashboardData();

// Obtener las denuncias recientes
$denunciasRecientes = $reporteAdminController->obtenerDenunciasRecientes();

// Obtener denuncias por estado (pendiente, en_proceso, resuelto)
$totalPendientes = $reporteAdminController->obtenerDenunciasPorEstado('pendiente');
$totalEnProceso = $reporteAdminController->obtenerDenunciasPorEstado('en_proceso');
$totalResueltas = $reporteAdminController->obtenerDenunciasPorEstado('resuelto');

// Obtener denuncias por categoría
$denunciasPorCategoria = $reporteAdminController->obtenerDenunciasPorCategoria();
$categorias = [];
$denunciasPorCategoriaCount = [];
foreach ($denunciasPorCategoria as $categoria) {
    $categorias[] = $categoria['categoria'];
    $denunciasPorCategoriaCount[] = $categoria['total_denuncias'];
}
?>


<div class="main-content">
    <div class="content-header">
        <h1 class="page-title">Dashboard de Administración</h1>
    </div>

    <!-- Sección de Métricas -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon" style="background-color: #3498db;">
                <i class="fas fa-users"></i>
            </div>
            <div class="metric-info">
                <h3>Usuarios Registrados</h3>
                <p id="total-users"><?php echo $datosDashboard['total_usuarios'] ?? 0; ?></p>

            </div>
            <a href="router_admin.php?page=listar_usuarios" class="metric-link">Ver todos</a>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background-color: #e74c3c;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="metric-info">
                <h3>Denuncias Totales</h3>
                <p id="total-complaints"><?php echo $datosDashboard['total_denuncias'] ?? 0; ?></p>
            </div>
            <a href="router_admin.php?page=denuncias_totales" class="metric-link">Ver todas</a>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background-color: #2ecc71;">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="metric-info">
                <h3>Agentes Registrados</h3>
                <p id="total-agents"><?php echo $datosDashboard['total_agentes'] ?? 0; ?></p>
            </div>
            <a href="router_admin.php?page=listar_trabajadores" class="metric-link">Ver todos</a>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background-color: #f39c12;">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="metric-info">
                <h3>Denuncias Resueltas</h3>
                <p id="resolved-complaints"><?php echo $datosDashboard['denuncias_resueltas'] ?? 0; ?></p>
            </div>
            <a href="router_admin.php?page=denuncias_resueltas" class="metric-link">Ver resueltas</a>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="charts-row">
        <div class="chart-container">
            <h3>Denuncias por Estado</h3>
            <canvas id="complaints-status-chart"></canvas>
        </div>
        <div class="chart-container">
            <h3>Denuncias por Categoría</h3>
            <canvas id="complaints-category-chart"></canvas>
        </div>
    </div>

    <!-- Tabla de Denuncias Recientes -->
    <div class="recent-table">
        <div class="table-header">
            <h3>Denuncias Recientes</h3>
            <div class="table-actions">
                <button id="export-recent" class="export-btn">Exportar PDF</button>
                <button id="refresh-recent" class="refresh-btn"><i class="fas fa-sync-alt"></i></button>
            </div>
        </div>
        <table id="recent-complaints-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Categoría</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($denunciasRecientes)): ?>
                    <?php foreach ($denunciasRecientes as $denuncia): ?>
                        <tr>
                            <td><?php echo $denuncia['id']; ?></td>
                            <td><?php echo htmlspecialchars($denuncia['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($denuncia['categoria']); ?></td>
                            <td><?php echo htmlspecialchars($denuncia['usuario']); ?></td>
                            <td><?php echo $denuncia['fecha']; ?></td>
                            <td><?php echo ucfirst($denuncia['estado']); ?></td>
                            <td>
                                <a href="router_admin.php?page=detalle_denuncia&id=<?php echo $denuncia['id']; ?>" class="action-btn" title="Ver detalles"><i class="fas fa-eye"></i></a>
                                <a href="router_admin.php?page=editar_denuncia&id=<?php echo $denuncia['id']; ?>" class="action-btn" title="Editar"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay denuncias recientes</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Estilos para el dashboard */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .metric-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .metric-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        margin-bottom: 15px;
    }

    .metric-info h3 {
        font-size: 16px;
        color: #555;
        margin-bottom: 10px;
    }

    .metric-info p {
        font-size: 28px;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .metric-trend {
        font-size: 12px;
        color: #777;
    }

    .metric-trend span {
        font-weight: bold;
    }

    .metric-link {
        position: absolute;
        bottom: 15px;
        right: 15px;
        color: #3498db;
        text-decoration: none;
        font-size: 12px;
        font-weight: bold;
    }

    .charts-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .chart-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .chart-container h3 {
        margin-bottom: 15px;
        font-size: 16px;
        color: #555;
    }

    .recent-table {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .table-actions {
        display: flex;
        gap: 10px;
    }

    .export-btn {
        background: #2c3e50;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .refresh-btn {
        background: #ecf0f1;
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    tr:hover {
        background-color: #f5f5f5;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }

    .status-pendiente {
        background-color: #f39c12;
        color: white;
    }

    .status-en_proceso {
        background-color: #3498db;
        color: white;
    }

    .status-resuelto {
        background-color: #2ecc71;
        color: white;
    }

    .action-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #3498db;
        margin-right: 10px;
        text-decoration: none;
    }

    .text-center {
        text-align: center;
    }

    @media (max-width: 1200px) {
        .metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .charts-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .metrics-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>


<script>
    // Datos para los gráficos
    const datosDashboard = <?php echo json_encode($datosDashboard); ?>; // Datos pasados desde el controlador

    // Gráfico de Denuncias por Estado
    const ctxEstado = document.getElementById('complaints-status-chart').getContext('2d');
    const complaintsStatusChart = new Chart(ctxEstado, {
        type: 'pie', // Usamos un gráfico de tipo "pie" para representar las tendencias
        data: {
            labels: ['Pendientes', 'En Proceso', 'Resueltas'],
            datasets: [{
                label: 'Estado de las Denuncias',
                data: [
                    <?php
                    // Total de denuncias por estado: pendiente, en_proceso, resuelto
                    $totalPendientes = $reporteAdminController->obtenerDenunciasPorEstado('pendiente');
                    $totalEnProceso = $reporteAdminController->obtenerDenunciasPorEstado('en_proceso');
                    $totalResueltas = $reporteAdminController->obtenerDenunciasPorEstado('resuelto');
                    echo "$totalPendientes, $totalEnProceso, $totalResueltas";
                    ?>
                ],
                backgroundColor: ['#f39c12', '#e74c3c', '#2ecc71'],
                borderColor: '#ffffff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw + ' denuncias';
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Denuncias por Categoría
    const ctxCategoria = document.getElementById('complaints-category-chart').getContext('2d');
    const complaintsCategoryChart = new Chart(ctxCategoria, {
        type: 'bar', // Gráfico de barras
        data: {
            labels: <?php echo json_encode($categorias); ?>, // Array de categorías de denuncias
            datasets: [{
                label: 'Denuncias por Categoría',
                data: <?php echo json_encode($denunciasPorCategoriaCount); ?>, // Array de denuncias por categoría
                backgroundColor: '#3498db',
                borderColor: '#2980b9',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw + ' denuncias';
                        }
                    }
                }
            }
        }
    });


    // Función para exportar la tabla a PDF
    document.getElementById('export-recent').addEventListener('click', function() {
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF();

        // Título del PDF
        doc.text('Denuncias Recientes', 14, 10);

        // Tabla de datos
        const table = document.getElementById('recent-complaints-table');
        const rows = table.querySelectorAll('tr');

        // Variables para la tabla
        const tableData = [];
        rows.forEach((row, rowIndex) => {
            const cells = row.querySelectorAll('td, th');
            const rowData = [];
            cells.forEach(cell => {
                rowData.push(cell.textContent.trim());
            });
            tableData.push(rowData);
        });

        // Añadir tabla al PDF
        doc.autoTable({
            head: [
                ['ID', 'Título', 'Categoría', 'Usuario', 'Fecha', 'Estado', 'Acciones']
            ],
            body: tableData.slice(1), // Excluimos la primera fila de encabezado
            startY: 20
        });

        // Guardar el PDF
        doc.save('denuncias_recientes.pdf');
    });
</script>