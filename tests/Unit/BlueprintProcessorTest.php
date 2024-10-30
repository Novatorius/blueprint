<?php


use Novatorius\Blueprint\BlueprintProcessor;
use Novatorius\Blueprint\PlaceholderDefinition;
use Novatorius\Blueprint\Tests\TestCase;

class BlueprintProcessorTest extends TestCase
{
    /**
     * @dataProvider blueprintProvider
     */
    public function testReplacePlaceholders(string $blueprint, array $definitions, string $expected)
    {
        $processor = new BlueprintProcessor($blueprint);

        foreach ($definitions as $definition) {
            $processor->processDefinition($definition);
        }

        $this->assertEquals($expected, (string) $processor);
    }

    public function blueprintProvider(): Generator
    {
        // Standard Test Cases
        yield 'Empty Blueprint' => [
            '',
            [new PlaceholderDefinition('{', '}', ['name' => 'Alice'])],
            ''
        ];

        yield 'Empty Definitions' => [
            'Hello, {name}!',
            [new PlaceholderDefinition('{', '}', [])],
            'Hello, {name}!'
        ];

        yield 'No Placeholders in Blueprint' => [
            'Hello, world!',
            [new PlaceholderDefinition('{', '}', ['name' => 'Alice'])],
            'Hello, world!'
        ];

        yield 'Overlapping Placeholders' => [
            'Hello, {name_full} and {name}',
            [new PlaceholderDefinition('{', '}', ['name' => 'Alice', 'name_full' => 'Alice Johnson'])],
            'Hello, Alice Johnson and Alice'
        ];

        yield 'Overlapping Placeholders with %' => [
            'Hello, %name_full% and %name%',
            [new PlaceholderDefinition('%', '%', ['name' => 'Alice', 'name_full' => 'Alice Johnson'])],
            'Hello, Alice Johnson and Alice'
        ];

        yield 'Multi Character Placeholder' => [
            'Hello, {{name_full}} and {{name}}',
            [new PlaceholderDefinition('{{', '}}', ['name' => 'Alice', 'name_full' => 'Alice Johnson'])],
            'Hello, Alice Johnson and Alice'
        ];

        yield 'Mixed Character Placeholder' => [
            'Hello, |*name_full*| and |*name*|',
            [new PlaceholderDefinition('|*', '*|', ['name' => 'Alice', 'name_full' => 'Alice Johnson'])],
            'Hello, Alice Johnson and Alice'
        ];

        yield 'Missing Placeholder in Input' => [
            'Hello, {name} and {unknown}!',
            [new PlaceholderDefinition('{', '}', ['name' => 'Alice'])],
            'Hello, Alice and {unknown}!'
        ];

        yield 'Placeholders with Special Characters' => [
            'Welcome, {user.name}!',
            [new PlaceholderDefinition('{', '}', ['user.name' => 'Alice'])],
            'Welcome, Alice!'
        ];

        yield 'Repeated Placeholders' => [
            '{name} is {name}!',
            [new PlaceholderDefinition('{', '}', ['name' => 'Alice'])],
            'Alice is Alice!'
        ];

        yield 'Non-String Placeholder Values' => [
            'Your age is {age} and verified: {verified}',
            [new PlaceholderDefinition('{', '}', ['age' => 30, 'verified' => true])],
            'Your age is 30 and verified: 1'
        ];

        yield 'No Closing Placeholder Symbol in Blueprint' => [
            'Hello, {name',
            [new PlaceholderDefinition('{', '}', ['name' => 'Alice'])],
            'Hello, {name'
        ];

        // Large Array Test Cases
        $largeInput = array_fill_keys(range(1, 1000), 'TestValue');

        yield 'Large Array - Empty Blueprint' => [
            '',
            [new PlaceholderDefinition('{', '}', $largeInput)],
            ''
        ];

        yield 'Large Array - Empty Definitions' => [
            'Hello, {name}!',
            [new PlaceholderDefinition('{', '}', $largeInput)],
            'Hello, {name}!'
        ];

        yield 'Large Array - No Placeholders in Blueprint' => [
            'Hello, world!',
            [new PlaceholderDefinition('{', '}', $largeInput)],
            'Hello, world!'
        ];

        yield 'Large Array - Overlapping Placeholders' => [
            'Hello, {name_full} and {name}',
            [new PlaceholderDefinition('{', '}', array_merge($largeInput, ['name' => 'Alice', 'name_full' => 'Alice Johnson']))],
            'Hello, Alice Johnson and Alice'
        ];

        yield 'Large Array - Overlapping Placeholders with %' => [
            'Hello, %name_full% and %name%',
            [new PlaceholderDefinition('%', '%', array_merge($largeInput, ['name' => 'Alice', 'name_full' => 'Alice Johnson']))],
            'Hello, Alice Johnson and Alice'
        ];

        yield 'Large Array - Multi Character Placeholder' => [
            'Hello, {{name_full}} and {{name}}',
            [new PlaceholderDefinition('{{', '}}', array_merge($largeInput, ['name' => 'Alice', 'name_full' => 'Alice Johnson']))],
            'Hello, Alice Johnson and Alice'
        ];

        yield 'Large Array - Mixed Character Placeholder' => [
            'Hello, |*name_full*| and |*name*|',
            [new PlaceholderDefinition('|*', '*|', array_merge($largeInput, ['name' => 'Alice', 'name_full' => 'Alice Johnson']))],
            'Hello, Alice Johnson and Alice'
        ];

        yield 'Large Array - Missing Placeholder in Input' => [
            'Hello, {name} and {unknown}!',
            [new PlaceholderDefinition('{', '}', array_merge($largeInput, ['name' => 'Alice']))],
            'Hello, Alice and {unknown}!'
        ];

        yield 'Large Array - Placeholders with Special Characters' => [
            'Welcome, {user.name}!',
            [new PlaceholderDefinition('{', '}', array_merge($largeInput, ['user.name' => 'Alice']))],
            'Welcome, Alice!'
        ];

        yield 'Large Array - Repeated Placeholders' => [
            '{name} is {name}!',
            [new PlaceholderDefinition('{', '}', array_merge($largeInput, ['name' => 'Alice']))],
            'Alice is Alice!'
        ];

        yield 'Large Array - Non-String Placeholder Values' => [
            'Your age is {age} and verified: {verified}',
            [new PlaceholderDefinition('{', '}', array_merge($largeInput, ['age' => 30, 'verified' => true]))],
            'Your age is 30 and verified: 1'
        ];

        yield 'Large Array - No Closing Placeholder Symbol in Blueprint' => [
            'Hello, {name',
            [new PlaceholderDefinition('{', '}', array_merge($largeInput, ['name' => 'Alice']))],
            'Hello, {name'
        ];
    }
}