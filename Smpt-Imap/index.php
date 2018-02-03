<?php
require 'session_conf.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mailer</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <script
            src="http://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>
</head>
<body>
<div class="container-fluid">
    <div class="row content">
        <div class="col-sm-6">
            <h3 class="text-center">Wyślij wiadomość</h3>
            <?php if (isset($_GET['success']) && (int)$_GET['success']): ?>
                <div class="alert alert-success">
                    Poprawnie wysłano wiadomość
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['success']) && (int)$_GET['success'] === 0): ?>
                <div class="alert alert-danger">
                    Nie udało się wysłać wiadomości. Spórbuj ponownie.
                </div>
            <?php endif; ?>
            <div class="" style="position: relative; left: 30%">
                <form class="" action="send.php" method="post" enctype="multipart/form-data">
                    <div class="row content">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Do:</label>
                                <input class="form-control input-sm col-xs-2" name="to" value=""/>
                            </div>
                        </div>
                    </div>
                    <div class="row content">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Reply to:</label>
                                <input class="form-control input-sm col-xs-2" name="reply" value=""/>
                            </div>
                        </div>
                    </div>
                    <div class="row content">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Temat:</label>
                                <input class="form-control input-sm col-xs-2" name="subject" value=""/>
                            </div>
                        </div>
                    </div>
                    <div class="row content">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="">Treść:</label>
                                <textarea class="form-control" cols="10" rows="10" name="body" value=""></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row content">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="">Załącznik:</label>
                                <input type="file" class="input-btn " name="attachment" id="get_file"/>
                            </div>
                        </div>
                    </div>
                    <div class="row content">
                        <div class="col-sm-3">
                            <input type="submit" class="form-control btn btn-primary col-sm-3" value="Wyślij">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-sm-6">
            <h3 class="text-center">Sprawdź spocztę (Nie działa dla GMAIL.COM)</h3>
            <?php if (isset($_GET['error']) && $_GET['error']) : ?>
            <div class="alert alert-danger">Wystąpił błąd podczas pobierania poczty lub nie można się połączyć</div>
            <?php endif; ?>
            <div class="row content">
                <form class="" action="receiver.php" method="post">
                    <div class="row content">
                        <div class="col-sm-3">
                            <label>Host:</label>
                            <input type="text" placeholder="mail.zut.edu.pl" class="form-control input-sm" name="host"
                                   value="">
                        </div>
                    </div>
                    <div class="row content">
                        <div class="col-sm-3">
                            <label>Port (IMAP):</label>
                            <input type="text" placeholder="143" class="form-control input-sm" name="port" value="">
                        </div>
                    </div>
                    <div class="row content">
                        <div class="col-sm-3">
                            <label>Szyfrowanie:</label>
                            <input type="text" placeholder="TLS" class="form-control input-sm" name="security" value="">
                        </div>
                    </div>
                    <div class="row content">
                        <div class="col-sm-3">
                            <label>Login:</label>
                            <input type="text" placeholder="login" class="form-control input-sm" name="login" value="">
                        </div>
                    </div>
                    <div class="row content">
                        <div class="col-sm-3">
                            <label>Hasło:</label>
                            <input type="password" placeholder="hasło" class="form-control input-sm" name="password"
                                   value="">
                        </div>
                    </div>
                    <div class="row content">
                        <div class="col-sm-12">
                            <label>Mail ID:</label>
                            <?php
                            if (!isset($_GET['key']) || empty($_SESSION[$_GET['key']])) {
                                echo 'Sprawdź nowe wiadomości aby uzyskać mail ID\'s tych wiadomości';
                            } else {
                                echo '<input type="text" placeholder="Mail ID" class="form-control input-sm" name="mail_id"
                                   value="">';
                            }
                            ?>

                        </div>
                    </div>
                    <div class="row content" style="margin-top: 10px;">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="submit" class="form-control btn btn-primary col-sm-3" value="Sprawdź nowe wiadomości">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <?php if(!empty($_SESSION['mail_data'])) : ?>
            <div class="row content" style="margin-top: 10px;">
                <div class="col-sm-12">
                    <table class="table table-striped">
                        <tr>
                            <th><?php echo $_SESSION['mail_subject']; unset($_SESSION['mail_subject']); ?></th>
                        </tr>
                        <tr>
                            <td> <?php echo $_SESSION['mail_data']; unset($_SESSION['mail_data']); ?></td>
                        </tr>
                        <?php if (!empty($_SESSION['attachments'])) :?>
                        <tr>
                            <td>
                            <?php foreach ($_SESSION['attachments'] as $num => $attachment) :?>
                                <div>
                                    <a href="<?php echo $attachment;?>" target="_blank">Plik <?php echo $num; ?></a>
                                </div>
                            <?php endforeach;?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>

                </div>
            </div>
            <?php endif;?>
            <div class="row content" style="margin-top: 10px;">
                <div class="col-sm-12">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Mail ID</th>
                            <th>TEMAT</th>
                        </tr>
                        </thead>
                        <?php
                        if (isset($_SESSION[$_GET['key']]) && $_SESSION[$_GET['key']]) {
                            $subjects = $_SESSION[$_GET['key']];
                            if ($subjects) {
                                foreach (json_decode($subjects, true) as $id => $subject) {
                                    echo '<tr>';
                                    echo '<td>' . $id . '</td>';
                                    echo '<td>' . $subject . '</td>';
                                    echo '</tr>';
                                }
                            }
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>