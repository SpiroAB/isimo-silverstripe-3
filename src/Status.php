<?php

	class SpiroAB_Isimo_Status extends \Controller
	{
		private static $url_handlers = [
			'' => 'status',
		];

		private static $allowed_actions = [
			'status',
		];

		public function status($request)
		{
			$correct_token = 'vG7ccq&RVyY7HPhTSaFTYQ4UAdmw5YyF';
			if(defined('ISIMO_TOKEN')) {
				$correct_token = ISIMO_TOKEN;
			}
			if($request->param('token') === $correct_token) {
				header('Content-Type: application/json');
				echo json_encode(
					[
						'debug' => $request->param('token'),
						'report' => [],
						'software' => 'Silverstripe',
						'version' => '0.0.0', // TODO
						'client' => 'isimo-silverstripe-3 v0.0.1',
					], JSON_PRETTY_PRINT), PHP_EOL;
				return;
			}

			header('HTTP/1.1 403 Wrong token');
			echo 'Invalid token', PHP_EOL, '<style>BODY {color: red;}</style>';
		}
	}
