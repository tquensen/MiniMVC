<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Server Error</title>
    </head>
    <body>
        <h1>Server Error</h1>
        <p>Something went wrong :(</p>
        <?php if (isset($e)): ?>
            <pre>
            <?php echo $e; ?>
        </pre>
        <?php endif; ?>
    </body>
</html>