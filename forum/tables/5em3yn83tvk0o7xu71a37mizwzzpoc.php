<? return array (
  'name' => 'forum_post',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'id',
      'type' => 'jft7-kef8-ccd6-kg85-iueh',
      'editable' => '1',
      'id' => 'a0nniq8fcny0adt72ppin4bi6hi7xq',
    ),
    1 => 
    array (
      'name' => 'title',
      'type' => 'v324-89xr-24nk-0z30-r243',
      'editable' => '1',
      'id' => '0kskqcrah852ar3csb1f9omgz81a1o',
      'label' => 'Заголовок',
      'indexEnabled' => '0',
    ),
    2 => 
    array (
      'name' => 'topic',
      'type' => 'pg03-cv07-y16t-kli7-fe6x',
      'editable' => '1',
      'id' => 'trk6c681dxrxrxwnq7yydg3eqh3crc',
      'label' => 'Тема',
      'indexEnabled' => 0,
      'class' => 'forum_topic',
      'group' => '',
      'default' => '',
      'help' => '',
      'foreignKey' => '',
      'collection' => '',
      'titleMethod' => '',
    ),
    3 => 
    array (
      'name' => 'date',
      'type' => 'x8g2-xkgh-jc52-tpe2-jcgb',
      'editable' => '2',
      'id' => 'e88rhl6ww33zqkdd33f777uffkth5p',
      'label' => 'Дата и время',
      'default' => 'now()',
      'indexEnabled' => '0',
    ),
    4 => 
    array (
      'editable' => 2,
      'id' => 'yc7nn0jedatkdr7b9vqn9ynalmjxk4',
      'name' => 'editDate',
      'type' => 'x8g2-xkgh-jc52-tpe2-jcgb',
      'label' => 'Дата и  время последнего редактирования',
      'group' => '',
      'default' => 'now()',
      'indexEnabled' => 0,
      'help' => '',
    ),
    5 => 
    array (
      'editable' => 2,
      'id' => 'zcproc5ss29sra33x7rbg2jaansqti',
      'name' => 'edited',
      'type' => 'fsxp-lhdw-ghof-1rnk-5bqp',
      'label' => 'Редактировался',
      'group' => '',
      'default' => '',
      'indexEnabled' => 0,
      'help' => '',
    ),
    6 => 
    array (
      'name' => 'message',
      'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
      'editable' => '1',
      'id' => '6vdmgrhwsugpsrd84594eo03ur13fv',
      'label' => 'Сообщение',
      'indexEnabled' => '0',
    ),
    7 => 
    array (
      'name' => 'userID',
      'type' => 'pg03-cv07-y16t-kli7-fe6x',
      'editable' => '1',
      'id' => 'eej3urkmg4zv22xy5x2fbkqgjdj645',
      'indexEnabled' => 0,
      'class' => 'user',
      'label' => '',
      'group' => '',
      'default' => '',
      'help' => '',
      'foreignKey' => '',
      'collection' => '',
      'titleMethod' => '',
    ),
  ),
  'indexes' => 
  array (
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