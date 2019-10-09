<?php
// подгружаем членов группы
// В запросах нужно указать свой group_id и access_token
// Token можно получить на http://vkhost.github.io
// В файле config.php необходимо указать данные для подключения к базе данных MySQL
// После выполнения кода можно скачать дамп таблицы matrix в формате CSV и ввести путь к нему в фалйле count.py
$fd = fopen("group_followers.txt", 'w') or die("не удалось создать файл");
$str = file_get_contents('https://api.vk.com/method/groups.getMembers?group_id=180732056&sort=id_asc&count=300&access_token=СЕКРЕТНЫЙ ТОКЕН&v=5.92');
fwrite($fd, $str);
fclose($fd);
$fd = null;
$str = null;
// FROM HERE
include 'config.php';
$link= new mysqli(HOST, USERNAME, PASS, DBNAME);
mysqli_query($link, "CREATE TABLE `".DBNAME."`.`followers` ( `id` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;");
mysqli_query($link, "CREATE TABLE `".DBNAME."`.`links` ( `db` INT NOT NULL AUTO_INCREMENT , `links` LONGTEXT NOT NULL , `uid` INT NOT NULL , PRIMARY KEY (`db`)) ENGINE = InnoDB;");
mysqli_query($link, "CREATE TABLE `".DBNAME."`.`users` ( `u_id` INT NOT NULL , `links` LONGTEXT NOT NULL , PRIMARY KEY (`u_id`)) ENGINE = InnoDB;");
mysqli_query($link, "CREATE TABLE `".DBNAME."`.`matrix` ( `db` INT NOT NULL AUTO_INCREMENT , `uid` INT NOT NULL , PRIMARY KEY (`db`)) ENGINE = InnoDB;");
$fd = file_get_contents('group_followers.txt');
$data = json_decode($fd);
$count = $data -> response -> count;
//echo $count.'<br><br>';
$arr = $data -> response -> items;
$c = 0;
while ($c < $count)
{
//    echo $arr[$c].'<br>';
    mysqli_query($link,"INSERT INTO `followers`(`id`) VALUES ('".$arr[$c]."')");
    $c++;
}
//
$str = null;
$c = null;
$data = null;
$arr = null;
$count = null;
$fd = null;
//
$ids = mysqli_query($link,"SELECT * FROM `followers`"); 
$fd = fopen("list.txt", 'a') or die("не удалось создать файл");
while($id = mysqli_fetch_assoc($ids)){
        $c = 0;
        $str = '{"id":'.$id['id'].',"'.$id['id'].'":'.file_get_contents('https://api.vk.com/method/users.getSubscriptions?user_id='.$id['id'].'&access_token=СЕКРЕТНЫЙ ТОКЕН&count=200&extended=0&v=5.92').'}
';
    fwrite($fd, $str);
sleep(1);
}
fclose($fd);
//
$ids = null;
$fd = null;
$id = null;
$c = null;
$str = null;
//
$handle = @fopen("list.txt", "r");
$fd = fopen("list_sorted.txt", 'a') or die("не удалось создать файл");
if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
        $tmp = json_decode($buffer);
        $id = $tmp -> id;
        $items = $tmp -> $id -> response -> items;
        $count = $tmp -> $id -> response -> count;
        $error = $tmp -> $id -> error -> error_code;
        if($error > 0){
            mysqli_query($link,"DELETE FROM `followers` WHERE `id` = '".$id."'");
        }
        else{
            if($count > 0){
                $c = 0;
                $sorted_string = '';
                while($c < $count){
                    $response = mysqli_num_rows(mysqli_query($link,"SELECT * FROM `followers` WHERE `id` = '".$items[$c]."'"));
                    if($response > 0){
                        $sorted_string = $items[$c].','.$sorted_string ;
                    }
                    $c++;
                }
                if($sorted_string != ''){
                    fwrite($fd,"{");
                    fwrite($fd,'"id":'.$id.', "'.$id.'":[');
                    $sorted_string = mb_substr($sorted_string,0,-1);
                    fwrite($fd, $sorted_string."]}\n");
                }
            }
        }
    }
    if (!feof($handle)) {
        echo "Ошибка: fgets() неожиданно потерпел неудачу\n";
    }
    fclose($handle);
}
//
$handle = null;
$fd = null;
$buffer = null;
$temp = null;
$id = null;
$items = null;
$count = null;
$error = null;
$c = null;
$sorted_string = null;
$response = null;
$items = null;
//
$ids = mysqli_query($link,"SELECT * FROM `followers`"); 
$fd = fopen("friends_list.txt", 'a') or die("не удалось создать файл");
while($id = mysqli_fetch_assoc($ids)){
    $count = $data -> reponse -> count;
    if ($count <= 500){
        $c = 0;
        $str = '{"id":'.$id['id'].',"'.$id['id'].'":'.file_get_contents('https://api.vk.com/method/friends.get?user_id='.$id['id'].'&access_token=СЕКРЕТНЫЙ ТОКЕН&count=5000&v=5.92').'}
';
    fwrite($fd, $str);
    }
sleep(1);
}
fclose($fd);
//
$query = mysqli_query($link,"SELECT * FROM `followers` WHERE 1");
while ($tmp = mysqli_fetch_assoc($query)){
    mysqli_query($link, "INSERT INTO `links` (`uid`) values ('".$tmp['id']."')");
    echo 'ok';
}
$handle = @fopen("friends_list_sorted.txt", "r");
if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
        $tmp = json_decode($buffer);
        $id = $tmp -> id;
        $items = $tmp -> $id;
        $c = 0;
        while($items[$c]){
            $tmp_data = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `links` WHERE `uid` = '".$items[$c]."'"));
            mysqli_query($link, "UPDATE `links` SET `links` = '".$tmp_data['links'].','.$id."' WHERE `uid` = '".$items[$c]."'");
            $c++;
        }
    }
    if (!feof($handle)) {
        echo "Ошибка: fgets() неожиданно потерпел неудачу\n";
    }
    fclose($handle);
}
$fd = fopen("friends_list.txt", 'a') or die("не удалось создать файл");
$query = mysqli_query($link, "SELECT * FROM `links` WHERE 1");
while($tmp = mysqli_fetch_assoc($query)){
    $str = '{"id": "'.$tmp['uid'].'", "items": ['.mb_substr($tmp['links'], 1).']';
    fwrite($fd, $str);
}
fclose($fd);

