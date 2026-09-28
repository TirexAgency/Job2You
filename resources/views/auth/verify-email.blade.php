<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vérification email - Job2You</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0"><i class="bi bi-briefcase-fill"></i> Job2You</h4>
                    </div>
                    <div class="card-body p-4 text-center">

                        <i class="bi bi-envelope-check display-1 text-primary"></i>

                        <h5 class="mt-3">Vérifiez votre adresse email</h5>
                        <p class="text-muted">
                            Un lien de vérification a été envoyé à votre adresse email.
                            Cliquez sur ce lien pour activer votre compte.
                        </p>

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('message'))
                            <div class="alert alert-success">
                                {{ session('message') }}
                            </div>
                        @endif

                        @if (session('resent'))
                            <div class="alert alert-success">
                                {{ session('resent') }}
                            </div>
                        @endif

                        <div class="d-grid gap-2">
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> Renvoyer le lien
                                </button>
                            </form>
                        </div>

                        <hr>
                        <p class="text-center mb-0">
                            <a href="{{ route('logout') }}">Se déconnecter</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
