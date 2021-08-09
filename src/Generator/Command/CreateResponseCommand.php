<?php

namespace Zjwansui\EasyLaravel\Generator\Command;

use Illuminate\Console\GeneratorCommand;

/**
 * 含baseResponse创建
 * Class ResponseCommand
 * @package App\Console\Commands
 */
class CreateResponseCommand extends GeneratorCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:response';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Create a new response';

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
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);
        $stub = $this->replaceCustomizeSetting($stub, $class); //替换自定义内容
        return str_replace('DummyClass', $class, $stub);
    }


    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Http\Controllers\Response';
    }

    protected function getStub(): string
    {
        return __DIR__ . '/Stubs/NoResponse.stub';
    }


    /**
     * 替换自定义内容
     * @param $stub
     * @param $className
     * @return array|string|string[]
     */
    protected function replaceCustomizeSetting($stub, $className)
    {
        $title = $this->ask('标题。比如：测试接口返回参数');

        $dataOrList = $this->choice('返回是对象还是列表', ['对象', '列表'], '对象');
        if ($dataOrList === '对象') {
            $classItemName = substr($className, 0, -8) . 'Data';
        } else {
            $classItemName = substr($className, 0, -8) . 'Item';
        }
        $dataMust = $this->choice("这个 $classItemName$dataOrList 是否必反", ['yes', 'no'], 'yes');
        // object
        $classes = <<<EOF
\n
EOF;
        // data
        $members = <<<EOF
\n
EOF;
        // 组装data
        $dataMustWen = $dataMust === 'no' ? '?' : '';

        // 组装data里面的数据
        $this->_getParams($classItemName);
        foreach (self::$params as $key => $param) {
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

        $swaggers = $this->_getSwagger($classItemName);
        // 组装data和swagger

        if ($dataOrList === '对象') {
            $members .= <<<EOF
    /**
     * @OA\Property (
     *     description="data$dataOrList",
     *     type="object",
$swaggers
     * )
     */
   public $dataMustWen$classItemName \$data;
\n
EOF;
        } else {
            $members .= <<<EOF
    /**
     * @OA\Property (
     *     description="data$dataOrList",
     *     type="array",
     *     @OA\Items(
$swaggers
     *   ),
     * )
     */
   public {$dataMustWen}array \$data;
\n
EOF;
        }
        return str_replace(['CLASSES', '$title', 'MEMBER', 'className', 'PROPERTIES'], [$classes, $title, $members, $classItemName, $properties ?? ''], $stub);
    }

    private static array $params = [];

    private function _getSwagger($classItemName): string
    {
        $swaggers = <<<EOF
EOF;
        $data = self::$params[$classItemName];
        foreach ($data as $datum) {
            $paramName = $datum['name'];
            $paramType = $datum['typeName'];
            $paramDes = $datum['des'];

            if (in_array($datum['typeName'], ['object', 'array'])) {
                $itemSwagger = $this->_getSwagger($datum['type']);
                $itemSwagger = rtrim($itemSwagger);
                $swaggers .= <<<EOF
     *        @OA\Property (
     *         property="$paramName",
     *         type="$paramType",
     *         description="$paramDes",
$itemSwagger
     *         ),
EOF;
            } else {
                $swaggers .= <<<EOF
     *        @OA\Property (
     *         property="$paramName",
     *         type="$paramType",
     *         description="$paramDes",
     *         ),\n
EOF;
            }
        }
        return $swaggers;
    }


    private function _getParams($paramName): void
    {
        $number = $this->ask($paramName . '里面几个参数？');
        for ($i = 0; $i < $number; $i++) {
            $param = $this->ask($paramName . '中1个参数名字');
            $type = $this->anticipate('参数类型', ['string', 'integer', 'object', 'array', 'date', 'timestamp', 'bool'], 'string');
            $description = $this->ask('描述');
            $required = $this->choice('参数是不是必反', ['yes', 'no'], 'yes');
            if ($type === 'object' || $type === 'array') {
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

}
