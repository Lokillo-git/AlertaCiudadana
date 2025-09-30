<?php require_once __DIR__ . '../../config/database.php';

class ReporteAdminModel
{

    private $conexion;

    public function __construct()
    {
        $db = new Database();
        $this->conexion = $db->getConnection();
    }

    // Obtener los datos generales del Dashboard
    public function obtenerDatosDashboard()
    {
        // Fechas de los períodos actual y anterior
        $fechaActual = date('Y-m-d');
        $fechaAnterior = date('Y-m-d', strtotime('-1 month'));

        $query = "
            SELECT 
                (SELECT COUNT(*) FROM usuarios) AS total_usuarios,
                (SELECT COUNT(*) FROM denuncias) AS total_denuncias,
                (SELECT COUNT(*) FROM agentes) AS total_agentes,
                (SELECT COUNT(*) FROM denuncias WHERE estado = 'resuelto') AS denuncias_resueltas,
                
                -- Tendencia de usuarios
                (SELECT COUNT(*) FROM usuarios WHERE creado_en >= '$fechaAnterior' AND creado_en < '$fechaActual') AS usuarios_periodo_anterior,
                (SELECT COUNT(*) FROM usuarios WHERE creado_en >= '$fechaActual') AS usuarios_periodo_actual,
                
                -- Tendencia de denuncias
                (SELECT COUNT(*) FROM denuncias WHERE fecha_creacion >= '$fechaAnterior' AND fecha_creacion < '$fechaActual') AS denuncias_periodo_anterior,
                (SELECT COUNT(*) FROM denuncias WHERE fecha_creacion >= '$fechaActual') AS denuncias_periodo_actual,
                
                -- Tendencia de agentes
                (SELECT COUNT(*) FROM agentes WHERE creado_en >= '$fechaAnterior' AND creado_en < '$fechaActual') AS agentes_periodo_anterior,
                (SELECT COUNT(*) FROM agentes WHERE creado_en >= '$fechaActual') AS agentes_periodo_actual
        ";

        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calcular tendencias en porcentaje
        $resultado['tendencias_usuarios'] = $this->calcularTendencia(
            $resultado['usuarios_periodo_actual'],
            $resultado['usuarios_periodo_anterior']
        );
        $resultado['tendencias_denuncias'] = $this->calcularTendencia(
            $resultado['denuncias_periodo_actual'],
            $resultado['denuncias_periodo_anterior']
        );
        $resultado['tendencias_agentes'] = $this->calcularTendencia(
            $resultado['agentes_periodo_actual'],
            $resultado['agentes_periodo_anterior']
        );

        return $resultado;
    }

    // Función para calcular la tendencia en porcentaje
    private function calcularTendencia($actual, $anterior)
    {
        if ($anterior == 0) {
            return $actual > 0 ? 100 : 0;
        }

        return round((($actual - $anterior) / $anterior) * 100, 2);
    }

    // Obtener las denuncias recientes
    public function obtenerDenunciasRecientes()
    {
        $query = "
            SELECT 
                d.id, d.titulo, c.nombre AS categoria, u.nombre AS usuario, 
                d.fecha_creacion AS fecha, d.estado
            FROM denuncias d
            JOIN usuarios u ON d.usuario_id = u.id
            JOIN categorias c ON d.categoria_id = c.id
            ORDER BY d.fecha_creacion DESC LIMIT 5
        ";

        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;
    }

    public function obtenerDenunciasPorEstado($estado)
    {
        $query = "SELECT COUNT(*) FROM denuncias WHERE estado = :estado";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerDenunciasPorCategoria()
    {
        $query = "
        SELECT c.nombre AS categoria, COUNT(d.id) AS total_denuncias
        FROM denuncias d
        JOIN categorias c ON d.categoria_id = c.id
        GROUP BY c.nombre
    ";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
