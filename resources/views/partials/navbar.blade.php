<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('welcome') }}">
            <i class="fa-regular fa-calendar-days"></i> SADHCC CECyTEZ RG
        </a>
        
        @auth
        <div class="navbar-nav ms-auto">
            <span class="navbar-text me-3">
                ¡Hola, {{ Auth::user()->nombre }} {{ Auth::user()->apellido1 }}!
            </span>
                
            <!-- Dropdown de usuario -->
            <div class="user-dropdown">
                <button class="user-dropdown-btn">
                    <i class="fas fa-user-circle fa-2x"></i>
                </button>
                <div class="user-dropdown-content">
                    <a href="{{ route('mi-perfil') }}">
                        <i class="fas fa-id-card me-2"></i>Mi Perfil
                    </a>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                    </a>
                </div>
            </div>
                
            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                @csrf
            </form>
        </div>
        @endauth
    </div>
</nav>

<style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        /* Menú desplegable */
        .user-dropdown {
            position: relative;
            display: inline-block;
        }
        .user-dropdown-btn {
            background: transparent;
            border: none;
            color: white;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: background-color 0.3s;
        }
        .user-dropdown-btn:hover {
            background-color: rgba(255,255,255,0.1);
        }
        .user-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            border-radius: 5px;
            z-index: 1000;
        }
        .user-dropdown-content a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .user-dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .user-dropdown:hover .user-dropdown-content {
            display: block;
        }
    </style>