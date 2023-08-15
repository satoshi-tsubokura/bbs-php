<?php

namespace App\Middlewares\Validations;

use Valitron\Validator;

/**
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class RequestValidator
{
    /**
     * 2つ以上のバリデーションルールの値を持つバリデーションの種類
     */
    public const MULTIPLE_FIELD_RULES = ['lengthBetween'];
    /**
     * 配列でバリデーションルールの値を指定するバリデーションの種類
     */
    public const ARRAY_FIELD_RULES = ['in', 'subset'];

    private const ERROR_MESSAGE_FILE_PATH = __DIR__ . '/../../config/error_msgs.php';

    private array $defaultErrorMsgs;
    private Validator $validator;

    /**
     * @param array $validationDataList リクエストパラメーターキー => ['name': string, 'rules': string, 'messages': ['ルール名': string]]
     * ex) 'password' => [
     *  'name': 'パスワード',
     *  'rules': 'required|max:72' // 必須項目で最大72文字
     *  'messages': [
     *    'required' => '必須項目です。'
     *  ]
     * ]
     * @param array $requestParams 'リクエストパラメーターキー名' => mixed
     */
    public function __construct($validationDataList = [], $requestParams = [])
    {
        Validator::lang("ja");
        $this->validator = new Validator($requestParams);
        $this->defaultErrorMsgs = include(self::ERROR_MESSAGE_FILE_PATH) ?? [];
        // バリデーションルールの設定
        array_walk($validationDataList, fn ($data, $name) => $this->registerValidationData($name, $data));
    }

    public function validate(): array
    {
        // バリデーションの実施
        $errorMsgs = [];
        if (! $this->validator->validate()) {
            $errorMsgs = $this->validator->errors();
        }

        return $errorMsgs;
    }

    /**
     * バリデーションルールに関する設定
     *
     * @param string $validationName
     * @param array $validationData
     * @return void
     */
    private function registerValidationData(string $requestParamKey, array $validationData): void
    {
        // バリデーションルール設定の準備
        $rules = $this->generateRule($validationData['rules'] ?? []);

        // フォーム名を結びつける
        foreach($rules as $ruleName => $ruleField) {
            $firstRuleField = $ruleField;
            $othersRuleFields = [];
            if (in_array($ruleName, self::MULTIPLE_FIELD_RULES)) {
                $othersRuleFields = $ruleField;
                $firstRuleField = array_shift($othersRuleFields);
            }

            // バリデーションルールの設定
            $this->validator->rule($ruleName, $requestParamKey, $firstRuleField, ...$othersRuleFields);
            
            // 独自メッセージの設定
            $message = $validationData['messages'][$ruleName] ?? $this->defaultErrorMsgs[$ruleName] ?? null;
            $requestParamName = $validationData['name'] ?? null;
            $this->setLatestValidationMsgs($requestParamName, $message);
        }
    }

    /**
     * バリデーションルール毎の独自メッセージの設定
     *
     * @param string $requestParamName
     * @param string|null $message
     * @return void
     */
    private function setLatestValidationMsgs(?string
    $requestParamName, ?string $message): void
    {
        if (isset($message)) {
            $this->validator->message($message);
        }

        if (isset($requestParamName)) {
            $this->validator->label($requestParamName);
        }
    }

    /**
     * 'required|max:10'のような文字列から['ルール名'=>ルール]のような配列を返す
     *
     * @param string $ruleStr 0個以上のルールを'|'によって区切った文字列
     * @return array $ruleMap
     */
    private function generateRule(array $rules): array
    {
        $ruleMap = [];
        foreach($rules as $rule) {
            // バリデーションルール名と設定値の取得
            $ruleSet = explode(':', trim($rule), 2);
            $ruleName = trim($ruleSet[0]);
            $ruleField = $ruleSet[1] ?? null;

            // 複数の設定値を定義できるルールの場合の処理
            if (in_array($ruleName, [...self::MULTIPLE_FIELD_RULES, ...self::ARRAY_FIELD_RULES])) {
                $ruleField = explode(',', trim($ruleField));
                $ruleField = array_map(fn ($val) => trim($val), $ruleField);
            } else {
                // 設定値が一つである場合
                $ruleField = is_string($ruleField) ? trim($ruleField) : $ruleField;
            }

            $ruleMap[trim($ruleName)] = $ruleField;
        }

        return $ruleMap;
    }
}
