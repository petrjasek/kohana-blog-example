<!DOCTYPE html>
<html>
<head>
    <title>Kohana blog</title>
    <style type="text/css">
        body { font-family: Georgia; }
        h1 { font-style: italic; }
    </style>
</head>
<body>
    <p><a href="/frameworks/kohana/">Blog</a> > <strong>Edit</strong></p>
    <h1><?php echo ($post->id > 0 ) ? 'Edit post' : 'Add post'; ?></h1>

    <?php echo $form; ?>

</body>
</html>
