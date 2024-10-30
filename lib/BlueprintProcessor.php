<?php

namespace Novatorius\Blueprint;

class BlueprintProcessor
{
    public function __construct(private string $blueprint)
    {
    }

    /**
     * Processes definitions to replace placeholders in the blueprint.
     *
     * @param PlaceholderDefinition $definition
     * @return $this
     */
    public function processDefinition(PlaceholderDefinition $definition): self
    {
        $this->blueprint = $this->replacePlaceholders($this->blueprint, $definition);

        return $this;
    }

    /**
     * Get the final processed blueprint string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->blueprint;
    }

    /**
     * Get the final processed blueprint string.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->blueprint;
    }

    /**
     * Replaces placeholders using the given definition.
     *
     * @param string $blueprint
     * @param PlaceholderDefinition $definition
     * @return string
     */
    private function replacePlaceholders(string $blueprint, PlaceholderDefinition $definition): string
    {
        // Coefficients for heuristic calculation
        $a = 0.00066834;
        $b = 0.016041;
        $c = 1.7966;

        $numCharacters = strlen($blueprint);
        $numItems = count($definition->getInput());

        $estimatedItems = $a * $numCharacters ** 2 + $b * $numCharacters + $c;

        if ($numItems > $estimatedItems) {
            return $this->parseBlueprintUsingScan($blueprint, $definition);
        } else {
            return $this->parseBlueprintUsingReplace($blueprint, $definition);
        }
    }

    /**
     * Parses a blueprint string using the replace method.
     *
     * @param string $blueprint
     * @param PlaceholderDefinition $definition
     * @return string
     */
    private function parseBlueprintUsingReplace(string $blueprint, PlaceholderDefinition $definition): string
    {
        foreach ($definition->getInput() as $placeholder => $value) {
            $blueprint = str_replace($definition->start . $placeholder . $definition->end, $value, $blueprint);
        }
        return $blueprint;
    }

    /**
     * Parses a blueprint string using the scan method.
     *
     * @param string $blueprint
     * @param PlaceholderDefinition $definition
     * @return string
     */
    private function parseBlueprintUsingScan(string $blueprint, PlaceholderDefinition $definition): string
    {
        $result = '';
        $idx = 0;
        $currentKey = '';
        $startLen = strlen($definition->start);
        $endLen = strlen($definition->end);
        $isInPlaceholder = false;

        while ($idx < strlen($blueprint)) {
            // Check if we are currently scanning a placeholder
            if ($isInPlaceholder) {
                // Check if the current substring matches the ending delimiter
                if (substr($blueprint, $idx, $endLen) === $definition->end) {
                    // We've reached the end of a placeholder
                    $result .= $definition->getInput()[$currentKey] ?? "{$definition->start}{$currentKey}{$definition->end}";
                    $currentKey = '';
                    $isInPlaceholder = false;
                    $idx += $endLen;
                } else {
                    // Accumulate the current character as part of the placeholder key
                    $currentKey .= $blueprint[$idx++];
                }
            } else {
                // Check if the current substring matches the starting delimiter
                if (substr($blueprint, $idx, $startLen) === $definition->start) {
                    // We've hit a new placeholder, skip the start delimiter characters
                    $isInPlaceholder = true;
                    $idx += $startLen;
                } else {
                    // It's just a regular character, add it to the result
                    $result .= $blueprint[$idx++];
                }
            }
        }

        // If the blueprint string ends partway through a placeholder, retain it as-is
        if ($isInPlaceholder) {
            $result .= $definition->start . $currentKey;
        }

        return $result;
    }
}