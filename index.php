<?php
require_once __DIR__.'/_header.php';
?>
<a href="comments.php">Комментарии</a>

<h2>Ручной поиск</h2>
<form method="get">
    <table class="table">
        <tr>
            <td>Запрос</td>
            <td><input type="text" class="form-control" name="search"></td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" name="" value="Искать"></td>
        </tr>
    </table>
    <input type="hidden" name="action" value="requestParseByKeys">
</form>

<?php if (!empty($page_data['parseByKeys'])) : ?>

    <form method="post">
        <table class="table">
            <? foreach ($page_data['parseByKeys'] as $item) : ?>
            <tr>
                <td>
                    <input type="checkbox" name="url[]" value="<?=htmlspecialchars(json_encode(['url'=>$item['id']['videoId'], 'snippet'=>$item['snippet']]))?>">
                </td>
                <td><img src="<?=$item['snippet']['thumbnails']['default']['url']?>"></td>
                <td><?=$item['snippet']['title']?></td>
                <td><a href="https://www.youtube.com/watch?v=<?=$item['id']['videoId']?>" target="_blank">https://www.youtube.com/watch?v=<?=$item['id']['videoId']?></a></td>
            </tr>
            <? endforeach; ?>
            <tr>
                <td colspan="2"><input type="submit" name="" value="Запустить"></td>
            </tr>
        </table>
        <input type="hidden" name="action" value="requestToQueue">
    </form>


<?php endif;?>

<h2>История заданий</h2>
<table class="table dataTable" data-table="drawHistory">
    <thead>
        <tr>
            <th>ID</th>
            <th>Дата</th>
            <th>Url</th>
            <th>Комментарии</th>
            <th>Отладка</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
    <tr>
        <th>ID</th>
        <th>Дата</th>
        <th>Url</th>
        <th>Комментарии</th>
        <th>Отладка</th>
    </tr>
    </tfoot>
</table>
<?php
require_once __DIR__.'/_footer.php';
?>
