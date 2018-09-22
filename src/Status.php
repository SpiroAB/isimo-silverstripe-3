<?php

	class SpiroAB_Isimo_Status extends \Controller
	{
		const ISIMO_VERSION = 'v0.0.1';

		private static $url_handlers = [
			'' => 'status',
		];

		private static $allowed_actions = [
			'status',
		];

		public function status($request)
		{
			$correct_token = 'vG7ccqsRVyY7HPhTSaFTYQ4UAdmw5YyF';
			if(defined('ISIMO_TOKEN')) {
				$correct_token = ISIMO_TOKEN;
			}
			if($request->param('token') === $correct_token) {
				$data = (object) [];
				$data->report = [];
				$data->time = time();
				$data->client = 'isimo-silverstripe-3 ' . self::ISIMO_VERSION;
				$data->software = 'Silverstripe';
				$data->version = '0.0.0'; // Placeholder, real version from composer.lock

				ob_start();
				phpinfo();
				$data->phpinfo = ob_get_clean();

				$data->mysql = DB::query('SHOW VARIABLES')->map();

				$data->gitsha = NULL;
				$git_dirs = [
					$_SERVER['DOCUMENT_ROOT'] . '/.git',
					dirname(__DIR__) . '/.git',
				];
				$git_dirs = array_unique($git_dirs);

				foreach($git_dirs as $git_dir)
				{
					if(!is_dir($git_dir))
					{
						continue;
					}
					if(!is_file($git_dir . '/HEAD'))
					{
						continue;
					}
					$git_head = file_get_contents($git_dir . '/HEAD');
					if(!$git_head)
					{
						continue;
					}
					$git_head = trim(substr($git_head, 4));
					if(!$git_head)
					{
						continue;
					}
					if(!is_file($git_dir . '/' . $git_head))
					{
						continue;
					}
					$git_ref = file_get_contents($git_dir . '/' . $git_head);
					if(!$git_ref)
					{
						continue;
					}
					$git_ref = trim($git_ref);
					if(!$git_ref)
					{
						continue;
					}
					$data->gitsha = $git_ref;
					break;
				}

				$composer_dirs = [
					$_SERVER['DOCUMENT_ROOT'],
					dirname(__DIR__),
				];
				$composer_dirs = array_unique($composer_dirs);

				foreach($composer_dirs as $composer_dir)
				{
					if(!file_exists($composer_dir . '/composer.lock'))
					{
						continue;
					}

					$data->composer_lock = file_get_contents($composer_dir . "/composer.lock");
					$composer_lock = json_decode($data->composer_lock);
					if($composer_lock && isset($composer_lock->packages) && is_array($composer_lock->packages))
					{
						foreach($composer_lock->packages as $composer_package)
						{
							if(!isset($composer_package->name)) continue;
							if(!isset($composer_package->version)) continue;
							if($composer_package->name !== 'silverstripe/framework') continue;
							$data->version = $composer_package->version;
						}
					}
					break;
				}

				header('Content-Type: application/json');
				echo json_encode(
					$data, JSON_PRETTY_PRINT), PHP_EOL;
				return;
			}

			header('HTTP/1.1 403 Wrong token');
			echo 'Invalid token', PHP_EOL, '<style>BODY {color: red;}</style>';
		}
	}
