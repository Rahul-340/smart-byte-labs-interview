<?php
include 'db.php';

$ignorWords = ['and', 'the', 'of', 'to', 'a', 'is', 'that', 'with', 'on', 'for', 'as', 'was', 'have', 'are', 'from'];

if (isset($_POST['submit'])) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        if ($fileExtension !== 'txt') {
            echo "Please upload a valid .txt file.";
            exit;
        }
        $fileLines = file($fileTmpPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $totalLines = count($fileLines);
        // echo "<pre>";
        // print_r($fileLines);die;

        $numSections = 3;
        $chunkSize = ceil($totalLines / $numSections);

        $totalFileWordCount = 0;
        $results = [];
        for ($i = 0; $i < $numSections; $i++) {
            $start = $i * $chunkSize;
            $end = min(($i + 1) * $chunkSize, $totalLines);
            $sectionLines = array_slice($fileLines, $start, $end - $start);

            $paragraphs = [];
            $currentParagraph = [];
            foreach ($sectionLines as $line) {
                if (trim($line) === '') {
                    if (!empty($currentParagraph)) {
                        $paragraphs[] = $currentParagraph;
                        $currentParagraph = [];
                    }
                } else {
                    $currentParagraph[] = $line;
                }
            }
            if (!empty($currentParagraph)) {
                $paragraphs[] = implode("\n", $currentParagraph);
            }

            $results[] = [
                'section' => $i + 1,
                'line_count' => count($sectionLines),
                'paragraphs' => $paragraphs,
            ];
        }

        $sql = "INSERT INTO uploads (file_name, line_count) VALUES ('$fileName', $totalLines)";
        if ($conn->query($sql) === TRUE) {
            echo "File '$fileName' uploaded successfully.<br>";
            echo "Total lines: $totalLines<br>";

            foreach ($results as $result) {
                echo "Section {$result['section']}:<br>";
                echo "Lines: {$result['line_count']}<br>";
                // print_r($result['paragraphs']);die;
                foreach ($result['paragraphs'] as $index => $paragraph) {
                    $paragraphLower = strtolower($paragraph);
                    $paragraphLower = preg_replace('/[^\w\s]/', '', $paragraphLower);
                    $words = explode(' ', $paragraphLower);

                    $wordCount = [];
                    foreach ($words as $word) {
                        if (!empty($word)) {
                            if (isset($wordCount[$word])) {
                                $wordCount[$word]++;
                            } else {
                                $wordCount[$word] = 1;
                            }
                        }
                    }
                    $totalWordCount = count($wordCount);
                    $totalFileWordCount += $totalWordCount;

                    echo "Word Counts:<br>";
                    echo count($wordCount);
                    echo "<pre>" . htmlspecialchars($paragraph) . "</pre><br>";
                    echo "<br>";
                }
                echo "<br>";
            }
            echo "Total Word Count: $totalFileWordCount<br>";

        }
    }
}

$conn->close();
?>