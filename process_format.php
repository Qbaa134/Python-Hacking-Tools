<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pobierz dane z formularza
    $usb_port = $_POST['usb_port'];
    $format_type = $_POST['format_type'];
    $clean_files = isset($_POST['clean_files']) ? true : false;

    // Wyświetl informacje na ekranie (w celach debugowania)
    echo "<h2>Wybrane opcje:</h2>";
    echo "Port USB: " . htmlspecialchars($usb_port) . "<br>";
    echo "Typ formatowania: " . htmlspecialchars($format_type) . "<br>";
    echo "Czy wyczyścić pliki: " . ($clean_files ? 'Tak' : 'Nie') . "<br>";

    // Komenda do usuwania plików
    if ($clean_files) {
        $command = "rm -rf /mnt/" . escapeshellarg($usb_port) . "/*";  // Linux
        shell_exec($command);
        echo "Pliki zostały usunięte.";
    }

    // Formatowanie dysku
    $format_command = "mkfs." . escapeshellarg($format_type) . " /dev/" . escapeshellarg($usb_port);
    // Użycie shell_exec do formatowania (Linux)
    shell_exec($format_command);
    echo "Dysk został sformatowany na typ " . htmlspecialchars($format_type) . ".";
} else {
    echo "Błąd: formularz nie został wysłany prawidłowo.";
}
?>
