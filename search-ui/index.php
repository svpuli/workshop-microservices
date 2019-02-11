<?php

function escapedString($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

$search = escapedString($_GET['search']);
$response = '{ "news": [ { "title": "Hello world" }, { "title": "Tavares sucks!" } ] }';
$data = json_decode($response);

?>

<html>
    <head>
        <meta type="charset" value="utf-8">
        <title>Search</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
    </head>

    <body>
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
                <a class="navbar-brand" href="#">My News Center</a>
            </nav>

            <form class="mb-3">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Enter your search query" name="search">
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>

            <?php if (!$search): ?>
                <p>Enter a search term to search</p>
            <?php endif; ?>

            <?php if ($search && $data): ?>
                <h3>News</h3>

                <?php foreach($data->news as $news): ?>
                    <div>
                        <?php print $news->title; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </body>
</html>
