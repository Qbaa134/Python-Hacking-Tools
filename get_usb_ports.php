<?php
// Funkcja do wykrywania urządzeń USB na systemie Linux
function getLinuxUsbPorts() {
    // Polecenie do wykrywania urządzeń USB w systemie Linux
    $output = shell_exec("lsblk -o NAME,SIZE,MOUNTPOINT | grep -E '^sd'");
    $lines = explode("\n", $output);
    $ports = [];
    foreach ($lines as $line) {
        if (empty($line)) continue;
        $parts = preg_split('/\s+/', $line);
        $ports[] = '/dev/' . $parts[0];  // Zakładamy, że urządzenia są w /dev/sd*
    }
    return $ports;
}

// Funkcja do wykrywania urządzeń USB na systemie Windows
function getWindowsUsbPorts() {
    // Polecenie do wykrywania urządzeń USB w systemie Windows
    $output = shell_exec('wmic logicaldisk where "drivetype=2" get deviceid');
    $lines = explode("\n", $output);
    $ports = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || $line == "DeviceID") continue;
        $ports[] = $line;  // Nazwy dysków USB (np. D:, E:)
    }
    return $ports;
}

// Rozpoznaj system operacyjny
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows
    echo json_encode(getWindowsUsbPorts());
} else {
    // Linux
    echo json_encode(getLinuxUsbPorts());
}
?>
