<?php
    $connection = curl_init();
    //Устанавливаем адрес для подключения
    curl_setopt($connection, CURLOPT_URL, "http://localhost/StockNew/NaryadComplite/ActionComplite.php?idDoor=7759&Step=1");
    //Указываем, что мы будем вызывать методом POST
    //curl_setopt($connection, CURLOPT_, 1);
    //Передаем параметры методом POST
    //curl_setopt($connection, CURLOPT_POSTFIELDS, "idDoor=7759&Step=1");
    //Говорим, что нам необходим результат
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    //Выполняем запрос с сохранением результата в переменную
    $rezult=curl_exec($connection);
    //Завершает сеанс
    echo json_decode($rezult)->{"Result"};
?>