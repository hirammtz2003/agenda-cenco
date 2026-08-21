<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de Contraseña o de Email</title>
</head>
<body style="margin: 0; padding: 20px; font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; margin: 0 auto; background: white; border-collapse: collapse;">
        <!-- HEADER -->
        <tr>
            <td style="background-color: #8b0000; padding: 30px; text-align: center;">
                <h1 style="margin: 0; font-size: 24px; color: #ffffff;">Sistema de Agendado Digital de Horarios del Centro de Cómputo del CECyTEZ Plantel Río Grande</h1>
                <p style="margin: 10px 0 0; color: #ffffff;">Plataforma Oficial de Reservación</p>
            </td>
        </tr>
        
        <!-- CONTENIDO -->
        <tr>
            <td style="padding: 30px; background: white;">
                <h2 style="color: #333; margin-top: 0;">¡Hola, {{ $user->nombre }} {{ $user->apellido1 }}!</h2>
                
                <p>Le informamos que su contraseña de acceso al sistema, o bien, su correo electrónico personal, se ha actualizado exitosamente.</p>
                
                <!-- AVISO IMPORTANTE -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 20px 0; background: #fff3cd; border-left: 4px solid #ffc107;">
                    <tr>
                        <td style="padding: 15px;">
                            <strong style="color: #856404;">⚠️ IMPORTANTE:</strong>
                            <ul style="margin: 10px 0 0; padding-left: 20px; color: #856404;">
                                <li>Si usted no realizó este cambio, contacte inmediatamente al administrador del sistema.</li>
                                <li>Por seguridad, si reconoce el cambio, puede ignorar este mensaje.</li>
                                <li>Para cualquier aclaración, comuníquese con el área de sistemas.</li>
                            </ul>
                        </td>
                    </tr>
                </table>
                
                <!-- BOTÓN -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 20px 0;">
                    <tr>
                        <td align="center">
                            <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;">
                                <tr>
                                    <td style="background-color: #8b0000; border-radius: 5px;" bgcolor="#8b0000">
                                        <a href="{{ route('login') }}" style="display: inline-block; padding: 12px 30px; color: #ffffff; text-decoration: none; font-weight: bold; font-size: 16px;">Acceder al Sistema</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                
                <p style="color: #666; font-size: 14px;">
                    Si el botón no funciona, copie y pegue este enlace en su navegador:<br>
                    <span style="color: #8b0000;">{{ route('login') }}</span>
                </p>
            </td>
        </tr>
        
        <!-- FOOTER -->
        <tr>
            <td style="padding: 20px; text-align: center; background: #f8f9fa; font-size: 12px; color: #666;">
                <p style="margin: 0;">Este es un mensaje automático, favor de no responder.</p>
                <p style="margin: 10px 0 0;">&copy; {{ date('Y') }} CECyTEZ Plantel Río Grande - Todos los derechos reservados</p>
            </td>
        </tr>
    </table>
</body>
</html>