<?php
if ($data['use_collective'] == 'yes') {
	$text = '
<div class="col-md-12">
    <div class="form-group">
        {!! Form::label(\'{Convention}\',trans(\'{lang}.{Convention}\'),[\'class\'=>\'control-label\']) !!}
        <div class="col-md-12">
            {!! Form::textarea(\'{Convention}\',old(\'{Convention}\'),[\'class\'=>\'form-control ckeditor\',\'placeholder\'=>trans(\'{lang}.{Convention}\')]) !!}
        </div>
    </div>
</div>
';
} else {
	$text = '
<div class="col-md-12">
    <div class="form-group">
        <label for="{Convention}" class="control-label">{{trans(\'{lang}.{Convention}\')}}</label>
        <div class="col-md-12">
            <textarea id="{Convention}" class="form-control ckeditor" placeholder="{{trans(\'{lang}.{Convention}\')}}"
            name="{Convention}" >{{old(\'{Convention}\')}}</textarea>
        </div>
    </div>
</div>
';
}
$text = str_replace('{Convention}', $data['col_name_convention'], $text);
$text = str_replace('{lang}', $data['lang_file'], $text);
echo $text;
?>