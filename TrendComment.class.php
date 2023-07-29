<?php


class TrendComment {
    private $db;
    public $debug = false;
    public $youtube_key;
    public $jap;

    function __construct($db, $youtube_key) {
        $this->db = $db;
        $this->youtube_key = $youtube_key;
    }

    function requestSaveComment() {
        $this->saveComment($_REQUEST['id'], $_REQUEST['text']);
    }

    function saveComment($id, $text) {
        $this->db->update('comments', ['text'=>$text], ['id'=>$id]);
        $this->reloadPage('comments.php','success', 'Комментарий успешно сохранен');
    }

    function getComments() {
        return $this->db->select('comments', '*');
    }

    function requestDeleteComment() {
        $this->deleteComment($_REQUEST['id']);
        $this->reloadPage('comments.php','success', 'Комментарий успешно удален');
    }

    function deleteComment($id) {
        $this->db->delete('comments', ['id'=>$id]);
    }

    function requestAddListComments() {
        $str = $_REQUEST['list'];
        $arr = explode("\n", $str);
//        echo '<PRE>';print_r($arr);
        foreach ($arr as $item) $this->addComment(trim($item));

        $this->reloadPage('comments.php','success', 'Комментарии успешно добавлены');
    }

    function requestAddComment() {
        $this->addComment($_REQUEST['text']);
        $this->reloadPage('comments.php','success', 'Комментарий успешно добавлен');
    }
    
    function addComment($text) {
        $this->db->insert ('comments', ['text'=>$text]);
    }

    function ajaxCommentToggleStatus() {
        $this->commentToggleStatus($_REQUEST['id']);
        echo 'ok';
        die();
    }

    function commentToggleStatus($id) {
        $this->db->query('update `comments` set `status` = IF(`status`=1, 0, 1) where id='.(int)$id);
    }
    
