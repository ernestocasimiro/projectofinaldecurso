<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Educacional</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #2563eb;
            --primary-light: #60a5fa;
            --secondary-color: #1e40af;
            --accent-color: #93c5fd;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --bg-light: #f8fafc;
            --white: #ffffff;
            --error-color: #ef4444;
            --error-bg: #fef2f2;
            --error-border: #fecaca;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
            --shadow-hover: 0 20px 40px rgba(59, 130, 246, 0.25);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100vh;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* Animated background elements */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.08)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.08)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.04)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.04)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.04)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            animation: float 20s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(1deg); }
        }

        .login-container {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            width: 90vw;
            height: 80vh;
            max-width: 850px;
            max-height: 500px;
            min-height: 420px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            position: relative;
            z-index: 1;
            transition: var(--transition);
        }

        .login-container:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-3px);
        }

        /* Left side - Form */
        .form-section {
            padding: 25px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--white);
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
        }

        .logo-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }

        .logo svg {
            width: 22px;
            height: 22px;
            fill: white;
        }

        .welcome-text h1 {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .welcome-text p {
            color: var(--text-light);
            font-size: 13px;
            font-weight: 400;
        }

        /* Alert Styles */
        .alert {
            padding: 8px 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            animation: slideInDown 0.5s ease-out;
        }

        .alert.error {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            color: var(--error-color);
        }

        .alert.success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: var(--success-color);
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form Group Styles */
        .form-group {
            margin-bottom: 15px;
            position: relative;
        }

        .form-group.error {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--text-dark);
            font-size: 12px;
            letter-spacing: 0.5px;
            transition: var(--transition);
        }

        .form-group.error label {
            color: var(--error-color);
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            z-index: 2;
            color: var(--text-light);
            transition: var(--transition);
        }

        .form-control {
            width: 100%;
            padding: 10px 12px 10px 36px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            font-family: inherit;
            background: #fafafa;
            transition: var(--transition);
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-control.error {
            border-color: var(--error-color);
            background: var(--error-bg);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .form-control.success {
            border-color: var(--success-color);
            background: #f0fdf4;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-control:focus + .input-icon {
            color: var(--primary-color);
        }

        .form-control.error + .input-icon {
            color: var(--error-color);
        }

        .form-control.success + .input-icon {
            color: var(--success-color);
        }

        /* Error Message */
        .error-message {
            display: none;
            color: var(--error-color);
            font-size: 11px;
            font-weight: 500;
            margin-top: 4px;
            padding-left: 12px;
            animation: fadeInUp 0.3s ease-out;
        }

        .error-message.show {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-light);
            transition: var(--transition);
            z-index: 2;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .forgot-password {
            text-align: right;
            margin-top: 4px;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 11px;
            font-weight: 500;
            transition: var(--transition);
        }

        .forgot-password a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            margin-top: 5px;
        }

        .login-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.6);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Validation Icons */
        .validation-icon {
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            opacity: 0;
            transition: var(--transition);
        }

        .validation-icon.show {
            opacity: 1;
        }

        .validation-icon.error {
            color: var(--error-color);
        }

        .validation-icon.success {
            color: var(--success-color);
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--error-color);
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease-out;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast.success {
            background: var(--success-color);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }

        /* Right side - Slideshow */
        .slideshow-section {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .slideshow-container {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            visibility: hidden;
            transition: opacity 1.2s ease-in-out, visibility 1.2s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slide.active {
            opacity: 1;
            visibility: visible;
            z-index: 1;
        }

        .slide img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            filter: brightness(0.6) contrast(1.1) saturate(1.1);
        }

        .slide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, 
                rgba(59, 130, 246, 0.85) 0%, 
                rgba(30, 64, 175, 0.75) 50%, 
                rgba(37, 99, 235, 0.85) 100%);
            z-index: 1;
        }

        .slide-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            padding: 25px;
            max-width: 85%;
            animation: slideInUp 1s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-content h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.2;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            letter-spacing: -0.5px;
        }

        .slide-content p {
            font-size: 14px;
            font-weight: 300;
            opacity: 0.95;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            line-height: 1.4;
        }

        .slide-nav {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 3;
        }

        .slide-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .slide-dot:hover {
            background: rgba(255, 255, 255, 0.7);
            transform: scale(1.1);
        }

        .slide-dot.active {
            background: white;
            transform: scale(1.2);
            border-color: rgba(255, 255, 255, 0.3);
        }

        /* Navigation arrows */
        .slide-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            transition: var(--transition);
            z-index: 3;
            backdrop-filter: blur(10px);
        }

        .slide-arrow:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-50%) scale(1.1);
        }

        .slide-arrow.prev {
            left: 12px;
        }

        .slide-arrow.next {
            right: 12px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                width: 95vw;
                height: 85vh;
                max-width: 350px;
                max-height: 450px;
                min-height: 400px;
            }

            .slideshow-section {
                display: none;
            }

            .form-section {
                padding: 20px 25px;
            }

            .welcome-text h1 {
                font-size: 20px;
            }

            .welcome-text p {
                font-size: 12px;
            }

            .toast {
                top: 10px;
                right: 10px;
                left: 10px;
                transform: translateY(-100px);
            }

            .toast.show {
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .login-container {
                width: 98vw;
                height: 90vh;
                max-width: 320px;
                max-height: 420px;
                min-height: 380px;
            }

            .form-section {
                padding: 15px 20px;
            }

            .welcome-text h1 {
                font-size: 18px;
            }

            .form-control {
                padding: 9px 10px 9px 32px;
                font-size: 12px;
            }

            .login-btn {
                padding: 9px;
                font-size: 12px;
            }

            .form-group {
                margin-bottom: 12px;
            }

            .logo-section {
                margin-bottom: 15px;
            }
        }

        @media (max-height: 600px) {
            .login-container {
                height: 95vh;
                max-height: 450px;
                min-height: 380px;
            }

            .form-section {
                padding: 15px 25px;
            }

            .logo-section {
                margin-bottom: 15px;
            }

            .form-group {
                margin-bottom: 12px;
            }
        }

        @media (max-height: 500px) {
            .login-container {
                height: 98vh;
                max-height: 400px;
                min-height: 350px;
            }

            .form-section {
                padding: 10px 20px;
            }

            .logo-section {
                margin-bottom: 10px;
            }

            .form-group {
                margin-bottom: 10px;
            }

            .welcome-text h1 {
                font-size: 18px;
            }

            .logo {
                width: 35px;
                height: 35px;
                margin-bottom: 8px;
            }

            .logo svg {
                width: 18px;
                height: 18px;
            }
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Slideshow pause on hover */
        .slideshow-container:hover .slide-nav,
        .slideshow-container:hover .slide-arrow {
            opacity: 1;
        }

        .slide-nav,
        .slide-arrow {
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
    </style>
</head>
<body>
    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
        </svg>
        <span id="toastMessage"></span>
    </div>

    <div class="login-container">
        <!-- Form Section -->
        <div class="form-section">
            <div class="logo-section">
                <div class="logo">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10c0 5.55 3.84 9.74 9 11 5.16-1.26 9-5.45 9-11V7l-10-5z"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>
                </div>
                <div class="welcome-text">
                    <h1>Bem-vindo de volta!</h1>
                    <p>Acesse sua conta para continuar</p>
                </div>
            </div>

            <?php if (isset($_GET['error'])) { ?>
            <div class="alert error">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
            <?php } ?>

            <form method="post" action="req/login.php" id="loginForm" novalidate>
                <div class="form-group" id="unameGroup">
                    <label for="uname">Nome de Utilizador</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        <input type="text" id="uname" name="uname" class="form-control" placeholder="Digite seu nome de utilizador">
                        <svg class="validation-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                    </div>
                    <div class="error-message" id="unameError">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                        <span>Nome de utilizador é obrigatório</span>
                    </div>
                </div>

                <div class="form-group" id="passGroup">
                    <label for="pass">Palavra-Passe</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                        </svg>
                        <input type="password" id="pass" name="pass" class="form-control" placeholder="Digite sua palavra-passe">
                        <button type="button" class="password-toggle" id="togglePassword">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                        </button>
                        <svg class="validation-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                    </div>
                    <div class="forgot-password">
                        <a href="#">Esqueceu a palavra-passe?</a>
                    </div>
                    <div class="error-message" id="passError">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                        <span>Palavra-passe é obrigatória</span>
                    </div>
                </div>

                <!-- Campo role adicionado como hidden -->
                <div class="form-group" id="roleGroup" style="display: none;">
                    <input type="hidden" id="role" name="role" value="1">
                </div>

                <button type="submit" name="login" class="login-btn" id="loginBtn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 17l5-5-5-5v3H3v4h7v3z"/>
                        <path d="M21 3H11c-1.1 0-2 .9-2 2v4h2V5h10v14H11v-4H9v4c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/>
                    </svg>
                    <span id="btnText">Entrar</span>
                </button>
            </form>
        </div>

        <!-- Slideshow Section -->
        <div class="slideshow-section">
            <div class="slideshow-container" id="slideshowContainer">
                <div class="slide active" data-slide="0">
                    <img src="images/estudantes01.jpg" alt="Estudantes na escola" loading="lazy">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <h2>Transforme o Futuro</h2>
                        <p>Conectando educadores e estudantes em uma jornada de descoberta e crescimento</p>
                    </div>
                </div>
                
                <div class="slide" data-slide="1">
                    <img src="images/estudantes02.jpeg" alt="Sala de aula" loading="lazy">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <h2>Educação Inovadora</h2>
                        <p>Ferramentas modernas para uma experiência de aprendizagem excepcional</p>
                    </div>
                </div>
                
                <div class="slide" data-slide="2">
                    <img src="images/estudantes03.jpeg" alt="Estudantes colaborando" loading="lazy">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <h2>Comunidade Unida</h2>
                        <p>Construindo pontes entre conhecimento, tecnologia e pessoas</p>
                    </div>
                </div>

                <!-- Navigation Arrows -->
                <button class="slide-arrow prev" id="prevSlide" aria-label="Slide anterior">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </button>
                
                <button class="slide-arrow next" id="nextSlide" aria-label="Próximo slide">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                    </svg>
                </button>

                <div class="slide-nav" id="slideNav">
                    <span class="slide-dot active" data-slide="0" aria-label="Ir para slide 1"></span>
                    <span class="slide-dot" data-slide="1" aria-label="Ir para slide 2"></span>
                    <span class="slide-dot" data-slide="2" aria-label="Ir para slide 3"></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation elements
            const form = document.getElementById('loginForm');
            const unameInput = document.getElementById('uname');
            const passInput = document.getElementById('pass');
            const roleInput = document.getElementById('role');
            const loginBtn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');

            // Validation functions
            function showError(fieldId, message) {
                const group = document.getElementById(fieldId + 'Group');
                const input = document.getElementById(fieldId);
                const errorMsg = document.getElementById(fieldId + 'Error');
                const validationIcon = group.querySelector('.validation-icon');

                group.classList.add('error');
                input.classList.add('error');
                input.classList.remove('success');
                errorMsg.classList.add('show');
                
                if (validationIcon) {
                    validationIcon.classList.add('show', 'error');
                    validationIcon.classList.remove('success');
                    validationIcon.innerHTML = '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>';
                }

                if (message) {
                    errorMsg.querySelector('span').textContent = message;
                }
            }

            function showSuccess(fieldId) {
                const group = document.getElementById(fieldId + 'Group');
                const input = document.getElementById(fieldId);
                const errorMsg = document.getElementById(fieldId + 'Error');
                const validationIcon = group.querySelector('.validation-icon');

                group.classList.remove('error');
                input.classList.remove('error');
                input.classList.add('success');
                errorMsg.classList.remove('show');
                
                if (validationIcon) {
                    validationIcon.classList.add('show', 'success');
                    validationIcon.classList.remove('error');
                    validationIcon.innerHTML = '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>';
                }
            }

             function clearValidation(fieldId) {
                const group = document.getElementById(fieldId + 'Group');
                const input = document.getElementById(fieldId);
                const errorMsg = document.getElementById(fieldId + 'Error');
                const validationIcon = group.querySelector('.validation-icon');

                group.classList.remove('error');
                input.classList.remove('error', 'success');
                errorMsg.classList.remove('show');
                
                if (validationIcon) {
                    validationIcon.classList.remove('show', 'error', 'success');
                }
            }

            function showToast(message, type = 'error') {
                toastMessage.textContent = message;
                toast.className = `toast ${type}`;
                toast.classList.add('show');

                setTimeout(() => {
                    toast.classList.remove('show');
                }, 4000);
            }

            function validateField(field) {
                const value = field.value.trim();
                const fieldId = field.id;

                switch (fieldId) {
                    case 'uname':
                        if (!value) {
                            showError(fieldId, 'Nome de utilizador é obrigatório');
                            return false;
                        } else if (value.length < 3) {
                            showError(fieldId, 'Nome deve ter pelo menos 3 caracteres');
                            return false;
                        } else {
                            showSuccess(fieldId);
                            return true;
                        }

                    case 'pass':
                        if (!value) {
                            showError(fieldId, 'Palavra-passe é obrigatória');
                            return false;
                        } else {
                            showSuccess(fieldId);
                            return true;
                        }

                    case 'role':
                        if (!value) {
                            showError(fieldId, 'Selecione um tipo de usuário');
                            return false;
                        } else {
                            showSuccess(fieldId);
                            return true;
                        }

                    default:
                        return true;
                }
            }

            function validateForm() {
                let isValid = true;
                const fields = [unameInput, passInput, roleSelect];

                fields.forEach(field => {
                    if (!validateField(field)) {
                        isValid = false;
                    }
                });

                return isValid;
            }

            // Real-time validation
            [unameInput, passInput, roleSelect].forEach(field => {
                field.addEventListener('blur', () => validateField(field));
                field.addEventListener('input', () => {
                    if (field.classList.contains('error')) {
                        validateField(field);
                    }
                });
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    showToast('Por favor, preencha todos os campos obrigatórios!');
                    
                    // Focus on first error field
                    const firstError = form.querySelector('.form-control.error');
                    if (firstError) {
                        firstError.focus();
                    }
                    return;
                }

                // Show loading state
                loginBtn.disabled = true;
                btnText.innerHTML = '<div class="loading"></div>';

                // Simulate form submission (replace with actual submission)
                setTimeout(() => {
                    // If validation passes, submit the form
                    form.submit();
                }, 1000);
            });

            // Slideshow functionality
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.slide-dot');
            const prevBtn = document.getElementById('prevSlide');
            const nextBtn = document.getElementById('nextSlide');
            const slideshowContainer = document.getElementById('slideshowContainer');
            
            let currentSlide = 0;
            let slideInterval;
            let isTransitioning = false;

            function showSlide(index, direction = 'next') {
                if (isTransitioning) return;
                
                isTransitioning = true;
                
                slides[currentSlide].classList.remove('active');
                dots[currentSlide].classList.remove('active');
                
                currentSlide = index;
                
                setTimeout(() => {
                    slides[currentSlide].classList.add('active');
                    dots[currentSlide].classList.add('active');
                    
                    setTimeout(() => {
                        isTransitioning = false;
                    }, 100);
                }, 50);
            }

            function nextSlide() {
                const next = (currentSlide + 1) % slides.length;
                showSlide(next, 'next');
            }

            function prevSlide() {
                const prev = (currentSlide - 1 + slides.length) % slides.length;
                showSlide(prev, 'prev');
            }

            function startSlideshow() {
                slideInterval = setInterval(nextSlide, 5000);
            }

            function stopSlideshow() {
                if (slideInterval) {
                    clearInterval(slideInterval);
                    slideInterval = null;
                }
            }

            function restartSlideshow() {
                stopSlideshow();
                setTimeout(startSlideshow, 3000);
            }

            // Slideshow event listeners
            dots.forEach((dot, index) => {
                dot.addEventListener('click', function() {
                    if (index !== currentSlide && !isTransitioning) {
                        showSlide(index);
                        restartSlideshow();
                    }
                });
            });

            nextBtn.addEventListener('click', function() {
                if (!isTransitioning) {
                    nextSlide();
                    restartSlideshow();
                }
            });

            prevBtn.addEventListener('click', function() {
                if (!isTransitioning) {
                    prevSlide();
                    restartSlideshow();
                }
            });

            slideshowContainer.addEventListener('mouseenter', stopSlideshow);
            slideshowContainer.addEventListener('mouseleave', startSlideshow);

            document.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowLeft' && !isTransitioning) {
                    prevSlide();
                    restartSlideshow();
                } else if (e.key === 'ArrowRight' && !isTransitioning) {
                    nextSlide();
                    restartSlideshow();
                }
            });

            startSlideshow();

            // Password toggle functionality
            const togglePassword = document.getElementById('togglePassword');

            togglePassword.addEventListener('click', function() {
                const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passInput.setAttribute('type', type);
                
                const icon = this.querySelector('svg path');
                if (type === 'text') {
                    icon.setAttribute('d', 'M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z');
                } else {
                    icon.setAttribute('d', 'M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z');
                }
            });

            // Input focus animations
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });

            // Preload images
            const imageUrls = [
                'images/estudantes01.jpg',
                'images/estudantes02.jpeg',
                'images/estudantes03.jpeg'
            ];

            imageUrls.forEach(url => {
                const img = new Image();
                img.src = url;
            });
        });
    </script>
</body>
</html>