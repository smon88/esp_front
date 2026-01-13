<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Login</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
  <div class="w-full max-w-md bg-white rounded-2xl shadow p-6">
    <h1 class="text-2xl font-bold text-gray-900">Acceso Admin</h1>
    <p class="text-sm text-gray-600 mt-1">Ingresa la clave para abrir el dashboard.</p>

    @if ($errors->any())
      <div class="mt-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-6 space-y-4">
      @csrf

      <div>
        <label class="block text-sm font-medium text-gray-700">Clave</label>
        <input
          type="password"
          name="password"
          required
          class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
          placeholder="********"
        />
      </div>

      <button type="submit"
        class="w-full rounded-lg bg-indigo-600 text-white py-2 font-semibold hover:bg-indigo-700">
        Entrar
      </button>
    </form>
  </div>
</body>
</html>
