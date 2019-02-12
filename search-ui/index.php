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
        <div class="container-fluid">
            <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
                <a class="navbar-brand" href="#">Search Center</a>
            </nav>

            <form class="mb-3">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Enter your search query" name="search">
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>

            <?php if (!$searchFor): ?>
                <p>Enter a search term to search</p>
            <?php endif; ?>

            <div class ="row" style="margin:5px;">

            <!-- Card news -->
            <div class ="col-md-6" style="border-style:groove;">
                <div class="row">
                    <h5 class="col-sm-12 display-4" style="text-align:center;" >News API</h5>
                    <?php if ($searchFor && !empty($data->news)): ?>
                        <?php foreach($data->news as $news): ?>
                            <div class="col-sm-4 py-2">
                                <div class="card" style="min-height:50%!important;">
                                    <img src="<?php print $news->urlToImage;?>" style="height: 200px;" class="card-img-top" alt="...">
                                    <div class="card-body">
                                        <h6 class="card-title"><?php print $news->source->name; ?></h6>
                                        <h5 class="card-title"><?php print $news->title; ?></h5>
                                        <h6 class="card-title" style="font-size:11px;" ><?php print  date_format(date_create($news->publishedAt), 'd-m-Y'); ?></h6>
                                        <p class="card-text" style="font-size:smaller;"> <?php print $news->description; ?> </p>
                                        <a  href="<?php print $news->url;?>"
                                            style="position:absolute; bottom:10px; width:90%;"
                                            class="btn btn-primary btn-lg btn-block" 
                                            target="_blank" >
                                            Read
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="col-sm-12 display-5" style="text-align:center;" >Sorry, no results found</p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Card news -->
            
            <!-- Card GIFs -->  
            <div class ="col-md-6" style="border-style:groove;">
                <div class="row">
                    <h5 class="col-sm-12 display-4" style="text-align:center;" >Giphy API</h5>
                    <?php if ($searchFor && !empty($data->gifs->data)): ?>
                        <?php foreach($data->gifs->data as $gifs): ?>
                            <div class="col-sm-4 py-2">
                                <div class="card">
                                    <img src="<?php print $gifs->images->fixed_height->url ?>" style="height: 200px;" class="card-img-top" alt="...">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="col-sm-12 display-5" style="text-align:center;" >Sorry, no results found</p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Card GIFs -->

            </div>

        </div>
    </body>
</html>
