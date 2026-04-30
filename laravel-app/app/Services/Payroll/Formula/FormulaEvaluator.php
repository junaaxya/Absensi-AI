<?php

namespace App\Services\Payroll\Formula;

use App\Exceptions\FormulaException;

class FormulaEvaluator
{
    private const MAX_FORMULA_LENGTH = 500;
    private const MAX_AST_DEPTH = 10;
    private const MAX_AST_NODES = 50;
    private const MAX_VARIABLE_NAME = 30;

    private const WHITELISTED_FUNCTIONS = ['min', 'max', 'round', 'floor', 'ceil', 'abs', 'if'];

    private const FORBIDDEN_PATTERNS = [
        '$', '->', '::', '[', '{', ';',
        'eval', 'exec', 'system', 'shell', 'passthru',
        'include', 'require', 'function', 'class', 'new',
        'echo', 'print', '=>', '..', '`',
    ];

    private array $tokens;
    private int $pos;
    private int $nodeCount;
    private array $variablesUsed;

    public function evaluate(string $formula, array $variables): float
    {
        $this->guardFormula($formula);

        $this->tokens = $this->tokenize($formula);
        $this->pos = 0;
        $this->nodeCount = 0;
        $this->variablesUsed = [];

        $ast = $this->parseExpression();

        if ($this->pos < count($this->tokens)) {
            throw FormulaException::unexpectedToken($this->tokens[$this->pos]['value']);
        }

        $result = $this->evaluateNode($ast, $variables, 0);

        if (!is_finite($result)) {
            throw FormulaException::invalidResult($formula, 'Hasil bukan angka terbatas (NaN/Infinity)');
        }

        return round($result, 2);
    }

