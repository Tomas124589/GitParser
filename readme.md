# Git log parser

Knihovna pro pársování zprávy do objektu.

# Příklad použití

```php
use ortom\MyCommitMessageParser;

$input = "[add] [feature] @core #123456 Integrovat Premier: export objednávek

* Export objednávek cronem co hodinu.
* Export probíhá v dávkách.

BC: Refaktorovaný BaseImporter.

Feature: Nový logger.
TODO: Refactoring autoemail modulu.";

$parser = new MyCommitMessageParser();

$message = $parser->parse($input);

echo "Title: ", $message->getTitle(), PHP_EOL;
echo "TaskId: ", $message->getTaskId(), PHP_EOL;
echo "Tags: ", implode(", ", $message->getTags()), PHP_EOL;
echo "Details: ", implode(", ", $message->getDetails()), PHP_EOL;
echo "BCBreaks: ", implode(", ", $message->getBCBreaks()), PHP_EOL;
echo "Todos: ", implode(", ", $message->getTodos()), PHP_EOL;
```
