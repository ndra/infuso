<? class ndra_syntax extends mod_controller {

public static function indexTest() { return true; }
public static function index() {
    tmp::header();
    
?>
<pre><code>&lt;head&gt;
  &lt;title&gt;Title&lt;/title&gt;

  &lt;style&gt;
    body {
      width: 500px;
    }
  &lt;/style&gt;

  &lt;script type="application/javascript"&gt;
    function someFunction() {
      return true;
    }
  &lt;/script&gt;

&lt;body&gt;
  &lt;p class="something" id='12'&gt;Something&lt;/p&gt;
  &lt;p class=something&gt;Something&lt;/p&gt;
  &lt;!-- comment --&gt;
  &lt;p class&gt;Something&lt;/p&gt;
  &lt;p class="something" title="p"&gt;Something&lt;/p&gt;
&lt;/body&gt;
</code></pre>

<?
	self::add();
    tmp::footer();
}

// ----------------------------------------------------------------------------------------------

public static function add() {
	tmp::script("hljs.tabReplace = '    ';");
	tmp::script("hljs.initHighlightingOnLoad();");
	tmp::js("http://yandex.st/highlightjs/5.16/highlight.min.js");
	tmp::css("http://yandex.st/highlightjs/5.16/styles/default.min.css");
}

} ?>
