<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phoenix Collection</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .container {
            position: relative;
            min-height: 100vh;
            background: url("assets/bg-index.jpg") no-repeat;
            background-size: cover;
            background-position: center;
            justify-content: left;
            overflow: hidden;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); opacity: 0.3; }
            50% { transform: translateY(-20px); opacity: 0.6; }
        }

        /* Radial Overlay */

        /* Main Content */
        .content {
            position: relative;
            z-index: 10;
            max-width: 800px;
            width: 100%;
        }

        /* Phoenix Logo */
        .logo-container {
            margin-bottom: 10px;
            display: inline-block;
            transition: transform 0.3s ease;
            margin-left: 2em;
            margin-top: 2em;
        }

        .logo-container img {
            width: 6em;
        }

        .logo-container:hover {
            transform: scale(1.1);
        }

        .logo {
            position: relative;
            width: 160px;
            height: 160px;
            margin: 0 auto;
        }

        .logo-glow {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #f97316, #dc2626);
            border-radius: 50%;
            filter: blur(30px);
            opacity: 0.5;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.1); }
        }

        .logo-circle {
            position: relative;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border-radius: 50%;
            border: 4px solid #f97316;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .phoenix-icon {
            font-size: 80px;
            background: linear-gradient(135deg, #f97316, #dc2626);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Content Card */
        .card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            backdrop-filter: blur(20px);
            border-radius: 0 30px 30px 0;
            padding: 60px 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4);
            width: 80vw;
        }

        .title {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
            line-height: 1.2;
            text-align: center
        }

        .title-dark {
            color: #981008;
        }

        .title-gradient {
            background: linear-gradient(90deg, #fb923c, #dc2626, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: pulse 2s infinite;
        }

        .description {
            color: #e5e7eb;
            font-size: 1.25rem;
            line-height: 1.8;
            margin-bottom: 40px;
            padding: 0 20px;
        }

        /* Buttons */
        .buttons {
            display: flex;
            gap: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            position: relative;
            padding: 18px 50px;
            font-size: 1.25rem;
            font-weight: bold;
            color: white;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(220, 38, 38, 0.4);
            transition: all 0.3s ease;
            min-width: 160px;
        }

        .btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #f97316, #dc2626);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
            z-index: -1;
        }

        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(220, 38, 38, 0.6);
        }

        .btn:hover::before {
            transform: scaleX(1);
        }

        /* Decorative Wings */
        .wing {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.2;
            pointer-events: none;
            width: 200px;
            height: 400px;
        }

        .wing-left {
            left: 0;
        }

        .wing-right {
            right: 0;
            transform: translateY(-50%) scaleX(-1);
        }

        .wing svg {
            width: 100%;
            height: 100%;
            fill: url(#wingGradient);
            animation: wingPulse 3s infinite ease-in-out;
        }

        @keyframes wingPulse {
            0%, 100% { opacity: 0.2; }
            50% { opacity: 0.4; }
        }

        /* Bottom Glow */
        .bottom-glow {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            height: 150px;
            background: linear-gradient(to top, rgba(249, 115, 22, 0.3), transparent);
            filter: blur(60px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .title {
                font-size: 2.5rem;
            }

            .description {
                font-size: 1.1rem;
            }

            .card {
                padding: 40px 20px;
            }

            .logo {
                width: 120px;
                height: 120px;
            }

            .phoenix-icon {
                font-size: 60px;
            }

            .buttons {
                flex-direction: column;
                gap: 20px;
            }

            .btn {
                width: 100%;
            }

            .wing {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Background Particles -->
        <div class="particles" id="particles"></div>

        <!-- Radial Overlay -->
        <div class="overlay"></div>

        <!-- Main Content -->
        <div class="content">
            <!-- Phoenix Logo -->
            <div class="logo-container">
                <img src= "assets/index-logo.png">
            </div>

            <!-- Content Card -->
            <div class="card">
                <h1 class="title">
                    <span class="title-dark">Mulai Koleksi Pheonix Impianmu</span>
                </h1>

                <p class="description">
                    Rekrut berbagai tipe phoenix unik dengan menyelesaikan quest dan mengumpulkan poin. 
                    Daftar sekarang untuk menikmati pengalaman belanja phoenix yang berbeda dari lainnya.
                </p>

                <div class="buttons">
                    <a class="btn" href="auth/login_admin.php">Register</a>
                    <a class="btn" href="auth/login_user.php">Login</a>
                </div>
            </div>
        </div>



       
        <div class="bottom-glow"></div>
    </div>
</body>
</html>
