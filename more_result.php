<?php
require_once "mysql/connect.php";

#Функция высчитывания ключевых весов кадра для правильного вывода на сайте
function same_pages($first_array, $second_array)
{
    $output_array[] = array();
    $output_count = 0;
    for ($i = 0; $i < count($first_array); $i++) {
        $flag_out = 0;
        for ($j = 0; $j < count($second_array); $j++) {
            if ($first_array[$i][0] == $second_array[$j][0]) {
                if ($first_array[$i][1] > $second_array[$j][1]) {
                    $output_array[$output_count] = array($first_array[$i][0], $first_array[$i][1]);
                    $output_count++;
                    $flag_out = 1;
                } else {
                    $output_array[$output_count] = array($second_array[$j][0], $second_array[$j][1]);
                    $output_count++;
                    $flag_out = 1;
                }
            }
            if ($flag_out == 1) {
                break;
            }
        }
    }
    return $output_array;
}

function transcript_line($long_line)
{
    $long_array[] = array();
    $array_count = 0;
    $take_page = 0;
    $take_value = 0;
    $page = '';
    $value = '';

    for ($i = 0; $i < strlen($long_line); $i++) {
        if ($long_line[$i] == '[') {
            $take_page = 1;
        } elseif ($long_line[$i] == ',') {
            $take_page = 0;
            $take_value = 1;
        } elseif ($long_line[$i] == ']') {
            $long_array[$array_count] = array(intval($page), floatval($value));
            $array_count++;
            $page = '';
            $value = '';
        } elseif ($take_page) {
            $page .= $long_line[$i];
        } elseif ($take_value) {
            $value .= $long_line[$i];
        }
    }
    return $long_array;
}

#Все входящие настройки
$startFrom = $_POST['startFrom'];
$how_to_find = $_POST['htf'];
$query = $_POST['query'];
$how_much_to_show = $_POST['how_much_to_show'];
$user_id = $_POST['user_id'];
$art_count = 0;

#Очистка запроса
$query = trim($query);
$query = htmlspecialchars($query);
$query = mysqli_real_escape_string($connect, $query);
$query = strtolower($query);

#Массив шотов -> json-строка (передаем в Ajax)
if ($how_to_find == 'shot') { #Поиск по кадрам
    $request = $query;
    $words[] = array();
    $w_count = 0;
    $temp_words = '';
    $request .= ' ';
    #Разбиение запроса на поисковые слова
    for ($i = 0; $i < strlen($request); $i++) {
        if ($request[$i] == ' ') {
            $words[$w_count] = $temp_words;
            $temp_words = '';
            $w_count++;
        } else {
            $temp_words .= $request[$i];
        }
    }
    $all_mysql_line[] = array();
    $mysql_count = 0;
    #Алгоритм поиска взят с search.php
    for ($i = 0; $i < count($words); $i++) {
        $q_request = "SELECT `request`,`pages` FROM `search_table` WHERE `request` LIKE '$words[$i]'";
        $result_request = mysqli_query($connect, $q_request);
        $row_request = mysqli_fetch_array($result_request);
        $all_mysql_line[$mysql_count] = array($row_request['request'], transcript_line($row_request['pages']));
        while ($row_request = mysqli_fetch_array($result_request)) {
            $all_mysql_line[$mysql_count][1] = array_merge($all_mysql_line[$mysql_count][1], transcript_line($row_request['pages']));
        }
        $mysql_count++;
    }
    #Сортировка результатов по сумме весов
    if (count($all_mysql_line) >= 1) {
        for ($i = 1; $i < count($all_mysql_line); $i++) {
            $all_mysql_line[0][1] = same_pages($all_mysql_line[0][1], $all_mysql_line[$i][1]);
        }
        if (count($all_mysql_line[0][1][0]) == 0) {
            $shot_counts = 0;
        } else {
            $shot_counts = count($all_mysql_line[0][1]);
        }
    } else {
        $shot_counts = count($all_mysql_line[0][1]);
    }
    #Сохраняем поисковый результат для передачи в AJAX
    for ($i = $startFrom; $i < count($all_mysql_line[0][1]); $i++) {
        if ($i == ($startFrom + $how_much_to_show)) {
            break;
        }
        $for_q = $all_mysql_line[0][1][$i][0];
        $q_shots = "SELECT `folder`,`img` FROM `img_table` WHERE `page_id` LIKE '$for_q'";
        $result_shots = mysqli_query($connect, $q_shots);
        $link_shot = mysqli_fetch_array($result_shots);
        $articles[$art_count]['page_id'] = $for_q;
        $articles[$art_count]['folder'] = $link_shot['folder'];
        $articles[$art_count]['img'] = $link_shot['img'];

        $q_like = "SELECT user_id FROM `likes` WHERE `page_id` LIKE '$for_q' AND user_id LIKE '$user_id'";
        $result_like = mysqli_query($connect_user, $q_like);
        $articles[$art_count]['like'] = mysqli_num_rows($result_like);
        $art_count++;
    }
} elseif ($how_to_find == 'video') { #Поиск по видео
    $request = $query;
    $q_video = "SELECT `folder`,`video_id`,`title`,`description`,`author` FROM `videos`";
    $result_video = mysqli_query($connect, $q_video);
    $video_array = [];
    $video_count = 0;
    #Алгоритм поиска с search.php
    while ($row = mysqli_fetch_array($result_video)) {
        $low_request = strtolower($request);
        if ((strpos(strtolower($row['title']), $low_request)) or (strpos(strtolower($row['description']), $low_request)) or (strpos(strtolower($row['author']), $low_request))) {
            $video_array[$video_count] = $row;
            $video_array[$video_count]['weight'] = substr_count(strtolower($row['title']), $low_request) + (substr_count(strtolower($row['description']), $low_request)) * 0.1 + (substr_count(strtolower($row['author']), $low_request)) * 0.5;
            $video_count++;
        }
    }
    #Сортировка результатов по весам
    for ($video_count = 0; $video_count < count($video_array); $video_count++) {
        for ($video_count_j = $video_count; $video_count_j < count($video_array); $video_count_j++) {
            if ($video_array[$video_count_j]['weight'] > $video_array[$video_count]['weight']) {
                $temp_video_array = $video_array[$video_count];
                $video_array[$video_count] = $video_array[$video_count_j];
                $video_array[$video_count_j] = $temp_video_array;
            }
        }
    }
    $video_array_out = [];
    $j = 0;
    #Фильтрация по настройкам вывода
    for ($video_count = 0; $video_count < count($video_array); $video_count++) {
        if (($video_count >= $startFrom) and ($video_count < $startFrom + $how_much_to_show)) {
            $video_array_out[$j] = $video_array[$video_count];
            $j++;
        }
    }
    $video_array = $video_array_out;
    $i = 0;
    #Дополнительная информация для вывода кадров
    for ($i = 0; $i < count($video_array); $i++) {
        $q_video_shots = "SELECT `img` FROM `img_table` WHERE `folder` LIKE " . $video_array[$i]['folder'] . " ORDER BY RAND() LIMIT 12";
        $result_video_shots = mysqli_query($connect, $q_video_shots);
        $j = 0;
        while ($row_video_shots = mysqli_fetch_array($result_video_shots)) {
            $video_array[$i]['pages'][$j] = $row_video_shots['img'];
            $j++;
        }
    }

}

#Массив -> json-строка (передаем Ajax-запрос)
if ($how_to_find == 'shot') {
    echo json_encode($articles);
} elseif ($how_to_find == 'video') {
    echo json_encode($video_array);
}
?>

