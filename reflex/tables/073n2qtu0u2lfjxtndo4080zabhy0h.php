<? return array (
  'name' => 'reflex_log',
  'fields' => 
  array (
    0 => 
    array (
      'id' => 'npw2brke0i9j0ci4jc6gtxv44u1zwj',
      'name' => 'id',
      'type' => 'jft7-kef8-ccd6-kg85-iueh',
      'editable' => '0',
      'label' => '',
      'default' => '',
      'help' => '',
    ),
    1 => 
    array (
      'id' => 'em7ug8cx4bbvce7ofsh2mzg50d7m7z',
      'name' => 'datetime',
      'type' => 'x8g2-xkgh-jc52-tpe2-jcgb',
      'editable' => '2',
      'label' => 'Дата',
      'default' => '',
      'help' => '',
    ),
    2 => 
    array (
      'id' => 'n0h0jd3ntly3o5nmf5qllh35lda0cc',
      'name' => 'user',
      'type' => 'pg03-cv07-y16t-kli7-fe6x',
      'editable' => '2',
      'label' => 'Пользователь',
      'default' => '',
      'help' => '',
      'class' => 'user',
      'collection' => '',
      'titleMethod' => '',
      'group' => '',
      'indexEnabled' => 0,
      'foreignKey' => '',
    ),
    3 => 
    array (
      'id' => 'yzs5ok7262za4w5ikksimxdony5m5m',
      'name' => 'index',
      'type' => 'v324-89xr-24nk-0z30-r243',
      'editable' => '2',
      'label' => 'Индекс',
      'default' => '',
      'help' => '',
    ),
    4 => 
    array (
      'id' => 'fnrhajryjjpgk6obq7ep240l26yf1p',
      'name' => 'text',
      'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
      'editable' => '2',
      'label' => 'Текст',
      'default' => '',
      'help' => '',
    ),
    5 => 
    array (
      'id' => '10om16tm92t4erdyabo8e6t4usow1h',
      'name' => 'comment',
      'type' => 'fsxp-lhdw-ghof-1rnk-5bqp',
      'editable' => '2',
      'label' => 'Комментарий?',
      'default' => '',
      'help' => '',
    ),
  ),
  'indexes' => 
  array (
    0 => 
    array (
      'id' => '5hqmgz7ca2qk9zomvhow96t41bd450',
      'name' => 'main',
      'fields' => 'datetime,user,index,comment',
      'type' => 'index',
    ),
  ),
  'fieldGroups' => 
  array (
    0 => 
    array (
      'name' => NULL,
      'title' => NULL,
    ),
  ),
); ?>