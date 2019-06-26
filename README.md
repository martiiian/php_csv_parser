# PHP CSV parser
parse and write csv file by certain format in php

---
запуск тестов:
```
php phpunit-8.2.phar --configuration ./phpunit.xml ./Tests
```

---
# Пример использования:
### Парсинг с известным списком файлов
```php
// массив с названиями файлов для парсинга в дирректории <root>/data
$files = [
    [
        'name' => 'file1',
        'ext' => '.csv',
        'delimiter' => ','
    ],
    [
        'name' => 'file2',
        'ext' => '.csv',
        'delimiter' => ','
    ]
];

// Создание парсера 
// в конструктор передаем массив данных, типа:
// 
//  src_files - массив с информацией о исходных CSV файлах
//  count_same_id_constraint(optional) - Максимальное количество объектов с уникальным id
//  max_count_constraint - Максимальное количество строк в результирующем файле
//  sort_column - Индекс колонки, по которой производится сортировка
//  id_column - Колонка по которой делается ограничение на уникальность

$parser = new \Src\MultiFileParser([
    'src_files' => $files,
    'max_count_constraint' => 4
]);

// запуск пасинга
$parser->parse();

// получение распарсенных, отсортированных данных
$parser->getParsedData();

// запись результата в файл
$parser->write();
```
### Парсинг всех файлов из указанной дирректории
```php
$parser = new \Src\MultiFileParser([
    'src_directory' => $files,
    'max_count_constraint' => 4
]);

// запуск пасинга
$parser->parse();

// получение распарсенных, отсортированных данных
$parser->getParsedData();

// запись результата в файл
$parser->write();
```

