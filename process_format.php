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

    // Tutaj można wykonać operacje na systemie, np.:
    if ($clean_files) {
        // Przykładowe czyszczenie dysku (bardzo niebezpieczne w prawdziwym środowisku)
        // Przykład: usuwanie plików na wybranym dysku USB
        // Komenda bash do usunięcia plików (zależna od systemu)
        // WARNING: Używanie poniższego kodu w prawdziwym środowisku może prowadzić do trwałej utraty danych!
        $command = "rm -rf /mnt/" . escapeshellarg($usb_port) . "/*";
        // Uruchomienie komendy systemowej (prawdziwa operacja czyszczenia)
        // Możesz chcieć zaimplementować bardziej bezpieczne mechanizmy
        shell_exec($command);
        echo "Pliki zostały usunięte.";
    }

    // Formatowanie dysku (również zależne od systemu)
    // Przykład: formatowanie dysku (tutaj tylko przykład, nie uruchamiaj w środowisku produkcyjnym)
    $format_command = "mkfs." . escapeshellarg($format_type) . " /dev/" . escapeshellarg($usb_port);
    // Uruchomienie komendy formatowania
    // WARNING: Uruchamianie poniższego kodu w prawdziwym środowisku spowoduje utratę danych!
    shell_exec($format_command);
    echo "Dysk został sformatowany na typ " . htmlspecialchars($format_type) . ".";
} else {
    echo "Błąd: formularz nie został wysłany prawidłowo.";
}
?>
