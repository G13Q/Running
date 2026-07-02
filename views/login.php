<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/main.css" />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
    <script type="module" src="../assets/js/shared/nav.js" defer></script>
    <script type="module" src="../assets/js/shared/cart.js" defer></script>
    <style>
        .auth-page {
            padding-top: 120px;
            min-height: 100vh;
            background: #f0eee9;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .auth-form {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 400px;
            margin-top: 40px;
        }

        .auth-form h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 24px;
            color: #212121;
        }

        .auth-form label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
        }

        .auth-form input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

        .auth-form input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .auth-form button {
            width: 100%;
            padding: 12px;
            background: #212121;
            color: #fff;
            border: none;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .auth-form button:hover {
            background: #000;
        }

        .auth-form p {
            text-align: center;
            font-size: 0.85rem;
            color: #666;
            margin-top: 20px;
        }

        .auth-form a {
            color: #6366f1;
            font-weight: 600;
        }

        .auth-form__error {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 16px;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . "/components/navbar.php"; ?>

    <main class="auth-page">
        <div class="auth-form">
            <h1>Login</h1>

            <?php if (isset($error)): ?>
                <p class="auth-form__error"><?= e($error) ?></p>
            <?php endif; ?>

            <form method="POST" action="?route=login">
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required />
                </div>
                <div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required />
                </div>
                <button type="submit">Login</button>
            </form>

            <p>Don't have an account? <a href="?route=register">Register here</a></p>
        </div>
    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>
</body>

</html>