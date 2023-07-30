<?php
// Include the _header.php file
require_once __DIR__.'/_header.php';
?>
<!-- Link to comments page -->
<a href="comments.php">Комментарии</a>

<h2>Ручной поиск</h2>
<!-- Form for manual search -->
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
    <!-- Hidden input field to store action -->
    <input type="hidden" name="action" value="requestParseByKeys">
</form>

<?php 
// Check if parseByKeys is not empty
if (!empty($page_data['parseByKeys'])) : ?>

    <!-- Form to submit request to queue -->
    <form method="post">
        <table class="table">
            <?php foreach ($page_data['parseByKeys'] as $item) : ?>
            <tr>
                <td>
                    <!-- Checkbox to select url -->
                    <input type="checkbox" name="url[]" value="<?=htmlspecialchars(json_encode(['url'=>$item['id']['videoId'], 'snippet'=>$item['snippet']]))?>">
                </td>
                <td><img src="<?=$item['snippet']['thumbnails']['default']['url']?>"></td>
                <td><?=$item['snippet']['title']?></td>
                <td><a href="https://www.youtube.com/watch?v=<?=$item['id']['videoId']?>" target="_blank">https://www.youtube.com/watch?v=<?=$item['id']['videoId']?></a></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2"><input type="submit" name="" value="Запустить"></td>
            </tr>
        </table>
        <!-- Hidden input field to store action -->
        <input type="hidden" name="action" value="requestToQueue">
    </form>


<?php endif;?>

<h2>История заданий</h2>
<!-- Table to display history of tasks -->
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
// Include the _footer.php file
require_once __DIR__.'/_footer.php';
?>
