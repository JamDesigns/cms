<?php

return [
    'login' => [
        'username_or_email' => 'Nombre de usuario o correo electrónico',
        'forgot_password_link' => '¿Olvidaste tu contraseña?',
        'create_an_account' => 'Crear una cuenta',
    ],
    'password_confirm' => [
        'heading' => 'Confirmación de contraseña',
        'description' => 'Confirma tu contraseña para completar esta acción.',
        'current_password' => 'Contraseña actual',
    ],
    'two_factor' => [
        'heading' => 'Autenticación de dos factores',
        'description' => 'Teclea el código de autentificación proporcionado por tu aplicación de autentificación para confirmar el acceso a tu cuenta.',
        'code_placeholder' => 'XXX-XXX',
        'recovery' => [
            'heading' => 'Autentificación de dos factores',
            'description' => 'Teclea uno de tus códigos de recuperación de emergencia para confirmar el acceso a tu cuenta.',
        ],
        'recovery_code_placeholder' => 'abcdef-98765',
        'recovery_code_text' => '¿Perdiste tu dispositivo?',
        'recovery_code_link' => 'Utiliza un código de recuperación',
        'back_to_login_link' => 'Regresar a la página de acceso',
    ],
    'registration' => [
        'title' => 'Registro',
        'heading' => 'Creación de cuenta',
        'submit' => [
            'label' => 'Registrarse',
        ],
        'notification_unique' => 'Ya existe una cuenta asociada a este correo electrónico. Por favor accede.',
    ],
    'reset_password' => [
        'title' => 'Recuperación de contraseña',
        'heading' => 'Creación de una nueva contraseña',
        'submit' => [
            'label' => 'Enviar',
        ],
        'notification_error' => 'Error: inténtalo de nuevo más tarde.',
        'notification_error_link_text' => 'Inténtalo de nuevo',
        'notification_success' => 'Se ha enviado un correo, abre el mensaje para seguir las instrucciones.',
    ],
    'verification' => [
        'title' => 'Verificación de correo electrónico',
        'heading' => 'Debes verificar tu cuenta de correo electrónico',
        'submit' => [
            'label' => 'Cerrar sesión',
        ],
        'notification_success' => 'Se ha enviado un correo, abre el mensaje para seguir las instrucciones.',
        'notification_resend' => 'Se ha reenviado el correo con las instrucciones.',
        'before_proceeding' => 'Antes de continuar, verifica el correo que se te ha enviado y haz clic sobre el enlace de activación.',
        'not_receive' => 'Si no has recibido el mensaje de correo electrónico,',
        'request_another' => 'Haz clic aquí para solicitar otro',
    ],
    'profile' => [
        'account' => 'Cuenta',
        'profile' => 'Perfil',
        'subheading' => 'Desde aquí puedes gestionar tu perfil de usuario.',
        'my_profile' => 'Mi perfil',
        'personal_info' => [
            'heading' => 'Información personal',
            'subheading' => 'Administrar tu información personal.',
            'submit' => [
                'label' => 'Actualizar',
            ],
            'notify' => '¡Perfil actualizado exitosamente!',
        ],
        'password' => [
            'heading' => 'Contraseña',
            'subheading' => 'Debe contener al menos 8 caracteres.',
            'submit' => [
                'label' => 'Modificar',
            ],
            'notify' => '¡Contraseña actualizada exitosamente!',
        ],
        '2fa' => [
            'title' => 'Autentificación de dos factores',
            'description' => 'Administra el acceso a tu cuenta por autentificación de dos factores (recomendado).',
            'actions' => [
                'enable' => 'Habilitar',
                'regenerate_codes' => 'Regenerar los códigos de recuperación',
                'disable' => 'Deshabilitar',
                'confirm_finish' => 'Confirmar y terminar',
                'cancel_setup' => 'Cancelar la configuración',
            ],
            'setup_key' => 'Llave de configuración',
            'must_enable' => 'Debes habilitar la autenticación de dos factores para usar esta aplicación.',
            'not_enabled' => [
                'title' => 'Nno has habilitado la autentificación de dos factores.',
                'description' => 'Cuando la autentificación de dos factores se encuentra habilitada, se te pedirá un código aleatorio durante la autentificación. Puedes obtener dicho código desde la aplicación Autenticador de Google en tu teléfono.',
            ],
            'finish_enabling' => [
                'title' => 'Terminar la habilitación de la autentificación de dos factores.',
                'description' => 'Para terminar de habilitar la autentificación de dos factores, escanea el siguiente código QR utilizando la aplicación autenticadora de tu teléfono (por ejemplo, el Autenticador de Google) o teclea la llave de configuración e indica el código OTP generado.',
            ],
            'enabled' => [
                'notify' => 'Autenticación de dos factores habilitada.',
                'title' => '¡Hasa habilitado la autentificación de dos factores!',
                'description' => 'Se ha habilitado la autentificación de dos factores. Escanea el siguiente código QR mediante la aplicación autenticadora de tu teléfono (por ejemplo, el Autenticador de Google) o teclea la llave de configuración.',
                'store_codes' => 'Guarda estos códigos de recuperación en un administrador de contraseñas seguro. Pueden ser utilizadas para la recuperación del acceso a tu cuenta en caso de que el dispositivo asociado a tu autentificación de dos factores se pierda.',
                'show_codes' => 'Mostrar los códigos de recuperación',
                'hide_codes' => 'Esconder los códigos de recuperación',
            ],
            'disabling' => [
                'notify' => 'La autenticación de dos factores ha sido deshabilitada.',
            ],
            'regenerate_codes' => [
                'notify' => 'Se han generado nuevos códigos de recuperación.',
            ],
            'confirmation' => [
                'success_notification' => 'El código ha sido verificado. La autentificación de dos factores se ha habilitado.',
                'invalid_code' => 'El código tecleado no es válido.',
            ],
        ],
        'sanctum' => [
            'title' => 'Tokens de API',
            'description' => 'Administra los API tokens que permiten el acceso a esta aplicación a terceros en tu nombre. NOTA: tu token es mostrado una única vez después de su creación. Si pierdes tu token, deberás borrarlo y crear uno nuevo.',
            'create' => [
                'notify' => '¡Token creado exitosamente!',
                'message' => 'Tu token solo se muestra una vez después de la creación. Si pierdes tu token, deberás eliminarlo y crear uno nuevo.',
                'submit' => [
                    'label' => 'Nuevo',
                ],
            ],
            'update' => [
                'notify' => '¡Token actualizado exitosamente!',
            ],
            'copied' => [
                'label' => 'Copié mi ficha',
            ],
        ],
    ],
    'clipboard' => [
        'link' => 'Copiar al portapapeles',
        'tooltip' => '¡Copiado!',
    ],
    'fields' => [
        'avatar' => 'Avatar',
        'email' => 'Correo electrónico',
        'login' => 'Usuario',
        'name' => 'Nombre',
        'password' => 'Contraseña',
        'password_confirm' => 'Confirmar la contraseña',
        'new_password' => 'Nueva contraseña',
        'new_password_confirmation' => 'Confirma la nueva contraseña',
        'token_name' => 'Nombre del token',
        'token_expiry' => 'Caducidad del token',
        'abilities' => 'Capacidades',
        '2fa_code' => 'Código',
        '2fa_recovery_code' => 'Código de recuperación',
        'created' => 'Creado',
        'expires' => 'Expira',
    ],
    'or' => 'o',
    'cancel' => 'Cancelar',
];
