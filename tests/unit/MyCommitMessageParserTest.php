<?php

use ortom\MyCommitMessageParser;
use PHPUnit\Framework\TestCase;

require __DIR__ . "/../../vendor/autoload.php";

final class MyCommitMessageParserTest extends TestCase
{

    private MyCommitMessageParser $parser;

    protected function setUp(): void
    {

        $this->parser = new MyCommitMessageParser();
    }

    public function test_example()
    {

        $input = "[add] [feature] @core #123456 Integrovat Premier: export objednávek

        * Export objednávek cronem co hodinu.
        * Export probíhá v dávkách.
        
        BC: Refaktorovaný BaseImporter.

        Feature: Nový logger.
        TODO: Refactoring autoemail modulu.";

        $message = $this->parser->parse($input);

        $this->assertSame($message->getTitle(), 'Integrovat Premier: export objednávek');
        $this->assertSame($message->getTaskId(), 123456);
        $this->assertSame($message->getTags(), ['add', 'feature']);
        $this->assertSame($message->getDetails(), ['Export objednávek cronem co hodinu.', 'Export probíhá v dávkách.']);
        $this->assertSame($message->getBCBreaks(), ['Refaktorovaný BaseImporter.']);
        $this->assertSame($message->getTodos(), ['Refactoring autoemail modulu.']);
    }

    public function test_no_tags()
    {

        $input = "@core #123456 Integrovat Premier: export objednávek

        * Export objednávek cronem co hodinu.
        * Export probíhá v dávkách.
        * Test 1.
        
        BC: Refaktorovaný BaseImporter.
        BC: Refaktorovaný X.
        BC: Refaktorovaný Y.
        
        Feature: Nový logger.
        TODO: Refactoring autoemail modulu.";

        $message = $this->parser->parse($input);

        $this->assertSame($message->getTitle(), 'Integrovat Premier: export objednávek');
        $this->assertSame($message->getTaskId(), 123456);
        $this->assertSame($message->getTags(), []);
        $this->assertSame($message->getDetails(), ['Export objednávek cronem co hodinu.', 'Export probíhá v dávkách.', 'Test 1.']);
        $this->assertSame($message->getBCBreaks(), ['Refaktorovaný BaseImporter.', 'Refaktorovaný X.', 'Refaktorovaný Y.']);
        $this->assertSame($message->getTodos(), ['Refactoring autoemail modulu.']);
    }

    public function test_no_bcbreaks()
    {

        $input = "[add] [feature] @core #123456 Integrovat Premier: export objednávek
        
        * Export objednávek cronem co hodinu.
        * Export probíhá v dávkách.

        Feature: Nový logger.
        Feature: abc123.
        Feature: xyz789456454 ejbwj wkl ek w.
        TODO: Refactoring autoemail modulu.";

        $message = $this->parser->parse($input);

        $this->assertSame($message->getTitle(), 'Integrovat Premier: export objednávek');
        $this->assertSame($message->getTaskId(), 123456);
        $this->assertSame($message->getTags(), ['add', 'feature']);
        $this->assertSame($message->getDetails(), ['Export objednávek cronem co hodinu.', 'Export probíhá v dávkách.']);
        $this->assertSame($message->getBCBreaks(), []);
        $this->assertSame($message->getTodos(), ['Refactoring autoemail modulu.']);
    }

    public function test_no_details()
    {

        $input = "[add] [feature] @core #123456 Integrovat Premier: export objednávek

        BC: Refaktorovaný BaseImporter.
        
        Feature: Nový logger.
        TODO: Refactoring autoemail modulu.";

        $message = $this->parser->parse($input);

        $this->assertSame($message->getTitle(), 'Integrovat Premier: export objednávek');
        $this->assertSame($message->getTaskId(), 123456);
        $this->assertSame($message->getTags(), ['add', 'feature']);
        $this->assertSame($message->getDetails(), []);
        $this->assertSame($message->getBCBreaks(), ['Refaktorovaný BaseImporter.']);
        $this->assertSame($message->getTodos(), ['Refactoring autoemail modulu.']);
    }

