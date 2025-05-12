<!DOCTYPE html>
<html>
<head>
    <title>Redirecionando...</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #0d9571;
            margin: 0;
        }
        .loading-text {
            color: white;
            font-size: 20px;
            text-align: center;
        }
        .spinner {
            border: 6px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 6px solid white;
            width: 40px;
            height: 40px;
            margin: 15px auto;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div>
        <div class="loading-text">Buscando datas disponíveis...</div>
        <div class="spinner"></div>
        
        <form id="redirectForm" action="{{ $route }}" method="POST">
            @csrf
            @foreach($params as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
        </form>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('redirectForm').submit();
        });
    </script>
</body>
</html>