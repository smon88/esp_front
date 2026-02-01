<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmando...</title>

    <style>
        *{
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .spinner-container {
            margin-bottom: 2rem;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(232, 17, 75, 0.2);
            border-left: 4px solid rgb(232, 17, 75);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 3rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <div>
        <div style="width: 90%; margin-top: 45%;">
            <div class="spinner-container">
                <div class="spinner"></div>
            </div>
        </div>
    </div>

    <script>
        // Redirige al siguiente step definido por Laravel
        setTimeout(() => {
            window.location.href = "{{ route('pago.bank.step', [
            'bank' => $bankSlug, 
            'step' => 1
        ]) }}";
        }, 6000);
    </script>
</body>
</html>