    /**
     * @return array{valid: bool, error: string|null, variables_used: string[]}
     */
    public function validate(string $formula, array $availableVariables = []): array
    {
        try {
            $this->guardFormula($formula);

            $this->tokens = $this->tokenize($formula);
            $this->pos = 0;
            $this->nodeCount = 0;
            $this->variablesUsed = [];

            $ast = $this->parseExpression();

            if ($this->pos < count($this->tokens)) {
                throw FormulaException::unexpectedToken($this->tokens[$this->pos]['value']);
            }

            $this->collectVariables($ast);

            if (!empty($availableVariables)) {
                foreach ($this->variablesUsed as $var) {
                    if (!in_array($var, $availableVariables, true)) {
                        throw FormulaException::undefinedVariable($var, $availableVariables);
                    }
                }
            }

            return [
                'valid' => true,
                'error' => null,
                'variables_used' => array_values(array_unique($this->variablesUsed)),
            ];
        } catch (FormulaException $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage(),
                'variables_used' => array_values(array_unique($this->variablesUsed)),
            ];
        }
    }

    private function guardFormula(string $formula): void
    {
        $trimmed = trim($formula);

        if ($trimmed === '') {
            throw FormulaException::empty();
        }

        if (mb_strlen($trimmed) > self::MAX_FORMULA_LENGTH) {
            throw FormulaException::tooLong(self::MAX_FORMULA_LENGTH);
        }

        foreach (self::FORBIDDEN_PATTERNS as $pattern) {
            if (str_contains($trimmed, $pattern)) {
                throw FormulaException::forbidden($trimmed, $pattern);
            }
        }
    }

    private function tokenize(string $formula): array
    {
        $tokens = [];
        $length = strlen($formula);
        $i = 0;

        while ($i < $length) {
            $char = $formula[$i];

            if (ctype_space($char)) {
                $i++;
                continue;
            }

            if ($char === '(' || $char === ')' || $char === ',') {
                $tokens[] = ['type' => 'paren', 'value' => $char];
                $i++;
                continue;
            }

            if ($char === '+' || $char === '-' || $char === '*' || $char === '/' || $char === '%') {
                $tokens[] = ['type' => 'operator', 'value' => $char];
                $i++;
                continue;
            }

            if ($char === '>' || $char === '<' || $char === '=' || $char === '!') {
                $next = ($i + 1 < $length) ? $formula[$i + 1] : '';
                if ($next === '=') {
                    $tokens[] = ['type' => 'operator', 'value' => $char . '='];
                    $i += 2;
                } elseif ($char === '>' || $char === '<') {
                    $tokens[] = ['type' => 'operator', 'value' => $char];
                    $i++;
                } else {
                    throw FormulaException::invalidToken($char);
                }
                continue;
            }

            if (ctype_digit($char) || $char === '.') {
                $num = '';
                while ($i < $length && (ctype_digit($formula[$i]) || $formula[$i] === '.')) {
                    $num .= $formula[$i];
                    $i++;
                }
                $tokens[] = ['type' => 'number', 'value' => $num];
                continue;
            }

            if (ctype_alpha($char) || $char === '_') {
                $ident = '';
                while ($i < $length && (ctype_alnum($formula[$i]) || $formula[$i] === '_')) {
                    $ident .= $formula[$i];
                    $i++;
                }

                if (strlen($ident) > self::MAX_VARIABLE_NAME) {
                    throw FormulaException::invalidVariable($ident);
                }

                $lower = strtolower($ident);
                if (in_array($lower, self::WHITELISTED_FUNCTIONS, true) && $i < $length && $formula[$i] === '(') {
                    $tokens[] = ['type' => 'function', 'value' => $lower];
                } else {
                    $tokens[] = ['type' => 'identifier', 'value' => $ident];
                }
                continue;
            }

            throw FormulaException::invalidToken($char);
        }

        return $tokens;
    }

    private function peek(): ?array
    {
        return $this->tokens[$this->pos] ?? null;
    }

    private function consume(?string $expectedType = null, ?string $expectedValue = null): array
    {
        $token = $this->peek();

        if ($token === null) {
            throw FormulaException::unexpectedEnd();
        }

        if ($expectedType !== null && $token['type'] !== $expectedType) {
            throw FormulaException::expectedToken($expectedType, $token['type'] . ':' . $token['value']);
        }

        if ($expectedValue !== null && $token['value'] !== $expectedValue) {
            throw FormulaException::expectedToken($expectedValue, $token['value']);
        }

        $this->pos++;
        return $token;
    }

    private function addNode(): void
    {
        $this->nodeCount++;
        if ($this->nodeCount > self::MAX_AST_NODES) {
            throw FormulaException::tooComplex(self::MAX_AST_NODES);
        }
    }

    private function parseExpression(): array
    {
        return $this->parseComparison();
    }

    private function parseComparison(): array
    {
        $left = $this->parseAddSub();

        while (true) {
            $token = $this->peek();
            if ($token === null || $token['type'] !== 'operator') {
                break;
            }
            if (!in_array($token['value'], ['>', '<', '>=', '<=', '==', '!='], true)) {
                break;
            }
            $op = $this->consume();
            $right = $this->parseAddSub();
            $this->addNode();
            $left = ['type' => 'binary', 'op' => $op['value'], 'left' => $left, 'right' => $right];
        }

        return $left;
    }

    private function parseAddSub(): array
    {
        $left = $this->parseMulDiv();

        while (true) {
            $token = $this->peek();
            if ($token === null || $token['type'] !== 'operator') {
                break;
            }
            if ($token['value'] !== '+' && $token['value'] !== '-') {
                break;
            }
            $op = $this->consume();
            $right = $this->parseMulDiv();
            $this->addNode();
            $left = ['type' => 'binary', 'op' => $op['value'], 'left' => $left, 'right' => $right];
        }

        return $left;
    }

    private function parseMulDiv(): array
    {
        $left = $this->parseUnary();

        while (true) {
            $token = $this->peek();
            if ($token === null || $token['type'] !== 'operator') {
                break;
            }
            if ($token['value'] !== '*' && $token['value'] !== '/' && $token['value'] !== '%') {
                break;
            }
            $op = $this->consume();
            $right = $this->parseUnary();
            $this->addNode();
            $left = ['type' => 'binary', 'op' => $op['value'], 'left' => $left, 'right' => $right];
        }

        return $left;
    }

    private function parseUnary(): array
    {
        $token = $this->peek();

        if ($token !== null && $token['type'] === 'operator' && ($token['value'] === '-' || $token['value'] === '+')) {
            $op = $this->consume();
            $operand = $this->parsePrimary();
            $this->addNode();
            return ['type' => 'unary', 'op' => $op['value'], 'operand' => $operand];
        }

        return $this->parsePrimary();
    }

    private function parsePrimary(): array
    {
        $token = $this->peek();

        if ($token === null) {
            throw FormulaException::unexpectedEnd();
        }

        if ($token['type'] === 'number') {
            $this->consume();
            $this->addNode();
            return ['type' => 'number', 'value' => (float) $token['value']];
        }

        if ($token['type'] === 'function') {
            return $this->parseFunctionCall();
        }

        if ($token['type'] === 'identifier') {
            $this->consume();
            $this->addNode();
            $this->variablesUsed[] = $token['value'];
            return ['type' => 'variable', 'name' => $token['value']];
        }

        if ($token['type'] === 'paren' && $token['value'] === '(') {
            $this->consume();
            $expr = $this->parseExpression();
            $this->consume('paren', ')');
            return $expr;
        }

        throw FormulaException::unexpectedToken($token['value']);
    }

    private function parseFunctionCall(): array
    {
        $funcToken = $this->consume('function');
        $funcName = $funcToken['value'];

        if (!in_array($funcName, self::WHITELISTED_FUNCTIONS, true)) {
            throw FormulaException::forbiddenFunction($funcName);
        }

        $this->consume('paren', '(');
        $args = [];

        if ($this->peek() !== null && !($this->peek()['type'] === 'paren' && $this->peek()['value'] === ')')) {
            $args[] = $this->parseExpression();
            while ($this->peek() !== null && $this->peek()['type'] === 'paren' && $this->peek()['value'] === ',') {
                $this->consume();
                $args[] = $this->parseExpression();
            }
        }

        $this->consume('paren', ')');
        $this->addNode();

        $this->validateFunctionArgs($funcName, count($args));

        return ['type' => 'call', 'name' => $funcName, 'args' => $args];
    }

    private function validateFunctionArgs(string $func, int $count): void
    {
        $argRanges = [
            'min' => [2, 2],
            'max' => [2, 2],
            'round' => [1, 2],
            'floor' => [1, 1],
            'ceil' => [1, 1],
            'abs' => [1, 1],
            'if' => [3, 3],
        ];

        $range = $argRanges[$func] ?? null;
        if ($range === null) {
            return;
        }

        if ($count < $range[0] || $count > $range[1]) {
            throw FormulaException::wrongArgCount($func, $range[0], $range[1], $count);
        }
    }

    private function evaluateNode(array $node, array $variables, int $depth): float
    {
        if ($depth > self::MAX_AST_DEPTH) {
            throw FormulaException::tooDeep(self::MAX_AST_DEPTH);
        }

        return match ($node['type']) {
            'number' => $node['value'],
            'variable' => $this->resolveVariable($node['name'], $variables),
            'unary' => $this->evaluateUnary($node, $variables, $depth),
            'binary' => $this->evaluateBinary($node, $variables, $depth),
            'call' => $this->evaluateCall($node, $variables, $depth),
            default => throw FormulaException::unknownNodeType($node['type']),
        };
    }

    private function resolveVariable(string $name, array $variables): float
    {
        if (!array_key_exists($name, $variables)) {
            throw FormulaException::undefinedVariable($name, array_keys($variables));
        }

        $value = $variables[$name];

        if (!is_numeric($value)) {
            throw FormulaException::nonNumericVariable($name, $value);
        }

        return (float) $value;
    }

    private function evaluateUnary(array $node, array $variables, int $depth): float
    {
        $operand = $this->evaluateNode($node['operand'], $variables, $depth + 1);

        return match ($node['op']) {
            '-' => -$operand,
            '+' => $operand,
            default => throw FormulaException::unknownOperator($node['op']),
        };
    }

    private function evaluateBinary(array $node, array $variables, int $depth): float
    {
        $left = $this->evaluateNode($node['left'], $variables, $depth + 1);
        $right = $this->evaluateNode($node['right'], $variables, $depth + 1);

        return match ($node['op']) {
            '+' => $left + $right,
            '-' => $left - $right,
            '*' => $left * $right,
            '/' => $this->safeDivide($left, $right),
            '%' => $this->safeModulo($left, $right),
            '>' => ($left > $right) ? 1.0 : 0.0,
            '<' => ($left < $right) ? 1.0 : 0.0,
            '>=' => ($left >= $right) ? 1.0 : 0.0,
            '<=' => ($left <= $right) ? 1.0 : 0.0,
            '==' => (abs($left - $right) < 1e-9) ? 1.0 : 0.0,
            '!=' => (abs($left - $right) >= 1e-9) ? 1.0 : 0.0,
            default => throw FormulaException::unknownOperator($node['op']),
        };
    }

    private function safeDivide(float $left, float $right): float
    {
        if (abs($right) < 1e-15) {
            throw FormulaException::divisionByZero();
        }
        return $left / $right;
    }

    private function safeModulo(float $left, float $right): float
    {
        if (abs($right) < 1e-15) {
            throw FormulaException::divisionByZero();
        }
        return fmod($left, $right);
    }

    private function evaluateCall(array $node, array $variables, int $depth): float
    {
        $args = array_map(
            fn (array $arg) => $this->evaluateNode($arg, $variables, $depth + 1),
            $node['args']
        );

        return match ($node['name']) {
            'min' => min($args[0], $args[1]),
            'max' => max($args[0], $args[1]),
            'round' => round($args[0], (int) ($args[1] ?? 0)),
            'floor' => floor($args[0]),
            'ceil' => ceil($args[0]),
            'abs' => abs($args[0]),
            'if' => (abs($args[0]) >= 1e-9) ? $args[1] : $args[2],
            default => throw FormulaException::forbiddenFunction($node['name']),
        };
    }

    private function collectVariables(array $node): void
    {
        if ($node['type'] === 'variable') {
            $this->variablesUsed[] = $node['name'];
            return;
        }

        if ($node['type'] === 'binary') {
            $this->collectVariables($node['left']);
            $this->collectVariables($node['right']);
            return;
        }

        if ($node['type'] === 'unary') {
            $this->collectVariables($node['operand']);
            return;
        }

        if ($node['type'] === 'call') {
            foreach ($node['args'] as $arg) {
                $this->collectVariables($arg);
            }
        }
    }
}