    public function test_no_todos()
    {

        $input = "[add] [feature] @core #123456 Integrovat Premier: export objednávek

        * Export objednávek cronem co hodinu.
        * Export probíhá v dávkách.
        
        BC: Refaktorovaný BaseImporter.
        
        Feature: Nový logger.";

        $message = $this->parser->parse($input);

        $this->assertSame($message->getTitle(), 'Integrovat Premier: export objednávek');
        $this->assertSame($message->getTaskId(), 123456);
        $this->assertSame($message->getTags(), ['add', 'feature']);
        $this->assertSame($message->getDetails(), ['Export objednávek cronem co hodinu.', 'Export probíhá v dávkách.']);
        $this->assertSame($message->getBCBreaks(), ['Refaktorovaný BaseImporter.']);
        $this->assertSame($message->getTodos(), []);
    }

    public function test_empty_message_throws_exception()
    {

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Message is empty.");
        $this->parser->parse("");
    }

    public function test_empty_title_throws_exception()
    {

        $input = "[add] [feature] @core
        * Export objednávek cronem co hodinu.
        * Export probíhá v dávkách.
        
        BC: Refaktorovaný BaseImporter.
        
        Feature: Nový logger.";

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Title is empty");
        $msg = $this->parser->parse($input);
    }

    public function test_no_task_id()
    {

        $input = "[add] [feature] @core Integrovat Premier: export objednávek

        * Export objednávek cronem co hodinu.
        * Export probíhá v dávkách.
        
        BC: Refaktorovaný BaseImporter.
        
        Feature: Nový logger.
        TODO: Refactoring autoemail modulu.";

        $message = $this->parser->parse($input);

        $this->assertSame(null, $message->getTaskId());
    }

    public function test_malformed_body_exception_1()
    {

        $input = "[add] [feature] @core Integrovat Premier: export objednávek

        * Export objednávek cronem co hodinu.
        BC: Refaktorovaný BaseImporter.
        * Export probíhá v dávkách.
        
        
        Feature: Nový logger.
        TODO: Refactoring autoemail modulu.";

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Body is malformed.');
        $this->parser->parse($input);
    }

    public function test_malformed_body_exception_2()
    {

        $input = "[add] [feature] @core Integrovat Premier: export objednávek

        * Export objednávek cronem co hodinu.
        * Export probíhá v dávkách.
        
        BC: Refaktorovaný BaseImporter.
        TODO: this is wrong
        
        Feature: Nový logger.
        TODO: Refactoring autoemail modulu.";

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Body is malformed.');
        $this->parser->parse($input);
    }

    public function test_malformed_body_exception_3()
    {

        $input = "[add] [feature] @core Integrovat Premier: export objednávek

        * Export objednávek cronem co hodinu.
        * Export probíhá v dávkách.
        
        BC: Refaktorovaný BaseImporter.
        
        Feature: Nový logger.
        TODO: Refactoring autoemail modulu.
        BC: Test";

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Body is malformed.');
        $this->parser->parse($input);
    }

    public function test_malformed_header_exception_1()
    {

        $input = "[add] [feature] abdxed @core Integrovat Premier: export objednávek

        * Export objednávek cronem co hodinu.
        * Export probíhá v dávkách.
        
        Feature: Nový logger.
        TODO: Refactoring autoemail modulu.";

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Header is malformed.');
        $this->parser->parse($input);
    }

    public function test_malformed_header_exception_2()
    {

        $input = "[add] [feature] Integrovat Premier: export objednávek

        * Export objednávek cronem co hodinu.
        * Export probíhá v dávkách.
        
        Feature: Nový logger.
        TODO: Refactoring autoemail modulu.";

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Message header matching failed.');
        $this->parser->parse($input);
    }
}
