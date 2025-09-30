<?php require_once __DIR__ . '../../models/reportes_agente_model.php';

class ReporteController
{
    private $reportesModel;

    public function __construct()
    {
        $this->reportesModel = new ReportesModel();
    }

    /**
     * Obtiene todos los datos necesarios para el dashboard del agente
     */
    public function obtenerDatosDashboard($agente_id)
    {
        // Obtener información básica del agente
        $infoAgente = $this->reportesModel->obtenerInfoAgente($agente_id);

        // Obtener estadísticas generales
        $estadisticas = $this->reportesModel->obtenerEstadisticasAgente($agente_id);

        // Obtener distribución por estado para gráfico circular
        $distribucionEstados = $this->reportesModel->obtenerDistribucionEstados($agente_id);

        // Obtener tendencia mensual para gráfico de línea
        $tendenciaMensual = $this->reportesModel->obtenerTendenciaMensual($agente_id);

        // Obtener denuncias recientes
        $denunciasRecientes = $this->reportesModel->obtenerDenunciasRecientes($agente_id);

        // Obtener progreso de denuncias
        $progresoDenuncias = $this->reportesModel->obtenerProgresoDenuncias($agente_id);

        // Combinar denuncias recientes con su progreso
        foreach ($denunciasRecientes as &$denuncia) {
            foreach ($progresoDenuncias as $progreso) {
                if ($progreso['denuncia_id'] == $denuncia['id']) {
                    $denuncia['porcentaje'] = $progreso['porcentaje'];
                    break;
                }
            }
        }

        // Obtener actividad reciente
        $actividadReciente = $this->reportesModel->obtenerActividadReciente($agente_id);

        // Preparar datos para gráficos
        $datosGraficoEstado = $this->prepararDatosGraficoEstado($distribucionEstados);
        $datosGraficoTendencia = $this->prepararDatosGraficoTendencia($tendenciaMensual);

        return [
            'info_agente' => $infoAgente,
            'estadisticas' => $estadisticas,
            'grafico_estado' => $datosGraficoEstado,
            'grafico_tendencia' => $datosGraficoTendencia,
            'denuncias_recientes' => $denunciasRecientes,
            'actividad_reciente' => $actividadReciente
        ];
    }

    /**
     * Filtra denuncias según parámetros
     */
    public function filtrarDenuncias($agente_id, $filtros)
    {
        return $this->reportesModel->filtrarDenuncias(
            $agente_id,
            $filtros['fecha_inicio'] ?? null,
            $filtros['fecha_fin'] ?? null,
            $filtros['estado'] ?? null
        );
    }

    /**
     * Prepara los datos para el gráfico de distribución por estado
     */
    private function prepararDatosGraficoEstado($distribucionEstados)
    {
        $datos = [
            'pendiente' => 0,
            'en_proceso' => 0,
            'resuelto' => 0
        ];

        foreach ($distribucionEstados as $estado) {
            $datos[$estado['estado']] = (int)$estado['cantidad'];
        }

        return [
            'labels' => ['Pendientes', 'En Proceso', 'Resueltas'],
            'data' => [
                $datos['pendiente'],
                $datos['en_proceso'],
                $datos['resuelto']
            ],
            'colors' => ['#f39c12', '#3498db', '#2ecc71']
        ];
    }

    /**
     * Prepara los datos para el gráfico de tendencia mensual
     */
    private function prepararDatosGraficoTendencia($tendenciaMensual)
    {
        $meses = [];
        $cantidades = [];

        foreach ($tendenciaMensual as $mes) {
            $fecha = DateTime::createFromFormat('Y-m', $mes['mes']);
            $meses[] = $fecha->format('M'); // Nombre abreviado del mes
            $cantidades[] = (int)$mes['cantidad'];
        }

        return [
            'labels' => $meses,
            'data' => $cantidades,
            'color' => 'rgba(52, 152, 219, 1)'
        ];
    }

    /**
     * Genera un reporte PDF con los datos del dashboard
     */
    public function generarReportePDF($agente_id)
    {
        // Obtener todos los datos del dashboard
        $datos = $this->obtenerDatosDashboard($agente_id);

        // Aquí iría la lógica para generar el PDF usando una librería como TCPDF o Dompdf
        // Esta es una implementación simplificada

        return [
            'success' => true,
            'message' => 'Reporte generado exitosamente',
            'filename' => 'reporte_denuncias_' . date('Ymd') . '.pdf',
            'data' => $datos // Los datos que se usarían para generar el PDF
        ];
    }
}
