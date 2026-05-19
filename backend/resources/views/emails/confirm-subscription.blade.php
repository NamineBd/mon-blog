<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation d'abonnement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background-color: #4f46e5;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
            color: #333;
        }
        .button {
            display: inline-block;
            background-color: #4f46e5;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            background-color: #f4f4f4;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenue sur notre blog !</h1>
        </div>
        <div class="content">
            <p>Bonjour,</p>
            <p>Merci de vous être abonné à notre newsletter. Pour recevoir nos prochains articles, veuillez confirmer votre adresse email en cliquant sur le bouton ci-dessous :</p>
            <p style="text-align: center;">
                <a href="{{ $confirmationUrl }}" class="button">Confirmer mon abonnement</a>
            </p>
            <p>Si le bouton ne fonctionne pas, vous pouvez copier ce lien dans votre navigateur :<br>
            <a href="{{ $confirmationUrl }}">{{ $confirmationUrl }}</a>
            </p>
            <p>Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email.</p>
            <p>À très bientôt,<br>L'équipe du blog</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Blog Platform. Tous droits réservés.
        </div>
    </div>
</body>
</html>