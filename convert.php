<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['file'];
    $inputFormat = $_POST['inputFormat'];
    $outputFormat = $_POST['outputFormat'];

    // Sprawdzenie, czy plik został poprawnie przesłany
    if ($file['error'] === UPLOAD_ERR_OK) {
        $tmpFilePath = $file['tmp_name'];
        $originalName = $file['name'];
        $newFileName = pathinfo($originalName, PATHINFO_FILENAME) . '.' . $outputFormat;
        $convertedFile = "/tmp/$newFileName";

        // Funkcje konwertujące na różne formaty
        function convertImage($tmpFilePath, $outputFormat, $convertedFile) {
            $image = new Imagick($tmpFilePath);
            $image->setImageFormat($outputFormat);
            $image->writeImage($convertedFile);
            return $convertedFile;
        }

        function convertAudio($tmpFilePath, $outputFormat, $convertedFile) {
            $ffmpegCommand = "ffmpeg -i $tmpFilePath $convertedFile";
            exec($ffmpegCommand);
            return $convertedFile;
        }

        function convertDocument($tmpFilePath, $outputFormat, $convertedFile) {
            if ($outputFormat == 'pdf') {
                // Możesz użyć narzędzi takich jak `unoconv` lub `libreoffice` do konwersji dokumentów
                $command = "libreoffice --headless --convert-to pdf $tmpFilePath --outdir " . dirname($convertedFile);
                exec($command);
                return $convertedFile;
            }
            return null;
        }

        function convertArchive($tmpFilePath, $outputFormat, $convertedFile) {
            $command = "7z x $tmpFilePath -o" . dirname($convertedFile);
            exec($command);
            return $convertedFile;
        }

        function convertScript($tmpFilePath, $outputFormat, $convertedFile) {
            // Skrypty można jedynie przekopiować lub przerobić na inne typy w zależności od wymagań
            // Na razie nie zmieniamy rozszerzenia, ale możemy to dostosować
            copy($tmpFilePath, $convertedFile);
            return $convertedFile;
        }

        // Warunki konwersji w zależności od formatu
        switch ($inputFormat) {
            case 'png':
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'svg':
            case 'bmp':
            case 'tiff':
            case 'webp':
                if ($outputFormat == 'png' || $outputFormat == 'jpg' || $outputFormat == 'gif') {
                    $convertedFile = convertImage($tmpFilePath, $outputFormat, $convertedFile);
                }
                break;

            case 'mp3':
            case 'wav':
            case 'flac':
            case 'aac':
            case 'ogg':
                if ($outputFormat == 'mp3' || $outputFormat == 'wav' || $outputFormat == 'flac') {
                    $convertedFile = convertAudio($tmpFilePath, $outputFormat, $convertedFile);
                }
                break;

            case 'txt':
            case 'csv':
            case 'json':
            case 'xml':
            case 'md':
            case 'html':
            case 'pdf':
            case 'doc':
            case 'docx':
            case 'xls':
            case 'xlsx':
                if ($outputFormat == 'pdf' || $outputFormat == 'docx' || $outputFormat == 'html') {
                    $convertedFile = convertDocument($tmpFilePath, $outputFormat, $convertedFile);
                }
                break;

            case 'zip':
            case 'tar':
            case 'tar.gz':
            case 'rar':
            case '7z':
            case 'iso':
            case 'gz':
                if ($outputFormat == 'zip' || $outputFormat == 'tar' || $outputFormat == 'rar') {
                    $convertedFile = convertArchive($tmpFilePath, $outputFormat, $convertedFile);
                }
                break;

            case 'py':
            case 'js':
            case 'php':
            case 'sh':
            case 'bat':
            case 'ps1':
                // Skrypty przenosimy z jednym rozszerzeniem na drugie
                $convertedFile = convertScript($tmpFilePath, $outputFormat, $convertedFile);
                break;

            default:
                echo "Unsupported format conversion!";
                exit;
        }

        // Sprawdzenie, czy plik konwertowany istnieje i odpowiedź do pobrania
        if (file_exists($convertedFile)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($convertedFile) . '"');
            readfile($convertedFile);
            unlink($convertedFile);
        } else {
            echo "File conversion failed.";
        }
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "No file uploaded.";
}

?>
