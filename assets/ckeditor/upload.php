<?php
if(isset($_FILES['upload']['name']))
{
	$file=$_FILES['upload']['name'];
	$filetmp=$_FILES['upload']['tmp_name'];

	move_uploaded_file($filetmp,'../uploads/'.$file);
	$function_number=$_GET['CKEditorFuncNum'];
	$url='../uploads/'.$file;
	$message='';
	echo "<script>window.parent.CKEDITOR.tools.callFunction('".$function_number."','".$url."','".$message."');</script>";     
}
?>