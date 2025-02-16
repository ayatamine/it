<?php
namespace Phpanonymous\It\Controllers\Baboon;
use App\Http\Controllers\Controller;
use Phpanonymous\It\Controllers\Baboon\BaboonRulesAndAttributes as RulesAndAttributes;

class BaboonValidations extends Controller {

	public static $copyright = '[It V 1.5.0 | https://it.phpanonymous.com]';
	public static function validationClass($r) {
		$validation = '<?php
namespace App\Http\Controllers\Validations;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ' . $r->input('controller_name') . 'Request extends FormRequest {

	/**
	 * Baboon Script By ' . self::$copyright . '
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;
	}

	/**
	 * Baboon Script By ' . self::$copyright . '
	 * Get the validation rules that apply to the request.
	 *
	 * @return array (onCreate,onUpdate,rules) methods
	 */
	protected function onCreate() {
		return [' . "\n";
		$validation .= '' . RulesAndAttributes::rules($r);
		$validation .= '		];
	}

	protected function onUpdate() {
		return [' . "\n";
		$validation .= '' . RulesAndAttributes::rules($r);
		$validation .= '		];
	}

	public function rules() {
		return request()->isMethod(\'put\') || request()->isMethod(\'patch\') ?
		$this->onUpdate() : $this->onCreate();
	}


	/**
	 * Baboon Script By ' . self::$copyright . '
	 * Get the validation attributes that apply to the request.
	 *
	 * @return array
	 */
	public function attributes() {
		return [' . "\n";
		$validation .= '' . RulesAndAttributes::SetAttributeNames($r);
		$validation .= '		];
	}

	/**
	 * Baboon Script By ' . self::$copyright . '
	 * response redirect if fails or failed request
	 *
	 * @return redirect
	 */
	public function response(array $errors) {
		return $this->ajax() || $this->wantsJson() ?
		response([
			\'status\' => false,
			\'StatusCode\' => 422,
			\'StatusType\' => \'Unprocessable\',
			\'errors\' => $errors,
		], 422) :
		back()->withErrors($errors)->withInput(); // Redirect back
	}



}';

		return $validation;
	}

}
