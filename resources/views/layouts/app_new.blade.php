{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mon Stock – Dashboard</title>

  {{-- Bootstrap 5 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  {{-- FontAwesome pour icônes (optionnel mais utile) --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    body {
      min-height: 100vh;
      background-color: #f4f6f8;
      color: #1f2937;
      font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
    }
    .navbar-brand {
      font-weight: 700;
      letter-spacing: 0.2px;
    }
    .sidebar-link {
      color: #fff;
    }
    .card-btn {
      transition: transform .12s ease-in-out, box-shadow .12s ease-in-out;
      border-radius: .5rem;
    }
    .card-btn:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(31,41,55,0.08);
    }
    .location-card {
      transition: box-shadow .12s ease-in-out;
      border-radius: .6rem;
      overflow: hidden;
    }
    .location-card:hover {
      box-shadow: 0 8px 20px rgba(31,41,55,0.06);
    }

    /* Page container tweak */
    main.container-fluid {
      max-width: 1200px;
    }

    /* Cards and forms */
    .card {
      border: 0;
      border-radius: .6rem;
    }
    .card .card-body, .card .p-3 {
      padding: 1.0rem;
    }

    /* Buttons */
    .btn-primary {
      background-image: linear-gradient(90deg,#2563eb,#1e40af);
      border: none;
    }
    .btn-primary:hover {
      filter: brightness(.95);
    }

    /* List-group items subtle hover */
    .list-group-item {
      transition: background .12s ease-in-out, transform .08s ease-in-out;
    }
    .list-group-item:hover {
      background: #fbfdff;
      transform: translateY(-2px);
    }

    /* Form controls */
    .form-control, .form-select {
      border-radius: .45rem;
      box-shadow: none;
      border: 1px solid #e6e9ef;
    }
    .form-control:focus, .form-select:focus {
      box-shadow: 0 6px 20px rgba(37,99,235,0.08);
      border-color: #2563eb;
    }
  </style>
</head>
<body>

  {{-- Navbar--}}
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="{{ route("dashboard") }}">Gestion de Stock</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link" href="{{ route('stock-locations.index') }}">Locations</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('stock-items.index') }}">Items</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('stock-arrivals-admin.index') }}">Réception Livraison</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('stock-receptions.index') }}">Réception Stock</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('stock.movements') }}">Mouvements</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('statistics') }}">Statistiques</a></li>
        </ul>
        <ul class="navbar-nav ms-auto">
          @auth
            <li class="nav-item">
              <span class="nav-link text-white">{{ auth()->user()->name }}</span>
            </li>
            <li class="nav-item">
              <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">Déconnexion</button>
              </form>
            </li>
          @endauth
        </ul>
      </div>
    </div>
  </nav>

  <main class="container-fluid mt-4 px-4">
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  @yield('scripts')
</body>
</html>
