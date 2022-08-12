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


/**
 * Класс выгрузки
 * 
 * @category Components
 * @package  Parser
 * @author   Глобенко Игорь Валентинович <elepsis@bk.ru>
 * @license  https://github.com/elepsis5 My gitHub
 * @link     https://github.com/elepsis5/xml-parser.git
 * @since    0.1.0.0
 */
class Unload
{
    //подключение к бд и название таблицы
    private $_dbh;
    private $_tableName = 'auto_store';

    //данные xml файла
    public $xmlPath;
    public $data;

    /**
     * Получаем бд и путь до xml
     * 
     * @param object $db      Database
     * @param string $xmlPath путь xml файла
     */
    public function __construct($db, $xmlPath)
    {
        $this->_dbh = $db->getDb();
        $this->xmlPath = $xmlPath;
        $this->data = self::convertXml();
    }

    /**
     * Получаем данные из xml.
     * Валидируем.
     * Льем в базу. 
     * Если такая запись уже есть - обновляется. 
     * Если новая - добавляется. 
     * 
     * @return no
     */
    public function pushNewOffer()
    {
        if ($this->data) {
            foreach ($this->data['offers']['offer'] as $car) {

                $fieldsProp = array_fill(0, count($car), '?');
                $fieldsName = array_keys($car);
                $correctName = self::correctName($fieldsName);
                $updatingName = self::updatingName($correctName);
                $fieldsVal = array_values($car);
                
                $query = "INSERT INTO " .  $this->_tableName .
                ' ( '.implode(',', $correctName).' )
                values ( '.implode(',', $fieldsProp).' )
                ON DUPLICATE KEY UPDATE '.implode(',', $updatingName). ';'; 
                
                $stmt = $this->_dbh->prepare($query);
                $stmt->execute($fieldsVal);
                
            }
        }
        
    }
    /**
     * Конвертируем xml - json - array
     * 
     * @return array
     */
    public function convertXml()
    {
        $xml = simplexml_load_file($this->xmlPath);
        if (!$xml) {
            throw new Exception('Путь указан не верно!');
        }
        
        $json = json_encode($xml);
        $data = json_decode($json, true);
        
        return $data;
    }

    /**
     * Валидация полей 
     * 
     * @param array $array array
     * 
     * @return array
     */
    public function correctName($array)
    {
        $correctArr = [];

        foreach ($array as $key => $val) {
            $newVal = str_replace('-', '_', $val);
            $correctArr[] = $newVal;
        }

        return $correctArr;
    }

    /**
     * Преобразовываем ключи для sql запроса
     * 
     * @param array $array array
     * 
     * @return array
     */
    public function updatingName($array)
    {
        $updateArr = [];

        foreach ($array as $key => $val) {
            if ($val !== 'id') {
                $newVal = $val . '=VALUES(' . $val . ')';
                $updateArr[] = $newVal;
            }
        }

        return $updateArr;
    }

    /**
     * Удаляем записи из базы, которых нет в xml-выгрузке
     *
     * @return no
     */
    public function onDelete()
    {
        $arrDataIds = [];
        $arrDbIds = [];
        //$arrForDel = [];

        $countOfDel = 0;
        $countOfArrDataIds = 0;
        $countOfArrDbIds = 0;
        // заполняем массив с ключами из xml
        foreach ($this->data['offers']['offer'] as $car) {
            $arrDataIds[] = (int) $car['id'];
        }
        
        // заполняем массив с ключами из db
        $query = 'SELECT id FROM ' . $this->_tableName;

        $stmt = $this->_dbh->prepare($query);
        $stmt->execute();
        $arrDbIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $countOfArrDataIds = count($arrDataIds);
        $countOfArrDbIds = count($arrDbIds);

        //сравниваем массивы и отсеиваем ключи которые не совпали
        for ($i=0; $i <= $countOfArrDataIds; $i++) {
            for ($j=0; $j<=$countOfArrDbIds; $j++) {
                if (isset($arrDbIds[$j]) && isset($arrDataIds[$i])) {
                    if ($arrDataIds[$i] === $arrDbIds[$j]) {
                        unset($arrDbIds[$j]);
                    }
                }
            }
        }
        //удаляем записи из бд
        foreach ($arrDbIds as $id) {
            $queryToDel = 'DELETE FROM ' .
            $this->_tableName . ' WHERE id=' . $id . ';';
            $stmt = $this->_dbh->prepare($queryToDel);
            $stmt->execute();
            $countOfDel++;
        }
        
        echo 'УДАЛЕНО ЗАПИСЕЙ: ' . $countOfDel;
        

    }

}