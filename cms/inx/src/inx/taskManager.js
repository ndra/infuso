// @link_with_parent

inx.taskManager = {

    taskList:{},
    active:true,
    task:function(id,name,time) { 
    
        time = time || 0;
        
        var key = id+":"+name;
        var val = inx.taskManager.taskList[key];
        time = val===undefined ? time : Math.max(time,val);
        inx.taskManager.taskList[key] = time;
        
        if(!inx.taskManager.timeout)
            inx.taskManager.timeout = setTimeout(function(){
                inx.taskManager.exec();
            });
    },
    
    deleteTasks:function(id) {
        var tasks = {};
        for(var key in inx.taskManager.taskList) {
            var d = key.split(":");
            if(id!=d[0])
                tasks[key] = inx.taskManager.taskList[key];
        }
        inx.taskManager.taskList = tasks;
    },
    
    tick:function() {
        for(var key in inx.taskManager.taskList)
            inx.taskManager.taskList[key] -= 50; 
        inx.taskManager.exec();
    },
    
    exec:function(dep) {
    
        if(!inx.taskManager.active)
            return;
            
        var taskNow = {};
        var taskDelayed = {};
        for(var key in inx.taskManager.taskList) {
            var time = inx.taskManager.taskList[key];
            if(time>0)
                taskDelayed[key] = time;
            else
                taskNow[key] = time;
        }
        inx.taskManager.taskList = taskDelayed;

        dep = dep ? dep+1 : 1;
        
        if(dep>100) {
            alert("Task depth limit. Stop at "+l[0][0]+" : "+l[0][1]);
            inx.taskManager.active = false;
            return;
        }
        
        inx.taskManager.timeout = false;
        
        var n = 0;
        for(var key in taskNow) {  
            var task = key.split(":");
            inx(task[0]).cmd(task[1]);
            n++;
        }
        
        if(n)
            inx.taskManager.exec(dep);
        
    }
}

setInterval(inx.taskManager.tick,50)
