<?php


namespace Zjwansui\EasyLaravel\Generator;


use Illuminate\Console\GeneratorCommand;

class CreateRequestCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:request';

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

    /**
     * 替换自定义内容
     * @param $stub
     * @return mixed
     */
    protected function replaceCustomizeSetting($stub)
    {
        $number = $this->ask('几个参数?');

        $rules = <<<EOF
\n
EOF;
        $members = <<<EOF
\n
EOF;
        $params = '';
        for ($i = 0; $i < $number; $i++) {
            $param = $this->ask('其中1个参数名字');
            $required = $this->choice('参数是不是必填', ['yes', 'no'], 'yes');
            $type = $this->anticipate('参数类型', ['string', 'integer', 'date', 'timestamp', 'bool'], 'string');
            $description = $this->ask('描述');
            $oneRule = [];
            $wenhao = '?';
            if ($required === 'yes') {
                $oneRule = ['"required"'];
                $params .= '"' . $param . '",';
                $wenhao = '';
            }
            array_push($oneRule, '"' . $type . '"');
            $ruleStr = implode(',', $oneRule);
            $rules .= <<<EOF
             /**
             * @OA\Property (
             *          property="$param",
             *          type="$type",
             *          description="$description",
             *     ),
             *
             */
            '$param' => [$ruleStr],
\n
EOF;


            $desType = $this->_getType($type);
            // 添加成员：
            $members .= <<<EOF
    public $wenhao$desType $$param;
\n
EOF;


        }
        $title = $this->ask('标题。比如：测试接口请求参数');
        return str_replace(['RULES', '$title', '$params', 'MEMBER'], [$rules, $title, $params, $members], $stub);
    }


    public function _getType($type): string
    {
        switch ($type) {
            case 'numeric':
            case 'integer':
                return 'int';
                break;
            default:
                return 'string';
        }
    }
}

