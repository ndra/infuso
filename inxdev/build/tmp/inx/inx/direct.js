
inx.direct={check:function(){var h=(window.location.hash+"").substr(1).split("/");var hash=[];for(var i in h)if(h[i])hash.push(h[i])
hash=hash.join("/");if(inx.direct.last!=hash)
inx.direct.change(hash);},change:function(h){if(inx.direct.id){var a=h.split("/");inx(inx.direct.id).cmd(inx.direct.fn,a[0],a[1],a[2],a[3],a[4],a[5],a[6],a[7],a[8],a[9],a[10]);inx.direct.last=h;}},get:function(n){var h=(window.location.hash+"").substr(1);var a=h.split("/");return a[n];},set:function(){var a=[];for(var i=0;i<arguments.length;i++)
a.push(arguments[i])
a=a.join("/");window.location.hash=a;this.check();},bind:function(id,fn){inx.direct.id=inx(id).id();inx.direct.fn=fn;}}
setInterval(inx.direct.check,100);