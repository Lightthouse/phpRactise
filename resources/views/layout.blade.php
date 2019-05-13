<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title') </title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        header{
            height :200px;
            background-image:url(http://adicts.wapsite.me/1con/header/header_1.png);
            background-size: contain;
        }
        footer{
            height: 150px;
            background-image: url(https://smhttp-ssl-43233.nexcesscdn.net/media/catalog/product/cache/1/image/750x550/602f0fa2c1f0d1ba5e241f914e856ff9/m/e/mens-brooks-ghost-11-nyc-pop-art-running-shoe-color-blackgreenpop-regular-width-size-8-609465385063-01.2590_1.jpg);
            background-size: contain;
        }
        textarea{
            min-height: 200px;
            min-width: 300px;
            margin-left: 20px;
        }
        main{
            display: flex;
            justify-content: center;
            justify-items: center;
        }
        .main{
            display: flex;
            flex-direction: column;
        }
        .err{
            color :darkred;
        }
        .ok{
            color: mediumseagreen;
        }
        .allUsers{
            display: flex;
        }
        .right, .left{
            display: flex;
            flex-direction: column;
            margin-left: 30px;
        }

    </style>
</head>

<body>
<header></header>
<main>
    <div class="main">
        @yield('content')
    </div>
</main>
<footer></footer>
</body>
</html>
