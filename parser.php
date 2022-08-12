<?php
/**
 * Для запуска парсера введите в консоли полный путь до файла parser.php
 *
 * Php version 8.1.5

 * @category Components
 * @package  Parser
 * @author   Глобенко Игорь Валентинович <elepsis@bk.ru>
 * @license  https://github.com/elepsis5 My gitHub
 * @link     https://github.com/elepsis5/xml-parser.git
 * @since    0.1.0.0
 */

require_once 'config.php';
require_once 'database.php';
require_once 'unload.php';


$path = readline("Введите путь до xml-файла: ");


$db = new Database(HOST, DB_NAME, USER, PASS);
echo $db ? "Подключение к базе данных...OK!\n"
    : "Ошибка при подключении к базе данных!\n";


$unload = new Unload($db, pathCheck($path));
$unload->pushNewOffer();
$unload->onDelete();

/**
 * Получаем путь к файлу от пользователя.
 * Если нет, загружаем дефолтный.
 * 
 * @param string $path путь xml файла от пользователя
 * 
 * @return string path
 */
function pathCheck($path) 
{
    if (!empty($path)) {
        return $path;
    } else {
        echo "Вы не ввели путь до xml-файла. Будет использован файл по-умолчанию.\n";
        $path = 'data_light.xml';
        return $path;
    }
}
