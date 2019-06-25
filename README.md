# PHP CSV parser
parse and write csv file by certain format in php

---

для запуска установить composer 
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

собрать все зависимости  
```
composer install 
```

запуск тестов:
```
php ./vendor/phpunit/phpunit/phpunit --configuration ./phpunit.xml ./Tests
```

---
#Пример использования:
```php
// массив с названиями файлов для парсинга в дирректории <root>/data
$file_names = [
    'file1',
    'file2'
];

// Создание парсера 
// в конструктор передаем массив данных, типа:
// 
//  file_names - массив с названиями
//  count_same_id_constraint(optional) - Максимальное количество объектов с уникальным id
//  max_count_constraint - Максимальное количество строк в результирующем файле
//  sort_column - Индекс колонки, по которой производится сортировка
//  id_column - Колонка по которой делается ограничение на уникальность

$parser = new \Src\MultiFileParser([
    'file_names' => $file_names,
    'max_count_constraint' => 4
]);

// запуск пасинга
$parser->parse();

// получение распарсенных, отсортированных данных
$parser->getParsedData();

// запись результата в файл
$parser->write();
```

