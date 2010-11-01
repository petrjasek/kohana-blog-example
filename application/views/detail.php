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
    <p><a href="/frameworks/kohana/">Blog</a> > <strong><?php echo $post->title; ?></strong></p>
    <h1><?php echo $post->title; ?></h1>
    <p><?php echo $post->post; ?></p>
</body>
</html>
