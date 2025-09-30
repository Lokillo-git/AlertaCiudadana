<?php require_once __DIR__ . '/../config/database.php';

class AdministradorModel
{
    private $conexion;

    public function __construct()
    {
        $db = new Database();
        $this->conexion = $db->getConnection();
    }

    // Registrar un nuevo administrador en la base de datos.
    public function registrarAdministrador($nombre, $email, $passwordHash, $fotoPerfil = null)
    {
        try {
            // Modificación de la consulta para la nueva estructura de la tabla
            $sql = "INSERT INTO administradores (nombre, email, password, foto_perfil) 
                VALUES (:nombre, :email, :password, :foto_perfil)";

            $stmt = $this->conexion->prepare($sql);

            // Enlace de los parámetros a la consulta
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $passwordHash);
            $stmt->bindParam(':foto_perfil', $fotoPerfil, PDO::PARAM_LOB);

            // Ejecutar la consulta
            return $stmt->execute();
        } catch (PDOException $e) {
            // Puedes loguear el error si deseas
            return false;
        }
    }

    //Obtener un administrador por su email para iniciar sesion
    public function obtenerPorEmail($email)
    {
        try {
            $sql = "SELECT * FROM administradores WHERE email = :email LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            return $admin ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    //Obtener un agente por su email para iniciar sesion
    public function obtenerAgentePorEmail($email)
    {
        try {
            $sql = "SELECT * FROM agentes WHERE email = :email LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            return $admin ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    //listar todos los administradores
    public function obtenerTodos()
    {
        try {
            $sql = "SELECT id, nombre, email, foto_perfil, DATE(creado_en) as fecha_creacion FROM administradores ORDER BY creado_en DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    //Obtener un administrador por su ID
    public function obtenerPorId($id)
    {
        try {
            $sql = "SELECT id, nombre, email, foto_perfil FROM administradores WHERE id = :id LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    //Actualizar un administrador por su ID
    public function actualizarAdministrador($id, $nombre, $email, $fotoPerfil = null)
    {
        try {
            if ($fotoPerfil !== null) {
                $sql = "UPDATE administradores SET nombre = :nombre, email = :email, foto_perfil = :foto WHERE id = :id";
            } else {
                $sql = "UPDATE administradores SET nombre = :nombre, email = :email WHERE id = :id";
            }

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($fotoPerfil !== null) {
                $stmt->bindParam(':foto', $fotoPerfil, PDO::PARAM_LOB);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    //eliminar un administrador por su ID
    public function eliminarAdministrador($id)
    {
        try {
            $stmt = $this->conexion->prepare("DELETE FROM administradores WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Crear un nuevo trabajador
    public function registrarAgente($nombre, $email, $password, $telefono, $foto_perfil, $datos_faciales = null, $categoria_id = null)
    {
        try {
            $sql = "INSERT INTO agentes (nombre, email, password, telefono, foto_perfil, datos_faciales, categoria_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                $nombre,
                $email,
                $password,
                $telefono,
                $foto_perfil,
                $datos_faciales, // puede ir como null
                $categoria_id    // puede ir como null
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Obtener todos los agentes junto con su categoría
    public function obtenerAgentes()
    {
        try {
            $sql = "SELECT a.id, a.nombre, a.email, a.telefono, a.foto_perfil, a.creado_en, 
                       c.nombre AS categoria
                FROM agentes a
                LEFT JOIN categorias c ON a.categoria_id = c.id
                ORDER BY a.creado_en DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Obtener agente por ID para edición
    public function obtenerAgentePorId($id)
    {
        $sql = "SELECT * FROM agentes WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar agente existente
    public function actualizarAgente($id, $nombre, $email, $telefono, $categoria_id, $foto_perfil = null)
    {
        $sql = "UPDATE agentes SET nombre = ?, email = ?, telefono = ?, categoria_id = ?";
        $params = [$nombre, $email, $telefono, $categoria_id];

        if ($foto_perfil !== null) {
            $sql .= ", foto_perfil = ?";
            $params[] = $foto_perfil;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute($params);
    }

    // Eliminar agente por ID
    public function eliminarAgente($id)
    {
        try {
            $stmt = $this->conexion->prepare("DELETE FROM agentes WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Registrar una nueva categoría
    public function registrarCategoria($nombre, $encargado_id = null)
    {
        try {
            $sql = "INSERT INTO categorias (nombre, encargado_id) VALUES (?, ?)";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([$nombre, $encargado_id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Obtener todas las categorías con información del encargado
    public function obtenerCategorias()
    {
        try {
            $sql = "SELECT c.id, c.nombre, a.nombre AS nombre_encargado, a.telefono AS telefono_encargado
                FROM categorias c
                LEFT JOIN agentes a ON c.encargado_id = a.id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Obtener todas las categorías
    public function ObtenerTodasCategorias()
    {
        try {
            $sql = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Obtener nombre de la categoría, encargado y los agentes de esa categoría
    public function obtenerDatosCategoriaConAgentes($categoria_id)
    {
        try {
            // 1. Obtener la categoría y su encargado
            $sqlCategoria = "SELECT id, nombre, encargado_id FROM categorias WHERE id = ?";
            $stmtCat = $this->conexion->prepare($sqlCategoria);
            $stmtCat->execute([$categoria_id]);
            $categoria = $stmtCat->fetch(PDO::FETCH_ASSOC);

            if (!$categoria) return null;

            // 2. Obtener los agentes de esa categoría
            $sqlAgentes = "SELECT id, nombre, email FROM agentes WHERE categoria_id = ?";
            $stmtAgentes = $this->conexion->prepare($sqlAgentes);
            $stmtAgentes->execute([$categoria_id]);
            $agentes = $stmtAgentes->fetchAll(PDO::FETCH_ASSOC);

            return [
                'categoria' => $categoria,
                'agentes' => $agentes
            ];
        } catch (PDOException $e) {
            return null;
        }
    }

    // Editar una categoría existente
    public function actualizarCategoria($id, $nombre, $encargado_id = null)
    {
        try {
            $sql = "UPDATE categorias SET nombre = ?, encargado_id = ? WHERE id = ?";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([$nombre, $encargado_id, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerTodasDenuncias()
    {
        try {
            // Obtenemos todas las denuncias sin filtrar por estado
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
            d.categoria_id,
        FROM 
            denuncias d
        JOIN 
            categorias c ON d.categoria_id = c.id
        LEFT JOIN
            usuarios u ON d.agente_id = u.id
        ORDER BY 
            d.fecha_creacion DESC
        ";

            $stmtDenuncias = $this->conexion->prepare($queryDenuncias);
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
            error_log("Error en obtenerTodasDenuncias: " . $e->getMessage());
            return [];
        }
    }

    // Función para obtener todas las denuncias resueltas (para administrador)
    public function obtenerDenunciasResueltas()
    {
        try {
            // Obtenemos todas las denuncias con estado "resuelto"
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
            d.categoria_id,
            u.nombre AS nombre_agente
        FROM 
            denuncias d
        JOIN 
            categorias c ON d.categoria_id = c.id
        LEFT JOIN
            usuarios u ON d.agente_id = u.id
        WHERE 
            d.estado = 'resuelto'
        ORDER BY 
            d.fecha_creacion DESC
        ";

            $stmtDenuncias = $this->conexion->prepare($queryDenuncias);
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
            error_log("Error en obtenerDenunciasResueltas: " . $e->getMessage());
            return [];
        }
    }
}
