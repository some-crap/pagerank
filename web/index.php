<html>
<head>
    
</head>
<body>
<?php
echo'<h1>Page Rank Online Calculator</h1>';
$col = $_GET['col'];
if ($col > 0){
    if($_GET['count'] == 'true'){
        $fname = md5(time().$_SERVER['REMOTE_ADDR']);
        $fd = fopen($fname, 'a') or die("не удалось создать файл");
        $count = 0;
        while($count < $_POST['col']){
            $c = 0;
            fwrite($fd,$_POST['name;'.$count].';');
            while($c < $_POST['col']){
                fwrite($fd,$_POST[$count.';'.$c].';');
                $c++;
            }
            fwrite($fd,"\n");
            $count++;
        }
        $result = exec("python3 count.py $fname");
        unlink($fname);
        echo $result;
    }
    else{
        $count = 0;
        echo'<form action="index.php?count=true&col='.$col.'" method="POST">';
        echo'<table class="rounded">';
        while($count < $col){
            echo'<tr>';
            $c = 0;
            echo'<td><input type="text" name="name;'.$count.'" placeholder="item name"></td>';
                while($c < $col){
                    echo'<td>';
                    echo '<input type="text" name="'.$count.';'.$c.'">';
                    echo'</td>';
                    $c++;
                }
            echo'</tr>';
            $count++;
        }
        echo'</table>';
        echo'<input type="hidden" name="col" value="'.$col.'">';
        echo'<input type="hidden" name="count" value="true">';
        echo'<input type="submit" name="submit" value="send">';
        echo'</form>';
    }
}
else{
    echo'Enter amount of elements:';
    echo'<form action="index.php" method="GET">';
    echo'<input type="number" name="col" placeholder="5">';
    echo'<input type="submit" name="submit" value="Send">';
    echo'</form>';
}
?>
<a href="https://pagerank.somecrap.ru/count.py">Source Code</a>
</body>
</html>
