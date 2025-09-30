<?php require_once __DIR__ . '../../config/database.php';

class AgenteModel
{
    private $conexion;

    public function __construct()
    {
        $db = new Database();
        $this->conexion = $db->getConnection();
    }

    public function obtenerDenunciasPorCategoriaAgente($agenteId)
    {
        try {
            // Primero obtenemos la categoría del agente
            $queryCategoria = "SELECT categoria_id FROM agentes WHERE id = :agente_id";
            $stmtCategoria = $this->conexion->prepare($queryCategoria);
            $stmtCategoria->bindParam(':agente_id', $agenteId, PDO::PARAM_INT);
            $stmtCategoria->execute();

            $categoria = $stmtCategoria->fetch(PDO::FETCH_ASSOC);

            if (!$categoria || is_null($categoria['categoria_id'])) {
                return []; // No tiene categoría asignada
            }

            // Obtenemos las denuncias de esa categoría incluyendo latitud y longitud
            $queryDenuncias = "
            SELECT 
                d.id,
                d.titulo,
                d.descripcion,
                d.estado,
                DATE(d.fecha_creacion) AS fecha_creacion,
                d.ubicacion,
                d.latitud,
                d.longitud,
                c.nombre AS nombre_categoria,
                d.categoria_id
            FROM 
                denuncias d
            JOIN 
                categorias c ON d.categoria_id = c.id
            WHERE 
                d.categoria_id = :categoria_id
            ORDER BY 
                d.fecha_creacion DESC
        ";

            $stmtDenuncias = $this->conexion->prepare($queryDenuncias);
            $stmtDenuncias->bindParam(':categoria_id', $categoria['categoria_id'], PDO::PARAM_INT);
            $stmtDenuncias->execute();

            $denuncias = $stmtDenuncias->fetchAll(PDO::FETCH_ASSOC);

            if (empty($denuncias)) {
                return [];
            }

            // Obtenemos las evidencias para cada denuncia
            $denunciasConEvidencias = [];
            $queryEvidencias = "SELECT archivo_path, tipo FROM evidencias WHERE denuncia_id = :denuncia_id";
            $stmtEvidencias = $this->conexion->prepare($queryEvidencias);

            foreach ($denuncias as $denuncia) {
                $stmtEvidencias->bindParam(':denuncia_id', $denuncia['id'], PDO::PARAM_INT);
                $stmtEvidencias->execute();
                $evidencias = $stmtEvidencias->fetchAll(PDO::FETCH_ASSOC);

                // Procesamos cada evidencia
                $denuncia['evidencias'] = array_map(function ($evidencia) {
                    return [
                        'data' => base64_encode($evidencia['archivo_path']),
                        'tipo' => $evidencia['tipo'],
                        'mime' => $evidencia['tipo'] === 'foto' ? 'image/jpeg' : 'video/mp4'
                    ];
                }, $evidencias);

                $denunciasConEvidencias[] = $denuncia;
            }

            return $denunciasConEvidencias;
        } catch (PDOException $e) {
            error_log("Error en obtenerDenunciasPorCategoriaAgente: " . $e->getMessage());
            return [];
        }
    }

    // Función para obtener denuncias asignadas a un agente
    public function obtenerDenunciasAsignadas($agenteId)
    {
        try {
            // Obtenemos las denuncias donde el agente_id corresponde al agente que está logueado
            $queryDenuncias = "
            SELECT 
                d.id,
                d.titulo,
                d.descripcion,
                d.estado,
                DATE(d.fecha_creacion) AS fecha_creacion,
                d.ubicacion,
                d.latitud,
                d.longitud,
                c.nombre AS nombre_categoria,
                d.categoria_id
            FROM 
                denuncias d
            JOIN 
                categorias c ON d.categoria_id = c.id
            WHERE 
                d.agente_id = :agente_id
            ORDER BY 
                d.fecha_creacion DESC
            ";

            $stmtDenuncias = $this->conexion->prepare($queryDenuncias);
            $stmtDenuncias->bindParam(':agente_id', $agenteId, PDO::PARAM_INT);
            $stmtDenuncias->execute();

            $denuncias = $stmtDenuncias->fetchAll(PDO::FETCH_ASSOC);

            if (empty($denuncias)) {
                return [];
            }

            // Obtenemos las evidencias para cada denuncia
            $denunciasConEvidencias = [];
            $queryEvidencias = "SELECT archivo_path, tipo FROM evidencias WHERE denuncia_id = :denuncia_id";
            $stmtEvidencias = $this->conexion->prepare($queryEvidencias);

            foreach ($denuncias as $denuncia) {
                $stmtEvidencias->bindParam(':denuncia_id', $denuncia['id'], PDO::PARAM_INT);
                $stmtEvidencias->execute();
                $evidencias = $stmtEvidencias->fetchAll(PDO::FETCH_ASSOC);

                // Procesamos cada evidencia
                $denuncia['evidencias'] = array_map(function ($evidencia) {
                    return [
                        'data' => base64_encode($evidencia['archivo_path']),
                        'tipo' => $evidencia['tipo'],
                        'mime' => $evidencia['tipo'] === 'foto' ? 'image/jpeg' : 'video/mp4'
                    ];
                }, $evidencias);

                $denunciasConEvidencias[] = $denuncia;
            }

            return $denunciasConEvidencias;
        } catch (PDOException $e) {
            error_log("Error en obtenerDenunciasAsignadas: " . $e->getMessage());
            return [];
        }
    }

