<?php
declare(strict_types=1);

namespace Shinobi;

use Shinobi\Core\Kernel;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Dotenv\Dotenv;

class Shinobi
{
	private static Array $instances = array();
	
	private String $guid;
	
	private Array $systems = array();

	protected Application $cli;
        protected Dotenv $env;
	protected Kernel $kernel;


	protected Array $envConfig = array();
	protected Array $paths = array();
	protected Array $runtimes = array();

	private function __construct(private String $root)
	{
		$this->paths = ['root' => $root];
		$this->guid = self::class . '-' . md5($this->path('root'));
	}

	public static function boot(String $root, ?String $tag = null): self
	{
		$root = $root . (strcmp(substr($root, -1), '/') ? '/' : '') ?? SHINOBI_ROOT;
		$tag = md5(self::class . '-' . $root);

		if (!isset(self::$instances[$tag])) {
	                self::$instances[$tag] = new static(root: $root);
	                self::$instances[$tag]
						->setPaths()
						->bootEnv()
						->bootCli()
						->bootKernel();
		}

		return self::$instances[$tag];
	}

	public function env_cfg(?String $key = null): mixed
	{
		return (is_null($key) ? $this->envConfig : (isset($this->envConfig[$key]) ? $this->envConfig[$key] : FALSE));
	}

	public function cli(?Application $app = null): Application
	{
		if (!is_null($app) && ($app !== $this->cli)) {
			$this->cli = $app;
		}

		return $this->cli;
	}

	public function guid(?String $new_guid = null): String
	{
		if (!is_null($new_guid) && strcmp($this->guid, $new_guid)) {
			$this->guid = $new_guid;
		}

		return $this->guid;
	}

	public function kernel(?Kernel $kernel = null): Kernel
	{
		if (!is_null($kernel) && ($kernel !== $this->kernel)) {
			$this->kernel = $kernel;
		}
		
		return $this->kernel;
	}

	public function path(String $key = 'root'): String
	{
		return (isset($this->paths[$key]) ? $this->paths[$key] : SHINOBI_ROOT);
	}

	public function run(): int
	{
		return $this->cli->run();

	}

	public function server(?String $key = null, mixed $default = null): mixed
	{
		global $_SERVER;

		if (is_null($key) && is_null($default)) {
			return $_SERVER;
		}

		return $_SERVER[$key] ?? $default;
	}

	protected function setPaths(): self
	{
		$this->paths = array_merge($this->paths, [
			'bin' => $this->paths['root'] . 'bin/',
			'capabilities' => $this->paths['root'] . 'capabilities/',
			'config' => $this->paths['root'] . 'config/',
			'dna' => $this->paths['root'] . 'dna/',
			'etc' => $this->paths['root'] . 'etc/',
			'resources' => $this->paths['root'] . 'resources/',
			'src' => $this->paths['root'] . 'src/',
			'var' => $this->paths['root'] . 'var/',
			'vendor' => $this->paths['root'] . 'vendor/',
			'www' => $this->paths['root'] . 'www/'
		]);

		$this->paths = array_merge($this->paths, [
			'docker' => $this->paths['etc'] . 'docker/',
			'resources/assets' => $this->paths['resources'] . 'assets/',
			'resources/components' => $this->paths['resources'] . 'assets/components/',
			'resources/assets/images' => $this->paths['resources'] . 'assets/images/',
            'resources/assets/scripts' => $this->paths['resources'] . 'assets/scripts/',
            'resources/assets/stylesheets' => $this->paths['resources'] . 'assets/stylesheets/',			
			'content' => $this->paths['resources'] . 'content/',
			'layouts' => $this->paths['resources'] . 'layouts/',
			'pages' => $this->paths['resources'] . 'pages/',
			'assets' => $this->paths['www'] . 'assets/',
                        'components' => $this->paths['www'] . 'assets/components/',
			'images' => $this->paths['www'] . 'assets/images/',
			'scripts' => $this->paths['www'] . 'assets/scripts/',
			'stylesheets' => $this->paths['www'] . 'assets/stylesheets/',
			'cache' => $this->paths['var'] . 'cache/',
			'logs' => $this->paths['var'] . 'logs/',
			'run' => $this->paths['var'] . 'run/',
			'tmp' => $this->paths['var'] . 'tmp/'
		]);

		$this->paths = array_merge($this->paths, [
			'tenant.current' => 'shinobiforge'
		]);

		return $this;
	}

	protected function bootEnv(): self
	{
		global $_ENV;

		$this->env = Dotenv::createImmutable($this->path());
		$this->env->safeLoad();

		$this->envConfig = $_ENV;
		
		return $this;
	}


	protected function bootCli(): self
	{
		$this->cli = new Application('Shinobi', '0.1');

		return $this;
	}

	protected function bootKernel(): self
	{
		$this->kernel = Kernel::instance($this);

		return $this;
	}
}
