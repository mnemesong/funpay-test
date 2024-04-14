# funpay-test
Тестовое задание для funpay.


## Примечание
Тестовое задание реализовано с неизменными классами и скриптами, 
что были предоставлены в начале, с двумя исключениями: 
- создание mysqli объекта вынесено из тела теста в отдельный файл `mysqli.php`
- автолоадер был вынесен в отдельный файл `autoload.php`


## Принцип работы
Формирование запроса происходит в 3 шага
1. Токенизация по MYSQL синтаксису: проверка закрытия кавычек и корректности значений
2. Токенизация по условным выражениям. Проверка skip-значений
3. Токенизация по идентификаторам, форматирование и экранирование значений (с помощью TemplateTokenizer)


## Скрипты
- test.php - Исходный тест. Проверка корректности работы программы.
- tokenization-result-test.php - Проверка только логики класса TokenizationResult
- template-tokenizer-test.php - Проверка только логики класса TemplateTokenizer