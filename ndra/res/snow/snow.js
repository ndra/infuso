// Падающие снежинки, версия 2.0
// © 2009 Селезнёв Д. Л., info@webfilin.ru
// Лицензия: GNU PL

function Snow() {
	this.body = document.documentElement.body || document.body;

	var width = this.width;
	if (!this.width) {
		width = window.innerWidth || document.body.clientWidth;
	}

	var height = !this.height ? this.windowHeight() : this.height;
	
	var isIE6 = (window.navigator.userAgent.search(/MSIE 6/) == -1) ? false : true;
	this.prefixId = 'dot-' + this.id + '-';

    this.dx = [];
    this.xp = [];
    this.yp = [];
    this.zp = [];
    this.am = [];
    this.stx = [];
    this.sty = [];
    this.stz = [];
    this.isStop = false;
    this.isHide = false;	
    this.maxDotWidth = Math.floor(Math.sqrt(this.dotWidth * this.dotWidth + this.dotHeight * this.dotHeight));
    
	for (var i = 0; i < this.snowN; i++) {
		this.dx[i] = 0;
		this.xp[i] = Math.random() * width;
		this.yp[i] = Math.random() * height;        
		this.am[i] = Math.random() * 20;
		this.stx[i] = 0.02 + Math.random() / 10;
		this.sty[i] = 0.7 + Math.random();
        this.stz[i] = 0;
        this.zp[i] = Math.floor(Math.random() * 4);           
		
		var div = document.createElement('div'),
            st = div.style;
            
		div.id = this.prefixId + i;
        this.defaultStyle(div);
		st.width = this.dotWidth + 'px';
		st.height = this.dotHeight + 'px';
		st.left = '0';
		st.zIndex = i;
        
        var left = '-' + Math.floor(Math.random() * this.typeSnowN) * this.dotWidth + 'px';
		if (isIE6) {
            var divInner = document.createElement('div'),
                stInner = divInner.style;
                
            this.defaultStyle(divInner);
            stInner.width = this.imageWidth + 'px';
            stInner.height = this.dotHeight + 'px';
            stInner.left = left;
			stInner.filter = 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'' + this.imageUrl + '\',sizingMethod=crop)';
			stInner.background = 'none';
            st.overflow = 'hidden';
            div.appendChild(divInner);
		}
		else {
			st.background = 'url(\'' + this.imageUrl + '\') no-repeat ' + left + ' 0px';
		}
		
		this.body.appendChild(div);
	}    
	
	this.id = this.id;
	Snow.prototype.id++;
}

Snow.prototype.width = 0; // Ширина контейнера, 0 - вся ширина окна браузера
Snow.prototype.height = 0;	// Высота контайнера, 0 - вся высота окна браузера
Snow.prototype.snowN = 20; // Количество снежинок
Snow.prototype.typeSnowN = 9; // Количество видов снежинок
Snow.prototype.dotWidth = 16; // Ширина снежинки
Snow.prototype.dotHeight = 18; // Высота снежинки
Snow.prototype.imageUrl = '/ndra/res/snow/snow.png'; // Картинка со снежинками
Snow.prototype.imageWidth = 144; // Ширина картинки со снежинками
Snow.prototype.sin = true; // Перемещаем снежики по синусоиде?
Snow.prototype.id = 0;
Snow.prototype.timer = 20;

Snow.prototype.defaultStyle = function(obj) {
    var st = obj.style;
    st.position = 'absolute';
    st.top = '0';
    st.fontSize = '0';
    st.lineHeight = '0';
    st.visibility = 'visible';  
}

Snow.prototype.windowHeight = function() {
	var height;
	if (window.innerHeight) {
		height = window.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) {
		height = document.documentElement.clientHeight;
	} else if (document.body) {
		height = document.body.clientHeight;
	}
	
	return height;
}

Snow.prototype.refresh = function() {
	var width = this.width;
	if (!this.width) {
		width = window.innerWidth || document.body.clientWidth;
	}

	var height = this.height;
	if (!this.height) {
		height = this.windowHeight() - this.dotHeight;
	}
	
	var el;
	for (var i = 0; i < this.snowN; i++) {
		this.yp[i] += this.sty[i];
		el = this.dot(i);
		if (this.yp[i] > height - this.dotHeight) {
			this.yp[i] = -height;
			this.xp[i] = Math.random() * width - this.am[i];
            this.stx[i] = 0.02 + Math.random() / 10;
            this.sty[i] = 0.7 + Math.random();         
		}
		
//		if (this.yp[i] > (height - this.dotHeight * 2) && this.yp[i] < height - this.dotHeight) {
		//}
		
		this.dx[i] += this.stx[i];

        this.stz[i] += this.zp[i];    
        if (this.stz[i] > 360) {
            this.stz[i] -= 360;
        }
        
		var x, y; 
		if (this.sin) {
            x = this.xp[i] + this.am[i] * Math.sin(this.dx[i]);
        }
		else {
            x = this.xp[i] + this.am[i];
        }
        
        x = Math.floor(x);

        var st = el.style;
        
		if (x >= width - this.maxDotWidth) {
			if (x >= width) {
				st.display = 'none';
                this.stz[i] = 0;
			}
			else {
				st.display = 'block';
				st.width = ((width - x > this.dotWidth) ? this.dotWidth : (width - x)) + 'px';
                this.stz[i] = 0;
			}
		}
		else {
			st.display = 'block';
			st.width = this.dotWidth + 'px';
		}
        
		y = Math.floor(this.yp[i]);
			
		if (y > height) {
            y = height;
        }
        
		st.left = x + 'px';
		st.top = y + 'px';
        st.WebkitTransform = st.MozTransform = 'rotate(' + this.stz[i] + 'deg)';
	}
	
	var cl = this;
	if (!this.isStop) {
        setTimeout(function () {cl.refresh();}, this.timer);
    }
}

Snow.prototype.start = function ()  {
	this.isStop = false;
	this.refresh();
}

Snow.prototype.stop = function ()  {
	this.isStop = true;
}

Snow.prototype.show = function () {
	if (this.isHide) {
		this.isHide = false;
		for (var i = 0; i < this.snowN; i++) {
			this.dot(i).style.display = 'block';
		}
	}
}

Snow.prototype.hide = function () {
	this.stop();
	if (!this.isHide) {
		this.isHide = true;
		for (var i = 0; i < this.snowN; i++) {
			this.dot(i).style.display = 'none';
		}
	}
}

Snow.prototype.dot = function(i) {
    return document.getElementById(this.prefixId + i);
};

(function () {
    function addEvent(elem, type, func) {
    	if (elem.addEventListener) {
            elem.addEventListener(type, func, false);
        }
		else if (elem.attachEvent) {
            elem.attachEvent('on' + type, func);
        }
    }
    
    addEvent(window, 'load', function() {
    	new Snow().start();
    });
})();
