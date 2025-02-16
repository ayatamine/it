<?php
namespace Phpanonymous\It\Controllers\Baboon;

use App\Http\Controllers\Controller;
use Phpanonymous\It\Controllers\Baboon\MasterBaboon as Baboon;

class BaboonDataTable extends Controller {
	public static $copyright = '[It V 1.5.0 | https://it.phpanonymous.com]';
	public static function dbclass($r) {
		$datatable = '<?php
namespace App\DataTables;
use {Model};
//use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Services\DataTable;
// Auto DataTable By Baboon Script
// Baboon Maker has been Created And Developed By ' . self::$copyright . '
// Copyright Reserved ' . self::$copyright . '
class {ClassName}DataTable extends DataTable
{
    	' . "\n";
		$datatable .= self::ajaxMethod($r) . "\n";
		$datatable .= self::queryMethod($r) . "\n";
		$datatable .= self::htmlMethod($r) . "\n";
		$datatable .= self::getcolsMethod($r) . "\n";
		$datatable .= self::filenameMethod($r) . "\n";

		$datatable .= '}';

		$nameclass = str_replace('Controller', '', $r->input('controller_name'));
		$datatable = str_replace('{ClassName}', $nameclass, $datatable);
		$datatable = str_replace('{lang}', $r->input('lang_file'), $datatable);

		$datatable = str_replace('{Model}',
			$r->input('model_namespace') . '\\' . $r->input('model_name'), $datatable);

		return $datatable;
	}

	public static function filenameMethod($r) {
		$filename = '
	    /**
	     * Get filename for export.
	     * Auto filename Method By Baboon Script
	     * @return string
	     */
	    protected function filename()
	    {
	        return \'{name}_\' . time();
	    }
    	';
		$name = str_replace('Controller', '', $r->input('controller_name'));
		$filename = str_replace('{name}', strtolower($name), $filename);
		return $filename;
	}

	public static function getcolsMethod($r) {
		$cols = '
    	/**
	     * Get columns.
	     * Auto getColumns Method By Baboon Script ' . self::$copyright . '
	     * @return array
	     */

	    protected function getColumns()
	    {
	        return [
	        [
                \'name\' => \'checkbox\',
                \'data\' => \'checkbox\',
                \'title\' => \'<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                  <input type="checkbox" class="select-all" onclick="select_all()" >
                  <span></span></label>\',
                \'orderable\'      => false,
                \'searchable\'     => false,
                \'exportable\'     => false,
                \'printable\'      => false,
                \'width\'          => \'10px\',
                \'aaSorting\'      => \'none\'
            ],[
                \'name\' => \'id\',
                \'data\' => \'id\',
                \'title\' => trans(\'{lang}.record_id\'),
                \'width\'          => \'10px\',
                \'aaSorting\'      => \'none\'
            ],' . "\n";
		$i2 = 0;
		foreach ($r->input('col_name_convention') as $conv) {
			$cols .= '				[' . "\n";
			if (preg_match('/(\d+)\+(\d+)|,/i', $conv)) {

				$pre_conv = explode('|', $conv);
				if (request()->has('forginkeyto' . $i2)) {
					$pluck_name = explode('pluck(', $pre_conv[1]);
					$pluck_name = !empty($pluck_name) && count($pluck_name) > 0 ? explode(',', $pluck_name[1]) : [];
					$final_pluckName = str_replace("'", "", $pluck_name[0]);
				} else {
					$final_pluckName = '';
				}
				//return dd(str_replace("'", "", $pluck_name[0]));
				if (!empty($final_pluckName) && request()->has('forginkeyto' . $i2)) {

					$cols .= '                 \'name\'=>\'' . $pre_conv[0] . '.' . $final_pluckName . '' . '\',' . "\n";
					$cols .= '                 \'data\'=>\'' . $pre_conv[0] . '.' . $final_pluckName . '\',' . "\n";
				} elseif (!request()->has('forginkeyto' . $i2)) {
					$cols .= '                 \'name\'=>\'' . self::convention_name(request('model_name')) . '.' . $pre_conv[0] . '\',' . "\n";
					$cols .= '                 \'data\'=>\'' . $pre_conv[0] . '\',' . "\n";
					// $cols .= '                 \'exportable\' => false,' . "\n";
					// $cols .= '                 \'printable\'  => false,' . "\n";
					// $cols .= '                 \'searchable\' => false,' . "\n";
					// $cols .= '                 \'orderable\'  => false,' . "\n";

				} else {
					$cols .= '                 \'name\'=>\'' . $pre_conv[0] . '\',' . "\n";
					$cols .= '                 \'data\'=>\'' . $pre_conv[0] . '\',' . "\n";
				}

				$cols .= '                 \'title\'=>trans(\'{lang}.' . $pre_conv[0] . '\'),' . "\n";
			} elseif (preg_match('/#/i', $conv)) {
				$pre_conv = explode('#', $conv);
				if (!preg_match('/' . $pre_conv[0] . '/', $cols)) {
					$cols .= '                 \'name\'=>\'' . $pre_conv[0] . '\',' . "\n";
					$cols .= '                 \'data\'=>\'' . $pre_conv[0] . '\',' . "\n";
					$cols .= '                 \'title\'=>trans(\'{lang}.' . $pre_conv[0] . '\'),' . "\n";
				}
			} else {

				$cols .= '                 \'name\'=>\'' . $conv . '\',' . "\n";
				$cols .= '                 \'data\'=>\'' . $conv . '\',' . "\n";
				$cols .= '                 \'title\'=>trans(\'{lang}.' . $conv . '\'),' . "\n";
			}
			$cols .= '		    ],' . "\n";

			$i2++;
		}

		$cols .= '            [
	                \'name\' => \'actions\',
	                \'data\' => \'actions\',
	                \'title\' => trans(\'admin.actions\'),
	                \'exportable\' => false,
	                \'printable\'  => false,
	                \'searchable\' => false,
	                \'orderable\'  => false,
	            ]
	        ];
	    }
    	';
		$cols = str_replace('{lang}', $r->input('lang_file'), $cols);
		return $cols;
	}

