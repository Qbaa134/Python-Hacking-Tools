<?php
// Sprawdzamy, czy dane zostały wysłane z Androida
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['android_usb'])) {
        $android_usb = $_POST['android_usb'];
        echo json_encode(["usb_device" => $android_usb]);  // Przykładowa odpowiedź
    } else {
        echo json_encode(["error" => "Brak danych o urządzeniu USB"]);
    }
} else {
    // Jeśli brak danych z Androida, wykrywamy urządzenia lokalne (Linux/Windows)
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Wykrywanie urządzeń USB w systemie Windows
        $output = shell_exec('wmic logicaldisk where "drivetype=2" get deviceid');
        $lines = explode("\n", $output);
        $ports = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line == "DeviceID") continue;
            $ports[] = $line;  // Nazwy dysków USB (np. D:, E:)
        }
        echo json_encode($ports);
    } else {
        // Wykrywanie urządzeń USB w systemie Linux
        $output = shell_exec("lsblk -o NAME,SIZE,MOUNTPOINT | grep -E '^sd'");
        $lines = explode("\n", $output);
        $ports = [];
        foreach ($lines as $line) {
            if (empty($line)) continue;
            $parts = preg_split('/\s+/', $line);
            $ports[] = '/dev/' . $parts[0];
        }
        echo json_encode($ports);
    }
}
