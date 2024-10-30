# Blueprint

A PHP library for dynamically replacing placeholders within a template string. This package processes text templates by
allowing developers to define multiple sets of placeholders, each with specific start and end delimiters and associated
values.

## Table of Contents

- [Introduction](#introduction)
    - [What is a Blueprint?](#what-is-a-blueprint)
- [Installation](#installation)
- [Usage](#usage)
    - [Creating a Placeholder Definition](#creating-a-placeholder-definition)
    - [Processing Placeholder Definitions](#processing-placeholder-definitions)
- [License](#license)

## Introduction

When working with dynamic content, it's often necessary to define placeholders in a template string and replace them
with real values based on specific rules. This library enables the use of a flexible, reusable structure for replacing
placeholders, making template customization intuitive and efficient.

This library supports both a basic replacement method for long templates and a more efficient scanning method for short
templates, allowing it to adapt based on performance needs.

### What is a Blueprint?

In this library, a "blueprint" refers to a template string containing placeholders that follow specific start and end
delimiters. These placeholders are substituted with values according to PlaceholderDefinition instances.

The BlueprintProcessor processes each placeholder definition, replacing placeholders until the entire blueprint is
complete. This approach supports nested or recursive replacements by re-processing the blueprint until no further
changes occur.

## Installation

You can install this package via Composer:

```bash
composer require novatorius/blueprint
```

## Usage

This library uses two primary classes:

* **BlueprintProcessor**: Manages the placeholder processing for a given template.
* **PlaceholderDefinition**: Defines a set of placeholders with specific delimiters and values.

### Creating a Placeholder Definition

The PlaceholderDefinition class defines:

* A starting delimiter
* An ending delimiter
* An associative array of placeholder values

To create a placeholder definition:

```php
use Novatorius\Blueprint\PlaceholderDefinition;

$definition = new PlaceholderDefinition('{', '}', [
    'name' => 'Alice',
    'day' => 'Monday'
]);

$alternativeDefinition = new PlaceholderDefinition('|*', '*|', [
    'name' => 'Alice',
    'day' => 'Monday'
]);
```

## Processing Placeholder Definitions

Once you've created placeholder definitions, you can process them with BlueprintProcessor. This fluent interface allows
you to chain multiple definitions for successive replacement.

```php
use Novatorius\Blueprint\BlueprintProcessor;

// Define your initial template (the "blueprint").
$template = "Hello, {name}! Today is %%day%%.";

// Process the template using the defined placeholders.
$processed = (new BlueprintProcessor($template))
    ->processDefinition($definition)
    ->toString();

echo $processed; // Outputs: "Hello, Alice! Today is Monday."
```

For multiple definitions, processDefinitions can be called in sequence:

```php
$definition1 = new PlaceholderDefinition('{', '}', ['name' => 'Alice']);
$definition2 = new PlaceholderDefinition('%%', '%%', ['day' => 'Monday']);

$processed = (new BlueprintProcessor($template))
    ->processDefinition($definition1)
    ->processDefinition($definition2)
    ->toString();

echo $processed; // Outputs: "Hello, Alice! Today is Monday."
```

`BlueprintProcessor` implements PHP's [__toString](https://www.php.net/manual/en/stringable.tostring.php) magic method,
so it can be automatically cast into a string, as well.

```php
$definition1 = new PlaceholderDefinition('{', '}', ['name' => 'Alice']);
$definition2 = new PlaceholderDefinition('%%', '%%', ['day' => 'Monday']);

$processed = (new BlueprintProcessor($template))
    ->processDefinition($definition1)
    ->processDefinition($definition2);

echo $processed; // Outputs: "Hello, Alice! Today is Monday."
```

## License

This project is proprietary software developed by Novatorius, LLC. All rights reserved.