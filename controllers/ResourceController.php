<?php

/**
 * ResourceController
 *
 * @author atomita
 */
class ResourceController extends AppController {

	function beforeFilter() {
		parent::beforeFilter();

		$action = $this->action;

		$http_method = strtoupper(env('REQUEST_METHOD'));

		$params		 = &$this->params['pass'];
		$paramchange = null;

		switch ($http_method . '/' . $this->action) {
			case 'GET/index':
			case 'GET/create':
				break;
			case 'POST/index':
				$this->action	 = 'store';
				break;
			default:
				$arg_cnt		 = count($params);
				if (1 <= $arg_cnt and $params[0] === 'edit') {
					$this->action	 = 'edit';
					$paramchange	 = 'delmerge';
				}
				else {
					switch ($http_method) {
						case 'GET':
							$this->action	 = 'show';
							$paramchange	 = 'unshift';
							break;
						case 'PUT':
						case 'PATCH':
							$this->action	 = 'update';
							$paramchange	 = 'unshift';
							break;
						case 'DELETE':
							$this->action	 = 'destroy';
							$paramchange	 = 'unshift';
							break;
						default:
							$this->action	 = null;
							$paramchange	 = 'merge';
							break;
					}
				}
				break;
		}

		if ((
				!in_array($this->action, array('index', 'create', 'store', 'edit', 'show', 'update', 'destroy')) or
				!method_exists($this, $this->action)
				) and
				method_exists($this, 'missingMethod')
		) {
			$this->action	 = 'missingMethod';
			$paramchange	 = 'merge';
		}

		switch ($paramchange) {
			case 'delmerge':
				unset($params[0]);
			case 'merge':
				$params = array_merge(array($action), $params);
				break;

			case 'unshift':
				array_unshift($params, $action);
				break;
		}
	}

	function missingMethod($parameters) {
		$this->cakeError('error404');
	}

}
