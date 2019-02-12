<?php
    $searchFor = $_GET['search'];

    function escapedString($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    if(is_string($searchFor)){
        $query = http_build_query([
            'q'=>escapedString($_GET['search'])
        ]);

        $url = 'http://search-api:5000/search?'.$query;

        $response = file_get_contents($url);
        $data = json_decode($response);
    }
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
                <a class="navbar-brand" href="#">Search Center</a>
            </nav>

            <form class="mb-3">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Enter your search query" name="search">
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>

            <div class ="container">
                <div class="row">

                    <?php if (!$searchFor): ?>
                        <p>Enter a search term to search</p>
                    <?php endif; ?>
                    
                    <!-- Card news -->
                    <?php if ($searchFor && $data): ?>
                        <?php foreach($data->news as $news): ?>
                            <div class="col-sm-4 py-2">
                                <div class="card" style="height:45%!important;">
                                    <img src="<?php print $news->urlToImage;?>" style="height: 200px;" class="card-img-top" alt="...">
                                    <div class="card-body">
                                        <h4 class="card-title"><?php print $news->source->name; ?></h5>
                                        <h4 class="card-title"><?php print $news->title; ?></h4>
                                        <h6 class="card-title"><?php print  date_format(date_create($news->publishedAt), 'd-m-Y'); ?></h6>
                                        <p class="card-text"> <?php print $news->description; ?> </p>
                                        <a  href="<?php print $news->url;?>" 
                                            class="btn btn-primary btn-lg btn-block" 
                                            style="position:absolute; bottom:10px; width:90%;">
                                            Read
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <!-- Card news -->

                </div>
            </div>
        </div>
    </body>
</html>
