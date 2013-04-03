<? return array (
  'name' => 'pay_invoice',
  'fields' => 
  array (
    0 => 
    array (
      'id' => 'cblqv7dajkksq0b2tha6aoqcygt7x4',
      'name' => 'id',
      'type' => 'jft7-kef8-ccd6-kg85-iueh',
      'editable' => '2',
      'label' => 'Номер счета',
    ),
    1 => 
    array (
      'id' => 'pvl0py53eij3je3u9fmhydgoeqvrxw',
      'name' => 'date',
      'type' => 'x8g2-xkgh-jc52-tpe2-jcgb',
      'editable' => '2',
      'label' => 'Дата',
    ),
    2 => 
    array (
      'name' => 'status',
      'type' => 'fahq-we67-klh3-456t-plbo',
      'editable' => 2,
      'id' => 'k9sf45cq6csz649pgebxoymot31n3o',
      'label' => 'Статус заказа',
      'indexEnabled' => 0,
      'method' => 'pay_invoice::statusAll',
    ),
    3 => 
    array (
      'name' => 'driver',
      'type' => 'v324-89xr-24nk-0z30-r243',
      'editable' => 2,
      'id' => '85sxmehqc1h7c92xca0qcv0q896xf9',
      'label' => 'Оплачено через',
      'indexEnabled' => 0,
    ),
    4 => 
    array (
      'name' => 'driverUseonly',
      'type' => 'v324-89xr-24nk-0z30-r243',
      'editable' => 2,
      'id' => '1id8q6pib2bfl554k9ta02q94ofon5',
      'indexEnabled' => 0,
      'label' => 'Счет может быть оплачен только данным драйвером',
    ),
    5 => 
    array (
      'name' => 'timeCheck',
      'type' => 'x8g2-xkgh-jc52-tpe2-jcgb',
      'editable' => 2,
      'id' => 'df9z0bywrodpr3zc0xjo8lrokf8bir',
      'label' => 'Время последней проверки статуса оплаты',
      'indexEnabled' => 0,
    ),
    6 => 
    array (
      'id' => '8l6fgig9hk2y4ieim80n0r7a31ayl3',
      'name' => 'sum',
      'type' => 'nmu2-78a6-tcl6-owus-t4vb',
      'editable' => '2',
      'label' => 'Сумма счета',
    ),
    7 => 
    array (
      'id' => '9tvbztiabw6jb7slv2w4m7gjsx1mcq',
      'name' => 'currency',
      'type' => 'rtwaho8esx49ijy9rtc1',
      'editable' => '2',
      'label' => 'Валюта счета',
      'indexEnabled' => 0,
    ),
    8 => 
    array (
      'id' => 's68uuym7ewrm35zl2crndl6uhucaac',
      'name' => 'date_incoming',
      'type' => 'x8g2-xkgh-jc52-tpe2-jcgb',
      'editable' => '2',
      'label' => 'Дата оплаты',
    ),
    9 => 
    array (
      'id' => 'pq9j1rxbcj8bf1ulmigebqlrh8xc5k',
      'name' => 'for_order',
      'type' => 'v324-89xr-24nk-0z30-r243',
      'editable' => '2',
      'label' => 'Номер заказа',
      'indexEnabled' => 0,
    ),
    10 => 
    array (
      'id' => 'uaalcclywru0rr7qypzx38utbpps07',
      'name' => 'title',
      'type' => 'v324-89xr-24nk-0z30-r243',
      'editable' => '2',
      'label' => 'Назначение платежа',
    ),
    11 => 
    array (
      'name' => 'userId',
      'type' => 'pg03-cv07-y16t-kli7-fe6x',
      'editable' => 2,
      'id' => 'm0v3edesktzwil034c39jbylurscgc',
      'label' => 'Пользователь',
      'indexEnabled' => 1,
      'class' => 'user',
    ),
    12 => 
    array (
      'name' => 'redirect',
      'type' => 'v324-89xr-24nk-0z30-r243',
      'editable' => 2,
      'id' => 'cgsnfu0qm1278us74e0qmeh3fghjfl',
      'label' => 'URL возврата после оплаты',
      'indexEnabled' => 0,
    ),
    13 => 
    array (
      'name' => 'errorText',
      'type' => 'v324-89xr-24nk-0z30-r243',
      'editable' => '1',
      'id' => '6xpl6j492tfghtp507k9rx8lzd4g2q',
      'indexEnabled' => 0,
      'label' => 'Текст ошибки',
    ),
  ),
  'indexes' => 
  array (
  ),
); ?>