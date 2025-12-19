<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Bienvenue - UniBot</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

  <style>
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Roboto', sans-serif;
      overflow: hidden;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #0f2027;  /* dark base */
    }

    /* Particle background */
    #particles {
      position: absolute;
      width: 100%;
      height: 100%;
      z-index: 1;
      top: 0;
      left: 0;
    }

    .hero-box {
      position: relative;
      z-index: 2;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(15px);
      border-radius: 1.5rem;
      padding: 4rem 3rem;
      max-width: 500px;
      text-align: center;
      box-shadow: 0 20px 50px rgba(0,0,0,0.5);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .hero-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 25px 60px rgba(0,0,0,0.6);
    }

    .hero-box h1 {
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 1rem;
      color: #ffffff;
    }

    .hero-box p {
      font-size: 1.2rem;
      margin-bottom: 2rem;
      color: #e0e0ff;
    }

    .btn-primary {
      background: linear-gradient(90deg, #6a11cb, #2575fc);
      border: none;
      font-size: 1.1rem;
      padding: 0.75rem 2rem;
      border-radius: 50px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.5);
      background: linear-gradient(90deg, #2575fc, #6a11cb);
    }

    @media (max-width: 768px) {
      .hero-box {
        padding: 3rem 2rem;
      }
      .hero-box h1 {
        font-size: 2rem;
      }
    }

    /* Floating shapes */
    .shape {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      animation: float 15s linear infinite;
      z-index: 0;
    }

    @keyframes float {
      0% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-50px) rotate(180deg); }
      100% { transform: translateY(0) rotate(360deg); }
    }
  </style>
</head>
<body>

<!-- Floating background shapes -->
<div class="shape" style="width:80px;height:80px;top:10%;left:15%;animation-duration:12s;"></div>
<div class="shape" style="width:50px;height:50px;top:30%;left:70%;animation-duration:18s;"></div>
<div class="shape" style="width:100px;height:100px;top:70%;left:40%;animation-duration:20s;"></div>
<div class="shape" style="width:60px;height:60px;top:50%;left:80%;animation-duration:16s;"></div>

<!-- Hero box -->
<div class="hero-box">
  <h1>Bienvenue sur UniBot</h1>
  <p>L’assistant intelligent qui simplifie vos tâches  universitaire et répond à vos questions avant même que vous commenciez.</p>
  <a href="index.php" class="btn btn-primary">
    <i class="bi bi-box-arrow-in-right me-2"></i> Accéder à UniBot
  </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

