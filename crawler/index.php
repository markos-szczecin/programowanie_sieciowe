<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Crawl</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="bootstrap.min.css">

    <script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="bootstrap.min.js"></script>
    <style>
        .content {
            margin-top: 10px;
        }
        .red {
            color: red;
            font-weight: bold;
        }
        .green {
            color: green;
            font-weight: bold;
        }
        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite; /* Safari */
            animation: spin 2s linear infinite;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row content">
            <div class="col-sm-12 col-md-offset-3">
                <div class="input-group col-sm-6">
                    <label>Podaj startowy URL</label>
                    <input class="form-control input-sm" value="" id="start_url" placeholder="np. http://zut.edu.pl">
                </div>
            </div>
        </div>
        <div class="row content">
            <div class="col-sm-12 col-md-offset-3">
                <div class="input-group col-sm-6">
                    <label>Podaj maksymalny (może skończyć wcześniej) czas wykonania [s]: </label>
                    <input class="form-control input-sm" value="" id="time" placeholder="np. 180">
                </div>
            </div>
        </div>
        <div class="row content">
            <div class="col-sm-12 col-md-offset-3">
                <div class="input-group col-sm-6">
                    <label>Głębokość szukania: </label>
                    <input class="form-control input-sm" value="" id="depth" placeholder="np. 3">
                </div>
            </div>
        </div>
        <div class="row content">
            <div class="col-sm-12 col-md-offset-3">
                <div class="input-group col-sm-6">
                    <label>Opcjonalnie podaj treść jakiej szukasz: </label>
                    <input class="form-control input-sm" value="" id="search" placeholder="np. informatyka">
                </div>
            </div>
        </div>
        <div class="row content">
            <div class="col-sm-12 col-md-offset-3">
                <div class="input-group col-sm-6">
                    <label style="display: inline-block">Szukanie w ramach domeny: </label>
                    <input type="checkbox" class="" value="1" id="in_domain">
                </div>
            </div>
        </div>
        <div class="row content">
            <div class="col-sm-12 col-md-offset-5">
                <div class="input-group col-sm-6">
                    <button class="btn btn-primary btn-lg" id="start">START</button>
                </div>
            </div>
        </div>
        <div class="row content text-center hidden summary_wrapper">
            <h2>WYNIKI:</h2>
            <div id="summary" style="text-align: left;">
                <div style="margin: 0 auto; width: 400px; text-align: center;">
                    <h3>Crawler w akcji, proszę czekać....</h3>
                    <span id="counter" style="font-size: 24px; color: orange;"></span>
                </div>
                <div class="loader" style="margin: 0 auto;"></div>
            </div>
        </div>
    </div>
<script>
    $(document).ready(function() {
        var summaryCopy = $('.summary_wrapper').html();
        $('#start').click(function() {
            if (!$('.summary_wrapper').hasClass('hidden')) {
                $('.summary_wrapper').html(summaryCopy);
            }
            $(this).prop('disabled', true);
            if ($('#time').val()) {
                $('#counter').text($('#time').val());
            } else {
                $('#counter').text(180);
            }
            $('.summary_wrapper').removeClass('hidden');
            counter();
            $.ajax({
                url: 'crawl.php',
                method: 'post',
                data: {
                    url_start: $('#start_url').val(),
                    time: $('#time').val(),
                    search: $('#search').val(),
                    in_domain: $('#in_domain').is(':checked'),
                    depth: $('#depth').val()
                },
                success: function (data) {
                    if (data == '') {
                        $('#summary').text('Brak wyników');
                    } else {
                        $('#summary').html(data);
                    }
                    $('#start').prop('disabled', false);
                },
                failure: function () {
                    $('#start').prop('disabled', false);
                    alert('Wystąpił błąd')
                }
            })
        });
    });
    function counter() {
        var counter = $('#counter');
        var startTime = counter.text();
        var passed = 0;
        var intervar = setInterval(function() {
            if (startTime - passed <= 0) {
                clearInterval(intervar);
                $('#start').prop('disabled', false);
            }
            counter.text(startTime - passed);
            passed++;
        }, 1000)
    }
</script>
</body>
</html>