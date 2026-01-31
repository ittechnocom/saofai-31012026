<?php
// ========================================
// sensor_data.php (FIXED VERSION)
// ไฟล์รับข้อมูลจาก ESP32 และบันทึกลงฐานข้อมูล
// แก้ไข: Bug fix, Security, Rate limiting
// ========================================

header('Content-Type: application/json; charset=utf-8');

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "saofai";

// ฟังก์ชัน Rate Limiting (ป้องกัน Spam)
function checkRateLimit($conn) {
    // นับจำนวน requests ใน 1 นาทีล่าสุด
    $sql = "SELECT COUNT(*) as count FROM tb_sensor 
            WHERE sensor_time >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
    $result = $conn->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        // อนุญาตไม่เกิน 30 requests ต่อนาที
        if ($row['count'] >= 30) {
            return false;
        }
    }
    return true;
}

// ฟังก์ชันตรวจสอบค่าที่ได้รับ
function validateSensorData($value, $min, $max) {
    if ($value === null) return false;
    if (!is_numeric($value)) return false;
    if ($value < $min || $value > $max) return false;
    return true;
}

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    $response = array(
        "status" => "error",
        "message" => "Database connection failed",
        "code" => "DB_CONN_ERROR"
    );
    http_response_code(500);
    echo json_encode($response);
    exit();
}

// ตั้งค่า charset เป็น utf8
$conn->set_charset("utf8mb4");

// ตรวจสอบว่ามีข้อมูลส่งมาหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // ตรวจสอบ Rate Limit
    if (!checkRateLimit($conn)) {
        $response = array(
            "status" => "error",
            "message" => "Rate limit exceeded. Maximum 30 requests per minute.",
            "code" => "RATE_LIMIT"
        );
        http_response_code(429);
        echo json_encode($response);
        $conn->close();
        exit();
    }
    
    // รับข้อมูลจาก POST
    $temp = isset($_POST['temp']) ? floatval($_POST['temp']) : null;
    $humi = isset($_POST['humi']) ? floatval($_POST['humi']) : null;
    $lux = isset($_POST['lux']) ? floatval($_POST['lux']) : null;
    $windspeed = isset($_POST['windspeed']) ? floatval($_POST['windspeed']) : null;
    $dust = isset($_POST['dust']) ? floatval($_POST['dust']) : null;
    
    // Validation ranges
    $validations = array(
        'temp' => array('value' => $temp, 'min' => -50, 'max' => 100, 'name' => 'Temperature'),
        'humi' => array('value' => $humi, 'min' => 0, 'max' => 100, 'name' => 'Humidity'),
        'lux' => array('value' => $lux, 'min' => 0, 'max' => 200000, 'name' => 'Light'),
        'windspeed' => array('value' => $windspeed, 'min' => 0, 'max' => 100, 'name' => 'Wind Speed'),
        'dust' => array('value' => $dust, 'min' => 0, 'max' => 10, 'name' => 'Dust')
    );
    
    $errors = array();
    foreach ($validations as $key => $val) {
        if (!validateSensorData($val['value'], $val['min'], $val['max'])) {
            $errors[] = $val['name'] . " is invalid or out of range";
        }
    }
    
    // ถ้ามี errors
    if (!empty($errors)) {
        $response = array(
            "status" => "error",
            "message" => "Validation failed",
            "errors" => $errors,
            "received" => array(
                "temp" => $temp,
                "humi" => $humi,
                "lux" => $lux,
                "windspeed" => $windspeed,
                "dust" => $dust
            )
        );
        http_response_code(400);
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $conn->close();
        exit();
    }
    
    // ✅ FIX: กำหนดค่าตัวแปรก่อน bind_param
    // หมายเหตุ: ถ้าเปลี่ยน Database เป็น DECIMAL แล้ว ใช้ "ddddd" แทน "sssss"
    
    // สร้าง SQL query แบบ Prepared Statement
    $sql = "INSERT INTO tb_sensor 
            (sensor_temp, sensor_humi, sensor_lux, sensor_windspeed, sensor_dust, sensor_time) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // ถ้าใช้ DECIMAL ในฐานข้อมูล (แนะนำ)
        $stmt->bind_param("ddddd", $temp, $humi, $lux, $windspeed, $dust);
        
        // ถ้ายังใช้ VARCHAR (ไม่แนะนำ)
        // $temp_str = strval($temp);
        // $humi_str = strval($humi);
        // $lux_str = strval($lux);
        // $windspeed_str = strval($windspeed);
        // $dust_str = strval($dust);
        // $stmt->bind_param("sssss", $temp_str, $humi_str, $lux_str, $windspeed_str, $dust_str);
        
        // Execute statement
        if ($stmt->execute()) {
            $response = array(
                "status" => "success",
                "message" => "Data saved successfully",
                "inserted_id" => $stmt->insert_id,
                "timestamp" => date('Y-m-d H:i:s'),
                "data" => array(
                    "temperature" => $temp,
                    "humidity" => $humi,
                    "light" => $lux,
                    "windspeed" => $windspeed,
                    "dust" => $dust
                )
            );
            http_response_code(200);
        } else {
            $response = array(
                "status" => "error",
                "message" => "Error saving data",
                "code" => "DB_INSERT_ERROR",
                "error_detail" => $stmt->error
            );
            http_response_code(500);
        }
        
        $stmt->close();
    } else {
        $response = array(
            "status" => "error",
            "message" => "Error preparing statement",
            "code" => "DB_PREPARE_ERROR",
            "error_detail" => $conn->error
        );
        http_response_code(500);
    }
    
} else {
    $response = array(
        "status" => "error",
        "message" => "Invalid request method. Only POST is allowed.",
        "code" => "INVALID_METHOD"
    );
    http_response_code(405);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();

// ส่งผลลัพธ์กลับไปในรูปแบบ JSON
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>