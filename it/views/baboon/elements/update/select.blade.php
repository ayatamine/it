<?php
$ex = explode('|', $data['col_name_convention']);
if ($data['use_collective'] == 'yes') {
	$text = '
<div class="col-md-6">
	<div class="form-group">
		{!! Form::label(\'{Convention}\',trans(\'{lang}.{Convention}\'),[\'class\'=>\'control-label\']) !!}
		<div class="col-md-12">' . "\r\n";
	if ($data['forginkeyto'] == 'yes' || preg_match('/App/i', $ex[1])) {
		$text .= '{!! Form::select(\'{Convention}\',' . $ex[1];
	} else {
		$text .= '{!! Form::select(\'{Convention}\',[';
		if (!empty($ex[1])) {
			$options = explode('/', $ex[1]);
			foreach ($options as $op) {
				$kv = explode(',', $op);
				$text .= "'" . $kv[0] . "'=>trans('{lang}." . $kv[0] . "'),";
			}
			$text .= ']';
		}
	}
	$text .= ', {Convention2} ,[\'class\'=>\'form-control\',\'placeholder\'=>trans(\'{lang}.{Convention}\')]) !!}
		</div>
	</div>
</div>
';
} else {
	$text = '
<div class="col-md-6">
	<div class="form-group">
		<label for="{Convention}" class="control-label">{{trans(\'{lang}.{Convention}\')}}</label>
		<div class="col-md-12">
			<select id="{Convention}" name="{Convention}" class="form-control" placeholder="{{trans(\'{lang}.{Convention}\')}}" >' . "\n\r";
	if ($data['forginkeyto'] == 'yes' || preg_match('/App/', $ex[1])) {
		$text .= '    @foreach(' . $ex[1] . ' as ${Convention})' . "\r\n";
		$pluck_ex = str_replace('(', '', explode('pluck', $ex[1])[1]);
		$pluck_ex = str_replace(')', '', $pluck_ex);
		$pluck_ex = str_replace("'", "", $pluck_ex);
		$pluck_ex = explode(',', $pluck_ex);
		$text .= '      <option value="{{ ${Convention}->' . $pluck_ex[1] . ' }}" {{ {Convention2} == ${Convention}->' . $pluck_ex[1] . '?"selected":"" }} >{{ ${Convention}->' . $pluck_ex[0] . ' }}</option>' . "\r\n";
		$text .= '    @endforeach' . "\r\n";
	} else {
		$options = explode('/', $ex[1]);
		foreach ($options as $op) {
			$kv = explode(',', $op);
			$text .= "\n<option value=\"" . $kv[0] . "\" {{  {Convention2} == '" . $kv[0] . "'?'selected':'' }}  >{{trans('{lang}." . $kv[1] . "')}}</option>";
		}
		$text .= "\n";
	}
	$text .= '</select>
		</div>
	</div>
</div>
';
}
$text = str_replace('{Convention}', $ex[0], $text);
$text = str_replace('{Convention2}', $data['selectvar'] . $ex[0], $text);
$text = str_replace('{lang}', $data['lang_file'], $text);
echo $text;
?>