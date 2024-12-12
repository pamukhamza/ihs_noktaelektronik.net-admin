
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="ckeditor.js"></script>
    <title>CKEditor</title>
</head>
<body>
    <textarea name="editor"></textarea>
    <script>
    CKEDITOR.replace( 'editor', {
	filebrowserBrowseUrl: 'plugins/ckfinder/ckfinder.html',
	filebrowserUploadUrl: 'plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'
} );
    </script>
</body>
</html>