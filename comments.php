<?php
require_once __DIR__.'/_header.php';
$comments = $TC->getComments();
?>
    <a href="index.php">Задания</a>
    <h2>Комментарии</h2>
    <form method="post">
        <div class="row">
            <div class="col-md-1">Новый</div>
            <div class="col-md-9"><textarea name="text" class="form-control" style="width: 100%"></textarea></div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success" name="action" value="requestAddComment">Добавить</button>
            </div>
        </div>
    </form>
    <br><br>
    <h2>Импортировать списком</h2>
    <form method="post">
        <div class="row">
            <textarea name="list" class="form-control" style="width: 100%"></textarea>
        </div>
        <br><br>
        <button type="submit" class="btn btn-success" name="action" value="requestAddListComments">Добавить</button>
    </form>

    <br><br>
<? foreach ($comments as $item) : ?>
        <form method="post">
            <div class="row">
                <div class="col-md-1"><?=$item['id']?></div>
                <div class="col-md-1"><input class="toggle_comment" data-id="<?=$item['id']?>" type="checkbox" <?=$item['status'] == 1 ? 'checked':'' ?>></div>
                <div class="col-md-8"><textarea name="text" class="form-control" style="width: 100%"><?=$item['text']?></textarea></div>
                <div class="col-md-2">
                    <input type="hidden" name="id" value="<?=$item['id']?>">
                    <button type="submit" class="btn btn-success" name="action" value="requestSaveComment">Сохранить</button>
                    <button type="submit" class="btn btn-danger" name="action" value="requestDeleteComment">Удалить</button>
                </div>
            </div>
        </form>
<?php endforeach;?>



<?php
require_once __DIR__.'/_footer.php';
?>