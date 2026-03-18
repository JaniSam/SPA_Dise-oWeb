<?php
header("Access-Control-Allow-Origin: *"); // Permite que Vue acceda al PHP
header("Content-Type: application/json");

$host = "localhost";
$port = "5432";
$dbname = "tu_nombre_de_bd";
$user = "postgres";
$password = "tu_contraseña";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $sql = "SELECT 
                r.id,
                r.fecha,
                r.hora_inicio,
                r.hora_fin,
                r.estado,
                c.nombre AS cliente_nombre,
                t.nombre AS terapeuta_nombre,
                s.nombre AS servicio_nombre
            FROM public.reservas r
            JOIN public.usuarios c ON r.cliente_id = c.id
            JOIN public.usuarios t ON r.terapeuta_id = t.id
            JOIN public.servicios s ON r.servicio_id = s.id";

    $stmt = $pdo->query($sql);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mapeo de estados para coincidir con tu lógica de Vue
    $statusMap = [
        'PENDIENTE'  => 'pending',
        'CONFIRMADA' => 'confirmed',
        'CANCELADA'  => 'cancelled'
    ];

    $statusConfig = [
        'pending'   => ['bg' => '#FEF3C7', 'border' => '#F59E0B', 'text' => '#92400E'],
        'confirmed' => ['bg' => '#D1FAE5', 'border' => '#10B981', 'text' => '#065F46'],
        'cancelled' => ['bg' => '#FEE2E2', 'border' => '#EF4444', 'text' => '#991B1B'],
    ];

    $eventos = [];
    foreach ($reservas as $row) {
        $statusKey = $statusMap[$row['estado']] ?? 'pending';
        $config = $statusConfig[$statusKey];

        $eventos[] = [
            'id'              => $row['id'],
            'title'           => $row['servicio_nombre'] . " - " . $row['cliente_nombre'],
            'start'           => $row['fecha'] . 'T' . $row['hora_inicio'],
            'end'             => $row['fecha'] . 'T' . $row['hora_fin'],
            'backgroundColor' => $config['bg'],
            'borderColor'     => $config['border'],
            'textColor'       => $config['text'],
            'className'       => "status-border-$statusKey",
            'extendedProps'   => [
                'status' => $statusKey,
                'obs'    => "Terapeuta: " . $row['terapeuta_nombre'] // O el campo real de notas
            ]
        ];
    }

    echo json_encode($eventos);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}