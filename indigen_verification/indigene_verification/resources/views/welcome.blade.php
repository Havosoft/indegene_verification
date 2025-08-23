<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Taraba State Indigene Verification Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            position: relative;
            overflow-x: hidden;
        }
        .bg-carousel {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100vw;
            height: 100vh;
            z-index: 0;
        }
        .bg-carousel img {
            position: absolute;
            top: 0; left: 0; width: 100vw; height: 100vh;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1.5s ease;
            will-change: transform, opacity;
            z-index: 0;
            animation: bgZoom 18s ease-in-out infinite;
        }
        .bg-carousel img.active {
            opacity: 1;
            z-index: 1;
        }
        @keyframes bgZoom {
            0% { transform: scale(1) translateY(0); }
            50% { transform: scale(1.08) translateY(-10px); }
            100% { transform: scale(1) translateY(0); }
        }
        .bg-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(255,255,255,0.65);
            z-index: 1;
        }
        .main-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 2;
        }
        
        .container {
            width: 100%;
            max-width: 480px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header {
            background: linear-gradient(135deg, #006b47 0%, #007a3d 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            animation: float 20s infinite linear;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-20px, -20px) rotate(360deg); }
        }
        
        .logo-container {
            position: relative;
            z-index: 2;
            margin-bottom: 25px;
        }
        
        .logo {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 3px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .logo::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, #007a3d, #006b47);
            opacity: 0.9;
            border-radius: 50%;
        }
        
        .logo-text {
            position: relative;
            z-index: 1;
            font-size: 32px;
            font-weight: 700;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .header-content {
            position: relative;
            z-index: 2;
        }
        
        .header h1 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 8px;
            line-height: 1.2;
        }
        
        .header h2 {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 12px;
            opacity: 0.95;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 400;
        }
        
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        
        .welcome-message {
            font-size: 16px;
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 35px;
            font-weight: 400;
        }
        
        .action-buttons {
            margin-bottom: 30px;
        }
        
        .btn {
            display: inline-block;
            width: 100%;
            padding: 16px 24px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #007a3d 0%, #006b47 100%);
            color: white;
            margin-bottom: 16px;
            box-shadow: 0 4px 15px rgba(0, 122, 61, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 122, 61, 0.4);
        }
        
        .btn-secondary {
            background: white;
            color: #007a3d;
            border: 2px solid #007a3d;
        }
        
        .btn-secondary:hover {
            background: #007a3d;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 122, 61, 0.3);
        }
        
        .divider {
            margin: 25px 0;
            text-align: center;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e2e8f0;
        }
        
        .divider span {
            background: white;
            padding: 0 20px;
            color: #718096;
            font-size: 14px;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .feature {
            text-align: center;
            padding: 20px 10px;
            border-radius: 12px;
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            transition: transform 0.3s ease;
        }
        
        .feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #007a3d, #006b47);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            color: white;
            font-size: 18px;
            font-weight: 600;
        }
        
        .feature h3 {
            font-size: 13px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 6px;
        }
        
        .feature p {
            font-size: 12px;
            color: #718096;
            line-height: 1.4;
        }
        
        .footer {
            background: #f7fafc;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer p {
            font-size: 13px;
            color: #718096;
            margin-bottom: 10px;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .footer-link {
            color: #007a3d;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .footer-link:hover {
            color: #006b47;
            text-decoration: underline;
        }
        
        .government-seal {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }
        
        .seal-icon {
            width: 24px;
            height: 24px;
            background: #007a3d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: 700;
        }
        
        .seal-text {
            font-size: 12px;
            color: #4a5568;
            font-weight: 500;
        }
        
        @media (max-width: 480px) {
            .main-wrapper {
                padding: 15px;
            }
            
            .container {
                border-radius: 15px;
                max-width: 100%;
            }
            
            .header {
                padding: 25px 20px;
            }
            
            .content {
                padding: 25px 20px;
            }
            
            .footer {
                padding: 20px;
            }
            
            .features {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .header h1 {
                font-size: 20px;
            }
            
            .header h2 {
                font-size: 15px;
            }
            
            .welcome-message {
                font-size: 15px;
            }
            
            .btn {
                padding: 14px 20px;
                font-size: 15px;
            }
        }
        
        @media (min-width: 481px) and (max-width: 768px) {
            .main-wrapper {
                padding: 30px;
            }
            
            .container {
                max-width: 520px;
            }
            
            .header {
                padding: 45px 35px;
            }
            
            .content {
                padding: 45px 35px;
            }
            
            .footer {
                padding: 30px 35px;
            }
            
            .logo {
                width: 110px;
                height: 110px;
            }
            
            .logo-text {
                font-size: 36px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .header h2 {
                font-size: 17px;
            }
            
            .welcome-message {
                font-size: 17px;
            }
            
            .btn {
                padding: 18px 28px;
                font-size: 17px;
            }
        }
        
        @media (min-width: 769px) and (max-width: 1024px) {
            .main-wrapper {
                padding: 40px;
            }
            
            .container {
                max-width: 580px;
            }
            
            .header {
                padding: 50px 40px;
            }
            
            .content {
                padding: 50px 40px;
            }
            
            .footer {
                padding: 35px 40px;
            }
            
            .logo {
                width: 120px;
                height: 120px;
            }
            
            .logo-text {
                font-size: 38px;
            }
            
            .header h1 {
                font-size: 26px;
            }
            
            .header h2 {
                font-size: 18px;
            }
            
            .welcome-message {
                font-size: 18px;
            }
            
            .btn {
                padding: 20px 32px;
                font-size: 18px;
            }
            
            .features {
                grid-template-columns: repeat(3, 1fr);
                gap: 25px;
            }
        }
        
        @media (min-width: 1025px) {
            .main-wrapper {
                padding: 50px;
            }
            
            .container {
                max-width: 650px;
            }
            
            .header {
                padding: 60px 50px;
            }
            
            .content {
                padding: 60px 50px;
            }
            
            .footer {
                padding: 40px 50px;
            }
            
            .logo {
                width: 140px;
                height: 140px;
            }
            
            .logo-text {
                font-size: 42px;
            }
            
            .header h1 {
                font-size: 28px;
            }
            
            .header h2 {
                font-size: 20px;
            }
            
            .welcome-message {
                font-size: 19px;
                margin-bottom: 40px;
            }
            
            .btn {
                padding: 22px 36px;
                font-size: 19px;
            }
            
            .features {
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
            }
            
            .feature {
                padding: 25px 15px;
            }
            
            .feature-icon {
                width: 50px;
                height: 50px;
                font-size: 22px;
            }
            
            .feature h3 {
                font-size: 15px;
            }
            
            .feature p {
                font-size: 14px;
            }
        }
        
        @media (min-width: 1400px) {
            .main-wrapper {
                padding: 60px;
            }
            
            .container {
                max-width: 750px;
            }
            
            .header {
                padding: 70px 60px;
            }
            
            .content {
                padding: 70px 60px;
            }
            
            .footer {
                padding: 45px 60px;
            }
            
            .logo {
                width: 160px;
                height: 160px;
            }
            
            .logo-text {
                font-size: 48px;
            }
            
            .header h1 {
                font-size: 32px;
            }
            
            .header h2 {
                font-size: 22px;
            }
            
            .welcome-message {
                font-size: 21px;
                margin-bottom: 45px;
            }
            
            .btn {
                padding: 24px 40px;
                font-size: 20px;
            }
            
            .features {
                gap: 35px;
            }
            
            .feature {
                padding: 30px 20px;
            }
            
            .feature-icon {
                width: 60px;
                height: 60px;
                font-size: 26px;
            }
            
            .feature h3 {
                font-size: 16px;
            }
            
            .feature p {
                font-size: 15px;
            }
        }
    </style>
    </head>
<body>
    <div class="bg-carousel">
        <img src="{{ asset('img/bg/tarabe1.jpeg') }}" class="active" alt="Taraba 1">
        <img src="{{ asset('img/bg/taraba2.jpg') }}" alt="Taraba 2">
        <img src="{{ asset('img/bg/taraba3.jpg') }}" alt="Taraba 3">
                        </div>
    <div class="bg-overlay"></div>
    <div class="main-wrapper">
        <div class="container">
        <div class="header">
            <div class="logo-container">
                <div class="logo">
                    <img src="{{ asset('img/taraba-state-logo-nedc.png') }}" alt="Taraba State Logo" style="width: 90px; height: 90px; object-fit: contain; border-radius: 50%; box-shadow: 0 4px 18px rgba(0,0,0,0.13); border: 3px solid #fff; position: relative; z-index: 2; background: #fff;" />
                                </div>
                                        </div>
            <div class="header-content">
                <h1>Government of Taraba State</h1>
                <h2>Indigene Verification Platform</h2>
                <p>Official Digital Identity Management System</p>
                                        </div>
                                    </div>

        <div class="content">
            <div class="welcome-message">
                Welcome to the official portal for verifying and managing indigene status in Taraba State, Nigeria. Access secure, government-certified digital identity services.
                                </div>
            
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">🔐</div>
                    <h3>Secure</h3>
                    <p>Government-grade security protection</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">⚡</div>
                    <h3>Fast</h3>
                    <p>Quick verification process</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">📱</div>
                    <h3>Digital</h3>
                    <p>Mobile-friendly platform</p>
                </div>
                                </div>

            <div class="action-buttons">
                <a href="/login" class="btn btn-primary">Access Portal</a>
                <a href="/register" class="btn btn-secondary">Create Account</a>
                                </div>

            <div class="divider">
                <span>Need assistance?</span>
                                </div>

            <p style="color: #718096; font-size: 14px;">
                Contact our support team at <br>
                <a href="mailto:support@taraba.gov.ng" style="color: #007a3d; text-decoration: none;">support@taraba.gov.ng</a>
                                    </p>
                                </div>

        <div class="footer">
            <div class="footer-links">
                <a href="#" class="footer-link">Privacy Policy</a>
                <a href="#" class="footer-link">Terms of Service</a>
                <a href="#" class="footer-link">Help Center</a>
                <a href="#" class="footer-link">Contact Us</a>
                                </div>
            <p>&copy; 2025 Government of Taraba State, Nigeria. All rights reserved.</p>
            <div class="government-seal">
                <div class="seal-icon">🏛️</div>
                <span class="seal-text">Official Government Platform</span>
                            </div>
                </div>
            </div>
        </div>
    <script>
        // Simple JS carousel for background images
        const images = document.querySelectorAll('.bg-carousel img');
        let current = 0;
        setInterval(() => {
            images[current].classList.remove('active');
            current = (current + 1) % images.length;
            images[current].classList.add('active');
        }, 5000);
    </script>
    </body>
</html>