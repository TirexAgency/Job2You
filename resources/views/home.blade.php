<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Accueil - Job2You</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-briefcase-fill"></i> Job2You
            </a>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            <div class="p-5 mb-4 bg-light rounded-3">
                <div class="container-fluid py-5">
                    <h1 class="display-5 fw-bold">Bienvenue sur Job2You</h1>
                    <p class="col-md-8 fs-4">
                        Plateforme de matching d'offres d'emploi avec alertes SMS automatisées.
                        Trouvez le job qui vous correspond, recevez les offres compatibles directement sur votre téléphone.
                    </p>
                    <a href="#jobs" class="btn btn-primary btn-lg">Voir les offres</a>
                    <a href="#login" class="btn btn-outline-secondary btn-lg">Connexion</a>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-light py-4 mt-5">
        <div class="container text-center text-muted">
            <small>&copy; {{ date('Y') }} TekLab - Job2You MVP</small>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
