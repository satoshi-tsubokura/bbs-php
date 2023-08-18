<?php

namespace Tests\Unit\Kernels;

use App\Kernels\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\CustomTestCase;

class ValidatorTest extends CustomTestCase
{
    // リクエストパラメーターがない場合
    public function testNoReqParameterValidate(): void
    {
        $rules = [
        'requiredTest' => [
            'name' => '必須項目テスト',
            'rules' => ['required'],
            'messages' => [
              'required' => '必須項目です。'
            ]
          ],
        ];
        $validator = new Validator($rules);
        $errorMsgs = $validator->validate();

        $expeced = ['requiredTest' => ['必須項目です。']];
        $this->assertEquals($expeced, $errorMsgs);
    }

    // バリデーションルールなし
    public function testNoValidationRulesValidate(): void
    {
        $values = ['name' => 'validateRules'];
        $validator = new Validator(targetValues: $values);
        $errorMsgs = $validator->validate();

        $this->assertCount(0, $errorMsgs);
    }

    // バリデーションルールに不備がある場合
    public function testMissingNameValidate(): void
    {
        $rules = [
          'invalidTest' => [
            'name' => 'ルール不正値テスト',
            'rules' => ['aaaa'], // 存在しないルール
            'messages' => [
              'aaaa' => '必須項目です。'
            ]
          ],
        ];

        $values = ['missingName' => 0];
        $this->expectException(\InvalidArgumentException::class);
        $validator = new Validator($rules, $values);
    }

    public function testMissingRulesValidate(): void
    {
        $rules = [
          'missingRules' => [
            'name' => 'ルールなし'
          ],
        ];

        $values = ['missingRules' => 0];
        $validator = new Validator($rules, $values);
        $errorMsgs = $validator->validate();

        $this->assertCount(0, $errorMsgs);
    }

    public function testInvalidRulesValidate(): void
    {
        $rules = [
          'missingRules' => [
            'name' => 'ルールなし'
          ],
        ];

        $values = ['missingRules' => 0];
        $validator = new Validator($rules, $values);
        $errorMsgs = $validator->validate();

        $this->assertCount(0, $errorMsgs);
    }

    // テスト成功(エラーメッセージなし)
    #[DataProvider('targetPassValidateDataProvider')]
    public function testPassValidate(array $targetValues, array $rules): void
    {
        $validator = new Validator($rules, $targetValues);

        $errorMsgs = $validator->validate();

        $this->assertCount(0, $errorMsgs);
    }

    // テスト成功(エラーメッセージあり)
    #[DataProvider('targetFalureValidateDataProvider')]
    public function testHasErrorMsgsValidate(array $targetValues, array $rules, $expected)
    {
        $validator = new Validator($rules, $targetValues);

        $errorMsgs = $validator->validate();
        $this->assertEquals($expected, $errorMsgs);
    }



    // データプロバイダークラスメソッド
    public static function targetPassValidateDataProvider(): array
    {
        $rules = [
          'oneRulesTest' => [
            'name' => '単純なケース',
            'rules' => ['integer']
          ],
          'multiRulesTest' => [
            'name' => '複数のバリデーションルールを持つケース',
            'rules' => ['min:10', 'max:20']
          ],
          'complexedRegexRulesTest' => [
            'name' => '正規表現のテストケース',
            'rules' => ['regex:/\A[a-zA-Z0-9\-+=^$*.\[\]{}()?"!@#%&\/\\\\,><\':;_~`\-+=\|]+\z/']
            ],
            'multiFieldRulesTest' => [
              'name' => '一つのルールに複数の設定値を持つルールのテスト',
              'rules' => ['lengthBetween:1,10']
            ],
            'arrayFieldRulesTest' => [
              'name' => 'ルールの検証時に配列を引数に持つルールのテスト',
              'rules' => ['in:blue,green,red,purple']
            ],
          ];

        $targetValuesList = [
          [['oneRulesTest' => -1], $rules],
          [['multiRulesTest' => '10'], $rules],
          [['multiRulesTest'  => 20], $rules],
          [['complexedRegexRulesTest'  => 'aA9\-+=^$*.[]{}()?\"!@|#%&/\,><:;_~`-+='], $rules],
          [['multiFieldRulesTest'  => 'あ'], $rules],
          [['multiFieldRulesTest'  => 'あいうえおかきくけこ'], $rules],
          [['multiArratRulesTest'  => 'purple'], $rules],
          // 複数のリクエストパラメーターに対するテスト
          [
            [
              'oneRulesTest' => -1,
              'multiRulesTest' => '10'
            ],
            $rules
          ]
        ];

        return [...$targetValuesList];
    }

