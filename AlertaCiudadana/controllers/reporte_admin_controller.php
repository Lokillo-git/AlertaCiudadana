<?php require_once __DIR__ . '../../models/reporte_admin_model.php';

class ReporteAdminController
{
    private $reporteAdmins;

    public function __construct()
    {
        $this->reporteAdmins = new ReporteAdminModel();
    }

    // Obtener datos del Dashboard
    public function obtenerDashboardData()
    {
        $data = $this->reporteAdmins->obtenerDatosDashboard();
        return $data;
    }

    // Obtener denuncias recientes
    public function obtenerDenunciasRecientes()
    {
        $denuncias = $this->reporteAdmins->obtenerDenunciasRecientes();
        return $denuncias;
    }

    // Obtener denuncias por estado (Pendiente, En Proceso, Resuelto)
    public function obtenerDenunciasPorEstado($estado)
    {
        $denunciasPorEstado = $this->reporteAdmins->obtenerDenunciasPorEstado($estado);
        return $denunciasPorEstado;
    }

    // Obtener denuncias por categoría
    public function obtenerDenunciasPorCategoria()
    {
        $denunciasPorCategoria = $this->reporteAdmins->obtenerDenunciasPorCategoria();
        return $denunciasPorCategoria;
    }
}
