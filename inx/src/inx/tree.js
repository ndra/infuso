// @include inx.panel,inx.textfield

inx.css(
    ".inx-tree-node {cursor:pointer;white-space:nowrap;}",
    ".inx-tree-node .selected{background:#eaeaea}",
    ".inx-tree-node .selected{background:#eaeaea}",
    ".inx-tree-arrow {width:20px,height:16px;position:absolute;margin-top:3px;}",
    ".inx-tree-icon {width:16px;height:16px;}",
    ".inx-focused .inx-tree-node .selected{background:#d9e8fb}"
);

inx.tree = inx.panel.extend({

    constructor:function(p) {

        if(!p.data) {
            p.data = [];
        }

        if(!p.style) {
            p.style = {};
        }

        p.style.vscroll = true;

        // Создаем и нсатраиваем корень дерева
        if(typeof(p.root)=="string") {
            p.root = {text:p.root};
        }

        if(!p.root) {
            p.root = {};
        }

        p.root.parent = "xca0opez7fl2np56qm1b";
        p.root.depth = 0;
        p.root.id = 0;
        p.root.text = p.root.text || "/";
        p.root.expanded = true;
        p.data.unshift(p.root);

        if(p.showRoot===undefined) {
            p.showRoot = true;
        }

        p.editKey = p.editKey || "text";

        // Быстрые обработчики событий
        if(!p.listeners) {
            p.listeners = {};
        }
        if(p.onselect) {
            p.listeners.select = p.onselect;
        }
        if(p.onclick) {
            p.listeners.click = p.onclick;
        }

        this.base(p);
        this.heap = {};
        this.selection = {};
        this.children = {};
        this.private_expandedNodes = {};
    },

    /**
     * Рендерит компонент дерева
     * Вызывает загрузкик
     * Добавляет ноды из this.data
     **/
    cmd_render:function(c) {

        this.base(c);

        for(var i=0;i<this.data.length;i++) {
            this.cmd("addNode",this.data[i]);
        }

        if(this.loader) {
            this.cmd("load",0);
        }

        this.__body.addClass("inx-unselectable");
        inx.storage.onready(this,"restoreExpanded");

        this.private_rootContainer = $("<div>");
        this.cmd("html",this.private_rootContainer);

    },

    /**
     * Изменяет ноду
     * Добавляет свойства из obj в ноду
     **/
    cmd_updateNode:function(id,obj) {
        var node = this.info("node",id);
        if(!node) {
            return;
        }
        for(var i in obj) {
            node[i] = obj[i];
        }
        this.private_updateNode(id);
    },

    /**
     * Возвращает данные ноды
     * Если передан параметр key, возвращает элемент этого массива
     **/
    info_node:function(id,key) {
        var ret = this.heap[id];
        if(key && ret) {
            ret = ret[key];
        }
        return ret;
    },

    /**
     * Возвращает массив с id потомков ноды
     **/
    info_children:function(id) {
        var ret = [];
        var c = this.children[id] || [];
        for(var i in c) {
            ret.push(c[i]);
        }
        return ret;
    },

    /**
     * Возвращает соседей (массив id) данной ноды, включая саму ноду
     **/
    info_siblings:function(id) {
        var node = this.info("node",id);
        if(!node) {
            return [];
        }
        var parent = node.parent;
        return this.info("children",parent);
    },
    
    cmd_setLoader:function(loader) {
        this.loader = inx.deepCopy(loader);
    },

    /**
     * Загружает ноду id
     **/
    cmd_load:function(id) {

        if(!this.loader) {
            inx.msg("inx.tree loader is not defined",1);
            return;
        }
        
        var loader = inx.deepCopy(this.loader);

        loader.id = id;
        this.fire("beforeload",loader);
        this.loadingNodes++;
        this.call(
            loader,
            [this.id(),"handleNodeLoad"],
            [this.id(),"privateLoadFailed"],
            {nodeID:id});

        // Ставим у ноды пометку о том что она грузится
        this.cmd("updateNode",id,{loading:true});
    },

    /**
     * Внутрений обработчик неудачной загрузки
     **/
    cmd_privateLoadFailed:function() {
        inx.msg("Load failed");
    },

    /**
     * Внутрений обработчик удачной загрузки
     **/
    cmd_handleNodeLoad:function(fullData,meta) {

        if(!fullData) {
            fullData = [];
        }

        if(!(fullData.data instanceof Object)) {
            fullData.data = fullData;
        }

        var data = fullData.data;

        if(!data) {
            data = [];
        }

        this.loadingNodes--;

        var parent = meta.nodeID;

        // Не могу понять зачем это, но пусть будет
        if(!parent) {
            parent = 0;
        }
            

        // Сохраняем список того, что было выделено
        var keep = this.info("selection");

        var children = this.info("children",parent);
        this.cmd("removeNode",children);

        for(var i=0;i<data.length;i++) {

            data[i].parent = parent;

            this.cmd("addNode",data[i]);

            // Раскрываем ноду если она хранится в списке раскрытых
            if(this.private_expandedFromStorage) {
                if(this.private_expandedFromStorage[data[i].id]) {
                    this.cmd("expand",data[i].id);
                }
            }
        }

        // Выделяем то чтоы было выделено до перезагрузки
        this.cmd("select",keep);

        // Разворачиваем загруженную ноду
        this.cmd("expand",parent);

        // Отправляем событие об успешной загрузке
        this.fire("load",parent,fullData,meta);

        // Обновляем ноду-родитель. Снимаем флаг загрузки
        this.cmd("updateNode",parent,{loading:false,loaded:true});

        // Обновляем ноды немедленно, чтобы дерево не дергалось
        this.cmd("updateNodes");
    },

    /**
     * Ставим ноду в очередь на обновление
     * При обновлении будут созданы все необхомые элементы, если их еще нет
     **/
    private_updateNode:function(id) {
        if(!this.nodes_to_update)
            this.nodes_to_update = [];
        this.nodes_to_update.push(id);
        this.task("updateNodes");
    },

    /**
     * Ставим ноду в очередь на обновление
     * При обновлении будут создан элемен ноды, если его еще нет
     **/
    cmd_updateNodes:function() {

        this.task("syncLayout");

        var nodes = this.nodes_to_update;
        this.nodes_to_update = [];

        for(var n=0;n<nodes.length;n++) {

            var node = this.heap[nodes[n]];

            if(!node)
                continue;

            // Создаем элемент для ноды если он еще не был создан
            if(!node.el)
                this.cmd("createNodeElement",node);

            // Рендеим потомков, если они еще не отрендерены и данная нода раскрыта
            if(!node.__childrenRendered && node.expanded) {
                this.cmd("eachChild",function(id) { this.private_updateNode(id) });
                node.__childrenRendered = true;
            }

            this.private_renderNode(node);
        }
        
    },

    /**
     * Создает элемент ноды
     **/
    cmd_createNodeElement:function(node) {

        node.el = $("<div>").addClass("inx-tree-node");
        node.el.data("id",node.id);

        node.collapser = $("<img>").addClass("inx-tree-arrow").appendTo(node.el);
        node.body = $("<div>").css("padding","2px 0px 2px 0px").appendTo(node.el);
        var depth = (1 + node.depth + (this.showRoot ? 0:-1))*20;
        if(depth<0)
            depth = 0;
        node.body.css({
            paddingLeft:depth
        });
        node.collapser.css({left:depth-22});

        if(node.id==0 && !this.showRoot) {
            node.body.hide();
            node.collapser.hide();
        }

        // Прикрепляем ноду к родителю (dom)
        if(node.parent==="xca0opez7fl2np56qm1b")

            // Корень прикрепляем к __body
            node.el.appendTo(this.private_rootContainer);
        else {

            // Все ноды кроме корня прикрепляем к контейнеру потомков родителя ноды
            var parent = this.info("node",node.parent);
            var c = parent.childrenContainer;

            // Создаем контейнер потомков
            if(!c) {
                c = $("<div>").appendTo(parent.el);
                parent.childrenContainer = c;
                this.private_updateNode(node.parent);
            }
            node.el.appendTo(c);
        }

    },

    private_renderNode:function(node) {
        if(node.selected) node.body.addClass("selected")
        else node.body.removeClass("selected");

        if(node.childrenContainer) {
            node.childrenContainer.css({display: node.expanded ? "block" : "none"});
        }

        node.body.html(node.text+"");

        // Иконка
        var icon = inx.img(node.icon);
        if(icon) {
            $("<img>").prependTo(node.body).addClass("inx-tree-icon").attr({align:"absmiddle"}).css({marginRight:4}).attr("src",icon);
        }

        // Стрелка
        var arrow = "noarrow";
        if(this.info("children",node.id).length || node.folder) {
            arrow = node.expanded ? "minus" : "plus";
        }
        
        var img = inx.path("%res%/img/components/tree/"+arrow+".gif");
        node.collapser.attr("src",img);
    },

    cmd_expand:function(nodes,no) {

        if(typeof(nodes)!="array") nodes = [nodes];
        for(var i=0;i<nodes.length;i++) {
            var node = this.heap[nodes[i]];
            if(node) {
                var e2 = !no;
                if(e2!=node.expanded) {
                    if(e2) this.fire("expand",node.id);
                    node.expanded = e2;

                    if(node.expanded) this.private_expandedNodes[node.id] = true;
                    else delete this.private_expandedNodes[node.id];
                    this.task("storeExpanded");

                    // Загружаем ноду
                    if(this.loader)
                    if(node.expanded && !node.loading)
                        if(!node.loaded || this.loadOnEachExpand) this.cmd("load",node.id);

                    this.private_updateNode(node.id);
                }
            }
        }
    },

    cmd_collapse:function(nodes) {
        this.cmd("expand",nodes,true);
    },

    // Сворачивает или разворачивает ноду автоматически
    cmd_toggleCollapse:function(id) {
        var node = this.heap[id];
        if(!node) return;
        node.expanded ? this.cmd("collapse",id) : this.cmd("expand",id);
    },

    // Добавляет ноду в кучу и возвращает ее id
    // id нужно, т.к. он может быть присвоен при добавлении
    private_addToHeap:function(node) {

        if(!node) return null;
        if(node.id===undefined) node.id = inx.id();
        if(this.info("node",node.id)) {
            inx.msg("Такая нода уже есть ("+node.id+")");
            return null;
        }

        // Расчитываем глубину
        var parent = this.info("node",node.parent);
        node.depth = parent ? parent.depth+1 : 0;

        // Добавляем саму ноду
        this.heap[node.id] = node;

        return node.id;
    },

    // Удаляет ноду из this.heap и this.selection
    // Удаляет саму ноду и все дочерние
    private_removeFromHeap:function(ids) {

        for(var i=0;i<ids.length;i++) {

            var id = ids[i];
            var node = this.heap[id];

            var children = this.info("children",id);

            // Рекурсивно удаляем всех потомков
            this.private_removeFromHeap(children);

            // Удаляем ноду из всех хранилищ
            delete this.heap[id];
            delete this.selection[id];
            delete this.children[id];
            delete this.private_expandedNodes[id];

            // Удаляем ноду из списка потомков родителя
            var c = this.children[node.parent];
            var cc = [];
            for(var ii in c)
                if(c[ii]!=id)
                    cc.push(c[ii]);

            this.children[node.parent] = cc;


        }
    },

    cmd_select:function(selection,add) {

        if(typeof(selection)!="array") selection = [selection];

        // Проверяем, изменилось ли выделение
        var tmp = {};
        for(var i=0;i<selection.length;i++)
            tmp[selection[i]] = 1;
        for(var i in this.selection)
            tmp[i]--;

        changed = false;
        for(var i in tmp)
            if(tmp[i])
                changed = true;

        if(!changed) return;

        // Убираем предыдущее выделение
        if(!add)
        for(var i in this.selection) {
            var node = this.heap[i];
            node.selected = false;
            this.private_updateNode(node.id);
            delete this.selection[i];
        }

        // Добавляем новое выделение
        for(var i=0;i<selection.length;i++) {
            var node = this.heap[selection[i]];
            if(node && (node.selectable!==false)) {
                node.selected = true;
                this.private_updateNode(node.id);
                this.selection[selection[i]] = true;

                // Раскрываем родителя выбранной ноды
                this.cmd("expand",node.parent);
            }
        }
        this.cmd("scrollToSelection");
        this.fire("selectionchange",this.info("selection"));
    },

    // Возвращает текущее выделеие.
    // Массив с id выделенных нод
    info_selection:function() {
        var ret = [];
        for(var i in this.selection) ret.push(i);
        return ret;
    },

    cmd_selectUp:function() {
        var sel = this.info("selection")[0];
        var flag = 0;
        this.cmd("eachVisible",function(id) {
            if(flag) {
                this.cmd("select",id);
                flag = false;
            }
            if(id==sel) flag = true;
        },true);
    },

    cmd_selectDown:function() {
        var sel = this.info("selection")[0];
        var flag = 0;
        this.cmd("eachVisible",function(id) {
            if(flag) {
                this.cmd("select",id);
                flag = false;
            }
            if(id==sel) flag = true;
        });
    },

    cmd_scrollToSelection:function() {
        /*var sel = this.info("selection")[0];
        if(!sel) return;
        var node = this.info("node",sel);
        if(!node) return;
        if(!node.body) return;
        inx.core.scrollTo(node.body); */

        // Сделать

    },

    cmd_addNode:function(node) {

        if(!(node instanceof Object)) {
            inx.msg("inx.tree.addNode(): node cannot be "+typeof(node),1);
            return;
        }

        if(!node.parent)
            node.parent = 0;

        // Добавляем ноду в хранилище
        var id = this.private_addToHeap(node);
        if(id===null) return;

        // Расчитываем глубину
        var parent = this.info("node",node.parent);

        // Без родителя может быть только корнкевая нода
        if(!parent && node.parent!="xca0opez7fl2np56qm1b") return;

        if(node.parent!="xca0opez7fl2np56qm1b")
            node.depth = parent.depth+1;
        else
            node.depth = 0;

        // Добавляем ноду в список потомков ее родителя
        if(!this.children[node.parent]) this.children[node.parent] = [];
        this.children[node.parent].push(id);

        // Обновляем ноду
        this.private_updateNode(id);

        // Добавляем потомков
        var c = node.children || [];

        for(var i in c) {
            c[i].parent = node.id;
            this.cmd("addNode",c[i]);
        }

    },

    cmd_removeNode:function(nodes) {

        // Упрощаем себе жизнь
        if(typeof(nodes)!="object") nodes = [nodes];

        for(var i=0;i<nodes.length;i++) {
            var node = this.info("node",nodes[i]);
            if(node) {
                $(node.el).remove();
                this.cmd("updateNode",node.parent);
            }
        }

        this.private_removeFromHeap(nodes);

    },

    cmd_dblclick:function(e) {

        var arrow = $(e.target).filter(".inx-tree-arrow").length;
        if(arrow)
            return;

        clearTimeout(this.private_editTimeout);
        this.private_lastClickNode = null;

        var node = this.private_domToNodeObject(e.target);
        if(!node) return;
        if(this.fire("dblclick",node.id)!==false)
            this.cmd("toggleCollapse",node.id);
        return false;
    },

    cmd_click:function(e) {

        var node = this.private_domToNodeObject(e.target);
        var arrow = $(e.target).filter(".inx-tree-arrow").length;
        if(arrow) {
            this.cmd("toggleCollapse",node.id);
            return;
        }

        if(!node) return;

        // Проверка медленного нажатия два раза
        var time = new Date().getTime();
        var d = time-this.private_lastClickTime;
        if(d<1500 & node.id==this.private_lastClickNode) {
            var id = this.id();
            clearTimeout(this.private_editTimeout);
            this.private_editTimeout = setTimeout(function() {
                inx(id).cmd("editNode",node.id);
            },600);
        }
        this.private_lastClickTime = time;
        this.private_lastClickNode = node.id;

        this.cmd("select",node.id,e.ctrlKey);
        this.fire("click",node.id,e);
    },

    /**
     * Возвращает ноду, содержащую указанный dom-элемент.
     * Если ноды не обнаружено, вернет null
     **/
    private_domToNodeObject:function(el) {
        var id = $(el).parents(".inx-tree-node").eq(0).data("id");
        return this.info("node",id);
    },

    cmd_keydown:function(e) {

        switch(e.keyCode) {

            case 113:
                var sel = this.info("selection")[0];
                if(sel!==undefined)
                    this.cmd("editNode",sel);
                return false;

            case 38:
                this.cmd("selectUp");
                return false;
            case 40:
                this.cmd("selectDown");
                return false;


            // Вперед
            case 39:
                var sel = this.info("selection")[0];
                this.cmd("expand",sel);
                break;
            // Назад
            case 37:
                var sel = this.info("selection")[0];
                this.cmd("collapse",sel);
                break;

            case 13:
                var sel = this.info("selection")[0];
                if(sel) {
                    this.fire("click",sel);
                    this.fire("dblclick",sel);
                }
                break;
        }
    },

    /**
     * Возвращает путь к указанной ноде
     **/
    info_path:function(id,params) {
    
        if(!params) {
            params = {};
        }
    
        if(!params.separator) {
            params.separator = "/";
        }
        
        if(!params.key) {
            params.key = "text";
        }
        
        var node = this.info("node",id);
        var path = [];
        while(node) {
            if(node.id!=0) {
                path.unshift(node[params.key]);
            }
            node = this.info("node",node.parent);
        }
        return path.join(params.separator);
    },

    info_debug:function() {
        var ret = "";
        var n = 0;
        for(var i in this.heap)n++;
        ret+= "heap:"+n+"<br/>";
        n=0;
        for(var i in this.private_expandedNodes)n++;
        ret+= "expanded:"+n;
        return ret;
    },

    /**
     * Начинает редактирвоание ноды
     **/
    cmd_editNode:function(id) {
        var node = this.info("node",id);
        if(!node) return;
        if(!node.editable) return;
        var el = node.body;
        if(!el) return;
        var pos = el.offset();
        if(!pos) return;
        pos.left += node.depth*20+20;
        pos.top-=27;

        this.cmd("closeEditor");

        var input = inx({
            type:"inx.textfield",
            style:{
                autoWidth:true
            },
            value:node[this.editKey],
            name:"editor"
        }).task("focus");

        this.private_editor = inx({
            type:"inx.dialog",
            style:{
                border:0
            },
            x:pos.left,
            y:pos.top,
            modal:false,
            width:200,
            title:"Редактирование",
            items:[input],
            autoDestroy:true,
            nodeID:node.id
        }).cmd("render")
        .on("destroy",[this.id(),"focus"])
        .on("submit",[this.id(),"private_handleEditor"]);

    },

    cmd_closeEditor:function() {
        inx(this.private_editor).cmd("destroy");
    },

    cmd_private_handleEditor:function() {
        var val = inx(this.private_editor).info("data").editor;
        var id = inx(this.private_editor).info("param","nodeID");
        this.cmd("closeEditor");
        var data  = {};
        data[this.editKey] = val;
        var old = this.info("node",id,this.editKey);
        this.cmd("updateNode",id,data);
        this.fire("editComplete",id,val,old);
    },

    info_expandedNodes:function(parent,ret) {
        if(!parent) parent = 0;
        if(!ret) ret = [0];
        var c = this.info("children",parent);
        for(var i in c) {
            var node = this.info("node",c[i]);
            if(node.expanded) {
                ret.push(node.id);
                this.info("expandedNodes",node.id,ret);
            }
        }
        return ret;
    },

    cmd_storeExpanded:function() {
        if(!this.keepExpanded) return;
        if(inx(this).data("currentRequests")) return;
        inx.storage.set(this.keepExpanded,this.private_expandedNodes);
    },

    cmd_restoreExpanded:function() {
        if(!this.keepExpanded) return;
        var exp = inx.storage.get(this.keepExpanded) || {};
        this.private_expandedFromStorage = exp;

        for(var i in exp)
            this.cmd("expand",i);
    },

    cmd_eachNode:function (f) {
        for(var i in this.heap)
            f.apply(this,[this.heap[i]]);
    },

    cmd_eachChild:function (f) {
        var children = this.info("children");
        for(var i in children)
            f.apply(this,[children[i]]);
    },

    cmd_eachSelected:function (f) {
        var sel = this.info("selection");
        for(var i in sel)
            f.apply(this,[sel[i]]);
    },

    cmd_eachVisible:function(f,reverse,parent) {

        if(!parent) parent = 0;
        if(parent!=0 && !this.info("node",parent).expanded) return;

        var children = this.info("children",parent);
        if(!reverse)
            for(var i=0;i<children.length;i++) {
                f.apply(this,[children[i]]);
                this.cmd("eachVisible",f,reverse,children[i]);
            }
        else
            for(var i=children.length-1;i>=0;i--) {
                this.cmd("eachVisible",f,reverse,children[i]);
                f.apply(this,[children[i]]);
            }
    }


});
