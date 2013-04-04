// @link_with_parent
// @priority 1000

inx.service = function(name) {
    return inx.service.services[name];
}

inx.service.services = {}

inx.service.register = function(name,obj) {
    inx.service.services[name] = obj;    
}