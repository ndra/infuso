// @include inx.textfield

inx.css([".inx-textarea{font-size:12px;border:none;margin:0px;padding:0px;overflow-y:auto}"]);inx.textarea=inx.textfield.extend({autocreate:"<textarea class='inx-textarea' style='padding:5px;resize:none;' />",constructor:function(p){if(!p.value)p.value="";if(!p.width)p.width="auto";if(!p.height)p.height=150;if(!p.labelAlign)p.labelAlign="top";this.base(p);},cmd_syncLayout:function(){this.input.width(this.info("innerWidth")-10)
this.input.height(this.info("innerHeight")-10);},cmd_keydown:function(){return"stop";}});