    public function asignarAgenteADenuncia($denunciaId, $agenteId)
    {
        try {
            $query = "UPDATE denuncias SET estado = 'en_proceso', agente_id = :agente_id WHERE id = :denuncia_id";
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':agente_id', $agenteId, PDO::PARAM_INT);
            $stmt->bindParam(':denuncia_id', $denunciaId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al asignar agente a denuncia: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPasosPorCategoria($categoriaId)
    {
        try {
            $query = "SELECT id_paso, descripcion_paso, orden 
                  FROM pasos_denuncia 
                  WHERE id_categoria = :categoria_id 
                  ORDER BY orden";
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':categoria_id', $categoriaId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener pasos de denuncia: " . $e->getMessage());
            return [];
        }
    }

    public function registrarSeguimientoInicial($denunciaId, $pasos)
    {
        try {
            $this->conexion->beginTransaction();

            // Insertar todos los pasos para esta denuncia
            $query = "INSERT INTO seguimiento_denuncia (id_denuncia, id_paso) 
                  VALUES (:denuncia_id, :paso_id)";
            $stmt = $this->conexion->prepare($query);

            foreach ($pasos as $paso) {
                $stmt->bindParam(':denuncia_id', $denunciaId, PDO::PARAM_INT);
                $stmt->bindParam(':paso_id', $paso['id_paso'], PDO::PARAM_INT);
                $stmt->execute();
            }

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error al registrar seguimiento: " . $e->getMessage());
            return false;
        }
    }

    public function verificarDenunciaPendiente($denunciaId, $categoriaId)
    {
        try {
            $query = "SELECT id FROM denuncias 
                  WHERE id = :denuncia_id 
                  AND categoria_id = :categoria_id
                  AND estado = 'pendiente'";
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':denuncia_id', $denunciaId, PDO::PARAM_INT);
            $stmt->bindParam(':categoria_id', $categoriaId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (PDOException $e) {
            error_log("Error al verificar denuncia: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerAgentePorId($agenteId)
    {
        try {
            $query = "SELECT id, nombre, email, categoria_id, telefono, foto_perfil 
                  FROM agentes 
                  WHERE id = :agente_id";
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':agente_id', $agenteId, PDO::PARAM_INT);
            $stmt->execute();

            $agente = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si no se encuentra el agente, retornar false
            if (!$agente) {
                return false;
            }

            return $agente;
        } catch (PDOException $e) {
            error_log("Error al obtener agente por ID: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerDenunciaConProtocolo($denunciaId, $agenteId)
    {
        try {
            $query = "SELECT d.*, c.nombre AS nombre_categoria 
                      FROM denuncias d
                      JOIN categorias c ON d.categoria_id = c.id
                      WHERE d.id = :denuncia_id 
                      AND d.agente_id = :agente_id
                      AND d.estado = 'en_proceso'";

            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':denuncia_id', $denunciaId, PDO::PARAM_INT);
            $stmt->bindParam(':agente_id', $agenteId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener denuncia: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPasosProtocolo($categoriaId)
    {
        try {
            $query = "SELECT id_paso, descripcion_paso, orden 
                      FROM pasos_denuncia
                      WHERE id_categoria = :categoria_id
                      ORDER BY orden";

            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':categoria_id', $categoriaId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener pasos del protocolo: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerEstadoPasos($denunciaId)
    {
        try {
            $query = "SELECT id_paso, completado, fecha_completado
                  FROM seguimiento_denuncia
                  WHERE id_denuncia = :denuncia_id";

            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':denuncia_id', $denunciaId, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Convertir a formato más útil
            $estados = [];
            foreach ($resultados as $row) {
                // Asegurarnos que 'completado' es booleano
                $row['completado'] = (bool)$row['completado'];
                $estados[$row['id_paso']] = $row;
            }

            return $estados;
        } catch (PDOException $e) {
            error_log("Error al obtener estados de pasos: " . $e->getMessage());
            return false;
        }
    }

    public function marcarPasoComoCompletado($denunciaId, $pasoId)
    {
        try {
            $this->conexion->beginTransaction();

            // Actualizar el paso como completado con la fecha/hora actual
            $query = "UPDATE seguimiento_denuncia 
                  SET completado = TRUE, fecha_completado = NOW() 
                  WHERE id_denuncia = :denuncia_id AND id_paso = :paso_id";

            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':denuncia_id', $denunciaId, PDO::PARAM_INT);
            $stmt->bindParam(':paso_id', $pasoId, PDO::PARAM_INT);
            $stmt->execute();

            // Verificar si todos los pasos están completados para actualizar el estado de la denuncia
            $this->verificarCompletadoProtocolo($denunciaId);

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error al marcar paso como completado: " . $e->getMessage());
            return false;
        }
    }

    private function verificarCompletadoProtocolo($denunciaId)
    {
        // Obtener todos los pasos de esta denuncia
        $queryPasos = "SELECT sd.id_paso, sd.completado, p.id_categoria
                   FROM seguimiento_denuncia sd
                   JOIN pasos_denuncia p ON sd.id_paso = p.id_paso
                   WHERE sd.id_denuncia = :denuncia_id";

        $stmtPasos = $this->conexion->prepare($queryPasos);
        $stmtPasos->bindParam(':denuncia_id', $denunciaId, PDO::PARAM_INT);
        $stmtPasos->execute();
        $pasos = $stmtPasos->fetchAll(PDO::FETCH_ASSOC);

        if (empty($pasos)) {
            return false;
        }

        // Verificar si todos están completados
        $todosCompletados = true;
        foreach ($pasos as $paso) {
            if (!$paso['completado']) {
                $todosCompletados = false;
                break;
            }
        }

        // Si todos están completados, actualizar estado de la denuncia
        if ($todosCompletados) {
            $queryUpdate = "UPDATE denuncias SET estado = 'resuelto' WHERE id = :denuncia_id";
            $stmtUpdate = $this->conexion->prepare($queryUpdate);
            $stmtUpdate->bindParam(':denuncia_id', $denunciaId, PDO::PARAM_INT);
            $stmtUpdate->execute();
        }

        return $todosCompletados;
    }

    public function guardarEvidenciaSeguimiento($idSeguimiento, $descripcion, $archivos)
    {
        try {
            $this->conexion->beginTransaction();

            // Obtener id_seguimiento
            $query = "SELECT id_seguimiento FROM seguimiento_denuncia 
                 WHERE id_denuncia = :denuncia_id AND id_paso = :paso_id";
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':denuncia_id', $idSeguimiento['denuncia_id'], PDO::PARAM_INT);
            $stmt->bindParam(':paso_id', $idSeguimiento['paso_id'], PDO::PARAM_INT);
            $stmt->execute();

            $seguimiento = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$seguimiento) {
                throw new Exception("No se encontró el seguimiento");
            }

            $idSeguimiento = $seguimiento['id_seguimiento'];

            // Insertar evidencias
            $query = "INSERT INTO evidencias_seguimiento 
                 (id_seguimiento, Descripcion, archivo_path) 
                 VALUES (:id_seguimiento, :descripcion, :archivo_path)";
            $stmt = $this->conexion->prepare($query);

            foreach ($archivos as $archivo) {
                $fileContent = file_get_contents($archivo['tmp_name']);
                $stmt->bindParam(':id_seguimiento', $idSeguimiento, PDO::PARAM_INT);
                $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
                $stmt->bindParam(':archivo_path', $fileContent, PDO::PARAM_LOB);
                $stmt->execute();
            }

            // Marcar paso como completado
            $query = "UPDATE seguimiento_denuncia 
                 SET completado = TRUE, fecha_completado = NOW() 
                 WHERE id_seguimiento = :id_seguimiento";
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':id_seguimiento', $idSeguimiento, PDO::PARAM_INT);
            $stmt->execute();

            // Verificar si todos los pasos están completados
            $this->verificarCompletadoProtocolo($idSeguimiento['denuncia_id']);

            $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            $this->conexion->rollBack();
            error_log("Error al guardar evidencia: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerEvidenciasPorSeguimiento($idSeguimiento)
    {
        try {
            $query = "SELECT id, Descripcion, archivo_path, fecha_subida 
                  FROM evidencias_seguimiento 
                  WHERE id_seguimiento = :id_seguimiento 
                  ORDER BY fecha_subida DESC";
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':id_seguimiento', $idSeguimiento, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener evidencias: " . $e->getMessage());
            return [];
        }
    }
}
