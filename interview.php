<?php
// print_r("test");die;
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
</head>
<body>
    <h1>Question 1: File Reader and Line Counter with Channels</h1>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="file">Select a text file:</label>
        <input type="file" name="file" id="file" accept=".txt">
        <br><br>
        <input type="submit" name="submit" value="Upload">
    </form>
</body>
</html>