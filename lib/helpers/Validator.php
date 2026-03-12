<?php
/**
 * GatewayOS2 Website - Input Validator
 *
 * Validates associative data arrays against pipe-delimited rule strings.
 *
 * Supported rules:
 *   required          - field must be present and non-empty
 *   email             - must pass FILTER_VALIDATE_EMAIL
 *   min:N             - string length >= N
 *   max:N             - string length <= N
 *   matches:field     - value must match another field's value
 *   regex:pattern     - value must match the given regex
 *   alpha_num         - only letters, numbers, and underscores
 */

class Validator
{
    /**
     * Validate data against a set of rules.
     *
     * @param array $data  Associative array of field => value (typically $_POST).
     * @param array $rules Associative array of field => 'rule1|rule2:param|...'
     * @return array ['valid' => bool, 'errors' => ['field' => ['message', ...]]]
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? '';
            $label = ucfirst(str_replace('_', ' ', $field));

            foreach ($fieldRules as $rule) {
                $param = null;

                // Parse rule:param format
                if (strpos($rule, ':') !== false) {
                    [$rule, $param] = explode(':', $rule, 2);
                }

                $error = self::checkRule($rule, $value, $param, $label, $field, $data);
                if ($error !== null) {
                    $errors[$field][] = $error;
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Flatten a nested errors array into a simple list of messages.
     *
     * @param array $errors The errors array from validate().
     * @return array Flat list of error message strings.
     */
    public static function flatten(array $errors): array
    {
        $messages = [];
        foreach ($errors as $fieldErrors) {
            foreach ($fieldErrors as $msg) {
                $messages[] = $msg;
            }
        }
        return $messages;
    }

    /**
     * Check a single rule against a value.
     *
     * @return string|null Error message on failure, null on success.
     */
    private static function checkRule(string $rule, mixed $value, ?string $param, string $label, string $field, array $data): ?string
    {
        switch ($rule) {
            case 'required':
                if ($value === '' || $value === null) {
                    return "$label is required.";
                }
                break;

            case 'email':
                if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return "$label must be a valid email address.";
                }
                break;

            case 'min':
                $min = (int) $param;
                if ($value !== '' && mb_strlen((string) $value) < $min) {
                    return "$label must be at least $min characters.";
                }
                break;

            case 'max':
                $max = (int) $param;
                if ($value !== '' && mb_strlen((string) $value) > $max) {
                    return "$label must be no more than $max characters.";
                }
                break;

            case 'matches':
                $otherValue = $data[$param] ?? '';
                $otherLabel = ucfirst(str_replace('_', ' ', $param));
                if ($value !== $otherValue) {
                    return "$label must match $otherLabel.";
                }
                break;

            case 'regex':
                if ($value !== '' && !preg_match($param, (string) $value)) {
                    return "$label format is invalid.";
                }
                break;

            case 'alpha_num':
                if ($value !== '' && !preg_match('/^[a-zA-Z0-9_]+$/', (string) $value)) {
                    return "$label can only contain letters, numbers, and underscores.";
                }
                break;
        }

        return null;
    }
}
