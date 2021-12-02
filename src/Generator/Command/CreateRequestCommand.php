<?php


namespace Zjwansui\EasyLaravel\Generator\Command;


use Illuminate\Console\GeneratorCommand;

class CreateRequestCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:api-request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Create a new request';

    public function handle()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        if ((!$this->hasOption('force') || !$this->option('force')) && $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type . ' already exists!');

            return false;
        }
        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));
        $this->info($this->type . ' created successfully.');
    }


    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            ['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel'],
            [$this->getNamespace($name), $this->rootNamespace(), config('auth.providers.users.model')],
            $stub
        );

        return $this;
    }

    protected function replaceClass($stub, $name)
    {
        $stub = $this->replaceCustomizeSetting($stub); //替换自定义内容
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);
        return str_replace('DummyClass', $class, $stub);
    }


    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers\Request';
    }

    protected function getStub()
    {
        return __DIR__ . '/Stubs/Request.stub';
    }
//
//    /**
//     * 替换自定义内容
//     * @param $stub
//     * @return mixed
//     */
//    protected function replaceCustomizeSetting($stub)
//    {
//        $number = $this->ask('几个参数?');
//
//        $rules = <<<EOF
//\n
//EOF;
//        $members = <<<EOF
//\n
//EOF;
//        $params = '';
//        for ($i = 0; $i < $number; $i++) {
//            $param = $this->ask('其中1个参数名字');
//            $required = $this->choice('参数是不是必填', ['yes', 'no'], 'yes');
//            $type = $this->anticipate('参数类型', ['string', 'integer', 'date', 'timestamp', 'bool'], 'string');
//            $description = $this->ask('描述');
//            $oneRule = [];
//            $wenhao = '?';
//            if ($required === 'yes') {
//                $oneRule = ['"required"'];
//                $params .= '"' . $param . '",';
//                $wenhao = '';
//            }
//            array_push($oneRule, '"' . $type . '"');
//            $ruleStr = implode(',', $oneRule);
//            $rules .= <<<EOF
//             /**
//             * @OA\Property (
//             *          property="$param",
//             *          type="$type",
//             *          description="$description",
//             *     ),
//             *
//             */
//            '$param' => [$ruleStr],
//\n
//EOF;
//
//
//            $desType = $this->_getType($type);
//            // 添加成员：
//            $members .= <<<EOF
//    public $wenhao$desType $$param;
//\n
//EOF;
//
//
//        }
//        $title = $this->ask('标题。比如：测试接口请求参数');
//        return str_replace(['RULES', '$title', '$params', 'MEMBER'], [$rules, $title, $params, $members], $stub);
//    }
//
//
//    public function _getType($type): string
//    {
//        switch ($type) {
//            case 'numeric':
//            case 'integer':
//                return 'int';
//                break;
//            default:
//                return 'string';
//        }
//    }
//}

    /**
     * 替换自定义内容
     * @param $stub
     * @return mixed
     */
    protected function replaceCustomizeSetting($stub)
    {
        $title = $this->ask('标题。比如：测试接口请求参数');

        $members = <<<EOF
\n
EOF;

        $classes = <<<EOF
\n
EOF;
        $this->_getParams($title);


        $params = '';

        // 新建class
        foreach (self::$params as $key => $param) {
            if ($key !== $title) {
                $propertyContent = <<<EOF
EOF;
                foreach ($param as $value) {
                    $type = $value['type'];
                    $wh = $value['wh'];
                    $name = $value['name'];
                    $propertyContent .= <<<EOF
 * @property $type$wh $name\n
EOF;
                }
                $propertyContent = rtrim($propertyContent);
                $classes .= <<<EOF

/**
$propertyContent
 *
 */
class $key extends BaseResponse
{
}
EOF;
            }


        }

        $rules = $this->_getSwagger($title, $title);

        foreach (self::$params as $key => $param) {
            if ($key === $title) {
                foreach ($param as $value) {
                    $wenhao = '?';
                    $name = $value['name'];
                    if ($value['wh'] === ' ') {
                        $wenhao = '';
                        $params .= '"' . $name . '",';
                    }
                    $desType = $this->_getType($value['type']);
                    // 添加成员：
                    $members .= <<<EOF
    public $wenhao$desType $$name;
\n
EOF;
                }

            }
        }


        return str_replace(['RULES', '$title', '$params', 'MEMBER', 'CLASSES'], [$rules, $title, $params, $members, $classes ?? ""], $stub);
    }


    private function _getSwagger($classItemName, $title = ''): string
    {
        $rule = <<<EOF
\n
EOF;
        $data = self::$params[$classItemName];
        foreach ($data as $datum) {
            $paramName = $datum['name'];
            $paramType = $datum['typeName'];
            $paramDes = $datum['des'];
            $paramWh = $datum['wh'];
            $oneRule = [];
            if ($paramWh === ' ') {
                $oneRule = ['"required"'];
            }
            if ($paramType === 'float') {
                $oneRule[] = '"numeric"';
            } elseif ($paramType !== 'object') {
                $oneRule[] = '"' . $paramType . '"';
            }
            $ruleStr = implode(',', $oneRule);

            if (in_array($datum['typeName'], ['object', 'array'])) {
                if (array_key_exists('latType', $datum)) {
                    $latType = $datum['latType'];
                    $swaggers = <<<EOF
             * @OA\Property (
             *          property="$paramName",
             *          type="$paramType",
             *          description="$paramDes",
             *            @OA\Items(
             *             type="$latType",
             *           ),
             *     ),
             *
EOF;
                    if ($classItemName === $title) {
                        $rule .= <<<EOF
            /**
$swaggers
             */
            '$paramName' => [$ruleStr],

EOF;
                    } else {
                        $rule = $swaggers;
                    }
                } elseif($paramType ==='array') {
                    $itemSwagger = $this->_getSwagger($datum['type']);
                    $itemSwagger = rtrim($itemSwagger);
                    $swaggers = <<<EOF
              *        @OA\Property (
              *         property="$paramName",
              *         type="$paramType",
              *         description="$paramDes",
              *             @OA\Items(
$itemSwagger
              *              ),
              *         ),
EOF;
                    if ($classItemName === $title) {
                        $rule .= <<<EOF
             /**
$swaggers
              */
            '$paramName' => [$ruleStr],

EOF;
                    } else {
                        $rule = $swaggers;
                    }
                }else{
                    $itemSwagger = $this->_getSwagger($datum['type']);
                    $itemSwagger = rtrim($itemSwagger);
                    $swaggers = <<<EOF
              *        @OA\Property (
              *         property="$paramName",
              *         type="$paramType",
              *         description="$paramDes",
$itemSwagger
              *         ),
EOF;
                    if ($classItemName === $title) {
                        $rule .= <<<EOF
             /**
$swaggers
              */
            '$paramName' => [$ruleStr],

EOF;
                    } else {
                        $rule = $swaggers;
                    }
                }

            } else {

                $swaggers = <<<EOF
              * @OA\Property (
              *          property="$paramName",
              *          type="$paramType",
              *          description="$paramDes",
              *     ),
              *
EOF;
                if ($classItemName === $title) {
                    $rule .= <<<EOF
             /**
$swaggers
             */
            '$paramName' => [$ruleStr],

EOF;
                } else {
                    $rule = $swaggers;
                }

            }
        }
        return $rule;
    }


    private function _getParams($paramName): void
    {
        $number = $this->ask($paramName . '里面几个参数？');
        if ($number === 0) {
            return;
        }
        for ($i = 0; $i < $number; $i++) {
            $param = $this->ask($paramName . '中1个参数名字');
            $type = $this->anticipate('参数类型', ['string', 'integer', 'object', 'array', 'date', 'timestamp', 'bool'], 'string');
            $description = $this->ask('描述');
            $required = $this->choice('参数是不是必填', ['no', 'yes'], 'no');
            if (in_array($type, ['object', 'array'])) {
                if ($type === 'array') {
                    $arrayLatitude = $this->choice('数组是否一维', ['no', 'yes'], 'no');
                    if ($arrayLatitude === 'yes') {
                        $latType = $this->choice("$param 数组类型", ['string', 'integer'], 'string');
                        self::$params[$paramName][] = [
                            'wh' => $required === 'yes' ? ' ' : '|null',
                            'type' => $type,
                            'typeName' => $type,
                            'name' => $param,
                            'des' => $description,
                            'latType' => $latType
                        ];
                        continue;
                    }
                }
                $name = ucfirst($param);
                self::$params[$paramName][] = [
                    'wh' => $required === 'yes' ? ' ' : '|null',
                    'type' => $name,
                    'typeName' => $type,
                    'name' => $param,
                    'des' => $description,
                ];
                $this->_getParams($name);
            } else {
                self::$params[$paramName][] = [
                    'wh' => $required === 'yes' ? ' ' : '|null',
                    'type' => $type,
                    'typeName' => $type,
                    'name' => $param,
                    'des' => $description,
                ];
            }
        }
    }

    private
    static array $params;


    public function _getType($type): string
    {
        switch ($type) {
            case 'numeric':
            case 'integer':
                return 'int';
            default:
                return $type;
        }
    }

}