$handle = @fopen("data_matrix.txt", "r");
if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
        $tmp = json_decode($buffer);
        $id = $tmp -> id;
        echo $id.'<br>';
        $items = $tmp -> items;
        $count = $tmp -> count;
        mysqli_query($link,"ALTER TABLE `matrix` ADD `".$id."` TEXT NOT NULL AFTER `uid`");
    }
    if (!feof($handle)) {
        echo "Ошибка: fgets() неожиданно потерпел неудачу\n";
    }
    fclose($handle);
}
$handle = @fopen("data_matrix.txt", "r");
if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
        $tmp = json_decode($buffer);
        $id = $tmp -> id;
        $items = $tmp -> items;
        $count = $tmp -> count;
        mysqli_query($link,"INSERT INTO `matrix` SET `uid` = '".$id."'");
    }
    if (!feof($handle)) {
        echo "Ошибка: fgets() неожиданно потерпел неудачу\n";
    }
    fclose($handle);
}
$handle = @fopen("data_matrix.txt", "r");
if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
        $tmp = json_decode($buffer);
        $id = $tmp -> id;
        $items = $tmp -> items;
        $count = $tmp -> count;
        mysqli_query($link,"UPDATE `matrix` SET `".$id."` = '0' WHERE 1");
    }
    if (!feof($handle)) {
        echo "Ошибка: fgets() неожиданно потерпел неудачу\n";
    }
    fclose($handle);
}
$handle = @fopen("data_matrix.txt", "r");
if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
        $tmp = json_decode($buffer);
        $id = $tmp -> id;
        $items = $tmp -> items;
        $count = $tmp -> count;
        $link_weight = 1 / $count;
        $c = 0;
        while($c < $count){
            mysqli_query($link,"UPDATE `matrix` SET `".$id."` = '".$link_weight."' WHERE `uid` = '".$items[$c]."'");
            $c++;
        }
    }
    if (!feof($handle)) {
        echo "Ошибка: fgets() неожиданно потерпел неудачу\n";
    }
    fclose($handle);
}
?>
