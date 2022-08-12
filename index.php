<?php
/**
 * Description
 *
 * Php version 8.1.5

 * @category Components
 * @package  WordPress
 * @author   Your Name <yourname@example.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @link     https://yoursite.com
 * @since    1.0.0
 */

require_once 'config.php';
require_once 'database.php';
require_once 'offer.php';


$path = readline("Введите путь до xml-файла: ");


$db = new Database(HOST, DB_NAME, USER, PASS);
echo $db ? "Подключение к базе данных...OK!\n"
    : "Ошибка при подключении к базе данных!\n";


$offer = new Offer($db, pathCheck($path));
$offer->pushNewOffer();
$offer->onDelete();

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
