<?php require_once __DIR__ . '../../config/database.php';

class ReportesModel
{

    private $conexion;

    public function __construct()
    {
        $db = new Database();
        $this->conexion = $db->getConnection();
    }

    /**
     * Obtiene las estadísticas generales de denuncias para un agente específico
     */
    public function obtenerEstadisticasAgente($agente_id)
    {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'en_proceso' THEN 1 ELSE 0 END) as en_proceso,
                    SUM(CASE WHEN estado = 'resuelto' THEN 1 ELSE 0 END) as resueltas
                  FROM denuncias 
                  WHERE agente_id = :agente_id";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':agente_id', $agente_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la distribución de denuncias por estado para gráfico circular
     */
    public function obtenerDistribucionEstados($agente_id)
    {
        $query = "SELECT 
                    estado, 
                    COUNT(*) as cantidad
                  FROM denuncias 
                  WHERE agente_id = :agente_id
                  GROUP BY estado";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':agente_id', $agente_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la tendencia mensual de denuncias para gráfico de línea
     */
    public function obtenerTendenciaMensual($agente_id, $meses = 6)
    {
        $query = "SELECT 
                    DATE_FORMAT(fecha_creacion, '%Y-%m') as mes,
                    COUNT(*) as cantidad
                  FROM denuncias
                  WHERE agente_id = :agente_id
                    AND fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL :meses MONTH)
                  GROUP BY mes
                  ORDER BY mes ASC";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':agente_id', $agente_id);
        $stmt->bindParam(':meses', $meses, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene las denuncias recientes asignadas al agente
     */
    public function obtenerDenunciasRecientes($agente_id, $limite = 5)
    {
        $query = "SELECT 
                    d.id,
                    d.titulo,
                    DATE_FORMAT(d.fecha_creacion, '%d/%m/%Y') as fecha,
                    d.estado,
                    c.nombre as categoria
                  FROM denuncias d
                  JOIN categorias c ON d.categoria_id = c.id
                  WHERE d.agente_id = :agente_id
                  ORDER BY d.fecha_creacion DESC
                  LIMIT :limite";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':agente_id', $agente_id);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el progreso de cada denuncia basado en pasos completados
     */
    public function obtenerProgresoDenuncias($agente_id)
    {
        $query = "SELECT 
                    d.id as denuncia_id,
                    COUNT(sd.id_paso) as pasos_totales,
                    SUM(CASE WHEN sd.completado THEN 1 ELSE 0 END) as pasos_completados
                  FROM denuncias d
                  JOIN pasos_denuncia pd ON d.categoria_id = pd.id_categoria
                  LEFT JOIN seguimiento_denuncia sd ON d.id = sd.id_denuncia AND pd.id_paso = sd.id_paso
                  WHERE d.agente_id = :agente_id
                  GROUP BY d.id";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':agente_id', $agente_id);
        $stmt->execute();

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calcular porcentajes de progreso
        foreach ($resultados as &$denuncia) {
            $denuncia['porcentaje'] = $denuncia['pasos_totales'] > 0
                ? round(($denuncia['pasos_completados'] / $denuncia['pasos_totales']) * 100)
                : 0;
        }

        return $resultados;
    }

    /**
     * Obtiene la actividad reciente relacionada con las denuncias del agente
     */
    public function obtenerActividadReciente($agente_id, $limite = 3)
    {
        $query = "(
                    SELECT 
                        'paso_completado' as tipo,
                        CONCAT('Paso completado: ', pd.descripcion_paso) as descripcion,
                        d.id as denuncia_id,
                        d.titulo as denuncia_titulo,
                        sd.fecha_completado as fecha
                    FROM seguimiento_denuncia sd
                    JOIN pasos_denuncia pd ON sd.id_paso = pd.id_paso
                    JOIN denuncias d ON sd.id_denuncia = d.id
                    WHERE d.agente_id = :agente_id AND sd.completado = TRUE
                    ORDER BY sd.fecha_completado DESC
                    LIMIT :limite
                  )
                  UNION
                  (
                    SELECT 
                        'nueva_denuncia' as tipo,
                        'Nueva denuncia asignada' as descripcion,
                        d.id as denuncia_id,
                        d.titulo as denuncia_titulo,
                        d.fecha_creacion as fecha
                    FROM denuncias d
                    WHERE d.agente_id = :agente_id
                    ORDER BY d.fecha_creacion DESC
                    LIMIT :limite
                  )
                  ORDER BY fecha DESC
                  LIMIT :limite";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':agente_id', $agente_id);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene información del agente (incluyendo su categoría)
     */
    public function obtenerInfoAgente($agente_id)
    {
        $query = "SELECT 
                    a.nombre,
                    a.email,
                    c.nombre as categoria
                  FROM agentes a
                  LEFT JOIN categorias c ON a.categoria_id = c.id
                  WHERE a.id = :agente_id";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':agente_id', $agente_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Filtra denuncias según parámetros
     */
    public function filtrarDenuncias($agente_id, $fecha_inicio = null, $fecha_fin = null, $estado = null)
    {
        $query = "SELECT 
                    d.id,
                    d.titulo,
                    DATE_FORMAT(d.fecha_creacion, '%d/%m/%Y') as fecha,
                    d.estado,
                    c.nombre as categoria
                  FROM denuncias d
                  JOIN categorias c ON d.categoria_id = c.id
                  WHERE d.agente_id = :agente_id";

        $params = [':agente_id' => $agente_id];

        if ($fecha_inicio) {
            $query .= " AND d.fecha_creacion >= :fecha_inicio";
            $params[':fecha_inicio'] = $fecha_inicio;
        }

        if ($fecha_fin) {
            $query .= " AND d.fecha_creacion <= :fecha_fin";
            $params[':fecha_fin'] = $fecha_fin;
        }

        if ($estado && $estado != 'todos') {
            $query .= " AND d.estado = :estado";
            $params[':estado'] = $estado;
        }

        $query .= " ORDER BY d.fecha_creacion DESC";

        $stmt = $this->conexion->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
