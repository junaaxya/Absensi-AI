<?php

use App\Exceptions\FormulaException;
use App\Services\Payroll\Formula\FormulaEvaluator;

test('basic arithmetic: addition', function () {
    $evaluator = new FormulaEvaluator();
    expect($evaluator->evaluate('2 + 3', []))->toBe(5.0);
});

test('basic arithmetic: subtraction', function () {
    $evaluator = new FormulaEvaluator();
    expect($evaluator->evaluate('10 - 4', []))->toBe(6.0);
});

test('basic arithmetic: multiplication', function () {
    $evaluator = new FormulaEvaluator();
    expect($evaluator->evaluate('3 * 7', []))->toBe(21.0);
});

test('basic arithmetic: division', function () {
    $evaluator = new FormulaEvaluator();
    expect($evaluator->evaluate('15 / 3', []))->toBe(5.0);
});

test('variables: basic_salary * 0.05', function () {
    $evaluator = new FormulaEvaluator();
    $result = $evaluator->evaluate('basic_salary * 0.05', ['basic_salary' => 8000000]);
    expect($result)->toBe(400000.0);
});

test('functions: min(basic_salary, 12000000) * 0.04', function () {
    $evaluator = new FormulaEvaluator();
    $result = $evaluator->evaluate('min(basic_salary, 12000000) * 0.04', ['basic_salary' => 8000000]);
    expect($result)->toBe(320000.0);
});

test('functions: max(100, 200)', function () {
    $evaluator = new FormulaEvaluator();
    expect($evaluator->evaluate('max(100, 200)', []))->toBe(200.0);
});

test('functions: round(3.456, 2)', function () {
    $evaluator = new FormulaEvaluator();
    expect($evaluator->evaluate('round(3.456, 2)', []))->toBe(3.46);
});

test('functions: floor(3.7)', function () {
    $evaluator = new FormulaEvaluator();
    expect($evaluator->evaluate('floor(3.7)', []))->toBe(3.0);
});

test('functions: ceil(3.2)', function () {
    $evaluator = new FormulaEvaluator();
    expect($evaluator->evaluate('ceil(3.2)', []))->toBe(4.0);
});

test('functions: abs(-500)', function () {
    $evaluator = new FormulaEvaluator();
    expect($evaluator->evaluate('abs(-500)', []))->toBe(500.0);
});

test('if function: overtime_hours > 0 with overtime_hours=5', function () {
    $evaluator = new FormulaEvaluator();
    $result = $evaluator->evaluate(
        'if(overtime_hours > 0, overtime_hours * 50000, 0)',
        ['overtime_hours' => 5]
    );
    expect($result)->toBe(250000.0);
});

test('if function: overtime_hours > 0 with overtime_hours=0', function () {
    $evaluator = new FormulaEvaluator();
    $result = $evaluator->evaluate(
        'if(overtime_hours > 0, overtime_hours * 50000, 0)',
        ['overtime_hours' => 0]
    );
    expect($result)->toBe(0.0);
});

test('complex formula: basic_salary * 0.05 + if(present_days >= 20, 500000, 0)', function () {
    $evaluator = new FormulaEvaluator();
    $result = $evaluator->evaluate(
        'basic_salary * 0.05 + if(present_days >= 20, 500000, 0)',
        ['basic_salary' => 8000000, 'present_days' => 22]
    );
    expect($result)->toBe(900000.0);
});

test('division by zero throws FormulaException', function () {
    $evaluator = new FormulaEvaluator();
    $evaluator->evaluate('basic_salary / 0', ['basic_salary' => 8000000]);
})->throws(FormulaException::class);

test('forbidden pattern: $variable throws FormulaException', function () {
    $evaluator = new FormulaEvaluator();
    $evaluator->evaluate('$variable', []);
})->throws(FormulaException::class);

test('forbidden pattern: eval(1) throws FormulaException', function () {
    $evaluator = new FormulaEvaluator();
    $evaluator->evaluate('eval(1)', []);
})->throws(FormulaException::class);

test('forbidden pattern: system("ls") throws FormulaException', function () {
    $evaluator = new FormulaEvaluator();
    $evaluator->evaluate('system("ls")', []);
})->throws(FormulaException::class);

test('too long formula throws FormulaException', function () {
    $evaluator = new FormulaEvaluator();
    $longFormula = str_repeat('1 + ', 126) . '1';
    $evaluator->evaluate($longFormula, []);
})->throws(FormulaException::class);

test('undefined variable throws FormulaException with suggestion', function () {
    $evaluator = new FormulaEvaluator();
    try {
        $evaluator->evaluate('unknown_var * 2', ['basic_salary' => 8000000]);
        $this->fail('Expected FormulaException was not thrown');
    } catch (FormulaException $e) {
        expect($e->getMessage())->toContain('unknown_var');
        expect($e->getMessage())->toContain('tidak terdefinisi');
    }
});

test('validation: basic_salary * 0.05 with available=[basic_salary] is valid', function () {
    $evaluator = new FormulaEvaluator();
    $result = $evaluator->validate('basic_salary * 0.05', ['basic_salary']);
    expect($result['valid'])->toBeTrue();
    expect($result['error'])->toBeNull();
    expect($result['variables_used'])->toContain('basic_salary');
});

test('validation: unknown * 2 with available=[basic_salary] is invalid', function () {
    $evaluator = new FormulaEvaluator();
    $result = $evaluator->validate('unknown * 2', ['basic_salary']);
    expect($result['valid'])->toBeFalse();
    expect($result['error'])->not->toBeNull();
});

test('nested functions: round(min(basic_salary * 0.05, 500000), 0)', function () {
    $evaluator = new FormulaEvaluator();
    $result = $evaluator->evaluate(
        'round(min(basic_salary * 0.05, 500000), 0)',
        ['basic_salary' => 8000000]
    );
    expect($result)->toBe(400000.0);
});

test('comparison operators: if(basic_salary >= 10000000, basic_salary * 0.1, basic_salary * 0.05) with 8jt', function () {
    $evaluator = new FormulaEvaluator();
    $result = $evaluator->evaluate(
        'if(basic_salary >= 10000000, basic_salary * 0.1, basic_salary * 0.05)',
        ['basic_salary' => 8000000]
    );
    expect($result)->toBe(400000.0);
});
