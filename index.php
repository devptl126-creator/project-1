<?php
// index.php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RoadRescue – Roadside Assistance Platform</title>
  <style>
    :root {
      --primary: #2C687B;
      --primary-hover: #1e4653;
      --accent: #8CC7C4;
      --muted: #E6F3F2;
      --text: #0f172a;
      --text-muted: #4a5568;
      --card-bg: #FFFFFF;
      --border: #B0D7D5;
      --radius: 0.75rem;
      --shadow: 0 20px 25px -5px rgba(44, 104, 123, 0.1), 0 10px 10px -5px rgba(44, 104, 123, 0.04);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background-color: var(--muted);
      color: var(--text);
      line-height: 1.6;
      min-height: 100vh;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.25rem 2.5rem;
      background-color: var(--card-bg);
      border-bottom: 1px solid var(--border);
    }

    .brand {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--primary);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .nav-links {
      display: flex;
      gap: 1rem;
      align-items: center;
    }

    .btn {
      padding: 0.6rem 1.25rem;
      border-radius: var(--radius);
      font-size: 0.925rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
    }

    .btn-outline {
      border: 1.5px solid var(--border);
      color: var(--primary);
      background: transparent;
    }

    .btn-outline:hover {
      background: var(--muted);
    }

    .btn-primary {
      background: var(--primary);
      color: #FFFFFF;
      border: none;
    }

    .btn-primary:hover {
      background: var(--primary-hover);
      box-shadow: 0 4px 12px rgba(44, 104, 123, 0.3);
    }

    .hero {
      max-width: 1100px;
      margin: 3.5rem auto;
      text-align: center;
      padding: 0 1.5rem;
    }

    .hero h1 {
      font-size: 3.25rem;
      font-weight: 900;
      color: var(--primary);
      margin-bottom: 1rem;
      letter-spacing: -0.03em;
      line-height: 1.15;
    }

    .hero p {
      font-size: 1.15rem;
      color: var(--text-muted);
      max-width: 650px;
      margin: 0 auto 2.5rem;
    }

    /* 3D CARDS */
    .role-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
      margin-top: 3.5rem;
      padding: 0 1rem;
      perspective: 1000px;
    }

    .role-card {
      background: var(--card-bg);
      border: 1.5px solid var(--border);
      border-radius: var(--radius);
      padding: 2.5rem 2rem;
      box-shadow: 0 4px 6px -1px rgba(44, 104, 123, 0.05);
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      transform-style: preserve-3d;
      cursor: pointer;
      text-decoration: none;
      display: block;
      color: var(--text);
    }

    /* Unique Color Theme Classes for 3D Cards */
    .card-driver {
      --card-border: #0284c7;
      --card-shadow: rgba(2, 132, 199, 0.18);
    }
    .card-driver:hover {
      border-color: var(--card-border);
      box-shadow: 0 20px 25px -5px var(--card-shadow), 0 10px 10px -5px var(--card-shadow);
      background: #f0f9ff;
    }

    .card-garage {
      --card-border: #0d9488;
      --card-shadow: rgba(13, 148, 136, 0.18);
    }
    .card-garage:hover {
      border-color: var(--card-border);
      box-shadow: 0 20px 25px -5px var(--card-shadow), 0 10px 10px -5px var(--card-shadow);
      background: #f0fdfa;
    }

    .card-mechanic {
      --card-border: #d97706;
      --card-shadow: rgba(217, 119, 6, 0.18);
    }
    .card-mechanic:hover {
      border-color: var(--card-border);
      box-shadow: 0 20px 25px -5px var(--card-shadow), 0 10px 10px -5px var(--card-shadow);
      background: #fffbeb;
    }

    /* 3D Animation & Hover Effects */
    .role-card:hover {
      transform: translateY(-12px) rotateX(4deg) scale(1.02);
    }

    .role-card h3 {
      font-size: 1.45rem;
      font-weight: 800;
      color: var(--primary);
      margin-bottom: 1rem;
      transform: translateZ(24px);
    }

    .role-card p {
      font-size: 0.925rem;
      color: var(--text-muted);
      transform: translateZ(14px);
      line-height: 1.6;
    }
  </style>
</head>
<body>

  <nav class="navbar">
    <a href="#" class="brand">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path d="M19 17H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2Z"/>
        <path d="M9 17v2"/>
        <path d="M15 17v2"/>
      </svg>
      <span>RoadRescue</span>
    </a>
    <div class="nav-links">
      <a href="login.php" class="btn btn-outline">Log In</a>
      <a href="login.php" class="btn btn-primary">Sign Up</a>
    </div>
  </nav>

  <div class="hero">
    <h1>Your Trusted Partner in<br>Roadside Emergencies</h1>
    <p>Connect instantly with nearby garages, mechanics, and towing services. Fast, safe, and reliable tracking right from submission to completion.</p>
    
    <div style="display:flex; justify-content:center; gap:1.25rem;">
      <a href="login.php" class="btn btn-primary" style="padding: 0.75rem 1.75rem;">Get Started</a>
    </div>

    <div class="role-cards">
      <a href="login.php?role=driver" class="role-card card-driver">
        <h3>Driver Portal</h3>
        <p>Request towing services, battery jumps, or flat tire changes, and monitor the process in real-time.</p>
      </a>

      <a href="login.php?role=garage_owner" class="role-card card-garage">
        <h3>Garage Owner</h3>
        <p>Manage incoming jobs, assign skilled mechanics, track services, and run your workspace efficiently.</p>
      </a>

      <a href="login.php?role=mechanic" class="role-card card-mechanic">
        <h3>Mechanic Portal</h3>
        <p>Set availability, accept assigned service requests, track active jobs, and update completion status.</p>
      </a>
    </div>
  </div>

</body>
</html>