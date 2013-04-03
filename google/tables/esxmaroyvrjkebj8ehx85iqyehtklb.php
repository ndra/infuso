<? return array (
  'name' => 'google_translate_cache',
  'fields' => 
  array (
    0 => 
    array (
      'id' => 'd8es3fa07pasqk5rdwvzjwvitkl0dk',
      'name' => 'id',
      'type' => 'jft7-kef8-ccd6-kg85-iueh',
      'editable' => '0',
      'indexEnabled' => '0',
    ),
    1 => 
    array (
      'id' => 'ei7wustkvrnca0nk92xflbjylhx4e2',
      'name' => 'original',
      'type' => 'v324-89xr-24nk-0z30-r243',
      'editable' => '1',
      'label' => 'Оригинальный текст',
      'indexEnabled' => '1',
    ),
    2 => 
    array (
      'id' => 'nm10xcv234ah3yv6n8gb78li3k1rdc',
      'name' => 'translation',
      'type' => 'v324-89xr-24nk-0z30-r243',
      'editable' => '1',
      'label' => 'Текст перевода',
      'indexEnabled' => '1',
    ),
    3 => 
    array (
      'id' => '92ty10xfui3feio8eiqc92x850opa2',
      'name' => 'source',
      'type' => 'v324-89xr-24nk-0z30-r243',
      'editable' => '2',
      'label' => 'Язык оригинала',
      'indexEnabled' => '1',
    ),
    4 => 
    array (
      'id' => '96j4abo8l6dcaidyginf1rocernfe0',
      'name' => 'target',
      'type' => 'v324-89xr-24nk-0z30-r243',
      'editable' => '2',
      'label' => 'На какой язык переводить',
      'indexEnabled' => '1',
    ),
  ),
  'indexes' => 
  array (
    0 => 
    array (
      'id' => '7y9z389st4lbqku67cehdc1stye2om',
      'name' => 'all',
      'fields' => 'original,source(10),target(10)',
      'type' => 'index',
    ),
  ),
); ?>