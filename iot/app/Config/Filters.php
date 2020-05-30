<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Class Filters
 *
 * Filter for API Authorisation
 *
 * @package App\Config
 */
class Filters extends BaseConfig
{
	// Makes reading things below nicer,
	// and simpler to change out script that's used.
	// \App\Filters\APIAuth is API Authorisation
	public $aliases = [
		'csrf'     => \CodeIgniter\Filters\CSRF::class,
		'toolbar'  => \CodeIgniter\Filters\DebugToolbar::class,
		'honeypot' => \CodeIgniter\Filters\Honeypot::class,
		'apiauth'  => \App\Filters\APIAuth::class
	];

	// Always applied before every request
	public $globals = [
		'before' => [
			//'honeypot'
			// 'csrf',
		],
		'after'  => [
			'toolbar',
			//'honeypot'
		],
	];

	// Works on all of a particular HTTP method
	// (GET, POST, etc) as BEFORE filters only
	//     like: 'post' => ['CSRF', 'throttle'],
	public $methods = [];

	// List filter aliases and any before/after uri patterns
	// that they should run on, like:
	//    'isLoggedIn' => ['before' => ['account/*', 'profiles/*']],
	// Protect measurements and all sub URLs.
	public $filters = [
		'apiauth' => ['before' => ['measurements','measurements/*']]
	];
}
