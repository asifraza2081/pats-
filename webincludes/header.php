<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PATS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

   
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap Icons (optional for Details button) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            scroll-behavior: smooth;
        }

        .hero-overlay {
            background: rgba(0, 0, 0, 0.55);
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 30px;
        }

        .icon-box {
            font-size: 40px;
            color: #0d6efd;
        }

        .hero-img {
            height: 520px;
            object-fit: cover;
        }

        /* 🔥 1 SECOND FADE */
        .carousel-fade .carousel-item {
            transition: opacity 1s ease-in-out;
        }

        /* Glassmorphism */
        .hero-glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 14px;
            color: #fff;
            max-width: 720px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        /* Center caption vertically */
        .carousel-caption {
            top: 0;
            bottom: 0;
        }

        /* Caption animation */
        .animate-caption {
            animation: fadeUp 1s ease;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(25px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .hero-img {
                height: 420px;
            }

            .hero-glass h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <?php include('webincludes/navbar.php') ?>