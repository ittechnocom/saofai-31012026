<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "saofai";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saofai Environmental Monitor - Real-time Dashboard</title>
    
    <!-- Google Fonts - Distinctive Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Color System - Cyber-Environmental Theme */
            --bg-primary: #0a0e17;
            --bg-secondary: #0f1419;
            --bg-card: rgba(15, 20, 25, 0.95);
            --accent-cyan: #00f0ff;
            --accent-green: #00ff88;
            --accent-yellow: #ffd700;
            --accent-orange: #ff6b35;
            --accent-red: #ff3366;
            --accent-purple: #a855f7;
            --text-primary: #e8edf3;
            --text-secondary: #8b96a5;
            --border-glow: rgba(0, 240, 255, 0.3);
            
            /* Typography */
            --font-display: 'Orbitron', sans-serif;
            --font-body: 'Sarabun', sans-serif;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-body);
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        
        /* Animated Background Grid */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(0, 240, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 240, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
            pointer-events: none;
            z-index: 0;
        }
        
        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        
        /* Floating Particles */
        .particle {
            position: fixed;
            width: 3px;
            height: 3px;
            background: var(--accent-cyan);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
            opacity: 0;
            animation: particleFloat 15s linear infinite;
        }
        
        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) translateX(100px);
                opacity: 0;
            }
        }
        
        .container {
            max-width: 1800px;
            margin: 0 auto;
            padding: 30px;
            position: relative;
            z-index: 2;
        }
        
        /* Header with Glitch Effect */
        .header {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
            animation: slideDown 1s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header h1 {
            font-family: var(--font-display);
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: 900;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            position: relative;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .header h1::after {
            content: 'SAOFAI';
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: glitch 3s infinite;
            opacity: 0.8;
            clip-path: polygon(0 0, 100% 0, 100% 45%, 0 45%);
        }
        
        @keyframes glitch {
            0%, 100% { transform: translate(0); }
            33% { transform: translate(-2px, 2px); }
            66% { transform: translate(2px, -2px); }
        }
        
        .header p {
            font-size: 1.2rem;
            color: var(--text-secondary);
            font-weight: 300;
            letter-spacing: 3px;
        }
        
        /* Live Indicator */
        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: rgba(0, 255, 136, 0.1);
            border: 2px solid var(--accent-green);
            padding: 12px 30px;
            border-radius: 50px;
            margin-top: 20px;
            font-family: var(--font-display);
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 2px;
            box-shadow: 0 0 30px rgba(0, 255, 136, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 30px rgba(0, 255, 136, 0.3);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 50px rgba(0, 255, 136, 0.5);
                transform: scale(1.05);
            }
        }
        
        .pulse-dot {
            width: 12px;
            height: 12px;
            background: var(--accent-green);
            border-radius: 50%;
            position: relative;
            animation: pulseDot 2s ease-in-out infinite;
        }
        
        .pulse-dot::after {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            border: 2px solid var(--accent-green);
            border-radius: 50%;
            animation: ripple 2s ease-out infinite;
        }
        
        @keyframes pulseDot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        @keyframes ripple {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }
        
        /* Statistics Bar */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 50px;
            animation: fadeIn 1s ease 0.3s both;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stat-item {
            background: var(--bg-card);
            border: 1px solid rgba(0, 240, 255, 0.2);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .stat-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 240, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .stat-item:hover::before {
            left: 100%;
        }
        
        .stat-item:hover {
            transform: translateY(-5px);
            border-color: var(--accent-cyan);
            box-shadow: 0 10px 40px rgba(0, 240, 255, 0.3);
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-value {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Main Dashboard - 3x2 Grid */
        .dashboard {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 50px;
        }
        
        /* Sensor Cards */
        .sensor-card {
            background: var(--bg-card);
            border: 1px solid rgba(0, 240, 255, 0.2);
            border-radius: 20px;
            padding: 35px;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            animation: fadeInUp 0.8s ease both;
        }
        
        .sensor-card:nth-child(1) { animation-delay: 0.1s; }
        .sensor-card:nth-child(2) { animation-delay: 0.2s; }
        .sensor-card:nth-child(3) { animation-delay: 0.3s; }
        .sensor-card:nth-child(4) { animation-delay: 0.4s; }
        .sensor-card:nth-child(5) { animation-delay: 0.5s; }
        .sensor-card:nth-child(6) { animation-delay: 0.6s; }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .sensor-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--card-accent), transparent);
            transition: height 0.3s ease;
        }
        
        .sensor-card:hover::before {
            height: 100%;
            opacity: 0.05;
        }
        
        .sensor-card:hover {
            transform: translateY(-10px) scale(1.02);
            border-color: var(--card-accent);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5),
                        0 0 40px var(--card-accent-glow);
        }
        
        /* Card Color Themes */
        .sensor-card:nth-child(1) {
            --card-accent: var(--accent-orange);
            --card-accent-glow: rgba(255, 107, 53, 0.3);
        }
        
        .sensor-card:nth-child(2) {
            --card-accent: var(--accent-cyan);
            --card-accent-glow: rgba(0, 240, 255, 0.3);
        }
        
        .sensor-card:nth-child(3) {
            --card-accent: var(--accent-yellow);
            --card-accent-glow: rgba(255, 215, 0, 0.3);
        }
        
        .sensor-card:nth-child(4) {
            --card-accent: var(--accent-green);
            --card-accent-glow: rgba(0, 255, 136, 0.3);
        }
        
        .sensor-card:nth-child(5) {
            --card-accent: var(--accent-purple);
            --card-accent-glow: rgba(168, 85, 247, 0.3);
        }
        
        .sensor-card:nth-child(6) {
            --card-accent: var(--accent-red);
            --card-accent-glow: rgba(255, 51, 102, 0.3);
        }
        
        .card-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .card-icon {
            font-size: 3.5rem;
            filter: drop-shadow(0 0 10px var(--card-accent));
            animation: iconFloat 3s ease-in-out infinite;
        }
        
        @keyframes iconFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(5deg); }
        }
        
        .card-title {
            font-family: var(--font-display);
            font-size: 1.1rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 500;
        }
        
        .card-value {
            font-family: var(--font-display);
            font-size: 3.5rem;
            font-weight: 900;
            color: var(--card-accent);
            margin: 20px 0;
            display: flex;
            align-items: baseline;
            gap: 10px;
            text-shadow: 0 0 20px var(--card-accent-glow);
            position: relative;
        }
        
        .card-value::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 60%;
            height: 3px;
            background: linear-gradient(90deg, var(--card-accent), transparent);
            animation: scanLine 2s ease-in-out infinite;
        }
        
        @keyframes scanLine {
            0%, 100% { opacity: 0.3; width: 60%; }
            50% { opacity: 1; width: 80%; }
        }
        
        .card-unit {
            font-size: 1.2rem;
            color: var(--text-secondary);
            font-weight: 500;
        }
        
        .card-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            background: rgba(0, 240, 255, 0.1);
            border: 1px solid rgba(0, 240, 255, 0.3);
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 15px;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            background: var(--card-accent);
            border-radius: 50%;
            animation: statusBlink 2s ease-in-out infinite;
        }
        
        @keyframes statusBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        
        /* Data Table Section */
        .table-section {
            background: var(--bg-card);
            border: 1px solid rgba(0, 240, 255, 0.2);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            animation: fadeIn 1s ease 0.7s both;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .table-title {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .table-controls {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .table-controls select,
        .table-controls button {
            padding: 12px 24px;
            background: rgba(0, 240, 255, 0.1);
            border: 1px solid rgba(0, 240, 255, 0.3);
            border-radius: 10px;
            color: var(--text-primary);
            font-family: var(--font-body);
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .table-controls select:hover,
        .table-controls button:hover {
            background: rgba(0, 240, 255, 0.2);
            border-color: var(--accent-cyan);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 240, 255, 0.3);
        }
        
        .table-controls button {
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
            border: none;
            color: var(--bg-primary);
        }
        
        /* Table Styling */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        
        table thead {
            background: linear-gradient(135deg, rgba(0, 240, 255, 0.2), rgba(0, 255, 136, 0.2));
        }
        
        table th {
            padding: 18px;
            text-align: left;
            font-family: var(--font-display);
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--accent-cyan);
            border-bottom: 2px solid var(--accent-cyan);
        }
        
        table tbody tr {
            background: rgba(15, 20, 25, 0.5);
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        table tbody tr:hover {
            background: rgba(0, 240, 255, 0.1);
            border-left-color: var(--accent-cyan);
            transform: translateX(5px);
        }
        
        table td {
            padding: 18px;
            border-bottom: 1px solid rgba(0, 240, 255, 0.1);
            font-size: 0.95rem;
        }
        
        table td:first-child {
            font-weight: 700;
            color: var(--accent-cyan);
        }
        
        /* Loading States */
        .loading {
            text-align: center;
            padding: 80px;
            font-size: 1.5rem;
            color: var(--text-secondary);
        }
        
        .loading::after {
            content: '';
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid rgba(0, 240, 255, 0.2);
            border-top-color: var(--accent-cyan);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 20px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .error {
            background: rgba(255, 51, 102, 0.1);
            border: 2px solid var(--accent-red);
            color: var(--accent-red);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin: 30px 0;
            font-size: 1.2rem;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 30px;
            color: var(--text-secondary);
            font-size: 0.9rem;
            border-top: 1px solid rgba(0, 240, 255, 0.2);
            margin-top: 50px;
        }
        
        .footer .update-time {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(0, 240, 255, 0.1);
            padding: 15px 30px;
            border-radius: 10px;
            margin-top: 15px;
            font-family: var(--font-display);
        }
        
        /* Refresh Indicator */
        .refresh-indicator {
            position: fixed;
            top: 30px;
            right: 30px;
            background: var(--bg-card);
            border: 2px solid var(--accent-cyan);
            padding: 15px 30px;
            border-radius: 50px;
            display: none;
            align-items: center;
            gap: 15px;
            z-index: 1000;
            box-shadow: 0 10px 40px rgba(0, 240, 255, 0.5);
            animation: slideInRight 0.5s ease;
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .spinner {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(0, 240, 255, 0.3);
            border-top-color: var(--accent-cyan);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        /* Responsive Design */
        @media (max-width: 1400px) {
            .dashboard {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .stats-bar {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .sensor-card {
                padding: 25px;
            }
            
            .card-value {
                font-size: 2.5rem;
            }
            
            .table-section {
                padding: 20px;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Particles -->
    <script>
        for(let i = 0; i < 30; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 15 + 's';
            particle.style.animationDuration = (10 + Math.random() * 10) + 's';
            document.body.appendChild(particle);
        }
    </script>

    <!-- Refresh Indicator -->
    <div class="refresh-indicator" id="refreshIndicator">
        <div class="spinner"></div>
        <span>กำลังอัปเดต...</span>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>SAOFAI</h1>
            <p>ENVIRONMENTAL MONITORING SYSTEM</p>
            <div class="live-badge">
                <div class="pulse-dot"></div>
                <span>LIVE • REAL-TIME DATA</span>
            </div>
        </div>

        <?php
        // Get latest sensor data - FIXED: Changed table name and column names
        $sql_latest = "SELECT * FROM tb_sensor ORDER BY sensor_time DESC LIMIT 1";
        $result_latest = $conn->query($sql_latest);
        
        if ($result_latest && $result_latest->num_rows > 0) {
            $latest_data = $result_latest->fetch_assoc();
            
            // Calculate statistics - FIXED: Changed column names
            $sql_stats = "SELECT 
                COUNT(*) as total,
                AVG(sensor_temp) as avg_temp,
                AVG(sensor_humi) as avg_humi,
                AVG(sensor_dust) as avg_dust
                FROM tb_sensor";
            $result_stats = $conn->query($sql_stats);
            $stats = $result_stats->fetch_assoc();
        ?>

        <!-- Statistics Bar -->
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-label">Total Records</div>
                <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Avg Temperature</div>
                <div class="stat-value"><?php echo number_format($stats['avg_temp'], 1); ?>°C</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Avg Humidity</div>
                <div class="stat-value"><?php echo number_format($stats['avg_humi'], 1); ?>%</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Avg Dust Level</div>
                <div class="stat-value"><?php echo number_format($stats['avg_dust'], 3); ?></div>
            </div>
        </div>

        <!-- Main Dashboard - 3x2 Grid -->
        <div class="dashboard">
            <!-- Temperature Card - FIXED: Changed column name -->
            <div class="sensor-card">
                <div class="card-header">
                    <div class="card-icon">🌡️</div>
                    <div class="card-title">Temperature</div>
                </div>
                <div class="card-value">
                    <span><?php echo number_format($latest_data['sensor_temp'], 1); ?></span>
                    <span class="card-unit">°C</span>
                </div>
                <div class="card-status">
                    <div class="status-dot"></div>
                    <span>
                        <?php 
                        $temp = $latest_data['sensor_temp'];
                        if ($temp < 20) echo '❄️ Cold';
                        else if ($temp < 25) echo '😊 Comfortable';
                        else if ($temp < 30) echo '🌤️ Warm';
                        else echo '🔥 Hot';
                        ?>
                    </span>
                </div>
            </div>

            <!-- Humidity Card - FIXED: Changed column name -->
            <div class="sensor-card">
                <div class="card-header">
                    <div class="card-icon">💧</div>
                    <div class="card-title">Humidity</div>
                </div>
                <div class="card-value">
                    <span><?php echo number_format($latest_data['sensor_humi'], 1); ?></span>
                    <span class="card-unit">%</span>
                </div>
                <div class="card-status">
                    <div class="status-dot"></div>
                    <span>
                        <?php 
                        $humi = $latest_data['sensor_humi'];
                        if ($humi < 30) echo '🏜️ Very Dry';
                        else if ($humi < 60) echo '😊 Optimal';
                        else if ($humi < 80) echo '💦 Humid';
                        else echo '🌊 Very Humid';
                        ?>
                    </span>
                </div>
            </div>

            <!-- Light Card - FIXED: Changed column name -->
            <div class="sensor-card">
                <div class="card-header">
                    <div class="card-icon">💡</div>
                    <div class="card-title">Light Intensity</div>
                </div>
                <div class="card-value">
                    <span><?php echo number_format($latest_data['sensor_lux'], 0); ?></span>
                    <span class="card-unit">lux</span>
                </div>
                <div class="card-status">
                    <div class="status-dot"></div>
                    <span>
                        <?php 
                        $lux = $latest_data['sensor_lux'];
                        if ($lux < 100) echo '🌙 Dark';
                        else if ($lux < 500) echo '🏠 Indoor';
                        else if ($lux < 10000) echo '☁️ Daylight';
                        else echo '☀️ Bright Sun';
                        ?>
                    </span>
                </div>
            </div>

            <!-- Wind Speed Card - FIXED: Changed column name -->
            <div class="sensor-card">
                <div class="card-header">
                    <div class="card-icon">💨</div>
                    <div class="card-title">Wind Speed</div>
                </div>
                <div class="card-value">
                    <span><?php echo number_format($latest_data['sensor_windspeed'], 1); ?></span>
                    <span class="card-unit">m/s</span>
                </div>
                <div class="card-status">
                    <div class="status-dot"></div>
                    <span>
                        <?php 
                        $wind = $latest_data['sensor_windspeed'];
                        $kmh = $wind * 3.6;
                        if ($wind < 1) echo '🍃 Calm (' . number_format($kmh, 1) . ' km/h)';
                        else if ($wind < 3) echo '💨 Light (' . number_format($kmh, 1) . ' km/h)';
                        else if ($wind < 5) echo '🌬️ Moderate (' . number_format($kmh, 1) . ' km/h)';
                        else echo '🌪️ Strong (' . number_format($kmh, 1) . ' km/h)';
                        ?>
                    </span>
                </div>
            </div>

            <!-- Dust Card - FIXED: Changed column name -->
            <div class="sensor-card">
                <div class="card-header">
                    <div class="card-icon">🌫️</div>
                    <div class="card-title">Dust Level</div>
                </div>
                <div class="card-value">
                    <span><?php echo number_format($latest_data['sensor_dust'], 3); ?></span>
                    <span class="card-unit">mg/m³</span>
                </div>
                <div class="card-status">
                    <div class="status-dot"></div>
                    <span>
                        <?php 
                        $dust = $latest_data['sensor_dust'];
                        if ($dust < 0.05) echo '✨ Excellent';
                        else if ($dust < 0.1) echo '😊 Good';
                        else if ($dust < 0.15) echo '😐 Moderate';
                        else echo '⚠️ Poor';
                        ?>
                    </span>
                </div>
            </div>

            <!-- Air Quality Card -->
            <div class="sensor-card">
                <div class="card-header">
                    <div class="card-icon">🍃</div>
                    <div class="card-title">Air Quality</div>
                </div>
                <div class="card-value" style="font-size: 2rem; justify-content: center;">
                    <span style="text-shadow: none; color: 
                        <?php 
                        if ($dust < 0.05) echo 'var(--accent-green)';
                        else if ($dust < 0.1) echo 'var(--accent-cyan)';
                        else if ($dust < 0.15) echo 'var(--accent-yellow)';
                        else echo 'var(--accent-red)';
                        ?>
                    ">
                        <?php 
                        if ($dust < 0.05) echo 'EXCELLENT';
                        else if ($dust < 0.1) echo 'GOOD';
                        else if ($dust < 0.15) echo 'MODERATE';
                        else echo 'POOR';
                        ?>
                    </span>
                </div>
                <div class="card-status">
                    <div class="status-dot"></div>
                    <span>
                        <?php 
                        if ($dust < 0.05) echo '✨ Perfect air quality';
                        else if ($dust < 0.1) echo '😊 Safe to breathe';
                        else if ($dust < 0.15) echo '😐 Use mask if sensitive';
                        else echo '⚠️ Stay indoors';
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-section">
            <div class="table-header">
                <h2 class="table-title">📊 SENSOR HISTORY</h2>
                <div class="table-controls">
                    <form method="GET" style="display: flex; gap: 15px;">
                        <select name="limit" id="recordLimit" onchange="this.form.submit()">
                            <option value="10" <?php echo (!isset($_GET['limit']) || $_GET['limit'] == 10) ? 'selected' : ''; ?>>10 Records</option>
                            <option value="20" <?php echo (isset($_GET['limit']) && $_GET['limit'] == 20) ? 'selected' : ''; ?>>20 Records</option>
                            <option value="50" <?php echo (isset($_GET['limit']) && $_GET['limit'] == 50) ? 'selected' : ''; ?>>50 Records</option>
                            <option value="100" <?php echo (isset($_GET['limit']) && $_GET['limit'] == 100) ? 'selected' : ''; ?>>100 Records</option>
                        </select>
                    </form>
                    <button onclick="location.reload()">🔄 Refresh</button>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>🌡️ Temp</th>
                        <th>💧 Humidity</th>
                        <th>💡 Light</th>
                        <th>💨 Wind</th>
                        <th>🌫️ Dust</th>
                        <th>🕐 Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
                    // FIXED: Changed table name and column names
                    $sql_history = "SELECT * FROM tb_sensor ORDER BY sensor_time DESC LIMIT $limit";
                    $result_history = $conn->query($sql_history);
                    
                    if ($result_history && $result_history->num_rows > 0) {
                        $index = 1;
                        while ($row = $result_history->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><strong>{$index}</strong></td>";
                            echo "<td>" . number_format($row['sensor_temp'], 1) . " °C</td>";
                            echo "<td>" . number_format($row['sensor_humi'], 1) . " %</td>";
                            echo "<td>" . number_format($row['sensor_lux'], 0) . " lux</td>";
                            echo "<td>" . number_format($row['sensor_windspeed'], 1) . " m/s</td>";
                            echo "<td>" . number_format($row['sensor_dust'], 3) . " mg/m³</td>";
                            
                            $timestamp = new DateTime($row['sensor_time']);
                            $formatter = new IntlDateFormatter(
                                'th_TH',
                                IntlDateFormatter::LONG,
                                IntlDateFormatter::MEDIUM,
                                'Asia/Bangkok',
                                IntlDateFormatter::GREGORIAN
                            );
                            echo "<td>" . $formatter->format($timestamp) . "</td>";
                            echo "</tr>";
                            $index++;
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align: center; padding: 30px;'>No data available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>SYSTEM STATUS: OPERATIONAL</p>
            <div class="update-time">
                🕐 LAST UPDATE: 
                <span>
                    <?php 
                    $timestamp = new DateTime($latest_data['sensor_time']);
                    $formatter = new IntlDateFormatter(
                        'th_TH',
                        IntlDateFormatter::LONG,
                        IntlDateFormatter::MEDIUM,
                        'Asia/Bangkok',
                        IntlDateFormatter::GREGORIAN
                    );
                    echo $formatter->format($timestamp);
                    ?>
                </span>
            </div>
        </div>

        <?php
        } else {
            echo '<div class="error">⚠️ SYSTEM ERROR • UNABLE TO CONNECT TO DATABASE</div>';
        }
        
        $conn->close();
        ?>
    </div>

    <script>
        // Auto-refresh every 5 seconds
        setTimeout(function() {
            location.reload();
        }, 5000);
    </script>
</body>
</html>