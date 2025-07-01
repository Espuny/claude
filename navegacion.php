<?php
require_once 'core/config/config.php';
require_once 'application/Session.php';

Session::init();

$autenticado = Session::get('autentificado');
$user = Session::get('user');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Navegación - Sistema de Menús</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .card-hover:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-primary mb-4">
                    <i class="fas fa-compass"></i>
                    Panel de Navegación - Sistema de Menús Afra Gestión
                </h1>

                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> Estado Actual</h5>
                    <p><strong>Autenticado:</strong> <?php echo $autenticado ? '✅ Sí' : '❌ No'; ?></p>
                    <?php if ($user): ?>
                    <p><strong>Usuario:</strong> <?php echo $user->usuario ?? 'N/A'; ?> (ID: <?php echo $user->id ?? 'N/A'; ?>)</p>
                    <p><strong>Rol:</strong> <?php echo $user->rol ?? 'N/A'; ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card card-hover h-100 position-relative">
                    <span class="badge bg-success status-badge">Funciona</span>
                    <div class="card-header bg-primary text-white">
                        <h5><i class="fas fa-sign-in-alt"></i> 1. Simular Login</h5>
                    </div>
                    <div class="card-body">
                        <p>Crea una sesión simulada con usuario admin para probar el sistema rápidamente.</p>
                        <a href="simular_login.php" class="btn btn-primary">
                            <i class="fas fa-play"></i> Ejecutar Simulación
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card card-hover h-100 position-relative">
                    <span class="badge bg-<?php echo $autenticado ? 'success' : 'warning'; ?> status-badge">
                        <?php echo $autenticado ? 'Disponible' : 'Requiere Login'; ?>
                    </span>
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-home"></i> 2. Página Principal Admin</h5>
                    </div>
                    <div class="card-body">
                        <p>Página principal del administrador donde debe aparecer el menú.</p>
                        <a href="indexAdmin" class="btn btn-success <?php echo !$autenticado ? 'disabled' : ''; ?>">
                            <i class="fas fa-arrow-right"></i> Ir a IndexAdmin
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card card-hover h-100 position-relative">
                    <span class="badge bg-info status-badge">Debug</span>
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-bug"></i> 3. Debug Menú</h5>
                    </div>
                    <div class="card-body">
                        <p>Herramientas de diagnóstico para verificar el estado del menú.</p>
                        <div class="d-grid gap-2">
                            <a href="debug_menu_sesion.php" class="btn btn-info btn-sm">
                                <i class="fas fa-search"></i> Debug Sesión
                            </a>
                            <a href="test_login_flow.php" class="btn btn-info btn-sm">
                                <i class="fas fa-flow"></i> Test Login Flow
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card card-hover h-100 position-relative">
                    <span class="badge bg-secondary status-badge">Test</span>
                    <div class="card-header bg-secondary text-white">
                        <h5><i class="fas fa-vial"></i> 4. Pruebas del Sistema</h5>
                    </div>
                    <div class="card-body">
                        <p>Pruebas técnicas del sistema de menús.</p>
                        <div class="d-grid gap-2">
                            <a href="test_menu_simple.php" class="btn btn-secondary btn-sm">
                                <i class="fas fa-test-tube"></i> Test Simple
                            </a>
                            <a href="prueba_final.php" class="btn btn-secondary btn-sm">
                                <i class="fas fa-check-circle"></i> Prueba Final
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card card-hover h-100 position-relative">
                    <span class="badge bg-dark status-badge">Real</span>
                    <div class="card-header bg-dark text-white">
                        <h5><i class="fas fa-key"></i> 5. Login Real</h5>
                    </div>
                    <div class="card-body">
                        <p>Sistema de login real usando la base de datos.</p>
                        <a href="login_real.php" class="btn btn-dark">
                            <i class="fas fa-sign-in-alt"></i> Login Real
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card card-hover h-100 position-relative">
                    <span class="badge bg-warning status-badge">Sistema</span>
                    <div class="card-header bg-warning text-dark">
                        <h5><i class="fas fa-cogs"></i> 6. Sistema Original</h5>
                    </div>
                    <div class="card-body">
                        <p>Acceso al sistema de login original de la aplicación.</p>
                        <a href="login" class="btn btn-warning">
                            <i class="fas fa-door-open"></i> Login Original
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($autenticado): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-success">
                    <h4><i class="fas fa-check-circle"></i> ¡Sistema Listo!</h4>
                    <p>Ya tienes una sesión activa. Puedes probar:</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="indexAdmin" class="btn btn-success">
                            <i class="fas fa-home"></i> Ver Página Principal con Menú
                        </a>
                        <a href="menu" class="btn btn-primary">
                            <i class="fas fa-bars"></i> Controlador de Menús
                        </a>
                        <a href="login/cerrar" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list-check"></i> Instrucciones de Prueba</h5>
                    </div>
                    <div class="card-body">
                        <ol>
                            <li><strong>Paso 1:</strong> Haz clic en "Ejecutar Simulación" para crear una sesión de prueba</li>
                            <li><strong>Paso 2:</strong> Ve a "Ir a IndexAdmin" para ver la página con el menú</li>
                            <li><strong>Paso 3:</strong> El menú debería aparecer en el lateral izquierdo</li>
                            <li><strong>Alternativa:</strong> Usa "Login Real" o "Login Original" para autenticarte con credenciales reales</li>
                        </ol>

                        <div class="mt-3">
                            <h6>URLs que funcionan:</h6>
                            <ul class="list-unstyled">
                                <li><code>http://localhost:8080/RESTO/gestion/simular_login.php</code></li>
                                <li><code>http://localhost:8080/RESTO/gestion/indexAdmin</code></li>
                                <li><code>http://localhost:8080/RESTO/gestion/login</code></li>
                                <li><code>http://localhost:8080/RESTO/gestion/menu</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