	public static function htmlMethod($r) {
		$stud = '';
		// for ($i = 0; $i < count(request('col_name')); $i++) {
		// 	$stud .= ($i + 1) . ',';
		// }

		$x = 0;
		$finaldropdown = '';
		$finalinputs = '';
		$finalInputsCount = '';
		foreach ($r->input('col_name_convention') as $conv) {
			// select or dropdown static (enum) In Rules Start
			if ($r->input('col_type')[$x] == 'select') {
				$dropdown = '';
				$ex_select = explode('|', $conv);
				if (!preg_match('/App/i', $ex_select[1])) {
					if (!empty($ex_select[1])) {
						$lang = $r->input('lang_file');
						$options = explode('/', $ex_select[1]);
						foreach ($options as $op) {
							$kv = explode(',', $op);
							$dropdown .= "'" . $kv[0] . "'=>trans('" . $lang . "." . $kv[0] . "')," . "\n";
						}
					}
				}

				$finaldropdown .= '
				". filterElement(\'' . ($x + 2) . '\', \'select\', [
				' . $dropdown . ']) . "' . "\n";

			} elseif ($r->input('col_type')[$x] != 'file') {

				$finalInputsCount .= "1," . ($x + 2) . ",";

			}
			// select or dropdown static (enum) In Rules End
			$x++;
		}
		if (!empty($finalInputsCount)) {
			$finalinputs .= ' ". filterElement(\'' . rtrim($finalInputsCount, ",") . '\', \'input\') . "' . "\n";
		}

		$html = '
    	 /**
	     * Optional method if you want to use html builder.
	     *' . self::$copyright . '
	     * @return \Yajra\Datatables\Html\Builder
	     */
    	public function html()
	    {
	      $html =  $this->builder()
            ->columns($this->getColumns())
            //->ajax(\'\')
            ->parameters([
               \'responsive\'   => true,
                \'dom\' => \'Blfrtip\',
                "lengthMenu" => [[10, 25, 50,100, -1], [10, 25, 50,100, trans(\'admin.all_records\')]],
                \'buttons\' => [
                    [\'extend\' => \'print\', \'className\' => \'btn dark btn-outline\', \'text\' => \'<i class="fa fa-print"></i> \'.trans(\'admin.print\')],
                    [\'extend\' => \'excel\', \'className\' => \'btn green btn-outline\', \'text\' => \'<i class="fa fa-file-excel-o"> </i> \'.trans(\'admin.export_excel\')],
                    [\'extend\' => \'pdf\', \'className\' => \'btn red btn-outline\', \'text\' => \'<i class="fa fa-file-pdf-o"> </i> \'.trans(\'admin.export_pdf\')],
                    [\'extend\' => \'csv\', \'className\' => \'btn purple btn-outline\', \'text\' => \'<i class="fa fa-file-excel-o"> </i> \'.trans(\'admin.export_csv\')],
                    [\'extend\' => \'reload\', \'className\' => \'btn blue btn-outline\', \'text\' => \'<i class="fa fa fa-refresh"></i> \'.trans(\'admin.reload\')],
                    [
                        \'text\' => \'<i class="fa fa-trash"></i> \'.trans(\'admin.delete\'),
                        \'className\'    => \'btn red btn-outline deleteBtn\',
                    ], [
                        \'text\' => \'<i class="fa fa-plus"></i> \'.trans(\'admin.add\'),
                        \'className\'    => \'btn btn-primary\',
                        \'action\'    => \'function(){
                        	window.location.href =  "\'.\URL::current().\'/create";
                        }\',
                    ],
                ],
                \'initComplete\' => "function () {


            ' . $finalinputs . '
            ' . $finaldropdown . '
            }",
                \'order\' => [[1, \'desc\']],

                    \'language\' => [
                       \'sProcessing\' => trans(\'admin.sProcessing\'),
							\'sLengthMenu\'        => trans(\'admin.sLengthMenu\'),
							\'sZeroRecords\'       => trans(\'admin.sZeroRecords\'),
							\'sEmptyTable\'        => trans(\'admin.sEmptyTable\'),
							\'sInfo\'              => trans(\'admin.sInfo\'),
							\'sInfoEmpty\'         => trans(\'admin.sInfoEmpty\'),
							\'sInfoFiltered\'      => trans(\'admin.sInfoFiltered\'),
							\'sInfoPostFix\'       => trans(\'admin.sInfoPostFix\'),
							\'sSearch\'            => trans(\'admin.sSearch\'),
							\'sUrl\'               => trans(\'admin.sUrl\'),
							\'sInfoThousands\'     => trans(\'admin.sInfoThousands\'),
							\'sLoadingRecords\'    => trans(\'admin.sLoadingRecords\'),
							\'oPaginate\'          => [
								\'sFirst\'            => trans(\'admin.sFirst\'),
								\'sLast\'             => trans(\'admin.sLast\'),
								\'sNext\'             => trans(\'admin.sNext\'),
								\'sPrevious\'         => trans(\'admin.sPrevious\'),
							],
							\'oAria\'            => [
								\'sSortAscending\'  => trans(\'admin.sSortAscending\'),
								\'sSortDescending\' => trans(\'admin.sSortDescending\'),
							],
                    ]
                ]);

        return $html;

	    }

    	';
		return $html;
	}

	public static function convention_name($string) {
		$conv = strtolower(ltrim(preg_replace('/(?<!\ )[A-Z]/', '_$0', $string), '_'));
		if (!in_array(substr($conv, -1), ['s'])) {
			if (substr($conv, -1) == 'y') {
				$conv = substr($conv, 0, -1) . 'ies';
			} else {
				$conv = $conv . 's';
			}
		}
		return $conv;
	}

	public static function queryMethod($r) {
		$query = '
     /**
     * Get the query object to be processed by dataTables.
     * Auto Ajax Method By Baboon Script ' . self::$copyright . '
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
	public function query()
    {
        return {Model}::query(){WithRelation}->select("' . self::convention_name(request('model_name')) . '.*");

    }
    	';

		$WithRelation = '';
		$i2 = 0;
		foreach ($r->input('col_name_convention') as $conv) {
			if (preg_match('/(\d+)\+(\d+)|,/i', $conv)) {
				$pre_conv = explode('|', $conv);
				if ($r->has('forginkeyto' . $i2)) {
					$WithRelation .= "'$pre_conv[0]',";
				}
			}
			$i2++;
		}

		if (!empty($WithRelation)) {
			$query = str_replace('{WithRelation}', '->with([' . $WithRelation . '])', $query);
		} else {
			$query = str_replace('{WithRelation}', '', $query);
		}

		$query = str_replace('{Model}', $r->input('model_name'), $query);
		return $query;
	}

	public static function ajaxMethod($r) {
		$ajax = '
     /**
     * Display a listing of the resource.
     * Auto Ajax Method By Baboon Script ' . self::$copyright . '
     * @return \Illuminate\Http\Response
     */

     /**
     * Display ajax response.
     * Auto Ajax Method By Baboon Script ' . self::$copyright . '
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataTable(DataTables $dataTables, $query)
    {
        return datatables($query)
            ->addColumn(\'actions\', \'{path}.{name}.buttons.actions\')' . "\n\r";
		$i = 0;
		$rowColumnsHtml = '';
		foreach ($r->input('col_name_convention') as $conv) {

			// Here Add New Column Image To View Image with Modal Start//
			if ($r->has('image' . $i)) {
				$ajax .= '            ->addColumn(\'' . $conv . '\', \'{path}.{name}.buttons.' . $conv . '\')' . "\n\r";
			} elseif ($r->input('col_type')[$i] == 'file' && !$r->has('image' . $i)) {
				$ajax .= '            ->addColumn(\'' . $conv . '\', \'<a href="{{ it()->url($' . $conv . ') }}" target="_blank"><i class="fa fa-download fa-2x"></i></a>\')' . "\n\r";
				$rowColumnsHtml .= '"' . $conv . '"' . ',';
			}
			// Here Add New Column Image To View Image with Modal End//

			// Here Add Column To Enum Values Start //
			if (preg_match('/(\d+)\+(\d+)|,/i', $conv)) {
				$pre_conv = explode('|', $conv);
				if (!request()->has('forginkeyto' . $i)) {
					$ajax .= '            ->addColumn(\'' . $pre_conv[0] . '\', \'{{ trans("admin.".$' . $pre_conv[0] . ') }}\')' . "\n\r";
				}
			}
			// Here Add Column To Enum Values Start //

			$i++;
		}

		$ajax .= '            ->addColumn(\'checkbox\', \'<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
			<input type="checkbox" class="selected_data" name="selected_data[]" value="{{ $id }}"> <span></span></label>\')
            ->rawColumns([\'checkbox\',\'actions\',{images_html}' . $rowColumnsHtml . ']);
    }
  ';

		// Create Image Start //
		$x = 0;
		$images_html = '';
		foreach ($r->input('col_name_convention') as $conv) {

			if ($r->has('image' . $x)) {
				$images_html .= "'" . $conv . "',";
				$blade_name = str_replace('controller', '', strtolower(request('controller_name')));
				$img = '@if(!empty($' . $conv . '))
                <a href="#" data-toggle="modal" data-target="#img{{ $id }}"><img src="{{ it()->url($' . $conv . ') }}" style="width:32px;height:32px" /></a>';
				$img .= '
<div id="img{{ $id }}" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
        <img src="{{ it()->url($' . $conv . ') }}" style="width:100%;height:500px" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans("admin.close") }}</button>
      </div>
    </div>
  </div>
</div>
@endif
                ';
				if (!empty($img)) {
					Baboon::check_path($r->input('admin_folder_path') . '\\' . $blade_name);
					Baboon::write($img, $conv . '.blade', $r->input('admin_folder_path') . '\\' . $blade_name . '\\buttons');
				}

			}
			$x++;
		}
		// Create Image End //

		$nameclass = str_replace('controller', '', strtolower($r->input('controller_name')));
		$ajax = str_replace('{images_html}', $images_html, $ajax);
		$ajax = str_replace('{name}', $nameclass, $ajax);
		$blade_path = str_replace('resources.views.', '', str_replace('/', '.', $r->input('admin_folder_path')));
		$ajax = str_replace('{path}', $blade_path, $ajax);

		return $ajax;
	}
}