    function parseTrends() {

        $request_data = [
            'part'=>'snippet,contentDetails',
            'chart'=>'mostPopular',
            'regionCode'=>'RU',
            'maxResults'=>50,
            'key'=>$this->youtube_key,
        ];
        $json = $this->ext_request('https://www.googleapis.com/youtube/v3/videos', $request_data, 'get');

        $data = json_decode($json, true);
        if (is_array($data) && !empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $check = $this->db->get('tasks', '*',['url'=>$item['id']]);
                if (!$check) {
                    $db_data = [
                        'date'=>date("Y-m-d H:i:s"),
                        'info'=>json_encode($item['snippet']),
                        'url'=>$item['id'],
                    ];
                    $this->db->insert('tasks', $db_data);
                }
            }
        } else {
            throw new Exception('cant get trends page');
        }
    }

    function requestParseByKeys() {
        try {
            return ['parseByKeys'=> $this->parseByKeys($_REQUEST['search'])];
        } catch (Exception $e) {
            $this->reloadPage('index.php', 'danger', $e->getMessage());
        }
    }

    /**
     * @param $q
     * @return array
     * @throws Exception
     */
    function parseByKeys($q) {
        $request_data = [
            'part'=>'snippet',
            'q'=>$q,
            'type'=>'video',
            'key'=>$this->youtube_key,
        ];
        $json = $this->ext_request('https://www.googleapis.com/youtube/v3/search', $request_data, 'get');

        $data = json_decode($json, true);

        if (is_array($data) && !empty($data['items'])) {
            return $data['items'];
        } else {
            throw new Exception('Cant make search, wrong answer '.$json);
        }

    }

    function getRandomComments($count) {
        $res = $this->db->rand('comments', '*', ['status'=>1, 'LIMIT'=>$count]);
        $data = [];
        foreach ($res as $item) {
            $data[] = $item['text'];
        }

        return $data;
    }


    function drawHistory() {
        $order_rows = array(
            'id',
            'date',
            'url',
            'id',
            'id',
        );

        $from = intval($_REQUEST['start']);
        $limit = intval($_REQUEST['length']);

        if (!in_array($_REQUEST['order'][0]['dir'], array('asc', 'desc'))) throw new Exception('Wrong order direction');

        $where = ['ORDER'=>[$order_rows[$_REQUEST['order'][0]['column']]=>strtoupper($_REQUEST['order'][0]['dir'])], 'LIMIT'=>[$from, $limit]];
//        echo '<PRE>';
//        print_r($where);
        if ($_REQUEST['search']['value']) {
            $where['url'] = $_REQUEST['search']['value'];
        }
        $res = $this->db->select('tasks', '*', $where);

        $data = array();
        $data['draw'] = $_REQUEST['draw'];
        $data['recordsTotal'] = (int) $this->db->count('tasks');
        $data['recordsFiltered'] = (int) $this->db->count('tasks');
        $data['data'] = array();

        $i=0;

        foreach ($res as $r) {

            $debug = json_decode($r['debug'], true);
            $info = json_decode($r['info'], true);
            unset($debug['key']);

            $data['data'][$i] = array(
                $r['id'],
                $r['date'],
                '<img src="'.$info['thumbnails']['default']['url'].'">'.
                "<a href='https://www.youtube.com/watch?v={$r['url']}' target='_blank'>".$info['title']."</a>",
                '<textarea>'.$debug['comments'].'</textarea>',
                ($debug['answer']['error'] ? $debug['answer']['error'] .'<br>' : '').
                ($debug['answer']['order'] ? 'EXT ID: '.$debug['answer']['order'] .'<br>' : '').
                '<textarea>'.print_r($debug, true).'</textarea>',
            );

            if ($r['status'] == '1') {
                $data['data'][$i]['DT_RowClass'] = 'table-success';
            } elseif ($r['status'] == '0') {
                $data['data'][$i]['DT_RowClass'] = 'table-warning';
            } elseif ($r['status'] == '9') {
                $data['data'][$i]['DT_RowClass'] = 'table-danger';
            }

            $i++;
        }

        echo json_encode($data);
        die();
    }

    /**
     * @throws Exception
     */
    function requestToQueue() {
        $urls = $_REQUEST['url'];

        try {
            $this->addToQueue($urls);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $this->reloadPage('index.php', 'success', 'Задание успешно отправлено в очередь');
    }

    function reloadPage($url, $type = '', $message = '') {
        if (!empty($type)) $_SESSION['message']['type'] = $type;
        if (!empty($message)) $_SESSION['message']['text'] = $message;

        header("Location: $url");
        die();
    }

    static function showMessage() {
        if (empty($_SESSION['message']['text'])) return false;

        $str = '<div class="alert alert-'.(!empty($_SESSION['message']['type']) ? $_SESSION['message']['type'] : 'primary').'" role="alert">'.$_SESSION['message']['text'].'</div>';

        unset($_SESSION['message']);
        return $str;

    }

    function addToQueue($urls) {
        foreach ($urls as $item) {
            $info = json_decode($item, true);
            $data = [
                'url'=>$info['url'],
                'info'=>json_encode($info['snippet']),
                'date'=>date("Y-m-d H:i:s"),
            ];
            $this->db->insert('tasks', $data);
        }
    }

    function exportTasks() {
        $res = $this->db->select('tasks', '*', ['status'=>0]);
        foreach ($res as $item) $this->exportTask($item['id']);
    }

    function exportTask($id) {
        $row = $this->db->get('tasks', '*', ['id'=>$id]);
        $comments = $this->getRandomComments(TASK_COUNT);

        $data = array(
            'key'=>$this->jap['api_key'],
            'action'=>'add',
            'service'=>$this->jap['service_id'],
            'link'=>'https://www.youtube.com/watch?v='.$row['url'],
            'comments'=>implode("\n", $comments),
        );

        //        print_r($data);
        //        die();

        $res = $this->ext_request('https://justanotherpanel.com/api/v2', $data, 'post');

        $ans = json_decode($res, true);

        $data['answer'] = $ans;

        if (is_array($ans) && $ans['order']>0) {
            $this->db->update('tasks', ['debug'=>json_encode($data), 'status'=>1], ['id'=>$id]);
        } else {
            $this->db->update('tasks', ['debug'=>json_encode($data), 'status'=>9], ['id'=>$id]);
        }

    }

    function ext_request($url, $data, $method) {
        if ($method == 'get' && !empty($data)) {
            if (preg_match('/\?/', $url)) $url .= '&'.http_build_query($data);
            else $url .= '?'.http_build_query($data);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 125);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array("Expect:"));
        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if ($this->debug) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
        }

        $content = curl_exec($ch);

        if ($this->debug) {
            $info = curl_getinfo($ch);
            echo $url.'<br>';
            echo '<PRE>';
            print_r($data);
            print_r($info);
            echo '</PRE>';

            if ($content === FALSE) {
                printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
                    htmlspecialchars(curl_error($ch)));
            }
        }


        return $content;
    }
}