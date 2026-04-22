<?php

$FIELDS=array();
$FIELDS['kiji']=array(
'no'=>'INTEGER',
'resno'=>'INTEGER',
'name'=>'TEXT',
'title'=>'TEXT',
'fuki'=>'TEXT',
'email'=>'TEXT',
'url'=>'TEXT',
'comment'=>'TEXT',
'icon'=>'TEXT',
'color'=>'TEXT',
'img'=>'TEXT',
'date'=>'TEXT',
'pass'=>'TEXT',
'host'=>'TEXT',
'timer'=>'INTEGER',
'q'=>'TEXT',
'a1'=>'TEXT',
'a2'=>'TEXT',
'a3'=>'TEXT',
'a4'=>'TEXT',
'c1'=>'INTEGER',
'c2'=>'INTEGER',
'c3'=>'INTEGER',
'c4'=>'INTEGER',
'like'=>'INTEGER',
'bad'=>'INTEGER',
);

$kata=array(
'INTEGER',
'TEXT',
);

$ini_pkey=array(
'kiji'=>'no',
);

$ini_index=array(
'kiji'=>'CREATE INDEX s ON kiji(name,title,comment);',
);


?>
