<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Iniciar Sesión - SADHCC</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('/imagenes/logo-app.ico') }}">
    <link rel="shortcut icon" href="{{ asset('/imagenes/logo-app.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body, html {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }
        .split-screen {
            display: flex;
            height: 100vh;
        }
        .login {
            flex: 0 0 33.333%;
            background: linear-gradient(135deg, #4e0d0dff 0%, #ff2200 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .logo-app {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo-app img {
            max-width: 120px;
            height: auto;
        }
        .login-title {
            text-align: center;
            color: #333;
            font-weight: bold;
            font-size: 1.5rem;
            margin-bottom: 2rem;
            letter-spacing: 1px;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-color: #a01508;
        }
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #4e0d0dff 0%, #a01508 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.3s;
            margin-bottom: 1rem;
        }
        .btn-login:hover {
            opacity: 0.9;
        }
        .btn-login:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            text-align: center;
        }
        .forgot-password {
            text-align: center;
        }
        .forgot-password a {
            color: #a01508;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
        .portada {
            flex: 0 0 66.667%;
            background-image: url('/imagenes/fondo-login.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .portada::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }
        .welcome-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
        }
        .school-logo {
            margin-bottom: 2rem;
        }
        .school-logo img {
            max-width: 150px;
            height: auto;
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.5));
        }
        .welcome-message {
            font-size: 2.5rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            letter-spacing: 2px;
            line-height: 1.3;
        }
        .welcome-subtitle {
            font-size: 1.2rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            margin-top: 1rem;
        }
        .register-link {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        .register-link a {
            color: #a01508;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="split-screen">
        <!-- Mensaje de bienvenida y fondo -->
        <div class="portada">
            <div class="welcome-content">
                <div class="school-logo">
                    <img src="/imagenes/logo-cecyte.jpg" alt="CECyTEZ" style="display: block; margin: 0 auto; width: 40%; max-width: 800px; height: auto;" onerror="this.style.display='none'">
                </div>
                <div class="welcome-message">
                    SISTEMA DE AGENDADO DIGITAL<br>DE HORARIOS DEL CENTRO DE COMPUTO<br>DEL CECyTEZ PLANTEL RÍO GRANDE
                </div>
                <div class="welcome-subtitle">
                    Plataforma Oficial del Centro de Cómputo
                </div>
            </div>
        </div>

        <!-- Panel de inicio de sesión -->
        <div class="login">
            <div class="login-container">
                <div class="logo-app">
                    <img src="/imagenes/logo-app.jpg" alt="Logo SGDI" style="display: block; margin: 0 auto;" onerror="this.style.display='none'">
                </div>
                
                <h2 class="login-title">INICIAR SESIÓN</h2>
                
                <form method="POST" action="{{ route('login') }}" autocomplete="off" id="loginForm">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">Usuario</label>
                        <input type="text" 
                               class="form-control @error('num_empleado') is-invalid @enderror" 
                               name="num_empleado" 
                               value="{{ old('num_empleado') }}" 
                               placeholder="Ingrese su número de empleado"
                               autocomplete="off"
                               readonly
                               onfocus="this.removeAttribute('readonly')"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Contraseña</label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               name="password" 
                               placeholder="Ingrese su contraseña"
                               required>
                    </div>
                    
                    @if($errors->any())
                        <div class="alert-danger" id="errorMessage">
                            @php
                                $error = $errors->first('login');
                                if (is_array($error)) {
                                    $error = $error['message'] ?? 'Error en las credenciales';
                                }
                            @endphp
                            {{ $error }}
                        </div>
                    @endif
                    
                    <button type="submit" class="btn-login" id="btnLogin">Acceder</button>
                    
                    <div class="forgot-password">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#recuperacionModal">
                            Olvidé mi contraseña
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Recuperación de Contraseña -->
    <div class="modal fade" id="recuperacionModal" tabindex="-1" aria-labelledby="recuperacionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="recuperacionModalLabel">
                        <i class="fas fa-key"></i> Recuperación de Contraseña
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="recuperacionMensaje" style="display: none;"></div>
                    
                    <form id="recuperacionForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email_recuperacion" class="form-label">
                                <i class="fas fa-envelope"></i> Correo Electrónico Personal
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email_recuperacion" 
                                   name="email" 
                                   placeholder="Ingrese su correo electrónico"
                                   autocomplete="off"
                                   readonly
                                   onfocus="this.removeAttribute('readonly')"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="num_empleado_recuperacion" class="form-label">
                                <i class="fas fa-id-badge"></i> Número de Empleado
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="num_empleado_recuperacion" 
                                   name="num_empleado" 
                                   placeholder="Ingrese su número de empleado"
                                   autocomplete="off"
                                   readonly
                                   onfocus="this.removeAttribute('readonly')"
                                   required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnValidarRecuperacion">
                        <i class="fas fa-check"></i> Validar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Abrir modal cuando se hace clic en "Olvidé mi contraseña"
        document.querySelector('.forgot-password a').addEventListener('click', function(e) {
            e.preventDefault();

            // Limpiar formulario y mensajes anteriores
            document.getElementById('email_recuperacion').value = '';
            document.getElementById('num_empleado_recuperacion').value = '';
            document.getElementById('recuperacionMensaje').style.display = 'none';
            document.getElementById('recuperacionMensaje').innerHTML = '';
            
            // Habilitar inputs y botón por si estaban deshabilitados
            document.getElementById('btnValidarRecuperacion').disabled = false;
            document.querySelectorAll('#recuperacionForm input').forEach(input => input.disabled = false);

            var modal = new bootstrap.Modal(document.getElementById('recuperacionModal'));
            modal.show();
        });

        // Validar recuperación
        document.getElementById('btnValidarRecuperacion').addEventListener('click', function() {
            const email = document.getElementById('email_recuperacion').value;
            const numEmpleado = document.getElementById('num_empleado_recuperacion').value;
            const mensajeDiv = document.getElementById('recuperacionMensaje');
            const btnValidar = this;
            const inputs = document.querySelectorAll('#recuperacionForm input');
            
            if (!email || !numEmpleado) {
                mostrarMensajeRecuperacion('Por favor complete todos los campos.', 'error');
                return;
            }

            // Deshabilitar botón y inputs durante el proceso
            btnValidar.disabled = true;
            inputs.forEach(input => input.disabled = true);
            
            fetch('{{ route("recuperacion.validar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email: email,
                    num_empleado: numEmpleado
                })
            })
            .then(response => response.json())
            .then(data => {
                mostrarMensajeRecuperacion(data.message, data.tipo);
                
                if (data.success) {
                    document.getElementById('email_recuperacion').value = '';
                    document.getElementById('num_empleado_recuperacion').value = '';
                    
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('recuperacionModal'));
                        if (modal) modal.hide();
                        mensajeDiv.style.display = 'none';
                        // Recargar la página para reiniciar el formulario de login
                        location.reload();
                    }, 3000);
                } else if (data.bloqueado) {
                    // Iniciar temporizador de bloqueo
                    let minutosRestantes = data.minutes;
                    let segundosRestantes = data.seconds;
                    
                    const timerInterval = setInterval(() => {
                        if (segundosRestantes <= 0) {
                            if (minutosRestantes <= 0) {
                                clearInterval(timerInterval);
                                // Recargar página cuando termine el bloqueo
                                location.reload();
                            } else {
                                minutosRestantes--;
                                segundosRestantes = 59;
                            }
                        } else {
                            segundosRestantes--;
                        }
                        
                        mostrarMensajeRecuperacion(
                            `Ha excedido el número de intentos. Intente nuevamente en ${minutosRestantes} minutos y ${segundosRestantes} segundos.`,
                            'error'
                        );
                    }, 1000);
                } else {
                    // Restaurar botón e inputs si no fue exitoso y no está bloqueado
                    btnValidar.disabled = false;
                    inputs.forEach(input => input.disabled = false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensajeRecuperacion('Error de conexión. Intente más tarde.', 'error');
                btnValidar.disabled = false;
                inputs.forEach(input => input.disabled = false);
            });
        });

        function mostrarMensajeRecuperacion(texto, tipo) {
            const mensajeDiv = document.getElementById('recuperacionMensaje');
            mensajeDiv.textContent = texto;
            mensajeDiv.className = tipo === 'success' ? 'alert alert-success' : 'alert alert-danger';
            mensajeDiv.style.display = 'block';
        }

        // TEMPORIZADOR DE BLOQUEO
        const errorElement = document.getElementById('errorMessage');
        
        if (errorElement) {
            const errorText = errorElement.innerText;
            
            // Verificar si el error contiene información de minutos (bloqueo)
            const minutosMatch = errorText.match(/(\d+)\s*minutos/);
            const segundosMatch = errorText.match(/(\d+)\s*segundos/);
            
            if (minutosMatch) {
                let minutosRestantes = parseInt(minutosMatch[1]);
                let segundosRestantes = segundosMatch ? parseInt(segundosMatch[1]) : 0;
                
                // Deshabilitar el formulario
                const form = document.getElementById('loginForm');
                const inputs = form.querySelectorAll('input, button');
                inputs.forEach(input => input.disabled = true);
                
                const timerInterval = setInterval(() => {
                    if (segundosRestantes <= 0) {
                        if (minutosRestantes <= 0) {
                            clearInterval(timerInterval);
                            location.reload();
                        } else {
                            minutosRestantes--;
                            segundosRestantes = 59;
                        }
                    } else {
                        segundosRestantes--;
                    }
                    
                    errorElement.innerHTML = `
                        <i class="fas fa-clock me-2"></i>
                        Ha excedido el número de intentos. 
                        Intente nuevamente en ${minutosRestantes} minutos y ${segundosRestantes} segundos.
                    `;
                }, 1000);
            }
        }
    });
    </script>
</body>
</html>