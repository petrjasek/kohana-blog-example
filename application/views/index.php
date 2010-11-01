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
    <h1>Latest posts</h1>
    <ul>
        <?php foreach ($posts as $post) { ?>
        <li>
            <h2><a href="<?php echo Request::instance()->uri(array('action' => 'detail', 'id' => $post->id)); ?>"><?php echo $post->title; ?></a></h2>
            <p><?php echo substr($post->post, 0, 50); ?></p>
            <p><a href="<?php echo Request::instance()->uri(array('action' => 'edit', 'id' => $post->id)); ?>">edit</a> | <a href="<?php echo Request::instance()->uri(array('action' => 'delete', 'id' => $post->id)); ?>">delete</a></p>
        </li>
        <?php } ?>
    </ul>

    <p><a href="<?php echo Request::instance()->uri(array('action' => 'edit')); ?>">+ Add new</a></p>
</body>
</html>
