<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Wommate Technology' }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #d9d9d9;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, #1b97b1 0%, #dc2c8c 100%);
            text-align: center;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            height: 100px;
        }
        .email-logo-parent {
            text-align: center;
        }
        .email-logo {
            max-width: 200px;
            height: auto;
        }
        .email-body {
            padding: 40px 30px;
            color: #374151;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            color: #1b97b1;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
        }
        .button-secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 30px 0;
        }
        .social-links {
            margin-top: 20px;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #667eea;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 30px 20px;
            }
            .email-header {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-logo-parent">
            <img src="http://www.wommate.tech/img/logo_principal.png" alt="Wommate Learning" class="email-logo" />
        </div>
        <div class="email-header">
            {{-- <img src="http://www.wommate.tech/img/logo_principal.png" alt="Wommate Learning" class="email-logo" /> --}}
        </div>

        <!-- Body -->
        <div class="email-body">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p style="margin: 0 0 10px 0; color:#DC2C8C">
                <strong>Wommate Learning</strong>
            </p>
            <p style="margin: 0 0 20px 0; font-size: 13px;">
                Votre plateforme d'apprentissage en ligne
            </p>
            <div class="divider"></div>
            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                © {{ date('Y') }} Wommate Learning. Tous droits réservés.
            </p>
        </div>
    </div>
</body>
</html>