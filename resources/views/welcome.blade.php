<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Operations</title>
    <link rel="icon" type="image/x-icon" href="images/itopslogo.png">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
        /* Full-page dark night background */
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #0d1b2a;
            margin: 0;
            font-family: Arial, sans-serif;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        /* Canvas for animation */
        canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        /* Logo */
        img {
            width: 200px;
            height: auto;
            margin-bottom: 20px;
        }

        /* Google Sign-In Button */
        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #db4437;
            color: #fff;
            font-weight: 600;
            padding: 12px;
            border-radius: 6px;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: background 0.3s;
            font-size: 16px;
        }

        /* Hover effect */
        .google-btn:hover {
            background: #c1351d;
        }

        /* Google Icon */
        .google-btn i {
            font-size: 18px;
            margin-right: 10px;
        }
</style>
</head>
<body>
    <canvas id="backgroundCanvas"></canvas>
    
    <!-- Logo (Replace with your image) -->
    <img src="images/itopslogo.png" alt="Your Logo">

    <!-- Google Sign-In Button -->
    <a href="{{ route('google.login') }}" class="google-btn">
        <i class="fa-brands fa-google"></i> Sign in with Google
    </a>

    <script>
        const canvas = document.getElementById('backgroundCanvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        
        let stars = [];
        for(let i = 0; i < 100; i++) {
            stars.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                radius: Math.random() * 2,
                speed: Math.random() * 0.5 + 0.2
            });
        }
        
        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#ffffff';
            stars.forEach(star => {
                ctx.beginPath();
                ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
                ctx.fill();
                star.y += star.speed;
                if (star.y > canvas.height) star.y = 0;
            });
            requestAnimationFrame(animate);
        }
        animate();
        
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });
    </script>
</body>
</html>