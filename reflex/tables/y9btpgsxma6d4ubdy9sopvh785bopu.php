<? return array (
  'name' => 'reflex_meta_item',
  'fields' => 
  array (
    0 => 
    array (
      'id' => 'ow5iqc1sdf9snyu6dpuzxk507y9zxw',
      'name' => 'id',
      'type' => 'jft7-kef8-ccd6-kg85-iueh',
      'editable' => '0',
      'indexEnabled' => '0',
    ),
    1 => 
    array (
      'id' => 'n8e2ty5b7cuz34u6xyuij4a0xka2df',
      'name' => 'hash',
      'type' => 'v324-89xr-24nk-0z30-r243',
      'editable' => '0',
      'label' => 'Хэш (class:ID)',
      'indexEnabled' => '1',
    ),
    2 => 
    array (
      'id' => '9b7mlrtp96qyvhdk5ztm1bnk5sjwai',
      'name' => 'lang',
      'type' => 'pg03-cv07-y16t-kli7-fe6x',
      'editable' => '2',
      'label' => 'Язык',
      'indexEnabled' => 1,
      'class' => 'lang',
    ),
    3 => 
    array (
      'id' => 'q4abnpgsoye6789sqkvbq4usqpgbjc',
      'name' => 'url',
      'type' => 'v324-89xr-24nk-0z30-r243',
      'editable' => '0',
      'label' => 'Адрес url',
      'indexEnabled' => '1',
    ),
    4 => 
    array (
      'id' => 'rjcahofab3pubqfl038u6qw9stklzn',
      'name' => 'controller',
      'type' => 'v324-89xr-24nk-0z30-r243',
      'editable' => '0',
      'label' => 'Контроллер',
      'indexEnabled' => '1',
    ),
    5 => 
    array (
      'id' => 'wls3y5ix456qmab345iok1sxmvi7fe',
      'name' => 'params',
      'type' => 'puhj-w9sn-c10t-85bt-8e67',
      'editable' => '0',
      'label' => 'Параметры контроллера',
      'indexEnabled' => '1',
    ),
    6 => 
    array (
      'id' => 'npgin8ehqkvidyuhxcehdwv63curtw',
      'name' => 'title',
      'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
      'editable' => '1',
      'label' => 'Заголовок браузера &lt;title&gt;',
      'indexEnabled' => '1',
    ),
    7 => 
    array (
      'id' => 'ahtf5st81rdkvhnyl63y5r3c5htp9z',
      'name' => 'pageTitle',
      'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
      'editable' => '1',
      'label' => 'Заголовок на странице &lt;h1&gt;',
      'indexEnabled' => '1',
    ),
    8 => 
    array (
      'id' => 'urd4g2dw9zo8e2okus7wasqfa6dc9r',
      'name' => 'keywords',
      'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
      'editable' => '1',
      'label' => 'Keywords',
      'indexEnabled' => '1',
    ),
    9 => 
    array (
      'id' => 'tm96nwezqcg27plrdwai7p1hx8ahn8',
      'name' => 'description',
      'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
      'editable' => '1',
      'label' => 'Description',
      'indexEnabled' => '1',
    ),
    10 => 
    array (
      'id' => 'rj4l0dy9zncv6tfg63ye6tyazjmeho',
      'name' => 'links',
      'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
      'editable' => '1',
      'label' => 'Ссылки (через запятую)',
      'indexEnabled' => '1',
    ),
    11 => 
    array (
      'id' => 'vst8lh7810tpu0n45bjpv0tw50xf56',
      'name' => 'search',
      'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
      'editable' => '2',
      'label' => 'Контент для поиска',
      'indexEnabled' => '1',
    ),
    12 => 
    array (
      'id' => 'w9htkg2qme038gsxwgsxp1rdkgzdme',
      'name' => 'searchWeight',
      'type' => 'gklv-0ijh-uh7g-7fhu-4jtg',
      'editable' => '1',
      'label' => 'Важность для поиска',
      'indexEnabled' => '1',
    ),
    13 => 
    array (
      'id' => 'v0qf9sxk56d89rn4a23ylsowvsokeh',
      'name' => 'noindex',
      'type' => 'fsxp-lhdw-ghof-1rnk-5bqp',
      'editable' => '1',
      'label' => 'Не индексировать',
      'indexEnabled' => '1',
      'help' => 'Добавляет мета-тэг NOINDEX в шапку страницы и запрещает ее к индексации в поисковых системах.',
    ),
    14 => 
    array (
      'id' => 'x4abqyur7m92jparqfubx4l67mvzow',
      'name' => 'beforeAction',
      'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
      'editable' => '1',
      'label' => 'Бизнес-логика (php)',
      'indexEnabled' => '0',
      'help' => 'PHP код, который выполнится до начала работы контроллера',
    ),
  ),
  'indexes' => 
  array (
    0 => 
    array (
      'id' => '3fu0tfe07pu23kgitmlb7fg07fvs7k',
      'name' => 'hash-lang',
      'fields' => 'hash,lang',
      'type' => 'index',
    ),
  ),
); ?>