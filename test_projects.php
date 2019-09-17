<?php

#Функция добавление шота в проект к определенному пользователю
function add_new_shot_to_project($project_id, $page_id, $user_id, $connect_user)
{
    $q_project = "SELECT `sum_all` FROM `projects` WHERE `project_id` LIKE '$project_id' AND `user_id` LIKE '$user_id'";
    $result_project = mysqli_query($connect_user, $q_project);
    $row_project = mysqli_fetch_array($result_project);
    $position = (int)$row_project['sum_all'] + 1; #Обновляем всё количество шотов в проекте пользователя
    if (mysqli_num_rows($result_project) > 0) {
        $result_add = mysqli_query($connect_user, "INSERT INTO projects_shots (project_id,page_id,position) VALUES ('$project_id','$page_id','$position')");
        if ($result_add == TRUE) {
            $update_project = mysqli_query($connect_user, "UPDATE `projects` SET `sum_all`='$position' WHERE `project_id` LIKE '$project_id' AND `user_id` LIKE '$user_id'");
            if ($update_project == TRUE) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

#Функция создания демо проектов при регистрации для определенного пользователя
#Заготовленные шоты лежат в Trello списком
function create_test_projects($user_id, $user_name, $connect_user)
{
    $first_project = "INSERT INTO projects (title,caption,user_id,sum_all,add_date,add_time) VALUES('Demo - COLOR','You can search frames by main colors. Keywords - red, green, blue, white, purple, yellow, teal, brown, pink, orange, grey, black.',$user_id,0, NOW(),NOW())";
    $result = mysqli_query($connect_user, $first_project);
    if ($result == 'TRUE') {
        $check_first_project = "SELECT `project_id` FROM `projects` WHERE `title` LIKE 'Demo - COLOR' AND `user_id` LIKE '$user_id'";
        $result_check_first_project = mysqli_query($connect_user, $check_first_project);
        $row_check_first_project = mysqli_fetch_array($result_check_first_project);
        $first_project_shots = array(25280, 1107, 8601, 20016, 3133, 1914, 14904, 20955, 16930, 17083, 22931, 2557, 718, 20324, 22041, 13193, 15759, 3088, 21255, 16362, 21290, 16277, 16305, 19213, 15487, 17436, 12515, 20268, 18432, 1289, 14988, 15179, 12873, 2541, 16497, 7403, 17530, 451, 22574, 17454, 2133, 24191);
        for ($i = 0; $i < count($first_project_shots); $i++) {
            add_new_shot_to_project($row_check_first_project['project_id'], $first_project_shots[$i], $user_id, $connect_user);
        }
    }
    $first_project = "INSERT INTO projects (title,caption,user_id,sum_all,add_date,add_time) VALUES('Demo - ACTIVITY','You can find different activities. For example use keywords - jump, kiss, dance, sitting, swimming, smiling, surfing, cooking, drawing, cycling.',$user_id,0, NOW(),NOW())";
    $result = mysqli_query($connect_user, $first_project);
    if ($result == 'TRUE') {
        $check_first_project = "SELECT `project_id` FROM `projects` WHERE `title` LIKE 'Demo - ACTIVITY' AND `user_id` LIKE '$user_id'";
        $result_check_first_project = mysqli_query($connect_user, $check_first_project);
        $row_check_first_project = mysqli_fetch_array($result_check_first_project);
        $first_project_shots2 = array(4898, 20806, 7885, 6186, 13613, 16275, 21544, 19896, 19090, 22407, 12279, 22469, 14249, 20775, 23174, 20395, 20347, 15543, 20978, 23810, 3035, 14405, 6060, 20321, 25046, 11805, 16954, 19673, 19657, 232, 5083, 15096, 3421, 15128, 2805, 851, 2798, 2148, 5108, 12698, 2092, 8219, 24290, 1477, 20951, 24657, 11694, 9082, 18514, 12615, 21004, 23140, 4721, 15078, 13681, 5067, 9285, 4504, 9126, 7293);
        for ($i = 0; $i < count($first_project_shots2); $i++) {
            add_new_shot_to_project($row_check_first_project['project_id'], $first_project_shots2[$i], $user_id, $connect_user);
        }
    }
    $first_project = "INSERT INTO projects (title,caption,user_id,sum_all,add_date,add_time) VALUES('Demo - LOCATIONS','Also you can find any locations in our base. Keywords - room, mountain, road, beach, stairs, forest, field.',$user_id,0, NOW(),NOW())";
    $result = mysqli_query($connect_user, $first_project);
    if ($result == 'TRUE') {
        $check_first_project = "SELECT `project_id` FROM `projects` WHERE `title` LIKE 'Demo - LOCATIONS' AND `user_id` LIKE '$user_id'";
        $result_check_first_project = mysqli_query($connect_user, $check_first_project);
        $row_check_first_project = mysqli_fetch_array($result_check_first_project);
        $first_project_shots3 = array(14407, 3462, 14421, 14398, 21960, 21785, 7099, 14105, 13504, 13382, 14113, 4889, 14120, 7779, 14070, 7720, 7794, 11963, 11436, 6755, 11418, 9863, 8300, 266, 21304, 14336, 19985, 14888, 13865, 21679, 16036, 18523, 5951, 9351, 14616, 10392, 8127, 8128, 463, 12105, 12116, 7769);
        for ($i = 0; $i < count($first_project_shots3); $i++) {
            add_new_shot_to_project($row_check_first_project['project_id'], $first_project_shots3[$i], $user_id, $connect_user);
        }
    }

    $first_project = "INSERT INTO projects (title,caption,user_id,sum_all,add_date,add_time) VALUES('Demo - TITLE','Our base has examples of titles, keyword - text',$user_id,0, NOW(),NOW())";
    $result = mysqli_query($connect_user, $first_project);
    if ($result == 'TRUE') {
        $check_first_project = "SELECT `project_id` FROM `projects` WHERE `title` LIKE 'Demo - TITLE' AND `user_id` LIKE '$user_id'";
        $result_check_first_project = mysqli_query($connect_user, $check_first_project);
        $row_check_first_project = mysqli_fetch_array($result_check_first_project);
        $first_project_shots4 = array(7283, 4509, 10603, 6722, 10413, 3156, 5604, 11441, 10476, 3062, 10651, 10420, 1415, 7493, 7927, 6540, 3550, 6037, 2079, 3034, 9844, 10151, 11497, 10889, 11440, 11565, 4593, 10048, 4561, 11030, 7702, 11185, 11403, 10400, 11373, 10476);
        for ($i = 0; $i < count($first_project_shots4); $i++) {
            add_new_shot_to_project($row_check_first_project['project_id'], $first_project_shots4[$i], $user_id, $connect_user);
        }
    }
}

?>