    public static function targetFalureValidateDataProvider(): array
    {
        $rules = [
          'requiredTest' => [
            'name' => '必須項目テスト',
            'rules' => ['required'],
            'messages' => [
              'required' => '必須項目です。'
            ]
          ],
          'multiRulesTest' => [
            'name' => '複数のバリデーションルールを持つケース',
            'rules' => ['min:10', 'max:20'],
            'messages' => [
              'min' => '10以上の数値を入力してください。',
              'max' => '20以下の数値を入力してください。'
            ]
          ],
          'complexedRegexRulesTest' => [
            'name' => '正規表現のテストケース',
            'rules' => ['regex:/\A[a-zA-Z0-9_]+\z/'],
            'messages' => [
              'regex' => '{field} の形式が正しくありません。'
              ]
            ],
            'multiFieldRulesTest' => [
              'name' => '一つのルールに複数の設定値を持つルールのテスト',
              'rules' => ['lengthBetween:2,10'],
              'messages' => [
                'lengthBetween' => '1~10文字以内の文字列を入力してください。'
              ]
            ],
            'arrayFieldRulesTest' => [
              'name' => 'ルールの検証時に配列を引数に持つルールのテスト',
              'rules' => ['in:blue,green,red,purple'],
              'messages' => [
                'in' => '{field}には選択できない値が含まれています'
              ]
            ],
          ];

        $targetValuesList = [
          // テストケース0
          ['multiRulesTest' => 9],
          // テストケース1
          ['multiRulesTest'  => 21],
          // テストケース2
          ['complexedRegexRulesTest'  => 'aA0_$'],
          // テストケース3
          ['multiFieldRulesTest'  => 'あ'],
          // テストケース4
          ['multiFieldRulesTest'  => 'あいうえおかきくけこさ'],
          // テストケース5
          ['arrayFieldRulesTest'  => 'yellow'],
          // テストケース6
          [
            'arrayFieldRulesTest' => 'pink',
            'multiRulesTest' => 9.9
          ],
        ];

        $expectedList = [
          // テストケース0
          [
            'requiredTest' => [$rules['requiredTest']['messages']['required']],
            'multiRulesTest' => [$rules['multiRulesTest']['messages']['min']]
          ],
          // テストケース1
          [
            'requiredTest' => [$rules['requiredTest']['messages']['required']],
            'multiRulesTest' => [$rules['multiRulesTest']['messages']['max']]
          ],
          // テストケース2
          [
            'requiredTest' => [$rules['requiredTest']['messages']['required']],
            'complexedRegexRulesTest' => [$rules['complexedRegexRulesTest']['name'] . ' の形式が正しくありません。']
          ],
          // テストケース3
          [
            'requiredTest' => [$rules['requiredTest']['messages']['required']],
            'multiFieldRulesTest' => [$rules['multiFieldRulesTest']['messages']['lengthBetween']]
          ],
          // テストケース4
          [
            'requiredTest' => [$rules['requiredTest']['messages']['required']],
            'multiFieldRulesTest' => [$rules['multiFieldRulesTest']['messages']['lengthBetween']]
          ],
          // テストケース5
          [
            'requiredTest' => [$rules['requiredTest']['messages']['required']],
            'arrayFieldRulesTest' => [$rules['arrayFieldRulesTest']['name'] . 'には選択できない値が含まれています']
          ],
          // テストケース6
          [
            'requiredTest' => [$rules['requiredTest']['messages']['required']],
            'arrayFieldRulesTest' => [$rules['arrayFieldRulesTest']['name'] . 'には選択できない値が含まれています'],
            'multiRulesTest' => [$rules['multiRulesTest']['messages']['min']]
          ]
        ];

        $testCases = array_map(fn ($value, $expected) => [$value, $rules,
        $expected], $targetValuesList, $expectedList);
        return $testCases;
    }
}
