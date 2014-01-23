// @link_with_parent

inx.ns("inx.code.lang").js = {
    normal:{
        triggers:[
            {re:/\d+/,name:"digit"},
            {re:/\/\*/,name:"comment_block"},
            {re:/"/,name:"string"},
            {re:/\/\/.*/,name:"comment"},
            {re:/\/([^\/]|(\\_\/))+\//,name:"regex"},
            {re:/\bfunction\b|\breturn\b|\bfor\b|\bvar\b|\bin\b|\bthis\b|\bif\b|\bwhile\b/,name:"keyword"}
        ],
        style:"normal"
    },
    regex:{
        style:"variable"
    },    
    regex_escape_slash:{style:"string"}, 
    comment_block:{
        triggers:[
            {re:/\*\//,name:"back"}
        ],
        style:"comment"
    },
    digit:{style:"digit"},
    string:{
        triggers:[
            {re:/"/,name:"back"},
            {re:/\\"/,name:"quote_escape"}
        ],
        style:"string"
    },
    quote_escape:{},
    keyword:{style:"keyword"},
    comment:{style:"comment"}        
}